<?
PHP::requireCustom('SubtitleFormat', 'Subtitle', 'Unicode');

/**
 * 1
 * 00:00:42,200 --> 00:00:45,106
 * - ¿Puedo jugar?
 * - ¿Tienes bolas?
 * 
 * 2
 * 00:00:46,273 --> 00:00:49,150
 * - Sí.
 * - Está bien.
 */
class SubRipSubtitleFormat extends SubtitleFormat {
	
	function getName() {
		return 'SubRip';
	}
	
	function getExtension() {
		return 'srt';
	}
	
	function canRead($filename, $encoding) {
		
		// Tomemos unas cuantas líneas...
		$lines = array(); 
		$file = fopen($filename, 'r');
		
		// Descarto las líneas en blanco
		$line = fgets($file);
		while(!trim($line)) {
			$line = fgets($file);
		}
		
		array_push($lines, $line);
		
		for ($i = 0; $i < 5; $i++) {
			$line = fgets($file);
			$line = magicConversion($line, $encoding);
			array_push($lines, $line);
		}
		fclose($file);
		
		// Busquemos el 1 inicial
		$line = $lines[0];
		if (trim($line) != '1') {
			return false;
		}
		
		// Ahora veamos el tiempo
		$line = $lines[1];
		if (!$this->_parseTimes($line)) {
			return false;
		}
		
		return true;
	}
	
	function readSubtitles($filename, $encoding) {
		$this->fireReadingStarted();
		
		$status = 1;		
		$subtitle = new Subtitle();
		
		$file = fopen($filename, 'r');
		while(!feof($file) and $line = magicConversion(fgets($file), $encoding)) {
			switch($status) {
				case 1: // esperando numero
					if (trim($line)) {
						$status = 2; // el numero no importa
					}
					break;
				case 2: // esperando tiempo
					list($subtitle->from, $subtitle->to) = $this->_parseTimes($line);
					$status = 3;
					break;
				case 3: // esperando texto
					$line = trim($line);
					if ($line) {
						if ($subtitle->text) {
							$subtitle->text .= "\n";
						}
						$subtitle->text .= $line;
					} else {
						$this->fireSubtitleRead($subtitle);
						$subtitle = new Subtitle();
						$status = 1;
					}					
					break; 
			}
		}
		fclose($file);
		
		$this->fireReadingFinished();
	}
	
	function writeSubtitles($provider, $writer) {
		$num = 1;
		while($subtitle = $provider->nextSubtitle()) {
			$writer->write($num . "\r\n");
			$writer->write($subtitle->from . ' --> ' . $subtitle->to . "\r\n");
			$writer->write(trim($subtitle->text) . "\r\n\r\n");
			$num++;
		}
	}
	
	function _parseTimes($line) {
		$first = substr($line, 0, 12);
		$second = substr($line, 17, 12);
		
		if (!$first or !$second) {
			return false;
		}
		
		return array($first, $second);
	}
	
}
?>