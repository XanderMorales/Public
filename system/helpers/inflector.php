<?php  if (!defined('SYSPATH')) exit('No direct script access allowed');

if (! function_exists('singular'))
{	
	function singular($str)
	{
	    $str = strtolower(trim($str));
	    $end = substr($str, -3);
    
	    if ($end == 'ies')
	    {
	        $str = substr($str, 0, strlen($str)-3).'y';
	    }
	    elseif ($end == 'ses')
	    {
	        $str = substr($str, 0, strlen($str)-2);
	    }
	    else
	    {
	        $end = substr($str, -1);
        
	        if ($end == 's')
	        {
	            $str = substr($str, 0, strlen($str)-1);
	        }
	    }
    
	    return $str;
	}
}

// --------------------------------------------------------------------

/**
 * Plural
 *
 * Takes a singular word and makes it plural
 *
 * @access	public
 * @param	string
 * @param	bool
 * @return	str
 */	
if (! function_exists('plural'))
{	
	function plural($str, $force = FALSE)
	{
	    $str = strtolower(trim($str));
	    $end = substr($str, -1);

	    if ($end == 'y')
	    {
	        $str = substr($str, 0, strlen($str)-1).'ies';
	    }
	    elseif ($end == 's')
	    {
	        if ($force == TRUE)
	        {
	            $str .= 'es';
	        }
	    }
	    else
	    {
	        $str .= 's';
	    }

	    return $str;
	}
}

// --------------------------------------------------------------------

/**
 * Camelize
 *
 * Takes multiple words separated by spaces or underscores and camelizes them
 *
 * @access	public
 * @param	string
 * @return	str
 */	
if (! function_exists('camelize'))
{	
	function camelize($str)
	{		
		$str = 'x'.strtolower(trim($str));
		$str = ucwords(preg_replace('/[\s_]+/', ' ', $str));
		return substr(str_replace(' ', '', $str), 1);
	}
}

// --------------------------------------------------------------------

/**
 * Underscore
 *
 * Takes multiple words separated by spaces and underscores them
 *
 * @access	public
 * @param	string
 * @return	str
 */	
if (! function_exists('underscore'))
{
	function underscore($str)
	{
		return preg_replace('/[\s]+/', '_', strtolower(trim($str)));
	}
}

// --------------------------------------------------------------------

/**
 * Humanize
 *
 * Takes multiple words separated by underscores and changes them to spaces
 *
 * @access	public
 * @param	string
 * @return	str
 */	
if (! function_exists('humanize'))
{	
	function humanize($str)
	{
		return ucwords(preg_replace('/[_]+/', ' ', strtolower(trim($str))));
	}
}