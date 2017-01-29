<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Log;
use App\Member;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class MemberController extends Controller
{
    use ProcessesMemberRecordsTrait;
    
    //const ResponseCodes::ERR_POSTDATA_MISSING = 4001;
	//const ResponsesCodes::ERR_POSTDATA_FORMAT = 4002;
	// const ResponseCodes::ERR_EX = 5000;
	// const ResponseCodes::ERR_DB_PERSIST = 5001;
	// const ResponseCodes::ERR_REGT_NUM = 5002;
    
    protected $orderByAliases;
    
    public function __construct(){
        $this->orderByAliases = collect([
            'CREATED' => ['created_at'],
            'NAME' => ['last_name', 'first_name'],
            'SCHOOL' => ['school'],
        ]);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
	{
        $members = Member::with('current_posting', 'current_platoon', 'current_rank')->get();
        $membersFlat = $members->toArray(); 
        $this->transformMembersPostingPromo($membersFlat);
		
		// TODO: pagination
		return response()->json([
            // todo: any other way other than manual transforms?
            // 'members' => $this->transformMemberPostingPromo($members->all())
            'members' => $membersFlat
        ]);
    }
    
    
    /**
     * Search the resource by providing a querystring
     *
     * @return Response
     */
    public function search(Request $request)
	{
		// ?discharged -- [ none | include | only ]
		$withDischarged = $request->query('discharged', 'none');
		if ($withDischarged == 'include'){
			$query = Member::withTrashed();
		}
		elseif ($withDischarged == 'only'){
			$query = Member::onlyTrashed();
		}
		else {
			$query = Member::select();
		}
        
        // Determine sort order. The param value must be mapped to prevent injection.
        $orderByAlias = $request->query('orderBy', 'CREATED');
        $orderBy = collect($this->orderByAliases->get($orderByAlias));
        $orderByDir = strtolower($request->query('orderByDir', 'desc'));
        if (!in_array($orderByDir, ['desc', 'asc'])){
            $orderByDir = 'desc';
        }
		
		// join on pp table 
		$query->join('posting_promo as pp1', function ($join) {
            $join->on('members.regt_num', '=', 'pp1.regt_num');
        });
        // Self-join to reduce numbers per: http://stackoverflow.com/questions/2111384/sql-join-selecting-the-last-records-in-a-one-to-many-relationship
        $query->leftJoin('posting_promo as pp2', function ($join) {
            $join->on('members.regt_num', '=', 'pp2.regt_num')
                ->where(function ($query) {
                    $query->whereColumn('pp1.effective_date', '<', 'pp2.effective_date')
                    ->orWhere(function ($query) {       // resolve effective_date ties
                        $query->whereColumn('pp1.effective_date', 'pp2.effective_date')
                            ->whereColumn('pp1.promo_id', '<', 'pp2.promo_id');
                    });
                });
        });
        $query->whereNull('pp2.promo_id');          // pp1 bubbles to the top
		$query->select('members.*', 'pp1.new_rank as rank');
        $orderBy->each(function($orderByColumn, $key) use ($query, $orderByDir) {
            $query->orderBy($orderByColumn, $orderByDir);
        });

		if ($request->has('keywords')){
			$keywords = explode(' ', $request->query('keywords'));
			foreach ($keywords as $keyword){
				// If keyword resembles a regt number_format
				if ($this->keywordLikeRegtNum($keyword)){
					$query->where('members.regt_num', 'like', "$keyword%");
					continue;
				}
				// If keyword matches a known rank abbrev 
				if ($this->keywordLikeRank($keyword)){
					$rank = $this->keywordExtractRank($keyword);
					$query->where('rank', $rank);
					continue;
				}
				// Otherwise add this as a name search
				$query->where(function($query) use ($keyword){
					$query->where('last_name', 'like', "%$keyword%")
                        ->orWhere('first_name', 'like', "%$keyword%");
				});
			}
		}
		else {
			foreach($request->only('rank', 'last_name', 'first_name', 'regt_num') as $name => $input){
				$input = trim($input);
				if (!empty($input)){
					$query->where($name, 'like', "$input%");
					$query->orderBy($name, 'asc');
				}
			}
			foreach($request->only('sex') as $name => $input){			// ('sex', 'is_active')
				$input = trim($input);
				if (!empty($input)){
					$query->where($name, $input);
				}
			}
		}

		return response()->json([
            'members' => $query->get()
        ]);
    }

    /**
     * Persist a newly created resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
	{
		$recordId = 0;
		$initialPostingId = 0;
		
		// Deal with the context data
		$context = $this->getContextDefaults();
		if ($request->has('context')){
			$postDataContext = $request->input('context');
			if (array_key_exists('name', $postDataContext)){
				$contextDefaults = $this->getContextDefaults($postDataContext['name']);
			}
			else {
				$contextDefaults = $this->getContextDefaults();
			}
			
			if (array_key_exists('hasOverrides', $postDataContext)){
				$massOverrideKeys = ['newRank', 'newPlatoon', 'newPosting'];
				foreach ($massOverrideKeys as $overrideKey){
					if (array_key_exists($overrideKey, $postDataContext)){
						$contextDefaults[$overrideKey] = $postDataContext[$overrideKey];
					}
				}
				if (array_key_exists('thisYear', $postDataContext)){
					$contextDefaults['thisYear'] = substr($postDataContext['thisYear'], 2, 2);
				}
				if (array_key_exists('thisCycle', $postDataContext)){
					$contextDefaults['thisCycle'] = substr($postDataContext['thisCycle'], 0, 1);
				}
			}
			
			$context = $contextDefaults;
		}
		
		if ($request->has('member')){
			// Deal with the member data
			$postDataMember = $request->input('member');
			
			DB::beginTransaction();
			try {
				// Get their regimental number
				$newRegtNum = $this->generateStandardRegtNumber($context) . ($postDataMember['sex'] == 'F' ? 'F' : '');	
				
				if (!empty($newRegtNum)){
					
					// Assign regt num, create new record, and generate initial posting
					$postDataMember['regt_num'] = $newRegtNum;
					$newMember = Member::create($postDataMember);
					
					if ($newMember->regt_num > 0){
						$recordId = $newMember->regt_num;
						$initialPostingId = $this->generateInitialPostingRecord($recordId, $context);	
					}
					else {
						// $error = [
							// 'code' => 'CANNOT_SAVE_REGT_NUM', 
							// 'valueExpected' => $newRegtNum, 
							// 'valueActual' => $newMember->regt_num, 
							// 'reason' => 'Looks like the database rejected this regt num'];
						throw new \Exception('Looks like the database rejected this regt num. ' . "Value Expected: $newRegtNum, Value Actual: {$newMember->regt_num}", ResponseCodes::ERR_REGT_NUM);
					}
					
				}
				else {
					throw new Exception('Could not generate a regt num', ResponseCodes::ERR_REGT_NUM);
				}
                
                DB::commit();
                return response()->json([
                    'id' => $recordId,
                    'initialPostingId' => $initialPostingId
                ]);
			}
			catch (\Exception $ex){
				DB::rollBack();
                return response()->json([
                    'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
                ], 500);
                
			}
		}
		
        return response()->json([
            'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'Member postdata missing']
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
	{
		// Fetch the first match
		// detail -- [ high | med | low ]
		$detailLevel = $request->input('detail', 'low');
		
		// Todo: Add $detailLevel == med
		try {
            if ($detailLevel == 'high'){	
                // Todo: to add more "eager loaded" relationshions, add extra args to Member::with()
                $member = Member::with('postings')->where('regt_num', $id)->withTrashed()->firstOrFail();
            }
            else {
                //  when $detailLevel == low
                $member = Member::where('regt_num', $id)->withTrashed()->firstOrFail();
            }
            return response()->json([
                'member' => $member->toArray()
            ]);
        }
        catch (\Exception $ex){
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 404);
        }
    
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
	{
		$updated = false;
		
		// if (is_array($postData) && array_key_exists('member', $postData)){
		try {
			if ($request->has('member')){
				// $postDataUpdate = $request->member;
				$updated = Member::updateOrCreate(['regt_num' => $id], $request->input('member'));
			}
			else {
				throw new \Exception('Post data incorrect format', ResponseCodes::ERR_POSTDATA_FORMAT);
			}
			return response()->json([
                'id' => $updated
            ]);
		}
		catch (\Exception $ex){
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 400);
		}
		
    }

    /**
     * Remove the specified resource from storage.
	 * Defaults to a soft delete; add ?remove=permanent to do hard delete
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
	{
		// remove -- [ discharge | permanent ]
		$removeMode = $request->query('remove', 'discharge');
		$deleted = 0;
		
		try {
			if ($removeMode == 'permanent'){
				$deletionMember = Member::where('regt_num', $id)->withTrashed()->firstOrFail();
				
				// Todo: future permissions check
				$permissionsCheck = true;
				if ($permissionsCheck || $deletionMember->is_active == '0'){		// Allow anybody to delete inactive records
					$deletionMember->postings()->forceDelete();
					$deletionMember->forceDelete();			// this returns void
					$deleted = true;		// we just presume it worked, since we deleted the postings already
				}
				else {
					throw new \Exception('You don\'t have permission to permanently delete this record', ResponseCodes::ERR_PERM_NOPE);
				}
			}
			else {
				// Soft delete -- read overrides from context
				$deleted = Member::findOrFail($id)->delete();
			}
            
            return response('', 204);
            // return response()->json([
				// 'success' => $deleted,
				// 'deletionMode' => $removeMode,
			// ]);
		}
		catch (\Exception $ex){
            if (!$deleted){
                return response()->json([
                    'error' => ['code' => ResponseCodes::ERR_DELETION, 'deletionResult' => print_r($deleted, true), 'reason' => "Could not delete this record $id"]
                ], 401);
            }
            else {
                return response()->json([
                    'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
                ], 403);
            }
		}
    }
	
}