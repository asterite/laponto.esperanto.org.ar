<?
PHP::requireCustom('SubtitleFormat', 'Subtitle', 'Unicode');

/**
 * [INFORMATION]
 * [AUTHOR]
 * [SOURCE]
 * [PRG]
 * [FILEPATH]
 * [DELAY]
 * [CD TRACK]
 * [COMMENT]
 * [END INFORMATION]
 * 
 * [SUBTITLE]
 * [COLF]&HFFFFFF,[STYLE]no,[SIZE]18,[FONT]Arial
 * 
 * 00:00:20.20,00:00:22.48
 * Just a subtitle
 * 
 * 00:03:00.20,00:03:04.60
 * First line of a subtitle[br]Second line of a subtitle
 * 
 * 00:03:04.60,00:03:05.80
 * another sub
 */
class SubViewerSubtitleFormat extends SubtitleFormat {

	function getName() {
		return 'SubViewer';
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
		if (trim($line) != '[INFORMATION]') {
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
			if (!trim($line)) continue;
			
			$pos = strpos($line, '[');
			if ($pos === false) {
				// Terminè de leer lo extra, ahora empieza lo bueno
				break;
			} else {
				$extraInfo .= $line;
			}
		}

		$line = trim($line);

		$subtitle = new Subtitle();
		list($subtitle->from, $subtitle->to) = explode(',', $line);
		$status = 2;		
				
		while(!feof($file) and $line = magicConversion(fgets($file), $encoding)) {
			$line = trim($line);
			if (!$line) continue;
			
			switch($status) {
				case 1: // Esperando el tiempo
					list($subtitle->from, $subtitle->to) = explode(',', $line);
					$status = 2;
					break;
				case 2: // Esperando el texto
					$subtitle->text = $line;
					$subtitle->text = str_replace('[br]', "\n", $subtitle->text);
					$this->fireSubtitleRead($subtitle);
					
					$subtitle = new Subtitle();
					$status = 1;
					break;
			} 
		}
		
		fclose($file);
		
		$this->fireReadingFinished($extraInfo);
	}
	
	function writeSubtitles($provider, $writer) {
		$writer->write($provider->getSubtitlesExtraInfo() . "\n");
		
		while($subtitle = $provider->nextSubtitle()) {
			$writer->write($subtitle->from . ',' . $subtitle->to . "\n");
			$writer->write(str_replace("\n", '[br]', $subtitle->text) . "\n\n");
		}
	}
	
}
?>