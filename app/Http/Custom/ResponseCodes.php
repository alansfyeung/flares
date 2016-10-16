<?php
namespace App\Http\Flares;

// Todo: place all our error code definitions in here
class ResponseCodes
{
    const ERR_POSTDATA_MISSING = 4001;
	const ERR_POSTDATA_FORMAT = 4002;
	const ERR_EX = 5000;
	const ERR_DB_PERSIST = 5001;
	const ERR_REGT_NUM = 5002;
	
	const ERR_DELETION = 6002;
	
	const ERR_PERM_NOPE = 7000;
	const ERR_PERM_NOT_ADMIN = 7001;
}
