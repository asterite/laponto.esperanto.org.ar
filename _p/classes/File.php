<?
$__parsed_inis = array();

/**
 * Utility class for file handling routines.
 * @package Common
 * @static
 */
class FileUtils {

	/**
	 * Ensures that a path ends with the "/"
	 * character.
	 * @param string $path the path to ensure
	 * @static
	 */
	function ensureDir(&$dir) {
		if ($dir{strlen($dir)-1} != '/') {
			$dir .= '/';
		}
	}

	/**
	 * Returns a valid filename for the specified name.
	 * Spaces are turned '_', the accents turned normal letters
	 * and the rest of the charactes are removed.
	 * @return string a valid filename
	 * @static
	 */
	function validFilename($name) {
		$name = str_replace('á', 'a', $name);
		$name = str_replace('é', 'e', $name);
		$name = str_replace('í', 'i', $name);
		$name = str_replace('ó', 'o', $name);
		$name = str_replace('ú', 'u', $name);
		$name = str_replace('ñ', 'ni', $name);
		return ereg_replace("[^a-zA-Z0-9_]", "", str_replace(" ", "_", $name));
	}

	/**
	 * Returns the extension of a filename in lower case.
	 * @return string the extension
	 * @static
	 */
	function extension($filename) {
		return strtolower(substr(strrchr($filename, '.'), 1));
	}

	/**
	 * Parses an ini file.
	 * Better function than the php parse_ini_file() since
	 * it has a workaround for "strange" values.
	 *
	 * <b>¡No comments allowed in the ini file!</b>
	 * @param string $filename the filename to parse
	 * @param boolean $process_sections wether to process sections or no
	 * @static
	 */
	function parseIniFile($filename, $process_sections = false) {
		global $__parsed_inis;
		if (!$__parsed_inis[$filename . '*' . $process_sections]) {
			$ini_array = array();
			$sec_name = '';
			$lines = file($filename);
			foreach($lines as $line) {
				$line = trim($line);

				if($line[0] == '[' && $line[strlen($line) - 1] == ']') {
					$sec_name = substr($line, 1, strlen($line) - 2);
				} else {
					$pos = strpos($line, '=');
					$property = trim(substr($line, 0, $pos));
					$value = trim(substr($line, $pos + 1));

					if($process_sections) {
						$ini_array[$sec_name][$property] = $value;
					}
					else {
						$ini_array[$property] = $value;
					}
				}
			}
			$__parsed_inis[$filename . '*' . $process_sections] = $ini_array;
		}
		return $__parsed_inis[$filename . '*' . $process_sections];
	}

	/**
	 * Writes an ini file.
	 * The structure of $array is the same as the array returned by parseIniFile
	 * and it depends on the $process_sections parameter.
	 * This method does not allow writing comments in the ini file.
	 * @param string $filename the filename to parse
	 * @param array $array the parameters
	 * @param boolean $process_sections wether to process sections or no
	 * @static
	 */
	function writeIniFile($filename, $array, $process_sections = false) {
		$file = fopen($filename, 'w');
		if ($process_sections) {
			foreach($array as $section => $contents) {
				fwrite($file, '[' . $section . "]\n");
				foreach($contents as $key => $value) {
					fwrite($file, $key . ' = ' . $value . "\n");
				}
			}
		} else {
			foreach($array as $key => $value) {
				fwrite($file, $key . ' = ' . $value . "\n");
			}
		}
		fclose($file);
	}

}
?>