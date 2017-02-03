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
                
                // Set precedence to be next available within tier
                $maxPrecedenceResult = DB::table('decorations')->select('COALESCE(MAX(precedence), 0) as max')->where('tier', $postData['tier'])->first();
                $maxPrecedence = intval($maxPrecedenceResult->max);
                $nextPrecedence = $maxPrecedence + 1;
                $postData['precedence'] = $nextPrecedence;
                
                // Calculate a shortcode if not provided. Perform collision check.
                
                $collisionAvoidCounter = 0;
                $collisionAvoidMax = 20;
                if (!empty($postData['shortcode'])){
                    $shortcode = $postData['shortcode'];
                    $shortcodeDecollider = function($shortcode, $suffix = '') {
                        return $shortcode . $suffix;
                    };
                }
                else {
                    $shortcodeLength = 6;
                    $shortcodeMasterOffset = 0;
                    $shortcodeMaster = preg_replace('/\s+', '', $postData['name']);
                    $shortcode = substr($shortcodeMaster, $shortcodeMasterOffset, $shortcodeLength);         // emphasis on "short"
                    $shortcodeDecollider = function($shortcode, $suffix = '') use ($shortcodeMaster, &$shortcodeMasterOffset, $shortcodeLength) {
                        if ($shortcodeMasterOffset + $shortcodeLength + 1 < strlen($shortcodeMaster)){
                            $shortcodeMasterOffset++;
                            return substr($shortcodeMaster, $shortcodeMasterOffset, $shortcodeLength);
                        }
                        else {
                            return $shortcode . $suffix;
                        }
                    };
                }
                
                $shortcodeCollisionCheck = DB::table('decorations')->select('1')->where('shortcode', $shortcode)->first();
                $isCollision = !empty($shortcodeCollisionCheck);
                while ($isCollision && $collisionAvoidCounter < $collisionAvoidMax){
                    $collisionAvoidCounter++;
                    $shortcode = $shortcodeDecollider($shortcode, $collisionAvoidCounter);
                    $shortcodeCollisionCheck = DB::table('decorations')->select('1')->where('shortcode', $shortcode)->first();
                    $isCollision = !empty($shortcodeCollisionCheck);
                }
                
                if ($isCollision){
                    DB::rollBack();
                    return response()->json([
                        'error' => ['code' => ERR_DECORATION_SHORTCODES_EXHAUSTED, 'reason' => 'Attempted to generate non-conflicting shortcode but exhausted retries']
                    ], 400);
                }
                
                
                $dec = Decoration::create($postData);
                
                DB::commit();
                return response()->json([
                    'id' => $dec->dec_id,
                ]);
			} catch (\Exception $ex) {
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
			if ($request->has('decoration')) {
				$updated = Decoration::updateOrCreate(['dec_id' => $id], $request->input('decoration'));
			} else {
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
