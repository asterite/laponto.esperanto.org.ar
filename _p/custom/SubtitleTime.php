<?
PHP::requireCustom('Utils');

class SubtitleTime {
	
	var $hours;
	var $minutes;
	var $seconds;
	var $millis;
	
	function SubtitleTime($hours, $minutes, $seconds, $millis) {
		$this->hours = $hours;
		$this->minutes = $minutes;
		$this->seconds = $seconds;
		$this->millis = $millis;
	}
	
	function parseString($str) {
		return new SubtitleTime(
			substr($str, 0, 2),
			substr($str, 3, 2),
			substr($str, 6, 2),
			substr($str, 9, 3)
		);	
	}
	
	function fromMillis($m) {
		$hours = (int) ($m / (1000 * 60 * 60));
		$m -= $hours * 1000 * 60 * 60;
		$minutes = (int) ($m / (1000 * 60));
		$m -= $minutes * 1000 * 60; 
		$seconds = (int) ($m / 1000);
		$m -= $seconds * 1000;
		$s =  new SubtitleTime($hours, $minutes, $seconds, $m);
		return $s;
	}
	
	function getTotalMillis() {
		return $this->millis + $this->seconds * 1000 + $this->minutes * 1000 * 60 + $this->hours * 1000 * 60 * 60;
	}
	
	function addMillis($m) {
		$mi = $this->getTotalMillis();
		$mi += $m;
		$s =  SubtitleTime::fromMillis($mi);
		$this->hours = $s->hours;
		$this->minutes = $s->minutes;
		$this->seconds = $s->seconds;
		$this->millis = $s->millis;
	}
	
	function toString($sep1, $sep2, $sep3) {
		return twoDigits($this->hours) . $sep1 . 
			   twoDigits($this->minutes) . $sep2 . 
			   twoDigits($this->seconds) . $sep3 . 
			   threeDigits($this->millis); 
	}
	
}
?>