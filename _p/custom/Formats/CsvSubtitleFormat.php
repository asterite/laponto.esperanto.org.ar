<?
PHP::requireCustom('SubtitleFormat', 'Subtitle', 'Unicode');

/**
 * Reads a csv that contains subtitles in the following format:
 *
 * from, to, original, translated
 *
 * in each line.
 */
class CsvSubtitleFormat extends SubtitleFormat {

	function getName() {
		return 'Csv';
	}
	
	function getExtension() {
		return 'csv';
	}

	function canRead($filename, $encoding) {
		return true;
	}
	
	function readSubtitles($filename, $encoding) {
		$this->fireReadingStarted();
		
		$file = fopen($filename, 'r');
		
		// Read each of the lines
		while(!feof($file) and $line = magicConversion(fgets($file), $encoding)) {
			$line = trim($line);
			
			// Skip empty lines
			if (!$line) continue;
			
			$delimiters = array();
			$inQuote = false;
			
			$len = strlen($line);
			for($i = 0; $i < $len; $i++) {
				$c = $line[$i];
				if ($c == '"') {
					if ($inQuote) {
						if ($i < $len - 1) {
							$next = $line[$i + 1];
							if ($next == '"') {
  							$i++;
								continue;
							}
						}
						
						array_push($delimiters, $i);
						$inQuote = false;
					} else {
						array_push($delimiters, $i + 1);
						$inQuote = true;
					}
				}
			}
			
			$numDelimiters = sizeof($delimiters);
			if ($numDelimiters != 6 and $numDelimiters != 8)
				continue;
			
			$sub = new Subtitle();
			$sub->from = substr($line, $delimiters[0], $delimiters[1] - $delimiters[0]);
			$sub->to = substr($line, $delimiters[2], $delimiters[3] - $delimiters[2]);
			$sub->text = substr($line, $delimiters[4], $delimiters[5] - $delimiters[4]);
			$sub->text = str_replace('|', "\n", $sub->text);
			$sub->text = str_replace('""', '"', $sub->text);
			if ($numDelimiters == 8) {
				$sub->translatedText = substr($line, $delimiters[6], $delimiters[7] - $delimiters[6]);
				$sub->translatedText = str_replace('|', "\n", $sub->translatedText);
				$sub->translatedText = str_replace('""', '"', $sub->translatedText);
			}
			$this->fireSubtitleRead($sub);
		}
		
		fclose($file);
		
		$this->fireReadingFinished();
	}
	
	function writeSubtitles($provider, $writer) {
		while($subtitle = $provider->nextSubtitle()) {
			$writer->write('"' . $subtitle->from . '", ');
			$writer->write('"' . $subtitle->to . '", ');
			$writer->write('"' . str_replace('"', '""', str_replace("\n", '|', $subtitle->text)) . '", ');
			$writer->write('"' . str_replace('"', '""', str_replace("\n", '|', $subtitle->translatedText)) . '"' . "\n");
		}
	}
	
}
?>