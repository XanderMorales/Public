<?php defined('SYSPATH') or die('No direct script access.');
/*
|---------------------------------------------------------------
| FILE: Router.php
|---------------------------------------------------------------
| author    - Web Editors
*/

final class Router {
	public static $current_uri;

	public static $segments = array();

	public static $file_name  = FALSE;
	public static $file_path  = '/';

	public static $method = FALSE;
	public static $arguments = array();

	/**
	* @desc get uri and url segments
	*/
	public static function setup()
	{
		// Get the current uri
		self::$current_uri = (!(isset($_SERVER['PATH_INFO']))) ? "/" : $_SERVER['PATH_INFO'];
		self::$current_uri = ltrim(rtrim(self::$current_uri, '/'), '/');
		self::$current_uri = (self::$current_uri == '') ? FrameworkConfig::$setting['controller.default'] : self::$current_uri;
	
		// Remove extra slashes from the segments that could cause erroneous routing
		self::$current_uri = preg_replace('!//+!', '/', trim(self::$current_uri, '/'));
		
		// Explode the segments by slashes
		self::$segments = explode('/', self::$current_uri);
		
		//args default - will unset segments as we loop through
		self::$arguments = self::$segments; 
				
		// Prepare controller search	
		self::segment_info();
	}
	/**
	* @desc Search for the controller, method to execute (and and arguments to pass - if any defined in url segments).
	*/
	public static function segment_info()
	{
		if(sizeof(self::$segments) > 1)
		{
			// looop through uri segments to find controller;
			$dir = 'controllers';
			$controller_path = '';

			foreach(self::$segments as $key => $segment)
			{
				// error check (controller and directory can not have the same names)
				if(is_dir(APPPATH . $dir . self::$file_path . $segment) AND is_file(APPPATH . $dir . self::$file_path . $segment . '.php'))
				{
					if(! IN_PRODUCTION)
					{
						die
						(
							'<div style="width:100%;margin:50px auto;text-align:center;">'.
								'<h3>Framework Error</h3>'.
								'<p>A file and directory can not share the same name in <code>' . APPPATH . $dir . '</code></p>' .
								'<p>The file <code>' . $segment . '.php' . '</code> and the directory <code>' . $segment . '</code>.</p>' .
								'<p>Rename one of them and/or make sure it is deleted form the server or you will continue to see this error.</p>' .
							'</div>'
						);
					}
					else
					{			
						echo "An error has occured: Please view log file for more information:";
						Log::add('Framework Error in File' . __FILE__ . ' on line:' . __LINE__, 'A file and directory can not share the same name in ' . APPPATH . $dir . 'The file ' . $segment . '.php' . 'and the directory ' . $segment . '. Rename one of them and/or make sure it is deleted form the server or you will continue to see this error.');
						exit();
					}
				}
				// directory search for controller
				elseif(is_dir(APPPATH . $dir . self::$file_path . $segment))
				{
					$dir .= '/' . $segment;
					$controller_path .= $segment . '/';
					unset(self::$arguments[$key]);
				}
				// controller file search - check directly for the controller.
				elseif (is_file(APPPATH . $dir . self::$file_path . $segment . '.php')) 
				{
					unset(self::$arguments[$key]);
					self::$file_name = $controller_path  . $segment . '.php';
				}
				else
				{
					if(self::$file_name == FALSE AND self::$method == FALSE)
					{
						unset(self::$arguments[$key]);
						self::$file_path = self::$file_path . $segment . '/';
					}
				}
				
				// We found the controller, now find the method
				if(self::$file_name != FALSE AND self::$method == FALSE)
				{
					unset(self::$arguments[$key + 1]);
					self::$method = isset(self::$segments[$key + 1]) ? self::$segments[$key + 1] : 'index';
				}
			}
		}
		// default or requested controller
		elseif(self::$file_name == FALSE)
		{
			self::$file_name = self::$current_uri . '.php';
		}
		// If no method is found, index is the default method
		self::$method = (self::$method == FALSE) ? 'index' : self::$method;
		
		// now build $self::arguments the way it should be
		$tmp_array = array();
		foreach(self::$arguments as $key => $value)
		{
			array_push($tmp_array, $value);
		}
		self::$arguments = $tmp_array;
		unset($tmp_array);
	}
}