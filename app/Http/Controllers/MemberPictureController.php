<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use DB;
use App\MemberPicture;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberPictureController extends MemberControllerBase
{		
	private $tmpDir;			// Use the PHP default
	
	public function __construct(){
		$this->tmpDir = sys_get_temp_dir();
	}
	
	public function check(){
		$config = new \Flow\Config();
		$config->setTempDir($this->tmpDir);		
		$file = new \Flow\FileReadable($config);
		
		if ($file->checkChunk()) {
			return response('', Response::HTTP_OK);				// 200
		} 
		return response('', Response::HTTP_NO_CONTENT);		// 204
	}
	
    public function store(Request $request, $memberId)
    {	
		$config = new \Flow\Config();
		$config->setTempDir($this->tmpDir);
		$file = new \Flow\FileReadable($config);

		if ($file->validateChunk()) {
			$file->saveChunk();
		} 
		else {
			// error, invalid chunk upload request, retry
			return response('', Response::HTTP_BAD_REQUEST);		// 400
		}

		// Check for completion
		if ($file->validateFile()) {
			$blob = $file->saveToStream();
			
			$mp = new MemberPicture();
			$mp->regt_num = $memberId;
			$mp->photo_blob = $blob;	
			$mp->save();
			
			return response('Upload OK', Response::HTTP_CREATED);		// 201
		}
		return response('', Response::HTTP_ACCEPTED);		// 202
    }

    public function show($memberId)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
