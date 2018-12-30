<?php
namespace App\Http\Custom;

/** @todo place all our error code definitions in here */
class ResponseCodes
{
    // Missing errors are 4000-series
    const ERR_POSTDATA_MISSING = 4001;
	const ERR_POSTDATA_FORMAT = 4002;
    const ERR_HAS_ROLL = 4100;

    // Hard server errors are 5000-series
	const ERR_EX = 5000;
	const ERR_DB_PERSIST = 5001;
    const ERR_REGT_NUM = 5002;
    const ERR_DECORATION_SHORTCODES_EXHAUSTED = 5020;
    const ERR_DECORATION_ALREADY_ASSIGNED = 5030;
    
    // Deletion, update and data integrity errors are 6000-series
	const ERR_DELETION = 6002;
    const ERR_IMAGE_DELETION = 6003;
    const ERR_MISMATCH = 6100;
    
    // Permission errors are 7000-series
	const ERR_P_NOPE = 7000;
    const ERR_P_NOT_ADMIN = 7001;
    const ERR_P_INSUFFICIENT = 7002;
    const ERR_P_OAUTH_SCOPE = 7100;
    
    // Gone and unavailable errors are 8000-series
    const ERR_OP_UNAVAILABLE = 8001;
    const ERR_LINK_INVALID = 8002;
    
}
