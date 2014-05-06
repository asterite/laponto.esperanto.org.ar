<?
PHP::requireCustom('SubtitleTime');

class MyProvider extends SubtitlesProvider {
	
	var $man;
	var $showNumbers;
	var $offset;
	var $fromEsperanto;
	
	function MyProvider($man, $showNumbers, $offset, $fromEsperanto) {
		$this->man = $man;
		$this->showNumbers = $showNumbers;
		$this->offset = $offset;
		$this->fromEsperanto = $fromEsperanto;
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
			
			// Corrijo los tiempos
			$sub->from = $this->_correctTime($sub->from, $this->offset);
			$sub->to = $this->_correctTime($sub->to, $this->offset);
			
			return $sub;
		} else {
			return false;
		}
	}
	function _correctTime($time, $offset) {
		if ($offset == 0) {
			return $time;
		} else {
			$s = SubtitleTime::parseString($time);
			$s->addMillis($offset);
			return $s->toString(':', ':', ',');
		}
	}
	
}

$saveOptions = Request::getBoolean('save_options');
$addHours = Request::getParameter('add_hours');
$addMinutes = Request::getParameter('add_minutes');
$addSeconds = Request::getParameter('add_seconds');
$addMilliseconds = Request::getParameter('add_milliseconds');

$st = new SubtitleTime($addHours, $addMinutes, $addSeconds, $addMilliseconds);
$offset = $st->getTotalMillis();
if (Request::getBoolean('invert_sign')) {
	$offset = -$offset;
}

if ($saveOptions) {
	$obj = new stdClass();
	$obj->addHours = $addHours;
	$obj->addMinutes = $addMinutes;
	$obj->addSeconds = $addSeconds;
	$obj->addMilliseconds = $addMilliseconds;
	$film->download_options = serialize($obj);
	
	$man2 = new FilmManager();
	$man2->update($film);
}

$fromLanguage = $film->getFromLanguage();
$theProvider = new MyProvider($man, $showNumbers, $offset, $fromLanguage->encoding == 'ESPERANTO');
?>