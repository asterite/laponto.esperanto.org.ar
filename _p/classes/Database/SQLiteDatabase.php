<?
/**
 * Implementation of the classes and interfaces defined in database.inc
 * for the SQLiteDatabase.
 * Public class provided: SQLiteDatabase
 *
 * @package Database
 * @subpackage Implementations
 */
PHP::requireClasses('Database', 'Date');

/**#@+
 * @access private
 */
class SQLiteResultSet {

	var $result;
	var $row;

	function next() {
		$this->row = sqlite_fetch_array($this->result);
		return $this->row;
	}

	function getBoolean($column_name) {
		return $this->row[$column_name] == 0 ? false : true;
	}

	function getInteger($column_name) {
		$data = $this->row[$column_name];
		if (is_null($data)) return null;
		return (int) $data;
	}

	function getFloat($column_name) {
		$data = $this->row[$column_name];
		if (is_null($data)) return null;
		return (float) $data;
	}

	function getString($column_name) {
		$data = $this->row[$column_name];
		if (is_null($data)) return null;
		return (string) $data;
	}

	function getDate($column_name) {
		$date = $this->row[$column_name];
		if (is_null($date)) return null;
		return new Date($date);
	}

	function getBlob($column_name) {
		return $this->row[$column_name];
	}

	function size() {
		return sqlite_num_rows($this->result);
	}

}

class SQLitePreparedStatement /* implements PreparedStatement */ {

	var $conn;
	var $query;

	function executeQuery() {
		return $this->conn->executeQuery($this->query);
	}

	function executeLimitedQuery($from, $count) {
		return $this->conn->executeLimitedQuery($this->query, $from, $count);
	}

	function executeUpdate() {
		return $this->conn->executeUpdate($this->query);
	}

	function setBoolean($name, $value) {
		$this->query = __special_replace(":$name", (string)$value ? '1' : '0', $this->query);
	}

	function setInteger($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = (int)$value;
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setFloat($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = (float)$value;
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setString($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = '\'' .  sqlite_escape_string($value) . '\'';
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setDate($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = '\'' .  sprintf('%04u-%02u-%02u', $value->year, $value->month, $value->day) . '\'';
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setDateTime($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = '\'' .  sprintf('%04u-%02u-%02u %02u:%02u:%02u', $value->year, $value->month, $value->day, $value->hour, $value->minute, $value->second) . '\'';
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setTime($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = '\'' .  sprintf('%02u:%02u:%02u', $value->hour, $value->minute, $value->second) . '\'';
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setTimestamp($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = '\'' .  sprintf('%04u%02u%02u%02u%02u%02u', $value->year, $value->month, $value->day, $value->hour, $value->minute, $value->second) . '\'';
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setBlob($name, $value) {
		if ($value === false) {
			$value = 'NULL';
		} else {
			$value = '\'' .  sqlite_escape_string($value) . '\'';
		}
		$this->query = __special_replace(":$name", $value, $this->query);
	}

	function setFile($name, $filename) {
		$handle = fopen($filename, 'rb');
		while ($chunk = fread($handle, 4096)) {
			$contents .= $chunk;
		}
		fclose($handle);
		$this->setBlob($name, $contents);
	}

}

class SQLiteConnection /* implements Connection */ {

	var $conn;

	function executeQuery($query) {
		//Print $query . '<br>';
		$result_set = new SQLiteResultSet();
		$result_set->result = sqlite_query($this->conn, $query);
		if (!$result_set->result) {
			$this->_error($query);
		}
		return $result_set;
	}

	function executeLimitedQuery($query, $from, $count) {
		if (is_null($from)) {
			return $this->executeQuery($query." LIMIT $count");
		} else {
			return $this->executeQuery($query." LIMIT $count, $from");
		}
	}

	function executeUpdate($query) {
		$this->executeQuery($query);
		return sqlite_changes($this->conn);
	}

	function prepareStatement($query) {
		$statement = new SQLitePreparedStatement();
		$statement->conn = $this;
		$statement->query = $query;
		return $statement;
	}

	function commit() {}

	function rollback() {}

	function getLastInsertID($table_name, $column_name) {
		return sqlite_last_insert_rowid($this->conn);
	}

	function _error($query) {
		print '<font color=red>Error in query:</font><br>';
		print $query;
		print '<br>';
		$error_code = sqlite_last_error($this->conn);
		print  $error_code . ': ' . sqlite_error_string($error_code);
		die();
	}

}
/**#@-*/

class SQLiteDatabase extends Database {
	
	var $connection;

	function hasAutonumericSequences() {
		return true;
	}

	function getSequenceNextVal($sequence_name) {
		return false;
	}

	function openConnection() {
		if (!$this->connection) {
			$this->connection = new SQLiteConnection();
			$this->connection->conn = sqlite_open(PHP::realPath('_database/'.$this->database));
		}
		return $this->connection;
	}

}

/** @access private */
function __special_replace($old, $new, $string) {
	$old_len = strlen($old);
	$offset = 0;
	$pos = strpos($string, $old);
	while($pos !== false) {
		$ch = $string{$pos + $old_len};
		if ($ch == '' or $ch == ' '
				or $ch == ')' or $ch == '('
				or $ch == '=' or $ch == '!'
				or $ch == '+' or $ch == '-' or $ch == '*' or $ch == '/' or $ch == '%'
				or $ch == '<' or $ch == '>' or $ch == ',' or $ch == ';') {
			$left = substr($string, 0, $pos);
	    $right = substr($string, $pos + $old_len);
	    $string = $left . $new . $right;
	  }
	  $offset = $pos + $old_len;
	  if ($offset < strlen($string)) {
	  	$pos = strpos($string, $old, $offset);
	  } else {
	  	break;
	  }
  }
  return $string;
}
?>