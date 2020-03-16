<?php defined('SYSPATH') or die('No direct script access.');
/**
* Model class.
*
* @package    Core
* @author     Web Editors Team
* @copyright  (c) 2007-2008 Web Editors Team
*/
abstract class Model
{
	protected $db;
	public $load; // object
	/**
	* Loads database to $this->db.
	*/
	public function __construct($data_base_group = 'default')
    {
		$this->db	= new Database($data_base_group, __FILE__, __LINE__);
		$this->load = new Loader();
	}
}
