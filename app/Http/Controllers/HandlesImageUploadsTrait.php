<?php 

namespace App\Http\Controllers;

trait HandlesImageUploadsTrait 
{
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
		
		return null;
	}
}