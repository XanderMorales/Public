<?php defined('SYSPATH') or die('No direct script access.');
/**
* Process control file, loaded by the front controller.
*
* @package    Core
* @author     Web Editors Team
*/
final class BootStrap
{ 	
	public $controller_class_name = FALSE;
	public $controller = FALSE;
	public $method = FALSE;
	
	public $model_class_name = FALSE;
	public $model = FALSE;
	
	public static $user_agent; // object
	
	public static $error = array();
	
	// hooks
	public static $hook_pre_system; // object;
	public static $hook_pre_controller; // object
	public static $hook_post_controller_constructor; // object
	public static $hook_post_controller; // object
	public static $hook_post_system; // object;
	
	public function __construct()
	{		
		$this->beginExecution();
		
		$this->requiredFiles();
		$this->setupApplication();
		$this->loadController();
		$this->endExecution();
	}
	/**
	* @desc Load benchmarking support - Benchmarks are prefixed by a random string to prevent collisions
	*/
	private function beginExecution()
	{
		require_once(SYSPATH.'core/Benchmark.php');
		define('SYSTEM_BENCHMARK', uniqid());
		Benchmark::start(SYSTEM_BENCHMARK.'_total_execution'); // Start total_execution
		if(FrameworkConfig::$setting['hook.pre_system']['active'])
		{
			require_once(APPPATH . 'hooks/HookPreSystem.php');
			$this->hook_pre_system = (FrameworkConfig::$setting['hook.pre_system']['params']) ? new HookPreSystem(FrameworkConfig::$setting['hook.pre_system']['params']) : new HookPreSystem();
		}
	}
	/**
	* @desc Load required framework files
	*/
	private function requiredFiles()
	{
		require_once(SYSPATH . 'core/Loader.php');
		require_once(SYSPATH . 'core/Log.php');
		require_once(SYSPATH . 'libraries/UserAgent.php');
		require_once(SYSPATH . 'libraries/Router.php');
		require_once(SYSPATH . 'libraries/Encrypt.php');
		require_once(SYSPATH . 'libraries/Controller.php');
		require_once(SYSPATH . 'libraries/View.php');
	}
	/**
	* @desc set up application
	*/
	private function setupApplication()
	{
		
		Router::setup();
		self::$user_agent = new UserAgent();
	}
	/**
	* @desc instatiate the controller and method
	*/
	private function loadController()
	{
		// pre controller hook
		if(FrameworkConfig::$setting['hook.pre_controller']['active'])
		{
			require_once(APPPATH . 'hooks/HookPreController.php');
			$this->hook_pre_controller = (FrameworkConfig::$setting['hook.pre_controller']['params']) ? new HookPreController(FrameworkConfig::$setting['hook.pre_controller']['params']) : new HookPreController();
		}
		if (is_file(APPPATH . 'controllers' . Router::$file_path . Router::$file_name))
		{
			require_once(APPPATH . 'controllers' . Router::$file_path . Router::$file_name);
			$this->controller_class_name = substr(ucfirst(Router::$file_name), 0, -4) . 'Controller';
			$this->controller_class_name = str_replace('/', '_', $this->controller_class_name); // for controllers in sub directories
			$this->controller = new $this->controller_class_name;
			$this->method = (method_exists($this->controller, Router::$method)) ? eval('$this->controller->' . Router::$method . '();') : 'NO METHOD FOUND';
		}
		else
		{
			//404 error controller not found;
			self::$error['Controller error'] = 'Could not find the requested model';
			Controller::error();
		}
			
		// method not found;
		if($this->method == 'NO METHOD FOUND')
		{
			self::$error['Controller error'] = 'Could not find the requested method';
			Controller::error();
		}
		
		// post controller hook
		if(FrameworkConfig::$setting['hook.post_controller']['active'])
		{
			require_once(APPPATH . 'hooks/HookPostController.php');
			$this->hook_post_controller = (FrameworkConfig::$setting['hook.post_controller']['params']) ? new HookPostController(FrameworkConfig::$setting['hook.post_controller']['params']) : new HookPostController();
		}
	}
	/**
	* @desc instatiate the model - triggered by Loader::model()
	*/
	public function loadModel($mod = false)
	{
		require_once(SYSPATH . 'libraries/Database.php');
		require_once(SYSPATH . 'libraries/Model.php');
		require_once(SYSPATH . 'libraries/Session.php');
		if (is_file(APPPATH . 'models' . Router::$file_path . Router::$file_name))
		{
			require_once(APPPATH . 'models' . Router::$file_path . Router::$file_name);
			$this->model_class_name = substr(ucfirst(Router::$file_name), 0, -4) . 'Model';
			//$this->model = new $this->model_class_name;
		}
		elseif (is_file(MODPATH . $mod . '/models/index.php'))
		{
			require_once(MODPATH . $mod . '/models/index.php');
			$this->model_class_name = $mod . 'Model';
		}
	}
	/**
	* @desc End total_execution
	*/
	private function endExecution()
	{
		Benchmark::stop(SYSTEM_BENCHMARK.'_total_execution');
		if (FrameworkConfig::$setting['log.threshold'] > 0)
		{
			// Set the log directory
			Log::directory(LOGDIR);
			// Enable log writing if the log threshold is above 0
			register_shutdown_function(array('Log', 'write'));
		}
		
		if(FrameworkConfig::$setting['hook.post_system']['active'])
		{
			require_once(APPPATH . 'hooks/HookPostSystem.php');
			BootStrap::$hook_post_system = (FrameworkConfig::$setting['hook.post_system']['params']) ? new HookPostSystem(FrameworkConfig::$setting['hook.post_system']['params']) : new HookPostSystem();
		}
	}
}