<?
/**
 * Defines an object for sending a response to the client.
 * The methods defined in this class are all static.
 *
 * @package Common
 * @static
 */
class Response {

	/**
	 * Sends a temporary redirect response to the client using the specified redirect
	 * location URL. This method can accept relative URLs.
	 * @param string $location the location to send to
	 * @static
	 */
	function sendRedirect($location) {
		header("Location: $location");
		die();
	}

}
?>