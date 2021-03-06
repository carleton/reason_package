<?php
/**
 * Error handler settings/configuration
 * @package carl_util
 * @subpackage error_handler
 */

/**
 * Define the error log file name
 */
$host = !empty( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'cli';
define( 'PHP_ERROR_LOG_FILE', '/tmp/php-errors-'.$host );

/**
 * Set up the developer/maintainer information
 *
 * This array is used by the error handling code for the following purposes:
 *
 * 1. Display notices, warnings, and other errors on the page for IP addresses specified
 *    (They are suppressed for all other IP addresses)
 * 2. Email/page/text message individuals specified when/if there is an emergency error
 * 3. Email individuals specified with a daily report of warnings and errors
 *
 **/

// Example:

$GLOBALS[ '_DEVELOPER_INFO' ] = array(

	'vagrant' => array(
		'ip' => array(
			'10.0.2.2',
			'192.168.56.1',
			'127.0.0.1',
			'172.18.0.1',
		)
	),
);

/* Where should the browser get sent if a fatal error occurs?
   Note that this may need to be changed based on where you unpacked Reason.
   It is best practice to copy oops.php to a web-available location
   outside the Reason package and to re-point this constant to that location. */
define( 'OHSHI_SCRIPT', REASON_PACKAGE_HTTP_BASE_PATH . 'oops.php');

/*
	
	MAINTENANCE MODE
	
	This variable triggers maintenance mode.  Do not change this lightly.  When set to false, all sites behave
	normally.  However, when this is set to true, all non-developer users will see the maintenance page which is
	stored in MAINTENTANCE_MODE_URL.  The developers are listed above.  IP address checks are used to determine
	who you are.
	
	$GLOBALS['_maintenance_estimate'] allows you to provide a time that the maintenance should be completed.  A nice message
	with the estimated time will be shown on the maintenance page.  Additionally, it's intelligent enough not to
	show negative times.  Make sure you know the form of mktime() before using it.  An absolute time is needed since
	a relative one would keep changing with each request.  Make sure you pay attention to the timezone as well.
	mktime uses the machine's local time zone.
	
	BIG NOTE: Maintenance mode should only be used when new code is being deployed to a production environment.
	Most changes should be thoroughly tested in a development environment.  There are times when major components
	need to be moved and there will certainly be downtime as files are being moved and/or databases are being
	changed or synchronized.  To further drive the point home:
	
	********* THIS TAKES DOWN ALL SERVICES THAT USE THE ERROR HANDLER -- INCLUDING REASON SITES -- UNTIL IT IS SWITCHED OFF. ************
	
*/

/* allow other files that use error_handler to activate maintenance mode.  if they set the var up,
we don't want this one overwriting it */
if( !defined( 'MAINTENANCE_MODE_ON' ) )
	define('MAINTENANCE_MODE_ON', false);

/* Where should the browser get sent if maintenance mode is on?
   Note that this may need to be changed based on where you unpacked Reason.
   It is best practice to copy maintenance.php to a web-available location
   outside the Reason package and to re-point this constant to that location. */
define('MAINTENTANCE_MODE_URL', REASON_PACKAGE_HTTP_BASE_PATH . 'maintenance.php');

// Commented out unless needed.  see note above.
// NOTE: pay attention to mktime's argument order:
// mktime( hour, minute, second, month, day, year )
//$GLOBALS['_maintenance_estimate'] = mktime( 1, 30, 0, 5, 3, 2005 );

?>