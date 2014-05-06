<?
PHP :: requireClasses('Database', 'WebApplication', 'Arrays');

/**
 * This class holds a data object information,
 * such as the table it belongs and the fields of it.
 * @package Database
 */
class DataObjectInfo {

	/**
	 * The table of this DataObjectInfo
	 * @var Table
	 */
	var $table;
	/**
	 * The DataObject class name of this DataObjectInfo
	 * @var string
	 */
	var $do_class_name;
	/**
	 * The sequence of this data object table. May be not defined
	 * @var string
	 */
	var $sequence_name;

	/**
	 * Constructs a DataObjectInfo from a Table and a
	 * DataObject class name.
	 * @param Table $table the table with its fields
	 * @param string $do_class_name the name of the class this DataObjectInfo
	 * corresponds to
	 */
	function DataObjectInfo($table, $do_class_name) {
		$this->table = $table;
		$this->do_class_name = $do_class_name;
	}

	/**
	 * Returns the names of the primary keys of the DataObjectInfo, as a strings array.
	 * @return string[] the names of the primary keys of the DataObjectInfo
	 */
	function getPrimaryKeys() {
		$keys = array ();
		foreach ($this->table->fields as $field) {
			if ($field->pk)
				array_push($keys, $field->name);
		}
		return $keys;
	}

	/**
	 * Returns the class name of the Data Object for this info.
	 * @return string the class name of the Data Object for this info
	 */
	function getDataObjectClassName() {
		return $this->do_class_name;
	}

}

/**
 * The class that manages data objects.
 * Specific implementations manages tables in databases.
 *
 * You should never invoke twice this methods: query, getFromKeys.
 *
 * In order to work, a section in the web.ini file (see WebApplication.php)
 * under the name "dataObjects" must be defined, with the following
 * parameters:
 * <ul>
 *   <li>database_class: the database class used (eg: MysqlDatabase). Must be in _p/classes/Database</li>
 *   <li>url: the url to the database</li>
 *   <li>database: the database to use in that url</li>
 *   <li>username: the name of a user of the database</li>
 *   <li>password: the password for that user</li>
 * </ul>
 * @package Database
 * @implements Browser
 * @abstract
 */
class DataObjectsManager {

	/**#@+
	 * @access private
	 */
	var $database;
	var $doi;
	var $query;

	var $current_page;
	var $results_per_page;
	var $start_from;
	var $show_only;
	var $result_set;
	var $conn;
	var $total_results;

	var $replacements;

	var $next_counter;
	/**#@-*/

	/**
	 * Constructs a DataObjectsManager.
	 */
	function DataObjectsManager() {
		$this->database = $this->getDatabase();
		$this->doi = $this->_getDataObjectInfo();
		$this->current_page = 1;
		$this->replacements = array ();

		// Armo el query
		$this->query = new SelectQuery($this->doi->table->name);
	}

	/** @access private */
	function _prepareQuerySelect() {
		$i = 0;
		foreach ($this->doi->table->fields as $field) {
			$this->query->addSelect($this->doi->table->name.'.'.$field->name.' as _'.$i ++);
		}
	}

	/**
	 * Returns the underlying Database used by all DataObjectsManager.
	 * This method is usefull to execute custom queries that cannot be handled
	 * by a DataObjectsManager (and to act as a data source).
	 * @return Database the underlying database
	 * @static.
	 */
	function getDatabase() {

		global $application;

		// Busco primero el atributo en la web application
		// para tener solo una instancia de la base de datos
		$database = $application->getAttribute('__dataObjects__database');
		if (!$database) {
			// Si no lo encuentro, lo creo por unica vez
			$section = $application->getIniSection('dataObjects');
			if (!$section) {
				print 'Fatal Error: the [dataObjects] entry was not found on the web.ini file';
				die();
			}

			$db_class = $section->getParameter('database_class');

			PHP :: requireClasses("Database/$db_class");

			$url = $section->getParameter('url');
			$db = $section->getParameter('database');
			$user = $section->getParameter('username');
			$pass = $section->getParameter('password');

			$database = new $db_class ($url, $db, $user, $pass);
			// Y lo guardo en la web application
			$application->setAttribute('__dataObjects__database', $database);
		}

		return $database;
	}

	/**
	 * Returns the DataObjectInfo of this manager.
	 * @return DataObjectInfo the DataObjectInfo of this manager
	 * @access protected
	 * @abstract
	 */
	function _getDataObjectInfo() {
	}

	/**
	 * This method is public and does exactly
	 * the same as _getDataObjectInfo().
	 * @return DataObjectInfo the DataObjectInfo of this manager
	 */
	function & getDataObjectInfo() {
		return $this->doi;
	}

	/**
	 * Returns the primary keys of the DataObjects this Manager manages.
	 * @return string[] the names of the primary keys of the DataObjectInfo
	 */
	function getPrimaryKeys() {
		return $this->doi->getPrimaryKeys();
	}

	/**
	 * Returns the classname of the objects managed by this manager.
	 * @return string the class name of the DataObjects managed by this manager
	 */
	function getObjectsClassName() {
		return $this->doi->do_class_name;
	}

	/**
	 * Adds a where clause with a condition and its replacements.
	 * The $replacements variable is an array containg QueryReplacement
	 * object to replace in the condition.
	 * For example:
	 *   addWhere("table.id > :id AND table.title = :title", new QueryReplacement("id", 2, SQLTypes::integer(), new...);
	 * Allways use the table prefix to avoid ambiguos names problems.
	 * @param string $condition the condition to add the where clause
	 * @param QueryReplacement $replacements replacements containing QuerReplacement objects
	 */
	function addWhere($condition, $replacements = null) {
		$this->query->addWhere($condition);
		$args = func_get_args();
		for ($i = 1; $i < sizeof($args); $i ++) {
			if (is_array($args[$i])) {
				foreach ($args[$i] as $rep) {
					array_push($this->replacements, $rep);
				}
			} else {
				array_push($this->replacements, $args[$i]);
			}
		}
	}

	/**
	 * Adds a where condition over a known field (a field of the objects this DataObjectsManager manages).
	 * Also can add a field over a field of unknown type, and provide its type.
	 *
	 * Examples:
	 * <code>
	 * $man->addWhereField('name', '=', 'manager');
	 * $man->addWhereField('id', '!=', 5);
	 * $man->addWhereField('author_id', '=', null); // this translates into 'author_id IS NULL'
	 * $man->addWhereField('media_id', '!=', null); // this translates into 'author_id IS NOT NULL'
	 *
	 * $man->addJoin('this_table', 'another_table', 'field1', 'field2');
	 * $man->addWhereField('another_table.field2', '=', 1, SQL_TYPE_INTEGER);
	 * </code>
	 * @param string $field_name the name of the field
	 * @param string $condition any of >, >=, =, !=, <=, <.
	 * @param mixed $value the value for the condition
	 */
	function addWhereField($field_name, $condition, $value, $type = null) {
		$uniqid = uniqid('_');
		if ($type) {
			$this->addWhere($field_name.' '.$condition.' :'.$uniqid, new QueryReplacement($uniqid, $value, $type));
		} else {
			foreach ($this->doi->table->fields as $field) {
				if ($field->name == $field_name) {
					if (is_null($value) and $condition == '=') {
						$this->addWhere($this->ensureField($field_name).' IS NULL');
					} else
						if (is_null($value) and $condition == '!=') {
							$this->addWhere($this->ensureField($field_name).' IS NOT NULL');
						} else {
							$this->addWhere($this->ensureField($field_name).' '.$condition.' :'.$uniqid, new QueryReplacement($uniqid, $value, $field->sqltype));
						}
				}
			}
		}
	}

	/**
	 * Adds an order by a field.
	 * The name of the field can be something like table.field or
	 * just field, assuming that it belongs to this DataObjectsManager table.
	 * @param string $field_name the name of the field
	 * @param boolean $ascendent true if the order is ascendent, false if it is descendent
	 */
	function addOrder($field_name, $ascendent = true) {
		$field_name = $this->ensureField($field_name);
		$this->query->addOrder($field_name, $ascendent);
	}

	/**
	 * Adds a join to the query of this manager.
	 * @param string $from_table the table from
	 * @param string $to_table the table to
	 * @param string $from_field the field from
	 * @param string $from_to the field to
	 */
	function addJoin($from_table, $to_table, $from_field, $to_field) {
		$this->query->addJoin($from_table, $to_table, $from_field, $to_field);
	}

	/**
	 * Adds a left join to the query. If $from_table does not yet exists in the query
	 * (it is not in the FROM clause), this method adds it.
	 * @param string $from_table the table from
	 * @param string $to_table the table to
	 * @param string $from_field the field from
	 * @param string $from_to the field to
	 */
	function addLeftJoin($from_table, $to_table, $from_field, $to_field) {
		$this->query->addLeftJoin($from_table, $to_table, $from_field, $to_field);
	}

	/**
	 * Adds a search in the specified field by the specified value.
	 * Currently, only supports string and integer types.
	 * If $field_name is null (or not set) then nothing is done
	 * @param string $field_name the name of the field. Can be something like table.field or
	 * just field, assuming that it belongs to this DataObjectsManager table.
	 * @param mixed $field_value the value to search
	 * @param DataObjectsSearchCriteria the search criteria
	 */
	function addSearch($search_value, $search_criteria) {
		// Delego el resto al criterio de busqueda
		$search_value = str_replace('%', '\%', $search_value);
		$search_criteria->addSearch($this, $search_value);
	}

	/**
	 * Sets the current page to show. If the number of the current
	 * page is bigger than the number of total pages, than
	 * the number of the current page will be the number of total
	 * pages.
	 * @param integer $page the page to show
	 */
	function setCurrentPage($page) {
		$page = (int) $page;
		if ($page < 0) {
			$this->current_page = 1;
		} else {
			$this->current_page = $page;
		}
	}

	/**
	 * Returns the current page shown.
	 * @return the current page shown
	 */
	function getCurrentPage() {
		return $this->current_page;
	}

	/**
	 * Sets the number of results to show in a page.
	 * after performing the query.
	 * @param integer $page the number of results to show in a page
	 */
	function setResultsPerPage($results_per_page) {
		$this->results_per_page = $results_per_page;
	}
	
	/**
	 * Sets where the query will start from (which row number).
	 * (only works if setShowOnly was set).
	 */
	function setStartFrom($start_from) {
		$this->start_from = $start_from;
	}
	
	/**
	 * Sets the number of results to show.
	 * If you don't interest about paginating the results shown,
	 * but just to show the first N results (starting
	 * from what was set in startFrom, or 0), use this method
	 * because it is better performancing.
	 * @param integer $results the number of results to show
	 */
	function setShowOnly($results) {
		$this->show_only = $results;
	}

	/**
	 * Returns the number of the first result.
	 * @return integer the number of the first result
	*/
	function getFirstResult() {
		if (!$this->results_per_page) {
			return $this->getShownResults() == 0 ? 0 : 1;
		}
		return $this->getTotalResults() == 0 ? 0 : ($this->current_page - 1) * $this->results_per_page + 1;
	}

	/**
	* Returns the number of the last result.
	* @return integer the number of the last result
	*/
	function getLastResult() {
		if ($this->getShownResults() == 0) {
			return 0;
		}
		if (!$this->results_per_page) {
			return $this->getShownResults();
		}
		return $this->getFirstResult() + $this->getShownResults() - 1;
	}

	/**
	* Returns the number of results shown.
	* @return integer the number of results shown
	*/
	function getShownResults() {
		return $this->result_set->size();
	}

	/**
	 * Returns the total results in the database that matches
	 * the settings, ignoring the "current page / results per page"
	 * settings. Or, if the "current page / results per page" was not set,
	 * returns the same as getShownResults().
	 * @return integer the total results in the database that matches the settings
	 */
	function getTotalResults() {
		if ($this->results_per_page) {
			return $this->total_results;
		} else {
			return $this->getShownResults();
		}
	}

	/**
	 * Returns the total number of pages found, according to the
	 * "current page / results per page" settings, if set, or
	 * 1 if not.
	 * @return integer the total number of pages found
	 */
	function getTotalPages() {
		if ($this->results_per_page) {
			$x = ceil($this->getTotalResults() / $this->results_per_page);
			if ($x == 0)
				$x = 1;
			return $x;
		} else {
			return 1;
		}
	}

	/**
	 * Determines if there are more results to show, that can be obtained
	 * with the next() method.
	 * @return boolean true there are more results to show, else false
	 */
	function hasNext() {
		return $this->next_counter < $this->getShownResults();
	}

	/**
	 * Returns the next result of the query, or false if there are no more results
	 * left.
	 * @return the next result or false
	 */
	function next() {
		if ($this->result_set->next()) {
			// Incremento el contador de cuantos resultados vi
			$this->next_counter++;

			$object = new $this->doi->do_class_name();
			$i = 0;
			foreach ($this->doi->table->fields as $field) {
				$name = $field->name;
				$object-> $name = SQLTypes :: getFromResultSet($this->result_set, '_'.$i ++, $field->sqltype);
			}
			return $object;
		} else {
			return false;
		}
	}

	/**
	 * Returns all the objects obtained by query() in an array.
	 */
	function getArray() {
		$array = array ();
		while ($do = $this->next()) {
			array_push($array, $do);
		}
		return $array;
	}

	/** @access private */
	function _count() {
		$query2 = $this->query;
		$query2->setSelect('COUNT(*)');
		$query = $query2->toString();
		$statement = $this->conn->prepareStatement($query);
		foreach ($this->replacements as $replacement) {
			SQLTypes :: setToPreparedStatement($statement, $replacement->name, $replacement->value, $replacement->sqltype);
		}
		$result_set = $statement->executeQuery($query);
		$result_set->next();
		$this->total_results = $result_set->getInteger('COUNT(*)');
		if ($this->current_page > $this->getTotalPages()) {
			$this->current_page = $this->getTotalPages();
		}
	}

	/**
	 * Performs the query. If setResultsPerPage was invoked then another
	 * query is performed to count the total number of results and
	 * calculate total pages, first and last results, etc.
	 */
	function query() {
		$this->_prepareQuerySelect();
		$query = $this->query->toString();
		$this->conn = $this->database->openConnection();
		if (isset ($this->results_per_page))
			$this->_count();

		$statement = $this->conn->prepareStatement($query);
		foreach ($this->replacements as $replacement) {
			SQLTypes :: setToPreparedStatement($statement, $replacement->name, $replacement->value, $replacement->sqltype);
		}

		if (isset ($this->results_per_page)) {
			$this->first_result = ($this->current_page - 1) * $this->results_per_page;
			$this->result_set = $statement->executeLimitedQuery($this->first_result, $this->results_per_page);
		} else {
			$this->first_result = isset($this->start_from) ? $this->start_from  + 1 : 1;
			if (isset ($this->show_only)) {
				$this->result_set = $statement->executeLimitedQuery($this->first_result - 1, $this->show_only);
			} else {
				$this->result_set = $statement->executeQuery();
			}
		}
	}

	/**
	 * Deletes a data object from the database, according to the keys
	 * specified in the DataObjectInfo and the field of the $do data object.
	 * If no primary keys are set in the DataObjects, then this method returns
	 * normally.
	 * @param mixed $do the object to delete
	 * @return the number of rows affected (generally one)
	 */
	function delete($do) {
		$query = "DELETE FROM {$this->doi->table->name} WHERE ";
		$first = true;

		$keys_counter = 0;
		foreach ($this->doi->table->fields as $key) {
			if ($key->pk) {
				$name = $key->name;
				if (isset ($do-> $name)) {
					if (!$first) {
						$query .= ' AND ';
					} else
						$first = false;
					$query .= "$name = :$name";
					$keys_counter ++;
				}
			}
		}
		// Si no hay keys seteados, no borro nada
		if ($keys_counter == 0)
			return 0;

		$conn = $this->database->openConnection();
		$statement = $conn->prepareStatement($query);
		foreach ($this->doi->table->fields as $key) {
			if ($key->pk) {
				$name = $key->name;
				if (isset ($do-> $name)) {
					SQLTypes :: setToPreparedStatement($statement, $name, $do-> $name, $key->sqltype);
				}
			}
		}

		$return = $statement->executeUpdate();
		$conn->commit();
		return $return;
	}

	/**
	 * Updated a data object intthe database, according to the keys
	 * specified in the DataObjectInfo and the field of the $do data object.
	 * Only the fields that are not keys and that are setted are updated.
	 * That is, primary key fields cannot be modified.
	 * If no primary keys are set in the DataObjects, then this method returns
	 * normally.
	 * @param mixed $do the object to update
	 * @return the number of rows affected (generally one)
	 */
	function update($do) {
		$query = "UPDATE {$this->doi->table->name} SET ";
		$first = true;

		$keys_counter = 0;
		foreach ($this->doi->table->fields as $field) {
			$name = $field->name;
			// Las claves no se pueden cambiar
			// Y los atributos que no estan seteados no se modifican
			if (!$field->pk && isset ($do-> $name)) {
				if (!$first) {
					$query .= ', ';
				} else
					$first = false;
				$query .= "$name = :$name";
				$keys_counter ++;
			}
		}
		// Si no hay keys seteados, no hago update
		if ($keys_counter == 0)
			return 0;
		$query .= ' WHERE ';
		$first = true;
		// Solo donde haya una clave seteada
		foreach ($this->doi->table->fields as $key) {
			if ($key->pk) {
				$name = $key->name;
				if (isset ($do-> $name)) {
					if (!$first) {
						$query .= ' AND ';
					} else
						$first = false;
					$query .= "$name = :$name";
				}
			}
		}
		// Si no hay claves, no hago update
		if ($first)
			return 0;

		$conn = $this->database->openConnection();
		$statement = $conn->prepareStatement($query);
		// Reemplazo solo en los atributos seteados
		foreach ($this->doi->table->fields as $field) {
			$name = $field->name;
			if (isset ($do-> $name)) {
				SQLTypes :: setToPreparedStatement($statement, $name, $do-> $name, $field->sqltype);
			}
		}

		$return = $statement->executeUpdate();
		$conn->commit();
		return $return;
	}

	/**
	 * Inserts a data object into the database. If the object has a unique
	 * integer primary key, then the next sequence value will be inserted
	 * (or, if the database support autonumerics, the next autonumeric value).
	 * If the object has a unique integer primary key
	 * then after this method is invoked, the $do object
	 * will have that field assigned with the recently created value.
	 * This method does nothing if the DataObject does not have at least
	 * one field set to a value.
	 * @param mixed $do the DataObject to insert
	 * @return boolean true if success, else false
	 */
	function insert(& $do) {
		$query = "INSERT INTO {$this->doi->table->name} (";
		$first = true;

		$fields_counter = 0;
		foreach ($this->doi->table->fields as $field) {
			$name = $field->name;
			// Si esta seteado el valor en el objecto, no me preocupo
			if (isset ($do-> $name)) {
				if (!$first) {
					$query .= ', ';
				} else
					$first = false;
				$query .= $name;
				$fields_counter ++;
			} else {
				// Si no esta seteado, me fijo si es autonumerico
				if ($field->autonumeric) {
					// Solo si la base de datos no acepta secuencias autonumericas
					if (!$this->database->hasAutonumericSequences()) {
						if (!$first) {
							$query .= ', ';
						} else
							$first = false;
						$query .= $name;
					}
				}
			}
		}
		// Si no hay campos seteados, no hago insert
		if ($fields_counter == 0)
			return false;

		$query .= ') VALUES (';
		$first = true;
		foreach ($this->doi->table->fields as $field) {
			$name = $field->name;
			if (isset ($do-> $name)) {
				if (!$first) {
					$query .= ', ';
				} else
					$first = false;
				$query .= ":$name";
			} else {
				if ($field->autonumeric) {
					if (!$this->database->hasAutonumericSequences()) {
						if (!$first) {
							$query .= ', ';
						} else
							$first = false;
						$query .= $this->database->getSequenceNextVal($this->doi->sequence_name);
					}
				}
			}
		}
		$query .= ")";

		$conn = $this->database->openConnection();
		$statement = $conn->prepareStatement($query);
		foreach ($this->doi->table->fields as $field) {
			$name = $field->name;
			if (isset ($do-> $name)) {
				SQLTypes :: setToPreparedStatement($statement, $name, $do-> $name, $field->sqltype);
			}
		}

		$return = $statement->executeUpdate();
		$conn->commit();

		// Me fijo de actualizar el campo autonumerico en el objeto
		$autonumeric = $this->doi->table->getAutonumericField();
		if ($autonumeric) {
			$name = $autonumeric->name;
			$do-> $name = $conn->getLastInsertID($this->doi->table->name, $name);
		}

		return $return;
	}

	/**
	 * Inserts many objets into the database. This has better performance
	 * than inserting many rows with insert($do).
	 */
	function insertMany($array) {
		$query = "INSERT INTO {$this->doi->table->name} (";
		$first = true;

		$do = $array[0];
		$fields_counter = 0;
		foreach ($this->doi->table->fields as $field) {
			$name = $field->name;
			// Si esta seteado el valor en el objecto, no me preocupo
			if (isset ($do-> $name)) {
				if (!$first) {
					$query .= ', ';
				} else
					$first = false;
				$query .= $name;
				$fields_counter ++;
			} else {
				// Si no esta seteado, me fijo si es autonumerico
				if ($field->autonumeric) {
					// Solo si la base de datos no acepta secuencias autonumericas
					if (!$this->database->hasAutonumericSequences()) {
						if (!$first) {
							$query .= ', ';
						} else
							$first = false;
						$query .= $name;
					}
				}
			}
		}
		// Si no hay campos seteados, no hago insert
		if ($fields_counter == 0)
			return false;

		$query .= ') VALUES ';

		$i = 0;
		foreach ($array as $do) {
			$query .= '(';
			$first = true;
			foreach ($this->doi->table->fields as $field) {
				$name = $field->name;
				if (isset ($do-> $name)) {
					if (!$first) {
						$query .= ', ';
					} else
						$first = false;
					$query .= ":{$name}_{$i}";
				}
			}
			$query .= ")";
			$i ++;
			if ($i != sizeof($array)) {
				$query .= ', ';
			}
		}

		$conn = $this->database->openConnection();
		$statement = $conn->prepareStatement($query);

		$i = 0;
		foreach ($array as $do) {
			foreach ($this->doi->table->fields as $field) {
				$name = $field->name;
				if (isset ($do-> $name)) {
					SQLTypes :: setToPreparedStatement($statement, "{$name}_{$i}", $do-> $name, $field->sqltype);
				}
			}
			$i ++;
		}

		$return = $statement->executeUpdate();
		$conn->commit();

		return $return;
	}

	/**
	 * Inserts or update a DataObject. If ALL of the keys of the object
	 * are set than the object is updated. Else, it is created.
	 * @param mixed $do the object to insert or update
	 */
	function insertOrUpdate(& $do) {
		foreach ($this->doi->table->fields as $key) {
			if ($key->pk) {
				$name = $key->name;
				if (!isset ($do-> $name)) {
					return $this->insert($do);
				}
			}
		}
		return $this->update($do);
	}

	/**
	 * Determines if there is an entry in the database with the
	 * DataObject's primary keys.
	 * @param mixed $do the object
	 */
	function existsMatchingKeys($do) {
		$the_keys = array ();
		foreach ($this->doi->table->fields as $key) {
			if ($key->pk) {
				$name = $key->name;
				$the_keys {
					$name }
				= $do-> $name;
			}
		}
		// Me clono
		$man = $this;
		return $man->getFromKeys($the_keys);
	}

	/**
	 * Inserts a DataObjects only if it is not already in the database.
	 * That means that there are no other entry with the same primary key.
	 * Returns true if it the insertion was made, else false.
	 * @param mixed $do the object to insert
	 */
	function insertIfNotExists($do) {
		if (!$this->existsMatchingKeys($do)) {
			return $this->insert(& $do);
		}
		return 0;
	}

	/**
	 * Retrieves a data object given its keys (or any of them).
	 * The keys not necesarily has to be the object primary keys.
	 * The $keys variable must be an associative array where the keys
	 * are the name of the fields, and the values, the field values.
	 * All previous filters set to the browser are still valid. That is,
	 * if a DataObject under the given keys exists but a filter hides it,
	 * then this method returns false.
	 * @param array $keys an associative array where the keys
	 * are the name of the fields, and the values, the field values
	 * @param boolean false_on_null_key if this is true, then false is returned when a null key is found
	 * @return the data object or false if no data object was found
	 */
	function getFromKeys($keys, $false_on_null_key = false) {
		if (is_null($keys))
			return false;
		if (!is_array($keys))
			return false;
		if (sizeof($keys) == 0)
			return false;
		// Si alguna clave es null entonces no tiene sentido
		foreach ($keys as $key => $value) {
			if (is_null($value))
				return false;
		}

		if ($false_on_null_key) {
			foreach ($this->doi->table->fields as $key) {
				if ($key->pk) {
					if (is_null($keys[$key->name]))
						return false;
					$this->addWhere("{$this->doi->table->name}.{$key->name} = :{$key->name}", new QueryReplacement("{$key->name}", $keys[$key->name], $key->sqltype));
				}
			}
		} else {
			// Recorro todos los campos del data object
			foreach ($this->doi->table->fields as $key) {
				// Lo busco en el array
				if (!is_null($keys[$key->name])) {
					$this->addWhere("{$this->doi->table->name}.{$key->name} = :{$key->name}", new QueryReplacement("{$key->name}", $keys[$key->name], $key->sqltype));
				}
			}
		}
		$this->query();
		return $this->next();
	}

	/**
	 * Executes a DELETE query statement from the table managed by this
	 * DataObjectsManager with the specified where clause.
	 * @param string $where the where clause
	 * @param QueryReplacement $replacements replacements for the query
	 * @return integer the number of rows affected by the query
	 */
	function deleteWhere($where, $replacements = null) {
		$rep = array ();
		$args = func_get_args();
		for ($i = 1; $i < sizeof($args); $i ++) {
			array_push($rep, $args[$i]);
		}
		$conn = $this->database->openConnection();
		$statement = $conn->prepareStatement("DELETE FROM {$this->doi->table->name} WHERE {$where}");
		foreach ($rep as $replacement) {
			SQLTypes :: setToPreparedStatement($statement, $replacement->name, $replacement->value, $replacement->sqltype);
		}
		return $statement->executeUpdate();
	}

	/**
	 * If a field name dosen't have a dot in it (example: field instead of table.field)
	 * then it is assumed to be a field of this manager table, thus adding it the
	 * table prefix (field will be table.field).
	 * @return string see method description
	 */
	function ensureField($field_name) {
		if (substr_count($field_name, '.') == 0) {
			$field_name = $this->doi->table->name.'.'.$field_name;
		}
		return $field_name;
	}

}

/**
 * This class represents a query replacement to use
 * in the addWhere() method of the DataObjectsManager class.
 * @package Database
 */
class QueryReplacement {

	/**#@+
	 * @access private
	 */
	var $name;
	var $value;
	var $sqltype;
	/**#@-*/

	/**
	 * Constructs a QueryReplacement with a field name, field
	 * value and sqltype.
	 * @param string $name the name of the field to replace
	 * @param string $value the value of the field to replace
	 * @param integer $sqltype one of SQL_TYPE_* constants
	 */
	function QueryReplacement($name, $value, $sqltype) {
		$this->name = $name;
		$this->value = $value;
		$this->sqltype = $sqltype;
	}

}

/**
 * Class that represents a select query to a database.
 * The limit clause is not handled by this class since
 * diferents databases handle this feautre differently.
 * @access private
 */
class SelectQuery {

	var $selects;
	var $froms;
	var $wheres;
	var $orders;
	var $left_joins;

	/**
	 * Constructs a query of the form "SELECT * FROM $from";
	 */
	function SelectQuery($from) {
		$this->selects = array ();
		$this->froms = array ($from);
		$this->wheres = array ();
		$this->orders = array ();
		$this->left_joins = array ();
	}

	/**
	 * Adds the specified tables to apear in the from clause.
	 * @param string $fields the name of the tables
	 */
	function addFrom($tables) {
		$args = func_get_args();
		foreach ($args as $table) {
			if (is_null(Arrays :: search($this->froms, $table))) {
				array_push($this->froms, $table);
			}
		}
	}

	/**
	 * Adds the specified fields to apear in the select clause.
	 * @param string $fields the name of the field
	 */
	function addSelect($fields) {
		$args = func_get_args();
		foreach ($args as $field) {
			if (is_null(Arrays :: search($this->selects, $field))) {
				array_push($this->selects, $field);
			}
		}
	}

	/**
	 * Sets the specified fields to apear in the select clause.
	 * @param array $fields an array containing the name of the fields
	 */
	function setSelect($fields) {
		$this->selects = array ();
		$args = func_get_args();
		foreach ($args as $field) {
			array_push($this->selects, $field);
		}
	}

	/**
	 * Ads a where clause. Where clauses are added by the AND glue.
	 * @param string $condition a condition
	 */
	function addWhere($condition) {
		array_push($this->wheres, $condition);
	}

	/**
	 * Adds an order by a field.
	 * @param string $field the name of the field
	 * @param boolean $ascendent true if the order is ascendent, false if it is descendent
	 */
	function addOrder($field, $ascendent = true) {
		$order = $field.' ';
		$order .= $ascendent ? 'ASC' : 'DESC';
		array_push($this->orders, $order);
	}

	/**
	 * Adds a join to this query.
	 * @param string $from_table the table from
	 * @param string $to_table the table to
	 * @param string $from_field the field from
	 * @param string $from_to the field to
	 */
	function addJoin($from_table, $to_table, $from_field, $to_field) {
		$this->addFrom($from_table);
		$this->addFrom($to_table);
		$this->addWhere("$from_table.$from_field = $to_table.$to_field");
	}

	/**
	 * Adds a left join to this query. If $from_table does not yet exists in the query
	 * (it is not in the FROM clause), this method adds it.
	 * @param string $from_table the table from
	 * @param string $to_table the table to
	 * @param string $from_field the field from
	 * @param string $from_to the field to
	 */
	function addLeftJoin($from_table, $to_table, $from_field, $to_field) {
		$this->addFrom($from_table);
		$left_join = array ();
		$left_join['from_table'] = $from_table;
		$left_join['to_table'] = $to_table;
		$left_join['from_field'] = $from_field;
		$left_join['to_field'] = $to_field;
		array_push($this->left_joins, $left_join);
	}

	/**
	 * Returns the formed query.
	 * @return string the formed query
	 */
	function toString() {
		// Select
		$query = 'SELECT ';
		if ($this->distinct) {
			$query .= 'DISTINCT ';
		}
		if (sizeof($this->selects) > 0) {
			$i = 0;
			foreach ($this->selects as $select) {
				$query .= "$select";
				if (++ $i != sizeof($this->selects))
					$query .= ', ';
			}
		} else {
			$query .= '*';
		}
		// From
		$query .= ' FROM ';
		$i = 0;
		foreach ($this->froms as $from) {
			$query .= "$from";
			// Left joins
			foreach ($this->left_joins as $left_join) {
				if ($left_join['from_table'] == $from) {
					$query .= ' LEFT JOIN '.$left_join['to_table'].' ON '.$left_join['from_table'].'.'.$left_join['from_field'].' = '.$left_join['to_table'].'.'.$left_join['to_field'];
				}
			}
			if (++ $i != sizeof($this->froms))
				$query .= ', ';
		}

		// Where
		if (sizeof($this->wheres)) {
			$query .= ' WHERE ';
			$i = 0;
			foreach ($this->wheres as $where) {
				$query .= "$where";
				if (++ $i != sizeof($this->wheres))
					$query .= ' AND ';
			}
		}
		// Order By
		if (sizeof($this->orders)) {
			$query .= ' ORDER BY ';
			$i = 0;
			foreach ($this->orders as $order) {
				$query .= "$order";
				if (++ $i != sizeof($this->orders))
					$query .= ', ';
			}
		}
		return $query;
	}

}

/**
 * This search criteria matches when the search value is a substring
 * of the object searched (case-insensitive).
 *
 * Only applicable to scalar values.
 *
 * @implements DataObjectsSearchCriteria
 */
class DataObjectsSubstringSearchCriteria {

	/** @access private */
	var $search_fields;

	/**
	 * Constructs a SubstringSearchCriteria.
	 * @param mixed $search_fields (...) the search fields in where to look
	 * for the search value.
	 */
	function DataObjectsSubstringSearchCriteria($search_fields) {
		$this->search_fields = func_get_args();
	}

	function addSearch(& $manager, $search_value) {
		for ($i = 0; $i < sizeof($this->search_fields); $i ++) {
			$field_name = $manager->ensureField($this->search_fields[$i]);
			$query .= "{$field_name} LIKE (:_the_search_value)";
			if ($i != sizeof($this->search_fields) - 1) {
				$query .= ' OR ';
			}
		}
		$manager->addWhere($query, new QueryReplacement('_the_search_value', "%{$search_value}%", SQL_TYPE_STRING));
	}

}

/**
 * This search criteria matches when the search value is the same as the object searched.
 *
 * Only applicable to scalar values.
 *
 * @implements DataObjectsSearchCriteria
 */
class DataObjectsSameValueSearchCriteria {

	/** @access private */
	var $search_field;

	/**
	 * Constructs a SameValueSearchCriteria.
	 * @param string $search_field the search field in where to look for
	 * the search value
	 */
	function DataObjectsSameValueSearchCriteria($search_field) {
		$this->search_field = $search_field;
	}

	function addSearch(& $manager, $search_value) {
		$manager->addWhere($this->search_field.' = :_the_search_value', new QueryReplacement('_the_search_value', $search_value, SQL_TYPE_STRING));
	}

}

/**
 * This search criteria matches a Date value, given its format.
 *
 * Only applicable to Date objets.
 *
 * @implements DataObjectsSearchCriteria
 */
class DataObjectsSameDateSearchCriteria {

	/**#@+
	 * @access private
	 */
	var $search_field;
	var $format;
	var $sql_date_type;
	/**#@-*/

	/**
	 * Constructs this criteria.
	 * @param string $search_field the search field in where to look for
	 * the search value
	 * @param string format the format of the search value, the same as the date() function of PHP.
	 * Currently only accepts the letters s, i, H, d, m, Y.
	 * @param int one of the SQL_TYPE_* constantes (SQL_TYPE_DATE, etc.).
	 */
	function DataObjectsSameDateSearchCriteria($search_field, $format, $sql_date_type) {
		$this->search_field = $search_field;
		$this->format = $format;
		$this->sql_date_type = $sql_date_type;
	}

	function addSearch(& $manager, $search_value) {
		for ($i = 0, $j = 0; $i < strlen($this->format); $i ++) {
			switch ($this->format {
				$i }) {
				case 's' :
					$second = $search_value {
						$j ++}
					.$search_value {
						$j ++};
					break;
				case 'i' :
					$minute = $search_value {
						$j ++}
					.$search_value {
						$j ++};
					break;
				case 'H' :
					$hour = $search_value {
						$j ++}
					.$search_value {
						$j ++};
					break;
				case 'd' :
					$day = $search_value {
						$j ++}
					.$search_value {
						$j ++};
					break;
				case 'm' :
					$month = $search_value {
						$j ++}
					.$search_value {
						$j ++};
					break;
				case 'Y' :
					$year = $search_value {
						$j ++}
					.$search_value {
						$j ++}
					.$search_value {
						$j ++}
					.$search_value {
						$j ++};
					break;
				default :
					$j ++;
			}
		}
		$search_value = Date :: getDateTime($year, $month, $day, $hour, $minute, $second);
		$manager->addWhere($this->search_field.' = :_the_search_value', new QueryReplacement('_the_search_value', $search_value, $this->sql_date_type));
	}

}
?>