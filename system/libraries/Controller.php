<?php defined('SYSPATH') or die('No direct script access.');
/**
* @desc Aggregate Class - some data memebers are objects.
*/
abstract class Controller
{
	public $arguments;
	public $load; // object
	/**
	*
	*/
	public function __construct()
    {
    	$this->arguments = Router::$arguments;
    	$this->load = new Loader();
    	if(FrameworkConfig::$setting['hook.post_controller_constructor']['active'])
		{
			require_once(APPPATH . 'hooks/HookPostControllerConstructor.php');
			BootStrap::$hook_post_controller_constructor = (FrameworkConfig::$setting['hook.post_controller_constructor']['params']) ? new HookPostControllerConstructor(FrameworkConfig::$setting['hook.post_controller_constructor']['params']) : new HookPostControllerConstructor();
		}
	}
	/**
	* Includes a View within the controller scope.
	*
	* @param   string  view filename
	* @param   array   array of view variables
	* @return  string
	*/
	public static function loadView($view_filename, $input_data)
	{
		if ($view_filename == '') { return; }

		// Buffering on
		ob_start();

		// Import the view variables to local namespace
		extract($input_data, EXTR_SKIP);

		// Views are straight HTML pages with embedded PHP, so importing them
		// this way insures that $this can be accessed as if the user was in
		// the controller, which gives the easiest access to libraries in views
		include $view_filename;

		// Fetch the output and close the buffer
		return ob_get_clean();
	}
	/**
	*
	*/
	public static function error()
	{
		// Send the 404 header
		header('HTTP/1.1 404 File Not Found');
		$view = new View('404');
		$view->header = new View('header');
        $view->footer = new View('footer');
		$view->header->title   = "404 Error";
        $view->footer->bechmark = Benchmark::get(SYSTEM_BENCHMARK . '_total_execution');
        $view->render(TRUE);
        exit();
	}
	/**
	*
	*/
	public function __set($variable, $value)
	{
		$this->$variable = $value;	
	}
	/**
	*
	*/
	public function __get($variable)
	{
		return $this->$variable;
	}
	/**
	*
	*/
	public function default_design_layout($main_view, $data = NULL)
	{
		$this->load->helper('meta_tags'); // load function meta_tags();
		
		$view = new View($main_view);
		if(isset($data['body']))
		{
			$view->datum = $data['body'];
		}
		
		$view->header = new View('header');
		$view->header->meta_tag = meta_tags($main_view);		
		
        $view->footer = new View('footer');
        $view->footer->bechmark = Benchmark::get(SYSTEM_BENCHMARK . '_total_execution');
        
		$view->render(TRUE);
	}
	
}
