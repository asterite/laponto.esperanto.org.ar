<?
class MyProvider extends SubtitlesProvider {
	
	var $man;
	var $showNumbers;
	var $fromEsperanto;
	
	function MyProvider($man, $showNumbers, $fromEsperanto) {
		$this->man = $man;
		$this->showNumbers = $showNumbers;
	}
	
	function nextSubtitle() {
		if ($subtitle = $this->man->next()) {
			$sub = new Subtitle();
			$sub->from = $subtitle->from_time;
			$sub->to = $subtitle->to_time;
			if ($this->showNumbers) {
				$sub->text .= $subtitle->number . '. ';
			}
			if ($subtitle->translated_text) {
				$sub->text .= $subtitle->translated_text;
			} else {
				$sub->text .= $subtitle->original_text;
				if ($this->fromEsperanto) {
					$sub->text = sombrerosAx($sub->text);
				}
			}
			return $sub;
		} else {
			return false;
		}
	}
	
}

$fromLanguage = $film->getFromLanguage();
$theProvider = new MyProvider($man, $showNumbers, $fromLanguage->encoding == 'ESPERANTO');
?>