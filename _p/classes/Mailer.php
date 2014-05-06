<?
PHP::requireClasses('PHPMailer/PHPMailer');

/**
 * This class extends the PHPMailer to set some variables to default
 * values based on web.ini parameters. A section under the name
 * "mailer" must be defined in the web.ini file. The attributes
 * accepted are:
 * <ul>
 *   <li><b>mailer:</b> mail, sendmila or smtp (mandatory)</li>
 *   <li><b>from:</b> the from address (optional)</li>
 *   <li><b>from_name:</b> the from name (optional)</li>
 *   <li><b>host:</b> the smtp host (only in smtp)</li>
 *   <li><b>username:</b> the smtp username (only in smtp)</li>
 *   <li><b>password:</b> the smtp password (only in smtp)</li>
 *   <li><b>sendmail:</b> the sendmail path (only in sendmail)</li>
 * </ul>
 */
class Mailer extends PHPMailer {

	function Mailer() {
		global $application;
		$section = $application->getIniSection('mailer');
		$this->Mailer = $section->getParameter('mailer');
		if ($this->Mailer == 'smtp') {
			$this->Host = $section->getParameter('host');
			$this->Username = $section->getParameter('username');
			$this->Password = $section->getParameter('password');
			$this->SMTPAuth = true;
		}
		if ($this->Mailer == 'sendmail') {
			$this->Sendmail = $section->getParameter('sendmail');
		}
		$this->From = $section->getParameter('from');
		$this->FromName = $section->getParameter('from_name');
	}

}
?>