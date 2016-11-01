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


class DecorationController
{		

	private $tmpDir;			// Use the PHP default
	
	public function __construct(){
		$this->tmpDir = sys_get_temp_dir();
	}

    public function store(Request $request, $decorationId)
    {	
		// Upload a chunk, then
		// check if all chunks are done. If so
		// save the chunk to the database
		
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
			
			$mimeType = $this->parseImageMimeType(strrchr($file->name(), '.'));
			if (!$mimeType){
				// Not going to save if we don't know the mime type
				return response('Cannot determine image mime type from filename: ' . $file->name(), Response::HTTP_UNSUPPORTED_MEDIA_TYPE); 	// 415
			}
            
            // TODO: Shrink this image
            
            // Shrink this image

			$dec = Decoration::find($decorationId);
			$dec->icon_blob = $blob;
			$dec->icon_mime_type = $mimeType;
            
            // $dec->icon_w = $file->
            // $dec->icon_h = $file->
			$dec->save();
            
			return response('Upload OK', Response::HTTP_CREATED);		// 201
		}
		
		return response('', Response::HTTP_ACCEPTED);		// 202
    }

	/*
	 * Used as a query by the client-side o check if a chunk was uploaded yet
	 */
	public function chunkCheck(){	
		$config = new \Flow\Config();
		$config->setTempDir($this->tmpDir);		
		$file = new \Flow\FileReadable($config);
		
		if ($file->checkChunk()) {
			return response('', Response::HTTP_OK);				// 200
		} 
		return response('', Response::HTTP_NO_CONTENT);		// 204
	}
	
	/*
	 * Return a status code and JSON obj indicating if the image resource/s exists (without returning
	 * the actual image data)
	 */
	public function exists($decorationId){
        $dec = Decoration::findOrFail($decorationId);
        return response()->json([ 'exists' => true ]);
	}

	/*
	 * Serve the image as a resource
	 */
    public function show($decorationId)
    {
        // Get the most recent image, serve it as whatever mimetype is recorded
		$dec = Decoration::findOrFail($decorationId);
        if ($mp->icon_blob !== null){
            return response($mp->icon_blob)->header('Content-Type', $mp->icon_mime_type);            
        } elseif ($mp->icon_uri !== null) {
            $url = $mp->icon_uri;
            // Assume it's a local image if not fully qualified url
            if (!str_contains($mp->icon_uri, '://')){   
                $url = secure_asset($url);                
            }
            return redirect($url);
        }
    }

    public function destroy(Request $request, $decorationId)
    {
        try {
            
            $dec = Decoration::findOrFail($decorationId);
            $dec->icon_blob = null;
            $dec->icon_mime_type = null;
            $dec->icon_uri = null;
            $dec->icon_w = null;
            $dec->icon_h = null;
            $dec->save();            
            return response('', Response::HTTP_NO_CONTENT);
            
        } catch (\Exception $ex) {
            
            // Not perfect... maybe filter based on 404 or otherwise
            return response('', Response::HTTP_NOT_FOUND);
            
        }
    }
	
	
	
	// =======
	// Private
	
	private function parseImageMimeType($ext)
	{
		if (strpos($ext, '.') === 0){
			// remove leading dot
			$ext = substr($ext, 1);
		}
		
		$ext = strtolower($ext);
		switch ($ext){
			case 'png':
				return 'image/png';
			case 'jpg':
			case 'jpeg':
				return 'image/jpeg';
		}
		
		return false;
	}
}
