<?
PHP::requireCustom('Formats/SubRipSubtitleFormat', 
	'Formats/MicroDVDSubtitleFormat',
	'Formats/DVDSubtitleSubtitleFormat',
	'Formats/SubViewerSubtitleFormat',
	'Formats/CsvSubtitleFormat'
	);

global $__subtitle_format_factory;
$__subtitle_format_factory = new SubtitleFormatFactory();
$__subtitle_format_factory->register(new SubRipSubtitleFormat());
$__subtitle_format_factory->register(new MicroDVDSubtitleFormat());
$__subtitle_format_factory->register(new DVDSubtitleSubtitleFormat());
$__subtitle_format_factory->register(new SubViewerSubtitleFormat());
$__subtitle_format_factory->register(new CsvSubtitleFormat());

/* abstract */ class SubtitleFormatFactory {
	
	var $formats;
	
	function SubtitleFormatFactory() {
		$this->formats = array();
	}
	
	function /* static */ getInstance() {
		global $__subtitle_format_factory;
		return $__subtitle_format_factory;
	}
	
	function register($format) {
		array_push($this->formats, $format);		
	}
	
	function getSubtitleFormat($name) {
		for($i = 0; $i < sizeof($this->formats); $i++) {
			if ($this->formats[$i]->getName() == $name) {
				return $this->formats[$i];
			}
		}
		return false;
	}
	
	function getSuitableSubtitleFormat($filename, $unicode) {
		for($i = 0; $i < sizeof($this->formats); $i++) {
			if ($this->formats[$i]->canRead($filename, $unicode)) {
				return $this->formats[$i];
			}
		}
		return false;
	}
	
}
?>