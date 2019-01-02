<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Member;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class MemberSyncController extends Controller
{
    use ProcessesMemberRecordsTrait;

    const SSO_SCOPE_NAME = 'sync-members';
    
    /**
     * Accepts a list of forums usernames, and Flares compares the list with its members. 
     * Users will be grouped into existing and not existing
     * 
     * @param  Request  $request
     * @return Response
     */
    public function presync(Request $request) 
    {
        if ($request->has('forums_usernames')) {
            $forumsUsernames = $request->input('forums_usernames');
            if (is_array($forumsUsernames)) {
                // Split up and check the database for each of these forums usernames
                // Make sure the usernames are all lowercased
                $forumsUsernames = collect($forumsUsernames)->map(function ($item) {
                    return strtolower($item);
                })->all();
                $existing = $this->getExistingForumsUsernames($forumsUsernames);
                $notExisting = collect($forumsUsernames)->filter(function ($name) use ($existing) {
                    return !$existing->contains($name);
                });

                return response()->json([
                    'existing' => $existing->values()->all(),
                    'unmatched' => $notExisting->values()->all(),           // use ->all() on the result of a collection filter
                ]);
            } else {
                return response()->json([
                    'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'Forums usernames must be an array']
                ], 400);
            }
        } else {
            return response()->json([
                'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'Member postdata missing']
            ], 400);
        }
    }


    /**
     * Create new member records for the given user list
     *
     * @param  Request  $request
     * @return Response
     */
    public function sync(Request $request)
	{
		$results = [];
		
		// Deal with the context data
		$context = $this->getContextDefaults();
		if ($request->has('context')) {
			$postDataContext = $request->input('context');
			if (!empty($postDataContext['onboardingType'])) {
				$contextDefaults = $this->getContextDefaults($postDataContext['onboardingType']);
			} else {
				$contextDefaults = $this->getContextDefaults();
			}
			
			if (!empty($postDataContext['hasOverrides'])) {
				$massOverrideKeys = ['newRank', 'newPlatoon', 'newPosting'];
				foreach ($massOverrideKeys as $overrideKey) {
					if (!empty($postDataContext[$overrideKey])) {
						$contextDefaults[$overrideKey] = $postDataContext[$overrideKey];
					}
				}
				if (!empty($postDataContext['thisYear'])) {
					$contextDefaults['thisYear'] = substr($postDataContext['thisYear'], 2, 2);
				}
				if (!empty($postDataContext['thisCycle'])) {
					$contextDefaults['thisCycle'] = substr($postDataContext['thisCycle'], 0, 1);
				}
			}
			
			$context = $contextDefaults;
		}

        // Expect 'members' is an array
		if ($request->has('members')) {
			// Deal with the member data
            $membersData = $request->input('members');
            if (is_array($membersData)) {
                DB::beginTransaction();
                try {
                    $existing = $this->getExistingForumsUsernames();       // Note this returns a collection()

                    foreach ($membersData as $postDataMember) {

                        if (empty($postDataMember['forums_username'])) {
                            throw new \Exception('Missing forums username key', ResponseCodes::ERR_POSTDATA_MISSING);
                        } else {
                            $postDataMember['forums_username'] = strtolower($postDataMember['forums_username']);        // Always lowercase
                        }

                        if ($existing->contains($postDataMember['forums_username'])) {
                            // Don't attempt to add duplicate forums_username records (which will fail an integrity check anyway)
                            $results[$postDataMember['forums_username']] = null;
                            continue;
                        }

                        // Generate a regimental number based on context (remember to apply overrides to ->thisYear and ->thisCycle as required)
                        $newRegtNum = $this->generateStandardRegtNumber($context);
                        if (!empty($newRegtNum)) {

                            if (!empty($postDataMember['sex']) && $postDataMember['sex'] == 'F') {
                                $newRegtNum .= 'F';
                            }
                            
                            // Assign regt num, create new record, and generate initial posting
                            $postDataMember['regt_num'] = $newRegtNum;

                            try {
                                $newMember = new Member();
                                $newMember->fill($postDataMember);
                                $newMember->regt_num = $postDataMember['regt_num'];     // guarded
                                $newMember->forums_username = $postDataMember['forums_username'];       // guarded
                                $newMember->is_enrolled = 1;       // guarded
                                $newMember->save();   
                                $initialPostingId = $this->generateInitialPostingRecord($newMember->regt_num, $context);
                                $results[$postDataMember['forums_username']] = [
                                    'id' => $newMember->regt_num,       // For dumb clients who don't know what regt_num is. 
                                    'regt_num' => $newMember->regt_num, 
                                    'posting_id' => $initialPostingId,
                                ]; 
                            } catch (\Exception $ex) {
                                throw new \Exception('Looks like the database rejected this member record. ' . "Expected regt num: $newRegtNum", ResponseCodes::ERR_REGT_NUM);
                            }
                            
                        } else {
                            throw new \Exception('Could not generate a valid regt num', ResponseCodes::ERR_REGT_NUM);
                        }
                    }
                    
                    DB::commit();
                    return response()->json([
                        'results' => $results,
                    ]);
                } catch (\Exception $ex) {
                    DB::rollBack();
                    return response()->json([
                        'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
                    ], 500);
                }
            } else {
                return response()->json([
                    'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'Member postdata expected an array of members']
                ], 400);
            }
		}
		
        return response()->json([
            'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'Member postdata missing']
        ], 400);
    }

    private function getExistingForumsUsernames($forumsUsernames = 0) {
        // Use DB:: facade on purpose... to avoid overhead of creating User objects?
        $userTableName = with(new Member)->getTable();
        $existingUsersQuery = DB::table($userTableName)->select('forums_username');
        if (is_array($forumsUsernames)) {
            $existingUsersQuery = $existingUsersQuery->whereIn('forums_username', $forumsUsernames);
        } else {
            $existingUsersQuery->whereNotNull('forums_username');
        }
        $existingUsers = $existingUsersQuery->get();
        return $existingUsers->map(function ($result) {
            return strtolower($result->forums_username);
        });
    }

    private function checkAccessErrors(Request $request) {
        // Check for access to delete users
        $requester = $request->user();
        if (empty($requester) || empty($requester->access_level) || $requester->access_level < User::ACCESS_ASSIGN) {
            return ['code' => ResponseCodes::ERR_P_INSUFFICIENT, 'reason' => 'Only ACCESS_ASSIGN or above can sync members'];
        }
        // Specifically check for SSO scope before actioning
        if (!$requester->tokenCan(self::SSO_SCOPE_NAME)) {
            return ['code' => ResponseCodes::ERR_P_OAUTH_SCOPE, 'reason' => 'OAuth token requires scope '.self::SSO_SCOPE_NAME.' to perform this action'];
        }
        return null;        // No issues!
    }
    
    
}