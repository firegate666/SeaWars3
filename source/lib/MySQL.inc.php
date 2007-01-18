<?
/**
 * MySQL Wrapper
 * in fact, this isn't yet a wrapper, much improved has
 * to be done
 */
class MySQL extends SQL {

	protected $failed = false;

	protected function addlog($msg, $loglevel) {
		FileLogger::write("QUERY: ".$msg, $loglevel);
	}

	protected function adderror($type, $query='none') {
		$this->failed();
		$this->lasterrors[] = "$type: ".mysql_error()."; Last query: ".$query;
	}

	/**
	* Connects to MySQL Database using global parameters
	* $dbserver
	* $dbuser
	* $dbpassword
	* $dbdatabase
	* 
	* @return	Ressource	databaselink
	*/
	function connect() {
		$dbserver = get_config('dbserver');
		$dbuser = get_config('dbuser');
		$dbpassword = get_config('dbpassword');
		$dbdatabase = get_config('dbdatabase');
		$this->querycount++;
		
		if(($this->dblink != null) && mysql_ping($this->dblink)) // connection still exists?
			return true;
		else {
	  		$flags = MYSQL_CLIENT_COMPRESS + MYSQL_CLIENT_INTERACTIVE;
	  		$this->dblink = @mysql_connect($dbserver, $dbuser, $dbpassword, false, $flags) or $this->adderror("connect");
	  		mysql_select_db($dbdatabase) or $this->adderror("connect");
	  		return false;
		}
	}

	/**
	* Disconnects database
	* @param	Ressource $dblink	databaselink
	*/
	function disconnect() {
		if($this->dblink != null)
			mysql_close($this->dblink);
	}

	/**
	* Executes SQL insert statement
	* @param	String	$query	sql query
	* @return	int	last insert id
	*/
	function insert($query) {
		$this->connect();
		$this->queries[] = $query;			
		$this->addlog($query, 8);		
		$result = mysql_query($query) or $this->adderror("insert", $query);
		$id = mysql_insert_id();
		return $id;
	}

	/**
	* Executes SQL select statement
	* @param	String	$query	sql query
	* @param	boolean	$assoc	if false, return array is numeric
	* @return	String[][]	result set as array
	*/
	function select($query, $assoc = true) {
		$this->connect();
		$this->queries[] = $query;	
		$this->addlog($query, 9);		
		$result = MYSQL_QUERY($query) or $this->adderror("select", $query);
		$return = array ();
		if (!$this->failed) {
			$counter = 0;
			if (!$assoc)
				while ($line = MYSQL_FETCH_ARRAY($result, MYSQL_NUM))
					$return[$counter ++] = $line;
			else
				while ($line = MYSQL_FETCH_ARRAY($result, MYSQL_ASSOC))
					$return[$counter ++] = $line;
		}
		return $return;
	}

	/**
	* Executes SQL statement
	* @param	String	$query	sql query
	* @return	String[]	result set with single row
	*/
	function executeSql($query) {
		$this->connect();
		$this->queries[] = $query;			
		$this->addlog($query, 9);		
		$result = MYSQL_QUERY($query) or $this->adderror("execute", $query);
		$result = MYSQL_FETCH_ARRAY($result, MYSQL_ASSOC);
		return $result;
	}

	function failed() {
		$this->failed = true;
	}

	/**
	* Executes SQL update statement
	* @param	String	$query	update statement
	* @return	int	number of affected rows
	*/
	function update($query, $mayfail = false) {
		$this->connect();
		$this->queries[] = $query;			
		$this->addlog($query, 5);
		if ($mayfail)
			@$result = MYSQL_QUERY($query) or $this->failed();
		else if(!$mayfail)
			$result = MYSQL_QUERY($query) or $this->adderror("update/delete", $query);
		
		if ($this->failed)
			return false;
		$rows = MYSQL_AFFECTED_ROWS();
		return $rows;
	}

	public function escape($string) {
		return mysql_escape_string($string);
	} 
}
?>
