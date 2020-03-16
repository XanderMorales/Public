<?php
/**
* This file acts as the "front controller" to your application. You can
* configure your application, modules, and system directories here.
* PHP error_reporting level may also be changed.
*
* @package    Core
* @author     Web Editors Team
*/

/**
* Only make you changes in framework.config.php and don't change anything else
*/
require_once('application/config/framework.config.php');
FrameworkConfig::setup();

date_default_timezone_set(FrameworkConfig::$setting['default.time_zone']);

define('WEB_EDITORS_VERSION', '1.0');
define('WEB_EDITORS_FILE',  basename(__FILE__));
define('FRAMEWORK_NAME', 'Freedom');
define('IN_PRODUCTION', FrameworkConfig::$setting['default.in_production']);
define('DOCROOT', getcwd().DIRECTORY_SEPARATOR);
define('APPPATH', str_replace('\\', '/', realpath(FrameworkConfig::$setting['default.application'])).'/');
define('MODPATH', str_replace('\\', '/', realpath(FrameworkConfig::$setting['default.modules'])).'/');
define('SYSPATH', str_replace('\\', '/', realpath(FrameworkConfig::$setting['default.system'])).'/');
define('VENDORPATH', str_replace('\\', '/', realpath(FrameworkConfig::$setting['default.vendor'])).'/');
define('VARPATH', str_replace('\\', '/', realpath(FrameworkConfig::$setting['default.var'])).'/');
define('LOGDIR', VARPATH . '/log');
define("DBTYPE", FrameworkConfig::$setting['default.dbtype']);

/**
* For the Zend Framework, phpmailer libraries PHPExcel Libraries
*/
ini_set('include_path',ini_get('include_path') . '.' . VENDORPATH . '/' . '.' . PATH_SEPARATOR . VENDORPATH . '/PHPExcel/Classes');

if(! IN_PRODUCTION)
{
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	
	/**
	* Test to make sure that we are running on PHP 5.1.3 or newer. Once you are
	* sure that your environment is compatible, you can disable this.
	* comment this out on production server.
	*/
	if(version_compare(PHP_VERSION, '5.2', '<'))
	{
		die
		(
			'<div style="width:100%;margin:50px auto;text-align:center;">' .
				'<h3>Framework Error</h3>' .
				'<p>' . FRAMEWORK_NAME . ' requires PHP 5.2 or newer</p>' .
			'</div>'
		);
	}
	if (! is_writable(DOCROOT . 'var') AND ! is_writable(LOGDIR) AND ! is_writable(DOCROOT . 'var/tmp'))
	{
		die
		(
			'<div style="width:100%;margin:50px auto;text-align:center;">' .
				'<h3>Framework Error</h3>' .
				'<p>' . FRAMEWORK_NAME . ' requires the Directory <code>' . DOCROOT . 'var</code> to be writtable and all subfolders in it</p>' .
			'</div>'
		);
	}
	if(! is_dir(APPPATH))
	{
		die
		(
			'<div style="width:100%;margin:50px auto;text-align:center;">' .
				'<h3>Application Directory Not Found</h3>' .
				'<p>The <code>APPPATH</code> directory does not exist.</p>' .
				'<p>Set <code>APPPATH</code> in <tt>' . WEB_EDITORS_FILE . '</tt> to a valid directory and refresh the page.</p>' .
			'</div>'
		);
	}
	if(! is_dir(SYSPATH))
	{
		die
		(
			'<div style="width:100%;margin:50px auto;text-align:center;">'.
				'<h3>System Directory Not Found</h3>'.
				'<p>The <code>SYSPATH</code> directory does not exist.</p>'.
				'<p>Set <code>SYSPATH</code> in <tt>' . WEB_EDITORS_FILE . '</tt> to a valid directory and refresh the page.</p>'.
			'</div>'
		);
	}
}
else
{
	error_reporting(E_ALL);
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
}

require_once(SYSPATH.'core/Bootstrap.php');
$exec_framework = new BootStrap();
