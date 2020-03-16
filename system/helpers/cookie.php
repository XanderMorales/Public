<?php  if (!defined('SYSPATH')) exit('No direct script access allowed');
/**
 * Set cookie
 *
 * Accepts six parameters
 *
 * @access	public
 * @param	mixed
 * @param	string	the value of the cookie
 * @param	string	the number of seconds until expiration
 * @param	string	the cookie domain.  Usually:  .yourdomain.com
 * @param	string	the cookie path
 * @param	string	the cookie prefix
 * @return	void
 */
if (! function_exists('set_cookie'))
{
	function set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/')
	{
		if ($domain == '' AND FrameworkConfig::$setting['cookie.domain'] != '')
		{
			$domain = FrameworkConfig::$setting['cookie.domain'];
		}
		if ($path == '/' AND FrameworkConfig::$setting['cookie.path'] != '/')
		{
			$path = FrameworkConfig::$setting['cookie.path'];
		}
		
		if ($expire > 0)
		{
			$expire = time() + $expire;
		}
		else
		{
			$expire = time() + FrameworkConfig::$setting['cookie.expire'];
		}
	
		setcookie($prefix . $name, $value, $expire, $path, $domain, 0);
	}
}
	
// --------------------------------------------------------------------

/**
 * Fetch an item from the COOKIE array
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	mixed
 */
if (! function_exists('get_cookie'))
{
	function get_cookie($name)
	{
		return $_COOKIE[$name];
	}
}

// --------------------------------------------------------------------

/**
 * Delete a COOKIE
 *
 * @param	mixed
 * @param	string	the cookie domain.  Usually:  .yourdomain.com
 * @param	string	the cookie path
 * @param	string	the cookie prefix
 * @return	void
 */
if (! function_exists('delete_cookie'))
{
	function delete_cookie($name = '', $domain = '', $path = '/', $prefix = '')
	{
		set_cookie($name, '', '', $domain, $path, $prefix);
	}
}