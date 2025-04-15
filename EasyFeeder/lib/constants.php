<?php
// HTTP Response Codes
define("HTTP_OK","HTTP/1.1 200 OK");
define("HTTP_FAIL","HTTP/1.1 304 Not Modified");
define("HTTP_INVALID","HTTP/1.1 400 Bad Request");
define("HTTP_UNAUTHORIZED","HTTP/1.1 401 Unauthorized");
define("HTTP_METHOD","HTTP/1.1 405 Method Not Allowed");
define("HTTP_ERROR","HTTP/1.1 500 Internal Server Error");
define("HTTP_UNAVAILABLE","HTTP/1.1 503 Service Unavailable");

// Processing Response Codes
/*
 * ****************
 * GENERAL ERRORS *
 ******************
 */
define("PROC_OK","0000");
define("E_INTERNAL","9999");
define("E_METHOD","9901");
define("E_PROCESS","9902");
define("E_AUTH_FAILED","9903");
define("E_INVALID_ACCOUNT","9904");
define("E_INVALID_DATA","9005");
define("E_INVALID_SESSION","90");


define("W_ACCOUNT_EXPIRING","0001");
define("E_ACCOUNT_LOCK","8001");
define("E_ACCOUNT_EXPIRED","9002");
define("E_ACCOUNT_EXIST","8003");
?>