<?php
defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);

        //terminals
        define("LOAD_TERMIANL","3LD0001");
        $success_codes=array("9000","90000","90009A","00");
        define("SUCCESS_CODES",$success_codes);
        define("PENDING_CODE",'90009');
        define("COMMISSION",0.8);
        define("YAAKA_SHARE",0.8);
        define("REFERRAL_COMS",0.05);

        define('EZEE_09',array('1026','1015'));
        //define("WEBSERVICE_URL","http://ug.ezeemoney.biz:8082/EMTerminalAPI/v1/API.svc?wsdl");
        define("WEBSERVICE_URL","http://ugapi.ezeemoney.biz/v1/API.svc?wsdl");
        define("EZEE_USERNAME","4295432316");
		define("EZEE_PASSWORD","6444CF2846");
		define("EZEE_CODE","40023841 ");
		define("AGENT_PREFIX","JAB");

		//wallet configs
		define("APP_KEY","APP_KEY");
		define("AGENT_ID","AGENT_ID");
		define("WALLET_KEY","123456789");

		define("SUCCESS_RESPONSE_MSG","Payment completed successfully");
		define("SUCCESS_RESPONSE_CODE","9000");
		define("SUCCESS_RESPONSE_CODE2","90009A");
		define("FUNDS_RESPONSE_CODE","90051");
		define("FUNDS_RESPONSE_MSG","Erro 51, contact support");

		//ERRORS
		define('AUTH_ERROR_CODE','504');
		define('AUTH_ERROR_MESSAGE','ACCESS DENIED');
		define('RESTRICTED_AGENT_CODE','999');
		define('RESTRICTED_AGENT_MESSAGE','WALLET INACTIVE');
		define('INVALID_AGENT_CODE','911');
		define('INVALID_AGENT_MESSAGE','WALLET  ID');

             define("CHARGED_EZEE", array('503509'));

		//LOGS
		define('LOG_FILE', 'wallet_logs.txt'); //wallet_logs'.date('ymd').'.txt
		define('AGENT_IN_VAL','AGENT INCOMING VALIDATION'); //from agent
		define('AGENT_OUT_VAL','AGENT OUTGOING VALIDATION RESPONSE'); //from agent
		define('AGENT_IN_PAY','AGENT INCOMING PAYMENT');
		define('AGENT_OUT_PAY','AGENT OUTCOMING PAYMENT RESPONSE');

		define('SYSTEM_OUT_VAL','SYSTEM OUTGOING VALIDATION'); //to provider
		define('SYSTEM_IN_VAL','SYSTEM INCOMING VALIDATION RESPONSE'); //from provider

		define('SYSTEM_OUT_PAY','SYSTEM OUTGOING PAYMENT');
		define('SYSTEM_IN_PAY','SYSTEM INCOMING PAYMENT RESPONSE');

              define("WITHDRAWS",array('28310716','4432361')); //transactions/codes that cach back

		//INTERSWITCH

		define("REF_PREFIX","RTG");
		define("EXEC_TIMEOUT", 6000);
		define("PROCESS_TIMEOUT", 400);


    //LIVE
       
		/*define("MY_TERMINAL","3RTG0001");
		define("DEVICE_TERMINAL","3RTG0001");
		define("SVA_BASE_URL","https://interswitch.io/api/v1A/svapayments/");
		define("APPSERVICE_URL","https://interswitch.io/api/v1/appservice/");
		define("QT_BASE_URL","https://interswitchug.io/api/v1/quickteller/");
		define("CLIENT_ID","IKIA8CD105856AEF454FA74E739A8F077331E2BB3407");
		define("CLIENT_SECRET","AFlb1IFC0epPZjTecx7h+y4WS7SCvj9WynDBFa6c0novV3yKbz8D/WaueJvmJrHI");
       */
		$base_url ="https://interswitch.io/api/";
        define("MY_TERMINAL","3IS00855*");
        define("DEVICE_TERMINAL","3APP0001*");
		define("SVA_BASE_URL", $base_url."v1A/svapayments/");
		define("APPSERVICE_URL", $base_url."v1/appservice/");
		define("QT_BASE_URL", $base_url."v1/quickteller/");
		//define("CLIENT_ID","IKIA8CD105856AEF454FA74E739A8F077331E2BB3407");
		//define("CLIENT_SECRET","AFlb1IFC0epPZjTecx7h+y4WS7SCvj9WynDBFa6c0novV3yKbz8D/WaueJvmJrHI");
		define("CLIENT_ID","IKIAD1C286FF27C103D830066E99ECC6ABFCAFAA17C4");
		define("CLIENT_SECRET","kh3a+8bl1twmqlsQsZufSuTU8nudmkB3PHP/rj82NW6g6BgH2jSKtG308QJC1zLu");
     

        define("TIMESTAMP","TIMESTAMP");
        define("NONCE","NONCE");
        define("CBN_CODE","100");
        define("SIGNATURE_REQ_METHOD","SHA-256");
	    define("SIGNATURE_METHOD","SIGNATURE_METHOD");
		define("SIGNATURE","SIGNATURE");
		define("AUTHORIZATION","AUTHORIZATION");

        $config['base_url'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
	 $config['base_url'] .= "://" . $_SERVER['HTTP_HOST'];
	 $config['base_url'] .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
        define('ASSET_URL', $config['base_url'].'assets/');
        define('BASEURL', $config['base_url']);
//links
       define("EDIT_AGENT_LINK",$config['base_url']."agents/edit");

       $config['index_page'] = '';
       $config['uri_protocol']	= 'REQUEST_URI';
       $config['url_suffix'] = '';
       $config['language']	= 'english';
       $config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the 'hooks' feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = FALSE;

/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| https://codeigniter.com/user_guide/general/core_classes.html
| https://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'MY_';

/*
|--------------------------------------------------------------------------
| Composer auto-loading
|--------------------------------------------------------------------------
|
| Enabling this setting will tell CodeIgniter to look for a Composer
| package auto-loader script in application/vendor/autoload.php.
|
|	$config['composer_autoload'] = TRUE;
|
| Or if you have your vendor/ directory located somewhere else, you
| can opt to set a specific path as well:
|
|	$config['composer_autoload'] = '/path/to/vendor/autoload.php';
|
| For more information about Composer, please visit http://getcomposer.org/
|
| Note: This will NOT disable or override the CodeIgniter-specific
|	autoloading (application/config/autoload.php)
*/
$config['composer_autoload'] = FALSE;

/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify which characters are permitted within your URLs.
| When someone tries to submit a URL with disallowed characters they will
| get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| The configured value is actually a regular expression character group
| and it will be executed as: ! preg_match('/^[<permitted_uri_chars>]+$/i
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The other items let you set the query string 'words' that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd';

/*
|--------------------------------------------------------------------------
| Allow $_GET array
|--------------------------------------------------------------------------
|
| By default CodeIgniter enables access to the $_GET array.  If for some
| reason you would like to disable it, set 'allow_get_array' to FALSE.
|
| WARNING: This feature is DEPRECATED and currently available only
|          for backwards compatibility purposes!
|
*/
$config['allow_get_array'] = TRUE;

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| You can also pass an array with threshold levels to show individual error types
|
| 	array(2) = Debug Messages, without Error Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 1;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/logs/ directory. Use a full server path with trailing slash.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Log File Extension
|--------------------------------------------------------------------------
|
| The default filename extension for log files. The default 'php' allows for
| protecting the log files via basic scripting, when they are to be stored
| under a publicly accessible directory.
|
| Note: Leaving it blank will default to 'php'.
|
*/
$config['log_file_extension'] = '';

/*
|--------------------------------------------------------------------------
| Log File Permissions
|--------------------------------------------------------------------------
|
| The file system permissions to be applied on newly created log files.
|
| IMPORTANT: This MUST be an integer (no quotes) and you MUST use octal
|            integer notation (i.e. 0700, 0644, etc.)
*/
$config['log_file_permissions'] = 0644;

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Error Views Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/views/errors/ directory.  Use a full server path with trailing slash.
|
*/
$config['error_views_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/cache/ directory.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Cache Include Query String
|--------------------------------------------------------------------------
|
| Whether to take the URL query string into consideration when generating
| output cache files. Valid options are:
|
|	FALSE      = Disabled
|	TRUE       = Enabled, take all query parameters into account.
|	             Please be aware that this may result in numerous cache
|	             files generated for the same page over and over again.
|	array('q') = Enabled, but only take into account the specified list
|	             of query parameters.
|
*/
$config['cache_query_string'] = FALSE;

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class, you must set an encryption key.
| See the user guide for more info.
|
| https://codeigniter.com/user_guide/libraries/encryption.html
|
*/
$config['encryption_key'] = '55fhfbfgffgh5656ghjfxcv46l366gsmjktu';

$config['sess_driver'] = 'files';
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 0;
$config['sess_save_path'] = NULL;
$config['sess_match_ip'] = FALSE;
$config['sess_time_to_update'] = 0;
$config['sess_regenerate_destroy'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix'   = Set a cookie name prefix if you need to avoid collisions
| 'cookie_domain'   = Set to .your-domain.com for site-wide cookies
| 'cookie_path'     = Typically will be a forward slash
| 'cookie_secure'   = Cookie will only be set if a secure HTTPS connection exists.
| 'cookie_httponly' = Cookie will only be accessible via HTTP(S) (no javascript)
|
| Note: These settings (with the exception of 'cookie_prefix' and
|       'cookie_httponly') will also affect sessions.
|
*/
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;

/*
|--------------------------------------------------------------------------
| Standardize newlines
|--------------------------------------------------------------------------
|
| Determines whether to standardize newline characters in input data,
| meaning to replace \r\n, \r, \n occurrences with the PHP_EOL value.
|
| WARNING: This feature is DEPRECATED and currently available only
|          for backwards compatibility purposes!
|
*/
$config['standardize_newlines'] = FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
| WARNING: This feature is DEPRECATED and currently available only
|          for backwards compatibility purposes!
|
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Enables a CSRF cookie token to be set. When set to TRUE, token will be
| checked on a submitted form. If you are accepting user data, it is strongly
| recommended CSRF protection be enabled.
|
| 'csrf_token_name' = The token name
| 'csrf_cookie_name' = The cookie name
| 'csrf_expire' = The number in seconds the token should expire.
| 'csrf_regenerate' = Regenerate token on every submission
| 'csrf_exclude_uris' = Array of URIs which ignore CSRF checks
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array();

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| Only used if zlib.output_compression is turned off in your php.ini.
| Please do not use it together with httpd-level output compression.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not 'echo' any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are 'local' or any PHP supported timezone. This preference tells
| the system whether to use your server's local time as the master 'now'
| reference, or convert it to the configured one timezone. See the 'date
| helper' page of the user guide for information regarding date handling.
|
*/
$config['time_reference'] = 'local';

/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
| Note: You need to have eval() enabled for this to work.
|
*/
$config['rewrite_short_tags'] = FALSE;

/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy
| IP addresses from which CodeIgniter should trust headers such as
| HTTP_X_FORWARDED_FOR and HTTP_CLIENT_IP in order to properly identify
| the visitor's IP address.
|
| You can use both an array or a comma-separated list of proxy addresses,
| as well as specifying whole subnets. Here are a few examples:
|
| Comma-separated:	'10.0.1.200,192.168.5.0/24'
| Array:		array('10.0.1.200', '192.168.5.0/24')
*/
$config['proxy_ips'] = '';
