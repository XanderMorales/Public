<?php
/**
* TODO: Set system info - browser, os in session library.
* @package    Library
* @author     Web Editors Team
*/

class Session extends Model
{	
	/**
	*
	*/
	function __construct()
	{
		if(session_id() == "")
		{
			session_save_path(VARPATH . FrameworkConfig::$setting['session.path']);
			session_name(FrameworkConfig::$setting['session.name']);
			session_start();
			header("Cache-control: private"); //IE 6 Fix
			
			if(!(isset($_SESSION['session_id'])))
			{
				
				$_SESSION = array();
				$_SESSION['session_id'] = session_id();
				
				// Store User Agent Session Variables.
				$_SESSION['agent'] = BootStrap::$user_agent->agent;
				if (BootStrap::$user_agent->isBrowser())
				{
					$_SESSION['browser'] = BootStrap::$user_agent->browser;
					$_SESSION['version'] = BootStrap::$user_agent->version;
				}
				if (BootStrap::$user_agent->isRobot())
				{
					$_SESSION['robot'] = BootStrap::$user_agent->robot;
				}
				if (BootStrap::$user_agent->isMobile())
				{
					$_SESSION['mobile'] = BootStrap::$user_agent->mobile;
				}
				if (BootStrap::$user_agent->isReferral() == TRUE)
				{
					$_SESSION['referral'] = BootStrap::$user_agent->referral;
				}
				$_SESSION['referrer'] = BootStrap::$user_agent->referrer();
				$_SESSION['platform'] = BootStrap::$user_agent->platform;
				
				$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
				
				Log::add('debug', "Session Library Initialed");
			}		
		}
		parent::__construct(FrameworkConfig::$setting['session.db']);
	}
	/**
	* $this->session->create() is used to create a new session. It will destroy the old session.
	* A new session is automatically created when you load the Session library.
	*/
	function create()
	{
		session_save_path(VARPATH . FrameworkConfig::$setting['session.path']);
		session_name(FrameworkConfig::$setting['session.name']);
		session_start();
		header("Cache-control: private"); //IE 6 Fix
		$_SESSION = array();
		$_SESSION['session_id'] = session_id();
	}
	/**
	* this->ession->get($name) is used to obtain data.
	*/
	function get($name)
	{
		if(isset($_SESSION[$name]))
		{
			return $_SESSION[$name];
		}
		else
		{
			return false;
		}
	}
	/**
	* this->ession->set($name) is used to set data.
	*/
	function set($name, $value)
	{
		$_SESSION[$name] = $value;
		$this->dbSave();
	}
	/**
	* this->ession->del($name) is used to delete data.
	*/
	function del($name)
	{
		$this->session_id = session_id();
		session_unregister($name);
	}
	/**
	* Return all data stored in the session for db storage.
	*/
	function all()
	{
		$sess_data = '';
		foreach($_SESSION AS $key => $value)
		{
			$sess_data .= $key . ":::" . $value . "---";
		}
		$sess_data = substr_replace($sess_data,"",-3);
		return $sess_data;
	}
	/**
	* this->ession->destroy() is used to terminate the session
	*/
	function destroy()
	{        	
		if (isset($_SESSION))
		{
			$query = $this->db->query("DELETE FROM session WHERE session_id = '" . $this->session_id."'");
			// Remove all session data
			session_unset();
			// Destroy the session
			session_destroy();
			// Re-initialize the array
			$_SESSION = array();
		}
	}
	/**
	* Regenerates a new session id - probably will not need to do this, but nice to have
	*/
	function regenerate()
	{
		// Generate a new session id
		// Note: also sets a new session cookie with the updated id
		session_regenerate_id(TRUE);

		// Update session with new id
		$_SESSION['session_id'] = session_id();
	}
	/**
	*
	*/
	function dbSave()
	{		
		// check to see if it exist first in the db
		$query = $this->db->query("SELECT * FROM session WHERE session_id = '" . session_id() . "'");
		if($this->db->affectedrows == 0)
		{
			$this->last_activity = time();
			$_SESSION['last_activity'] = $this->last_activity;
			$query = $this->db->query("INSERT INTO session set session_id = '" . session_id() . "', last_activity = '" . $this->last_activity."', data = '" . $this->all() ."'");
		}
		elseif($this->db->affectedrows == 1)
		{
			// check for session lifetime
			$row = $this->db->fetchAssoc($query);
			$this->checkLife($row['last_activity']);
			
			// update records
			$this->last_activity = time();
			$_SESSION['last_activity'] = $this->last_activity;
			$query = $this->db->query("UPDATE session set session_id = '" . session_id() . "', last_activity = '" . $this->last_activity . "', data = '" . $this->all() . "' WHERE session_id = '" . session_id() . "'");
		}
	}
	/**
	* return an array of all sesion data.
	*/
	function dbGet()
	{
		$data_array = array();
		$query = $this->db->query("SELECT data FROM session WHERE session_id = " . '\'' . session_id() . '\'');
		$session = $this->db->fetchArray($query);
        $name_value_pair =  preg_split("/---/", $session['data']);
        foreach($name_value_pair as $key => $value)
        {
        	$data = preg_split("/:::/", $value);
        	$data_array[$data[0]] = $data[1];
		}
        return $data_array;
	}
	/**
	* Check session lifetime and keep db clean
	*/
	function checkLife($last_visit)
	{
		// session not set to expire!
		if(FrameworkConfig::$setting['session.expr'] == 0)
		{
			// delete items older than 24 hours!
			$query_old_items = $this->db->query('SELECT * FROM session');
			while($row_old_items = $this->db->fetchAssoc($query_old_items))
        	{
        		if(($row_old_items['last_activity'] + 86400) < time())
        		{
        			$query_delete_old_items = $this->db->query("DELETE FROM session WHERE session_id = '" . $row_old_items['session_id'] . "'");
				}
			}
			return;
		}
		
		// clean db - remove all old entries from database from expired user sessions
		$query_old_items = $this->db->query('SELECT * FROM session');
		while($row_old_items = $this->db->fetchAssoc($query_old_items))
        {
        	if(($row_old_items['last_activity'] + FrameworkConfig::$setting['session.expr']) < time())
        	{
        		$query_delete_old_items = $this->db->query("DELETE FROM session WHERE session_id = '" . $row_old_items['session_id'] . "'");
			}
        }

		if($last_visit + FrameworkConfig::$setting['session.expr'] < time())
		{
			$url = FrameworkConfig::$setting['session.expr.redirect.url'];
			header("Location: $url");
		}
	}
}