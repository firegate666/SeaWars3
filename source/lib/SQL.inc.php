<?
   /** 
  This class is supposed to be seen as Interface for different database connections
  Abstract class, no functionality
*/
abstract class SQL {

	/** all queries are stored */
	protected $queries = array();

	/**
	* DB Ressource connection
	*/
	protected $dblink;

	/**
	 * array of last error messages
	 */
	protected $lasterrors = array();
	
	/**
	 * return array of last errors
	 */
	public function getLastErrors() {
		return $this->lasterrors;
	}

	/**
	* returns number of queries executed
	* @return	int	number of queries
	*/
	public function getQuerycount() {
		return count($this->queries);
	}

	/**
	 * Return all queries of this instance
	 */
	public function getQueries() {
		return $this->queries;
	}

	/**
	  Connects to Database using global parameters
	  return : databaselink
	*/
	abstract function connect();

	/**
	  Disconnects database
	  $dblink : databaselink
	*/
	abstract function disconnect();

	/**
	  Executes SQL insert statement
	  return : last insert id
	*/
	abstract function insert($query);

	/**
	  Executes SQL select statement
	  return : result set as numeric array
	*/
	abstract function select($query, $assoc = false);

	/**
	  Executes SQL update statement
	  return : number of affected rows
	*/
	abstract function update($query);
	
	abstract function escape($string);
}
?>