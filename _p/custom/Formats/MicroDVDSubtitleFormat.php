<?
PHP::requireCustom('SubtitleFormat', 'Subtitle', 'Unicode');

/**
 * {1997}{2103}În secolul al XII - lea, în preajma celei de-a Treia|Cruciade menitã a elibera Pãmântul Sfânt...
 * {2107}{2177}...Un cavaler saxon,|numit Wilfred de Ivanhoe...
 */
class MicroDVDSubtitleFormat extends SubtitleFormat {
	
	function getName() {
		return 'MicroDVD';
	}
	
	function getExtension() {
		return 'sub';
	}
	
	function canRead($filename, $encoding) {
		// Tomemos una línea... 
		$file = fopen($filename, 'r');
		
		// Descarto las líneas en blanco
		$line = fgets($file);
		while(!trim($line)) {
			$line = fgets($file);
		}
		
		$line = trim(magicConversion($line, $encoding));
		fclose($file);
		
		// Empieza con {
		if ($line{0} != '{') return false;
		
		// Tiene otro }
		$pos = strpos($line, '}');
		if ($pos === false) {
			return false;
		}
		
		// Entre medio hay un número {123}
		
		$num = substr($line, 1, $pos - 1);
		if (((int) $num) == 0) {
			return false;
		}		
		
		return true;
	}
	
	function readSubtitles($filename, $encoding) {
		$this->fireReadingStarted();
		
		$file = fopen($filename, 'r');
		while(!feof($file) and $line = magicConversion(fgets($file), $encoding)) {
			$line = trim($line);
			
			if (!$line) continue;
			
			$subtitle = new Subtitle();
			$firstClose = strpos($line, '}');
			$subtitle->from = substr($line, 1, $firstClose - 1);
			$secondClose = strpos($line, '}', $firstClose + 1);
			$subtitle->to = substr($line, $firstClose + 2, $secondClose - $firstClose - 2);
			$subtitle->text = substr($line, $secondClose + 1);
			$subtitle->text = str_replace('|', "\n", $subtitle->text);
			$this->fireSubtitleRead($subtitle);
		}
		fclose($file);
		
		$this->fireReadingFinished();
	}
	
	function writeSubtitles($provider, $writer) {
		while($subtitle = $provider->nextSubtitle()) {
			$writer->write('{' . $subtitle->from . '}{' . $subtitle->to . "}");
			$writer->write(str_replace("\n", "|", trim($subtitle->text)) . "\r\n");
		}
	}
	
}
?>