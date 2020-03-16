<?php  if (!defined('SYSPATH')) exit('No direct script access allowed');
/**
* @package    Helpers
* @author     Web Editors Team
* @desc		  Counts the lines of code in the frameworks folder and sub folders
* 
* USAGE:
* 	in controller:
* 	$this->load->helper('count_lines_of_code');
* 	$view->loc = new CountLinesOfCode(); // count_lines_of_code
* 
* 	in view:
* 	<?=$loc->show_count()?>
**/

define('SHOW_DETAILS', true);

class CountLinesOfCode
{	
	public $name;
	public $path;
	public $folders;
	public $files;
	public $exclude_extensions;
	public $exclude_files;
	public $exclude_folders;
	/**
	*
	*/
	function __construct($path = DOCROOT)
	{
		$this->path = $path;
		$this->name = array_pop(array_filter(explode(DIRECTORY_SEPARATOR, $path)));
		$this->folders = array();
		$this->files = array();
		$this->exclude_extensions = array('gif', 'jpg', 'jpeg', 'png', 'tft', 'bmp');
		$this->exclude_files = array('count_lines.php', '.htaccess',);
		$this->exclude_folders = array('picture_library', 'plesk-stat','tmp','_old','vendor','logs');
	}
	/**
	*
	*/
	function show_count()
	{
		echo "<pre>";
		echo $this->count_lines();
		echo '</pre>';
	}
	/**
	*
	*/
	function count_lines()
	{
		if(defined('SHOW_DETAILS'))
		{
			echo "<b>Folder: {$this->path}</b>\n";
		}
		$total_lines = 0;
		$this->get_contents();
		foreach($this->files as $file)
		{
			if(in_array($file->ext, $this->exclude_extensions) || in_array($file->name, $this->exclude_files))
			{
				if(defined('SHOW_DETAILS'))
				{
					//echo "	#---Skipping File: {$file->name};\n";
				}
				continue;
			}
			$total_lines += $file->get_num_lines();
		}
		foreach($this->folders as $folder)
		{
			if(in_array($folder->name, $this->exclude_folders))
			{
				if(defined('SHOW_DETAILS'))
				{
					//echo "<b>#Skipping Folder: {$folder->name};</b>\n";
				}
				continue;
			}
			$total_lines += $folder->count_lines();
		}
		if(defined('SHOW_DETAILS'))
		{
			echo "<i>Total lines in {$this->name}: $total_lines;</i>\n\n\n";
		}
		return $total_lines;
	}
	/**
	*
	*/
	function get_contents()
	{
		$contents = $this->_get_contents();
		foreach($contents as $key => $value)
		{
			if($value['type'] == 'Folder')
			{
				$this->folders[] = new CountLinesOfCode($value['item']);
			}
			else
			{
				$this->files[] = new CountFile($value['item']);
			}
		}
	}
	/**
	*
	*/
	function _get_contents()
	{
		$folder = $this->path;
		if(!is_dir($folder))
		{
			return array();
		}
		$return_array = array();
		$count = 0;
		if( $dh = opendir($folder) )
		{
			while( ($file = readdir($dh)) !== false )
			{
				if( $file == '.' || $file == '..' )
					continue;
				$return_array[$count]['item']	= $folder .$file .(is_dir($folder .$file) ? DIRECTORY_SEPARATOR : '');
				$return_array[$count]['type']	= is_dir($folder .$file) ? 'Folder' : 'File';
				$count++;				
			}
			closedir($dh);
		}
		return $return_array;
	}
}

class CountFile
{
	public $name;
	public $path;
	public $ext;
	/**
	*
	*/
	function __construct($path)
	{
		$this->path = $path;
		$this->name = basename($path);			
		$this->ext  = array_pop(explode('.', $this->name));
	}
	/**
	*
	*/
	function get_num_lines()
	{
		$count_lines = count(file($this->path));
		if(defined('SHOW_DETAILS'))
		{
			echo "	|---File: {$this->name}, lines: $count_lines;\n";
		}
		return $count_lines;
	}
}
