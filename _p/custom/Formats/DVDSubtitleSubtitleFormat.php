<?
PHP::requireCustom('SubtitleFormat', 'Subtitle', 'Unicode');

/**
 * {HEAD
 * DISCID=
 * DVDTITLE=Disney's Dinosaur
 * CODEPAGE=1250
 * FORMAT=ASCII
 * LANG=English
 * TITLE=1
 * ORIGINAL=ORIGINAL
 * AUTHOR=McPoodle
 * WEB=http://www.geocities.com/mcpoodle43/subs/
 * INFO=Extended Edition
 * LICENSE=
 * }
 * {T 00:00:50:28 This is the Earth at a time when the dinosaurs roamed...}
 * {T 00:00:54:08 a lush and fertile planet.}
 */
class DVDSubtitleSubtitleFormat extends SubtitleFormat {

	function getName() {
		return 'DVDSubtitle';
	}
	
	function getExtension() {
		return 'sub';
	}

	function canRead($filename, $encoding) {

		// Tomemos una línea 
		$file = fopen($filename, 'r');
		
		// Descarto las líneas en blanco
		$line = fgets($file);
		while(!trim($line)) {
			$line = fgets($file);
		}
		fclose($file);

		// Busquemos el 1 inicial
		if (trim($line) != '{HEAD') {
			return false;
		}

		return true;
	}
	
	function readSubtitles($filename, $encoding) {
		$this->fireReadingStarted();
		
		// Primero la info extra
		$extraInfo = '';
		
		$file = fopen($filename, 'r');
		while(!feof($file) and $line = magicConversion(fgets($file), $encoding)) {
			$extraInfo .= $line;
			$pos = strpos($line, '}');
			if ($pos !== false) {
				break;
			}
		}
		
		$lastSubtitle = null;
		$theLine = '';
		while(!feof($file) and $line = magicConversion(fgets($file), $encoding)) {
			$line = trim($line);
			if (!$line) continue;
			
			$theLine .= $line;
			if (strpos($line, '}') === false) {
				$theLine .= ' ';
				continue;
			} 
			
			// {T 00:00:50:28 This is the Earth at a time when the dinosaurs roamed...}
			$time = substr($theLine, 3, 11);
			$text = substr($theLine, 15, strlen($theLine) - 16);
			
			if (!trim($text)) {
				// Si no hay texto, le pego el tiempo al anterior
				$lastSubtitle->to = $time;
				$this->fireSubtitleRead($lastSubtitle);
				$lastSubtitle = null;
			} else {
				if ($lastSubtitle) {
					$this->fireSubtitleRead($lastSubtitle);
				}
				
				$lastSubtitle = new Subtitle();
				$lastSubtitle->from = $time;
				$lastSubtitle->to = '0';
				$lastSubtitle->text = $text;
			}
			
			$theLine = '';
		}
		
		if ($lastSubtitle) {
			$this->fireSubtitleRead($lastSubtitle);
		}
		
		fclose($file);
		
		$this->fireReadingFinished($extraInfo);
	}
	
	function writeSubtitles($provider, $writer) {
		$writer->write($provider->getSubtitlesExtraInfo() . "\n");
		
		while($subtitle = $provider->nextSubtitle()) {
			$writer->write('{T ' . $subtitle->from . "\n");
			$writer->write($subtitle->text . "\n");
			$writer->write("}\n");
			if ($subtitle->to) {
				$writer->write('{T ' . $subtitle->to . "\n");
				$writer->write("\n");
				$writer->write("}\n");
			}
		}
	}
	
}
?>