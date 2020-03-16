<?php

/**
* @desc Global Class for database hooks
*/
require_once(SYSPATH . 'libraries/DatabaseException.php');
final class Database
{
    private $db_func;
    private $mykey;
    public $ref_file;
    public $ref_line;
    public $affectedrows;
    
    public $database_connection;
    public $database_selection;
    /**
    * @desc Expecting a hash key input
    */
    final public function __construct($key_in, $ref_file, $ref_line)
    {
        $this->db_func = array(
            'mysql' => array(
								'mysql_pconnect',
								'mysql_select_db',
								'mysql_query',
								'mysql_free_result',
								'mysql_fetch_array',
								'mysql_num_rows',
								'mysql_execute',
								'mysql_close',
								'mysql_fetch_assoc',
								'mysql_errno',
								'mysql_error',
								'mysql_affected_rows',
								'mysql_result'
							)
        );
        $this->db_func = $this->db_func[DBTYPE]; // narrow it down to a specific function list sub array
        $this->mykey = FrameworkConfig::$setting['database'][$key_in];
        $this->ref_file = $ref_file;
        $this->ref_line = $ref_line;
        Log::add('debug', 'Database Library initialized');
    }
    /**
    *
    */
    final public function connect()
    {
        $dbconn = $this->db_func[0]($this->mykey[3], $this->mykey[1], $this->mykey[2]);
        $this->database_connection = $dbconn;
    }
    /**
    *
    */
    final public function select()
    {
        $this->database_selection = $this->db_func[1]($this->mykey[0], $this->database_connection);
        if(!$this->database_selection)
        {
            throw new DatabaseException($this->db_func[10]() ,$this->db_func[9](), $this->ref_file, $this->ref_line);
        }
    }
    /**
    *
    */
    final public function query($sql)
    {
    	$this->connect();
        $this->select();
        
        $query = $this->db_func[2]($sql, $this->database_connection);
        if(!$query)
        {
            throw new DatabaseException($this->db_func[10]() ,$this->db_func[9](), $this->ref_file, $this->ref_line);
        }
        else
        {
            $this->affectedrows = $this->db_func[11]();
            return $query;
        }
    }
    /**
    *
    */
    final public function update($table, $data, $where)
    {
    	$this->connect();
        $this->select();
        
        $sql = 'UPDATE ' . $table . ' SET ';
        $size_of_array = sizeof($data);
		$count = 1;	
        foreach($data as $name => $value)
		{
			$sql .= $name .= "='" . addslashes($value) . "'";
			if($count != $size_of_array) { $sql .= ', '; }
			$count ++;
		}
		$sql .= ' WHERE ' . $where;
		
		$query = $this->db_func[2]($sql, $this->database_connection);
        if(!$query)
        {
            throw new DatabaseException($this->db_func[10]() ,$this->db_func[9](), $this->ref_file, $this->ref_line);
        }
        else
        {
            $this->affectedrows = $this->db_func[11]();
            return $query;
        }
	}
	/**
	*
	*/
    final public function insert($table, $data)
    {
    	$this->connect();
        $this->select();
        
        $sql = 'INSERT INTO ' . $table . ' set ';
        $size_of_array = sizeof($data);
		$count = 1;	
        foreach($data as $name => $value)
		{
			$sql .= $name .= "='" . addslashes($value) . "'";
			if($count != $size_of_array) { $sql .= ', '; }
			$count ++;
		}
		
        $query = $this->db_func[2]($sql, $this->database_connection);
        if(!$query)
        {
            throw new DatabaseException($this->db_func[10]() ,$this->db_func[9](), $this->ref_file, $this->ref_line);
        }
        else
        {
            $this->affectedrows = $this->db_func[11]();
            return $query;
        }
    }
    /**
    *
    */
    final public function freeResult($query)
    {
        return $this->db_func[3]($query);
    }
    /**
    *
    */
    final public function fetchArray($query)
    {
        return $this->db_func[4]($query);
    }
    /**
    *
    */
    final public function numRows($result)
    {
        return $this->db_func[5]($result);
    }
    /**
    *
    */
    final public function execute($query)
    {
        return $this->db_func[6]($query);
    }
    /**
    *
    */
    final public function closeConnecton()
    {
        return $this->db_func[7]();
    }
    /**
    *
    */
    final public function fetchAssoc($query)
    {
        return $this->db_func[8]($query);
    }
    /**
    *
    */
    final public function lastInertID()
    {
    	return $this->db_func[12]($this->db_func[2]('SELECT LAST_INSERT_ID()'),0);
    }
    final public function __destruct()
    {
        
    }
}