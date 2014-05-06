<?
/**
 * Represents the results of a query.
 *
 * @package Database
 * @subpackage Interfaces
 * @abstract
 */
class ResultSet {

	/**
	 * Moves the cursor to the next row.
	 * @return boolean true if there's more results, else false.
	 */
	function next() {}

	/**
	 * Returns a boolean from a column.
	 * @param string $column_name the name of the column from which to get the boolean
	 * @return a boolean. If the column is null then false is returned
	 */
	function getBoolean($column_name) {}

	/**
	 * Returns an integer from a column.
	 * @param string $column_name the name of the column from which to get the integer
	 * @return an integer. If the column is null than 0 is returned
	 */
	function getInteger($column_name) {}

	/**
	 * Returns a float from a column.
	 * @param string $column_name the name of the column from which to get the float
	 * @return float a float. If the column is null then 0.0 is returned
	 */
	function getFloat($column_name) {}

	/**
	 * Returns a string from a column.
	 * @param string $column_name the name of the column from which to get the boolean
	 * @return string a string
	 */
	function getString($column_name) {}

	/**
	 * Returns a date from a column.
	 * @param string $column_name the name of the column from which to get the date
	 * @return Date a Date object
	 */
	function getDate($column_name) {}

	/**
	 * Returns a blob from a column.
	 * @param string $column_name the name of the column from which to get the blob
	 * @return blob the blob
	 */
	function getBlob($column_name) {}

	/**
	 * Returns the number of results in this ResultSet.
	 * @return integer the number of results in this ResultSet
	 */
	function size() {}

}

/**
 * Represents a parametrized query where each parameter is of the
 * form :name
 * To represent null values, use the value false instead.
 * @abstract
 */
class PreparedStatement {

	/**
	 * Executes a select query and returns the results in ResultSet.
	 * @return the ResultSet of the query
	 */
	function executeQuery() {}

	/**
	 * Executes a limited select query and returns the results in ResultSet.
	 * If $from is NULL than only $count results will be returned starting
	 * from 0.
	 * @param integer $from the first result to return
	 * @param integer $count the number of results to return
	 * @return the ResultSet of the query
	 */
	function executeLimitedQuery($from, $count) {}

	/**
	 * Executes an insert, update or delete query and returns the number of rows affected.
	 * @return the number of rows affected by the query
	 */
	function executeUpdate() {}

	/**
	 * Sets a parameter to be a boolean.
	 * @param string $name the name as ":name"
	 * @param boolean $value a boolean
	 */
	function setBoolean($name, $value) {}

	/**
	 * Sets a parameter to be an integer.
	 * @param string $name the name as ":name"
	 * @param integer $value an integer
	 */
	function setInteger($name, $value) {}

	/**
	 * Sets a parameter to be a float.
	 * @param string $name the name as ":name"
	 * @param float $value a float
	 */
	function setFloat($name, $value) {}

	/**
	 * Sets a parameter to be a string.
	 * @param string $name the name as ":name"
	 * @param string $value a string
	 */
	function setString($name, $value) {}

	/**
	 * Sets a parameter to be a date.
	 * @param string $name the name as ":name"
	 * @param Date $value a Date
	 */
	function setDate($name, $value) {}

	/**
	 * Sets a parameter to be a datetime.
	 * @param string $name the name as ":name"
	 * @param Date $value a Date
	 */
	function setDateTime($name, $value) {}

	/**
	 * Sets a parameter to be a time.
	 * @param string $name the name as ":name"
	 * @param Date $value a Date
	 */
	function setTime($name, $value) {}

	/**
	 * Sets a parameter to be a timestamp.
	 * @param string $name the name as ":name"
	 * @param Date $value a Date
	 */
	function setTimestamp($name, $value) {}

	/**
	 * Sets a parameter to be a blob.
	 * @param string $name the name as ":name"
	 * @param blob $value a blob
	 */
	function setBlob($name, $value) {}

	/**
	 * Sets a parameter to be a blob. The binary content is
	 * read from the file denoted by $filename.
	 * The blob can then be read by "ResultSet->getBlob()".
	 * @param string $name the name as ":name"
	 * @param string $filename a valid filename
	 */
	function setFile($name, $filename) {}

}

/**
 * Represents a Connection to a database. The connection can not
 * be closed manualy. Since the connection allways is a not persistent
 * connection, it will be closed by the end of the script execution.
 * @abstract
 */
class Connection {

	/**
	 * Executes a select query and returns the results in ResultSet.
	 * @param string $query the query to execute
	 */
	function executeQuery($query) {}

	/**
	 * Executes a limited select query and returns the results in ResultSet.
	 * If $from is NULL than only $count results will be returned starting
	 * from 0.
	 * @param string $query the query to execute
	 * @param integer $from the first result to return
	 * @param integer $count the number of results to return
	 * @return the ResultSet of the query
	 */
	function executeLimitedQuery($query, $from, $count) {}

	/**
	 * Executes an insert, update or delete query and returns the number of rows affected.
	 * @param string $query the query to execute
	 * @return the number of rows affected by the query
	 */
	function executeUpdate($query) {}

	/**
	 * Prepares a parametrized query and returns a PreparedStatement object
	 * representing it.
	 * @param string $query the query to execute
	 * @return PreparedStatement the prepared statement
	 */
	function prepareStatement($query) {}

	/**
	 * Commits all the operations made by this connection.
	 */
	function commit() {}

	/**
	 * Rollbacks all the operations made by this connection.
	 */
	function rollback() {}

	/**
	 * Returns the ID generated from the previous INSERT operation.
	 * @param string $table_name helper variable to obtain the last id
	 * @param string $column_name helper variable to obtain the last id
	 * @return integer the ID generated from the last INSERT query
	 */
	function getLastInsertID($table_name, $column_name) {}

}

/**
 * This interface defines the search criteria for the DataObjectsManager.
 *
 * @abstract
 */
class DataObjectsSearchCriteria {

	/**
	 * Adds a search to the DataObjectsManager.
	 * $param DataObjectsManager $manager the DataObjectsManager
	 * $param string $search_value the search value
	 */
	function addSearch(&$manager, $search_value) {}

}
?>