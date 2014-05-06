<?
/**
 * A stopwatch for debug purpouses.
 * Usage:
 * <code>
 *   $s = new StopWatch();
 *   $s->start();
 *   // Do something...
 *   $s->stop();
 *   print 'Elapsed seconds:' . $s->getElapsedSeconds();
 * </code>
 *
 * Can be used multiple times.
 *
 * @package Debug
 */
class StopWatch {

	/** @access private */
	var $time;

	/**
	 * Constructs a StopWatch.
	 */
	function StopWatch() {}

	/**
	 * Starts this StopWacth.
	 */
	function start() {
		$this->time = StopWatch::_micro();
	}

	/**
	 * Stops this StopWacth.
	 */
	function stop() {
		$this->time = StopWatch::_micro() - $this->time;
	}

	/**
	 * Returns the elapsed seconds between start() and stop().
	 * @return float the elapsed seconds
	 */
	function getElapsedSeconds() {
		return $this->time;
	}

	/** @access private */
	function _micro() {
		list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
	}

}
?>