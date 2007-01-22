<?
abstract class Object {
	/**
	 * object classname lowercase
	 */
	protected $classname = null;

	/**
	 * object data definition
	 */
	protected $definition = array ();

	/**
	 * object id
	 */
	protected $id = null;

	/**
	 * object attributes
	 */
	protected $data = array ();

	/**
	 * object information
	 */
	protected $info = array ();

	/**
	 * set attribute
	 * 
	 * @param	$key	String	name of attribute
	 * @param	$value	String	value of attribute
	 */
	public function set($key, $value) {
		// only set if key is defined for object
		if (isset($this->definition[$key])) {
			$this->data[$key] = $value;
			return true;
		} else
			return false
	}

	/**
	 * get attribute
	 * 
	 * @param	$key	String	name of attribute
	 * @return	$value	String	value of attribute
	 *
	 */
	public function get($key) {
		if (isset ($this->data[$key]))
			return $this->data[$key];
		else if (isset ($this->info[$key]))
			return $this->info[$key];
		else
			return null;
	}

	/**
	 * return id of logged in user else null
	 */
	public function loggedin() {
		return null;
	}

	protected function insert($table, $data) {
		global $mysql;
		return $mysql->insert($table, $data);
	}

	/**
	 * save object attributes only
	 */
	protected function save_attributes() {
		// delete all attributes, if existing
		$this->delete('attribute', array('field'=>'objectid', 'val'=>$this->id));
		
		// save attributes
		foreach ($this->definition as $key => $val) {
			if (isset ($this->data[$key])) {
				// TODO: validate
			} else {
				if (isset ($val['DEFAULT']))
					$this->data[$key] = $val['DEFAULT'];
				else
					$this->data[$key] = null;
			}
			$insert['objectid'] = $this->id;
			$insert['name'] = $key;
			$insert['value'] = $this->data[$key];
			$this->insert('attribute', $insert);
		}
	}

	protected function delete($table='object', $where=array()) {
		global $mysql;
		if (empty($where))
			$where[]= array('field'=>'id', 'val'=>$this->id);
		$mysql->delete($table, $where);
	}
	
	/**
	 * save object to database
	 */
	public function save() {
		if (empty ($this->id)) {
			$this->info['__createdon'] = Date :: now();
			$this->info['__createdby'] = $this->loggedin();
			$this->info['__changedon'] = null;
			$this->info['__changedby'] = null;
			$this->id = $this->insert('object', $this->info);
		} else {
			$this->info['__changedon'] = Date :: now();
			$this->info['__changedby'] = $this->loggedin();
		  	$where[]= array('field'=>'id', 'val'=>$this->id);
			$where[]= array('field'=>'type', 'val'=>$this->classname,'comp'=>'LIKE');
			global $mysql;
			$mysql->update('object', $this->info, $where);
		}
		$this->save_attributes();
		return $this->id;
	}

	/**
	 * public constructor
	 * 
	 * @param	$id	int	id ob object
	 */
	public function Object($id = null) {
		if (!empty ($id))
			$this->id = $id;
		$this->classname = strtolower(get_class($this));
		$this->initialize();
		$this->load();
	}

	/**
	 * execute query
	 */
	protected function select($fields, $tables, $where = array (), $orderby = array ()) {
		global $mysql;
		return $mysql->select($fields, $tables, $where, $orderby);
	}

	/**
	 * load object from database
	 */
	public function load() {
		$this->info['type'] = $this->classname;
		$this->info['__createdon'] = null;
		$this->info['__createdby'] = null;
		$this->info['__changedon'] = null;
		$this->info['__changedby'] = null;
		if ($this->id != null) {
			$fields = array ();
			$tables = array (
				'object_view'
			);
			$where[] = array (
				'field' => 'id',
				'val' => $this->id
			);
			$where[] = array (
				'field' => 'type',
				'val' => $this->classname,
				'comp' => 'LIKE'
			);
			$result = $this->select($fields, $tables, $where);
			if (!empty ($result)) {
				$this->info['__createdon'] = $result[0]['__createdon'];
				$this->info['__createdby'] = $result[0]['__createdby'];
				$this->info['__changedon'] = $result[0]['__changedon'];
				$this->info['__changedby'] = $result[0]['__changedby'];
				foreach ($result as $row) {
					if (isset ($this->definition[$row['name']]))
						$this->data[$row['name']] = $row['value'];
				}
			}
		}
	}

	protected function initialize() {
		$parser = new ObjectDefinitionParser($this->classname);
		$def = $parser->parse();
		$this->definition = $def['ATTRIBUTE'];
	}
	
	/**
	 * get list of objects
	 *
	 * @param unknown_type $where
	 * @param unknown_type $orderby
	 * @param unknown_type $limit
	 * @param unknown_type $limitstart
	 */
	public function getlist($where=array(), $orderby=array(), $limit=null, $limitstart=0) {
		// TODO
		return array();
	}
}
?>