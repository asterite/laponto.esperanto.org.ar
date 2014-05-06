<?
class MyProvider extends SubtitlesProvider {
	
	var $man;
	var $showNumbers;
	var $fromEsperanto;
	var $subtitlesExtraInfo;
	
	function MyProvider($man, $showNumbers, $subtitlesExtraInfo, $fromEsperanto) {
		$this->man = $man;
		$this->showNumbers = $showNumbers;
		$this->subtitlesExtraInfo = $subtitlesExtraInfo;
	}
	
	function nextSubtitle() {
		if ($subtitle = $this->man->next()) {
			$sub = new Subtitle();
			$sub->from = $subtitle->from_time;
			$sub->to = $subtitle->to_time;
			if ($this->showNumbers) {
				$sub->text .= $subtitle->number . '. ';
				$sub->translatedText .= $subtitle->number . '. ';
			}
			$sub->text .= $subtitle->original_text;
			$sub->translatedText .= $subtitle->translated_text;
			
			if ($this->fromEsperanto) {
				$sub->text = sombrerosAx($sub->text);
			} else if ($this->toEsperanto) {
				$sub->translatedText = sombrerosAx($sub->translatedText);
			}
			return $sub;
		} else {
			return false;
		}
	}
	
	function getSubtitlesExtraInfo() {
		return $this->subtitlesExtraInfo;
	}
	
}

$fromLanguage = $film->getFromLanguage();
$theProvider = new MyProvider($man, $showNumbers, 
	$film->subtitles_extra_info, $fromLanguage->encoding == 'ESPERANTO');
?>