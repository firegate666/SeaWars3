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
	 */
	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	/**
	 * get attribute
	 */
	public function get($key) {
		if (isset ($this->data[$key]))
			return $this->data[$key];
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

	/**
	 * save object to database
	 */
	public function save() {
		global $mysql;
		$this->type['__createdon'] = Date :: now();
		$this->type['__createdby'] = $this->loggedin();
		$this->type['__changedon'] = null;
		$this->type['__changedby'] = null;
		$this->id = $mysql->insert('object', $this->type);
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
	protected function select($fields, $tables, $where=array(), $orderby=array()) {
		global $mysql;
		return $mysql->select($fields, $tables, $where, $orderby);
	}

	/**
	 * load object from database
	 */
	public function load() {
		$this->type['type'] = $this->classname;
		$this->type['__createdon'] = null;
		$this->type['__createdby'] = null;
		$this->type['__changedon'] = null;
		$this->type['__changedby'] = null;
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
				$this->type['__createdon'] = $result[0]['__createdon'];
				$this->type['__createdby'] = $result[0]['__createdby'];
				$this->type['__changedon'] = $result[0]['__changedon'];
				$this->type['__changedby'] = $result[0]['__changedby'];
				foreach ($result as $row) {
					if (isset ($this->definition[$row['name']]))
						$this->data[$row['name']]=$row['value'];
				}
			}
		}
	}

	protected function initialize() {
		$parser = new ObjectDefinitionParser($this->classname);
		$def = $parser->parse();
		$this->definition = $def['ATTRIBUTE'];
	}
}
?>