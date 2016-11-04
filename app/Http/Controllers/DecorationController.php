<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;
use App\Decoration;
use App\Member;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class DecorationController extends Controller
{		

	private $tmpDir;			// Use the PHP default
	
	public function __construct(){
		$this->tmpDir = sys_get_temp_dir();
	}
    
    /**
     * Persist a newly created resource.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
	{
		if ($request->has('decoration') && count($request->input('decoration')) > 0){

            $postData = $request->input('decoration');
            
			DB::beginTransaction();
			try {
                
                $dec = Decoration::create($postData);
                DB::commit();
                
                return response()->json([
                    'id' => $dec->dec_id,
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
            'error' => ['flaresCode' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'Decoration postdata missing']
        ], 400);
    }
    
    /**
     * Update the specified resource.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
	{
        $updated = false;
		try {
			if ($request->has('decoration')){
				$updated = Decoration::updateOrCreate(['dec_id' => $id], $request->input('decoration'));
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
    
    /*
	 * Retrieve all the decorations
	 */
    public function index()
    {
        $dec = Decoration::all();
        return response()->json([
            'decorations' => $dec->toArray()
        ]);
    }

	/*
	 * Retrieve the decoration
	 */
    public function show($id)
    {
        $dec = Decoration::findOrFail($id);
        return response()->json([
            'decoration' => $dec->toArray()
        ]);
    }

    public function destroy(Request $request, $id)
    {
		try {
			
            Decoration::destroy($id);
            return response('', 204);
			
		} catch (\Exception $ex) {
            
            return response()->json([
                'error' => [ 
                    'flaresCode' => ResponseCodes::ERR_IMAGE_DELETION, 
                    'code' => $ex->getCode(), 
                    'reason' => $ex->getMessage(),
                ]
            ], 500);
            
        }
    }

}
