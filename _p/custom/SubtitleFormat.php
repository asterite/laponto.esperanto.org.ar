<?
PHP::requireCustom('SubtitleFormatListener');

/* abstract */ class SubtitleFormat {
	
	var $listeners;
	
	function SubtitleFormat() {
		$this->listeners = array();
	}
	
	function addListener($listener) {
		array_push($this->listeners, $listener);
	}
	
	function fireSubtitleRead($subtitle) {
		for($i = 0; $i < sizeof($this->listeners); $i++) {
			$this->listeners[$i]->subtitleRead($subtitle);
		}
	}
	
	function fireReadingStarted() {
		for($i = 0; $i < sizeof($this->listeners); $i++) {
			$this->listeners[$i]->readingStarted();
		}
	}
	
	function fireReadingFinished($subtitlesExtraInfo = 0) {
		for($i = 0; $i < sizeof($this->listeners); $i++) {
			$this->listeners[$i]->readingFinished($subtitlesExtraInfo);
		}
	}
	
	/* abstract */ function getName() { }
	
	/* abstract */ function getExtension() { }
	
	/* abstract */ function canRead($filename, $unicode) { }
	
	/* abstract */ function readSubtitles($filename, $unicode) { }
	
	/* abstract */ function writeSubtitles($provider, $writer) { }
	
}

class SubtitlesProvider {
	
	function getSubtitlesExtraInfo() { }
	
	function nextSubtitle() { } 
	
}

class SubtitlesWriter {
	
	function write($x) { }
	
}
?>