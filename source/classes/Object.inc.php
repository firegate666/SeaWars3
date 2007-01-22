<?
abstract class Object {
	/**
	 * object classname lowercase
	 */
	protected $classname = null;

	/**
	 * object data definition
	 */
	protected $att_definition = array();
	
	/**
	 * object relation definition
	 */
	protected $rel_definition = array();

	/**
	 * object id
	 */
	protected $id = null;

	/**
	 * object attributes
	 */
	protected $attributes = array ();
	
	/**
	 * object relations
	 */
	protected $relations = array();

	/**
	 * object information
	 */
	protected $info = array ();

	/**
	 * set attribute
	 * 
	 * @param	String	$key	name of attribute
	 * @param	String	$value	value of attribute
	 */
	public function set($key, $value) {
		// only set if key is defined for object
		if (isset($this->att_definition[$key])) {
			$this->attributes[$key] = $value;
			return true;
		// or maybe this is a relation? check class
		} else if(isset($this->rel_definition[$key]) && (strtolower(get_class($value)) == $this->rel_definition[$key]['RELATES'])) {
			$this->relations[$key] = $value;
			return true;
		// not found at all
		} else
			throw new AttributeNotDefinedException($key);
	}

	/**
	 * get attribute
	 * 
	 * @param	String	$key	name of attribute
	 * @return	String	value of attribute
	 *
	 */
	public function get($key) {
		// generic data?
		if (isset ($this->info[$key]))
			return $this->info[$key];
		// attribute?
		else if (isset ($this->attributes[$key]))
			return $this->attributes[$key];
		// relation?
		else if (isset ($this->relations[$key]))
			return $this->relations[$key];
		// not defined
		else
			throw new AttributeNotDefinedException($key);
	}

	/**
	 * return id of logged in user else null
	 */
	public function loggedin() {
		return null;
	}

	/**
	 * @see MySQLInterface#insert
	 */
	protected function insert($table, $data) {
		global $mysql;
		return $mysql->insert($table, $data);
	}

	/**
	 * save object relations only
	 */
	protected function save_relations() {
		// delete all relations, if existing
		$this->delete('relation', array(array('field'=>'object1', 'val'=>$this->id)));
		// save relations
		foreach ($this->rel_definition as $key => $val) {
			if (isset($this->relations[$key]) && ($this->rel_definition[$key]['RELATES']==$this->relations[$key]->classname)){
				// TODO: validate
				if (empty($this->relations[$key]->id)) // speichern wenn nicht geschehen
					$this->relations[$key]->save();
				$insert['object1'] = $this->id;
				$insert['name'] = $key;
				$insert['object2'] = $this->relations[$key]->id;
				$this->insert('relation', $insert);
			}
		}
	}

	/**
	 * save object attributes only
	 */
	protected function save_attributes() {
		// delete all attributes, if existing
		$this->delete('attribute', array(array('field'=>'objectid', 'val'=>$this->id)));
		
		// save attributes
		foreach ($this->att_definition as $key => $val) {
			if (isset ($this->attributes[$key])) {
				// TODO: validate
			} else {
				if (isset ($val['DEFAULT']))
					$this->attributes[$key] = $val['DEFAULT'];
				else
					$this->attributes[$key] = null;
			}
			$insert['objectid'] = $this->id;
			$insert['name'] = $key;
			$insert['value'] = $this->attributes[$key];
			$this->insert('attribute', $insert);
		}
	}

	/**
	 * @see MySQLInterface#update
	 */
	protected function update($table, $data, $where) {
		global $mysql;
		return $mysql->update($table, $data, $where);
	}

	/**
	 * @see MySQLInterface#delete
	 */
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
			$this->update('object', $this->info, $where);
		}
		$this->save_attributes();
		$this->save_relations();
		return $this->id;
	}

	/**
	 * public constructor
	 * 
	 * @param	int	$id	id ob object
	 */
	public function __construct($id = null) {
		if (!empty ($id))
			$this->id = $id;
		$this->classname = strtolower(get_class($this));
		$this->initialize();
		$this->load();
	}

	/**
	 * @see MySQLInterface#select
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
					if (isset ($this->att_definition[$row['name']]))
						$this->attributes[$row['name']] = $row['value'];
				}
				$this->load_relations();
			}
		} 
	}

	/**
	 * load relations for object
	 */
	protected function load_relations() {
			$fields = array ();
			$tables = array (
				'relation_view'
			);
			$where[] = array (
				'field' => 'object1',
				'val' => $this->id
			);
			$result = $this->select($fields, $tables, $where);
			foreach($result as $relation) {
				print_a($relation);
				$obj = new $relation['type']($relation['object2']);
				$this->relations[$relation['name']] = $obj;
			}
	}

	/**
	 * initialize object
	 */
	protected function initialize() {
		$parser = new ObjectDefinitionParser($this->classname);
		$def = $parser->parse();
		if (!empty($def['ATTRIBUTE']))
			$this->att_definition = $def['ATTRIBUTE'];
		if (!empty($def['RELATION']))
			$this->rel_definition = $def['RELATION'];
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