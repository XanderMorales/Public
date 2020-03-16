<?php defined('SYSPATH') or die('No direct script access.');
class Loader
{
	/**
	* Load view.
	*
	* @param   string  view name
	* @param   array   data to make accessible within view
	* @return  View
	*/
	public function view($name, $data = array())
	{
		return new View($name, $data);
	}
	/**
	 * Load model.
	 *
	 * @param   string             model name
	 * @param   string             custom name for accessing model, or TRUE to return instance of model
	 * @return  void|FALSE|Object  FALSE if model is already loaded, instance of model if alias is TRUE
	 */
	public function model($name, $alias = FALSE)
	{
		if($alias == 'MOD')
		{
			BootStrap::loadModel($name);
			$class = $name . 'Model';
			require_once Loader::find_file('models', $name . '/models/index');
			$model = new $class();
			return $model;
		}
		else
		{
			// load db libs and abstract model controller
			BootStrap::loadModel();
			
			// The alias is used for Controller->alias
			$alias = ($alias == FALSE) ? $name : $alias;
			$class = ucfirst($name) . 'Model';
			
			// Handle models in subdirectories
			require_once Loader::find_file('models', $name);
			
			// Reset the class name
			$class = end(explode('/', $class));
			
			// Load and return the model
			$model = new $class();
			return $model;
		}
	}
	/**
	*
	*/
	public function helper($name)
	{
		require_once (SYSPATH . 'helpers/' . $name . '.php');
	}
	/**
	* Attempt to load a module
	*/
	public function module($name)
	{
		$mod = MODPATH . $name . '/controllers/index.php';
		if (is_file($mod))
		{
			require_once $mod;
			$module = new $name();
			return $module;
		}
		else
		{
			trigger_error('MOD NOT FOUND: ' . $mod);
		}
		
		/*
		require_once Loader::find_file('modules', $mod);
		$module = new $name();
		return $module;
		*/
	}
		/**
	* @desc 
	*/
	public static function find_file($directory, $filename, $required = FALSE, $ext = FALSE)
	{
		// Users can define their own extensions, .css, etc
		$ext = ($ext == FALSE) ? '.php' : $ext;
		static $found = array();
		$search = $directory. '/' . $filename;
		
		$include_paths = array(APPPATH, SYSPATH, MODPATH);
		
		// Find the file and return its filename
		foreach ($include_paths as $path)
		{
			if($path == MODPATH)
			{
				if (is_file( $path . $filename . $ext))
				{
					return $path . $filename . $ext;
				}
			}
			
			if (is_file($path . $search . $ext))
			{
				return $path . $search . $ext;
			}
		}
		self::$error['File error'] = 'Could not find the requested file';
		Controller::error();
		return false;
	}
}
?>
