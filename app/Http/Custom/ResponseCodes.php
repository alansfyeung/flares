<?php
namespace App\Http\Custom;

// Todo: place all our error code definitions in here
class ResponseCodes
{
    const ERR_POSTDATA_MISSING = 4001;
	const ERR_POSTDATA_FORMAT = 4002;
    const ERR_HAS_ROLL = 4100;
	const ERR_EX = 5000;
	const ERR_DB_PERSIST = 5001;
	const ERR_REGT_NUM = 5002;
	
	const ERR_DELETION = 6002;
    const ERR_IMAGE_DELETION = 6003;
	
	const ERR_PERM_NOPE = 7000;
	const ERR_PERM_NOT_ADMIN = 7001;
    
    const ERR_DECORATION_SHORTCODES_EXHAUSTED = 5020;
    const ERR_DECORATION_ALREADY_ASSIGNED = 5030;
    
}
