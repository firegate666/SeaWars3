<?php
/**
 * Encapsulates the use of the mysql database
 * Queries are build from given arrays
 * 
 * WORK IN PROGRESS !! 
 */
 /*
  Example use:
   	$mysqli = new MySQLInterface();
  	$fields = array();
  	$tables = array('insel');
  	$where[]= array('field'=>'a', 'val'=>1,'next' => 'OR');
  	$where[]= array('field'=>'c', 'val'=>'b','comp'=>'<');
  	$orderby[]= array('orderby'=>'id', 'orderdir'=>'DESC');
  	$mysqli->select($fields, $tables, array(), $orderby));
  */
class MySQLInterface {
	
	private $mysql;
	
	public function connect() {
		$this->mysql->connect();
	}
	
	public function disconnect() {
		$this->mysql->disconnect();
	}
	
	/**
	 * return array of last errors
	 */
	public function getLastErrors() {
		return $this->mysql->getLastErrors();
	}

	/**
	* returns number of queries executed
	* @return	int	number of queries
	*/
	public function getQuerycount() {
		return count($this->mysql->getQueries());
	}

	/**
	 * Return all queries of this instance
	 */
	public function getQueries() {
		return $this->mysql->getQueries();
	}

	public function MySQLInterface() {
		$this->mysql = new MySQL();
	}
	
	public function insert($table, $data) {
		$query  = 'INSERT INTO '.$table.' ';
		$query .= '('.implode(', ', array_keys($data)).')';
		$query .= ' VALUES ';
		foreach($data as $key=>$val) {
			if ($val === null)
				$data[$key] = 'null';
			else
				$data[$key] = "'".$this->mysql->escape($val)."'";
		}
		$query .= '('.implode(', ', array_values($data)).');';
		return $this->mysql->insert($query);
	}
	
	/**
	 * @param	String[]	$fields	array of databasefields, if empty or null
	 * all fields are selected
	 * @param	String[][]	$orderby	array(array('orderby' => $name,
	 * 'orderdir' => $direction))
	 * @param	String[][]	$where	array(array('field', 'comp', 'val',
	 * 'next')), where default for comparator is = and next is AND
	 */
	public function select($fields, $tables, $where=array(), $orderby=array()) {
		$fields = $this->createFields($fields,"",'*');
		$tables = $this->createFields($tables,"",null);
		$orderby = $this->createOrderby($orderby);
		$where = $this->createWhere($where);

		$statement = $this->buildStatement($fields, $tables, $where, $orderby);
		return $this->mysql->select($statement);		
	}
	
	private function buildStatement($fields, $tables, $where, $orderby) {
		$result = '';
		$result .= 'SELECT '.implode(',', $fields);
		$result .= ' FROM '.implode(',', $tables);
		if(!empty($where)) {
			$result .= ' WHERE '.implode('', $where);
		}
		if(!empty($orderby)) {
			$result .= ' ORDER BY '.implode(',', $orderby);
		}
		return $result.";";
	}
	
	private function createWhere($where){
		$result = array();
		for($i = 0; $i < count($where); $i++) {
			$next = $where[$i];
			if(!isset($next['field']) || empty($next['field']))
				die("field must be set for where-claus");
			else
				$next['field'] = $this->mysql->escape($next['field']);
			if(!isset($next['comp']) || empty($next['field']))
				$next['comp'] = '=';
			else
				$next['comp'] = $this->mysql->escape($next['comp']);
			
			// escape values
			if(!empty($next['val']))
				$next['val'] = "'".$this->mysql->escape($next['val'])."'";
			else if(!empty($next['rawval']))
				$next['val'] = $this->mysql->escape($next['rawval']);
			else
				die("No value set for where clause");

			if(!isset($next['next']) || empty($next['next']))
				if($i < (count($where)-1))
					$next['next'] = 'AND';
			else			
				if(!($i < (count($where)-1)))
					$next['next'] = '';
				else
					$next['next'] = $this->mysql->escape($next['next']);
			
			$result[] = $next['field'].' '.$next['comp'].' '.$next['val'].' '.$next['next'].' '; 
		}
		return $result;
	}

	private function createFields($fields, $surround = '', $default = null) {
		$result = array();
		if(is_array($fields) && !empty($fields) && ($fields != null)) {
			foreach($fields as $field)
				$result[] = "$surround".$this->mysql->escape($field)."$surround";
		} else {
			if($default == null)
				die("no elements submitted and no default set");
			$result = array($default);
		}
		return $result;
	}

	private function createOrderby($orderby) {
		$result = array();
		foreach($orderby as $item) {
			if(!isset($item['orderby']))
				die("missing orderby in statement");
			else
				$item['orderby'] = $this->mysql->escape($item['orderby']);
			if(!isset($item['orderdir']) || empty($item['orderdir']))
				$item['orderdir'] = 'ASC';
			else
				$item['orderdir'] = $this->mysql->escape($item['orderdir']);
			$result[] = $item['orderby'].' '.$item['orderdir'];
		}
		return $result;
	}	
	
}
?>