<?
define('SQL_TYPE_BOOLEAN',                         1);
define('SQL_TYPE_INTEGER',                         2);
define('SQL_TYPE_FLOAT',                           3);
define('SQL_TYPE_STRING',                          4);
define('SQL_TYPE_DATE',                            5);
define('SQL_TYPE_TIME',                            6);
define('SQL_TYPE_DATETIME',                        7);
define('SQL_TYPE_TIMESTAMP',                       8);
define('SQL_TYPE_BLOB',                            9);
/** <b>Note:</b> The SQL_TYPE_FILE is set as a filename and read as a blob. */
define('SQL_TYPE_FILE',                           10);

/**
 * Provides static functions for the sql types.
 * @package Database
 * @static
 */
class SQLTypes {

  /**
   * Retrieves an object from a ResultSet given the specified
   * SQLType.
   * @param ResultSet $result_set the result set
   * @param string $field_name the name of the field
   * @para integer $sqltype one of SQLTypes functions
   * @static
   */
	function getFromResultSet($result_set, $field_name, $sqltype) {
		switch($sqltype) {
			case SQL_TYPE_BOOLEAN:
				$object = $result_set->getBoolean($field_name);
				break;
			case SQL_TYPE_INTEGER:
				$object = $result_set->getInteger($field_name);
				break;
			case SQL_TYPE_FLOAT:
				$object = $result_set->getFloat($field_name);
				break;
			case SQL_TYPE_STRING:
				$object = $result_set->getString($field_name);
				break;
			case SQL_TYPE_DATE:
			case SQL_TYPE_TIME:
			case SQL_TYPE_DATETIME:
			case SQL_TYPE_TIMESTAMP:
				$object = $result_set->getDate($field_name);
				break;
			case SQL_TYPE_BLOB:
			case SQL_TYPE_FILE:
				$object = $result_set->getBlob($field_name);
				break;
		}
		return $object;
	}

  /**
   * Sets an object to a prepared statement given the specified sql type.
   * @param PreparedStatement $statement the statement
   * @param string $field_name the name of the field
   * @param midex $field_value the value for the field
   * @para integer $sqltype one of SQLTypes functions
   * @static
   */
	function setToPreparedStatement(&$statement, $field_name, $field_value, $sqltype) {
		switch($sqltype) {
			case SQL_TYPE_BOOLEAN:
				$statement->setBoolean($field_name, $field_value);
				break;
			case SQL_TYPE_INTEGER:
				$statement->setInteger($field_name, $field_value);
				break;
			case SQL_TYPE_FLOAT:
				$statement->setFloat($field_name, $field_value);
				break;
			case SQL_TYPE_STRING:
				$statement->setString($field_name, $field_value);
				break;
			case SQL_TYPE_DATE:
				$statement->setDate($field_name, $field_value);
				break;
			case SQL_TYPE_TIME:
				$statement->setTime($field_name, $field_value);
				break;
			case SQL_TYPE_DATETIME:
				$statement->setDateTime($field_name, $field_value);
				break;
			case SQL_TYPE_TIMESTAMP:
				$statement->setTimestamp($field_name, $field_value);
				break;
			case SQL_TYPE_TIMESTAMP:
				$statement->setTimestamp($field_name, $field_value);
				break;
			case SQL_TYPE_BLOB:
				$statement->setBlob($field_name, $field_value);
				break;
			case SQL_TYPE_FILE:
				$statement->setFile($field_name, $field_value);
				break;
		}
	}

}

/**
 * Class that represents a Database. Concrete subclasses represent
 * specific databases (Oracle, Mysql, Postgrees, etc.).
 *
 * @package Database
 * @abstract
 */
class Database {

	/** The url to the database */
	var $url;
	/** The database in the database */
	var $database;
	/** The user of the database */
	var $user;
	/** The password of the database */
	var $password;

	/**
	 * Constructs a Database with an url, user and password.
	 * @param string $url the url to the database
	 * @param string $database the name of the database
	 * @param string $user the user
	 * @param string $password the password
	 */
	function Database($url, $database, $user, $password) {
		$this->url = $url;
		$this->database = $database;
		$this->user = $user;
		$this->password = $password;
	}

	/**
	 * Determines if this database has autonumeric sequences.
	 * @return boolean true if this database support autonumeric sequences,
	 * else false
	 * @abstract
	 */
	function hasAutonumericSequences() {}

	/**
	 * Returns the string that must be included in a query
	 * to denote the next value of a sequence.
	 * Implement only if this connection database dosen't
	 * have autonumeric sequences.
	 * @param string $sequence_name the name of the sequence, or false
	 * if this database does not support autonumeric sequences.
	 * @abstract
	 */
	function getSequenceNextVal($sequence_name) {}

	/**
	 * Opens a not persistent Connection to the database.
	 * @return Connection an instance of Connection.
	 * @abstract
	 */
	function openConnection() {}

}

/**
 * A field in a database.
 *
 * @package Database
 */
class Field {

	/**
	 * The name of this field
	 * @var string
	 */
	var $name;
	/**
	 * The type of this field (one of SQLTypes functions)
	 * @var a SQL_TYPE_* constant
	 */
	var $sqltype;
	/**
	 * A boolean that indicates whether this field is a primary key
	 * @var boolean
	 */
	var $pk;
	/**
	 * A boolean that indicates whether this field is autonumeric
	 * @var boolean
	 */
	var $autonumeric;

  /**
   * Constructs a Field.
   * @param string name the name of this field
   * @param integer sqltype one of the SQL_TYPE_* constants
   * @param boolean pk a boolean that indicates whether this field is a primary key
   * @param boolean autonumeric a boolean that indicates whether this field is autonumeric
   */
	function Field($name, $sqltype, $pk = false, $autonumeric = false) {
		$this->name = $name;
		$this->sqltype = $sqltype;
		$this->pk = $pk;
		$this->autonumeric = $autonumeric;
	}

}

/**
 * A table in a database.
 *
 * @package Database
 */
class Table {

	/**
	 * The name of this table
	 * @var string
	 */
	var $name;
	/**
	 * An array containing Field objects representing the fields of this table
	 * @var Field[]
	 */
	var $fields;

	/**
	 * Constructs a table with a name.
	 * @param string $name the name of the table
	 */
	function Table($name) {
		$this->name = $name;
		$this->fields = array();
	}

  /**
   * Adds a field to this table.
   * @param Field $field a field
   */
	function addField($field) {
		array_push($this->fields, $field);
	}

	/**
	 * Returns the autonumeric field of this table, if any.
	 */
	function getAutonumericField() {
		foreach($this->fields as $field) {
			if ($field->autonumeric) return $field;
		}
		return false;
	}

}
?>