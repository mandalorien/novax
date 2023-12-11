<?php
include(dirname(dirname(__FILE__)) .'/libraries/vendor/phpmailer/phpmailer/src/Exception.php');
include(dirname(dirname(__FILE__)) .'/libraries/vendor/phpmailer/phpmailer/src/PHPMailer.php');
include(dirname(dirname(__FILE__)) .'/libraries/vendor/phpmailer/phpmailer/src/SMTP.php');

class MailClass {
	private $Mailer;
	
	const USE_SMTP = true;
	const ENCODING = 'UTF-8';
	
	public function __construct() {
		$this->Mailer = new PHPMailer\PHPMailer\PHPMailer(true);

		if(self::USE_SMTP == true) {
			// $this->Mailer->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
			$this->Mailer->isSMTP();
			$this->Mailer->Host       = MAIL_HOST;
			$this->Mailer->SMTPAuth   = true;
			$this->Mailer->Username   = MAIL_LOGIN;
			$this->Mailer->Password   = MAIL_PASSWORD;
			$this->Mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
			$this->Mailer->Port       = MAIL_PORT;
			$this->Mailer->CharSet	  = self::ENCODING;
			$this->Mailer->SMTPDebug  = 0;
		}
		else {
			$this->Mailer->CharSet	  = self::ENCODING;
			$this->Mailer->SMTPDebug  = 0;
		}
	}
	
	public function get_GoMail() {
		return $this->Mailer;
	}
	
	public function sendMail($Subject, $IsHTML= true, $Attachements = false, $Template) {
		if($Attachements) {
			foreach($Attachements as $attachement) {
				$this->Mailer->addAttachment($attachement->File, $attachement->Name);
			}
		}
		
		$this->Mailer->isHTML($IsHTML);
		
		$this->Mailer->Subject = $Subject;
		$this->Mailer->Body    = $Template;
		
		try {
			$this->Mailer->send();
			return true;
		}
		catch(Exception $e) {
			error_log(sprintf('Message could not be sent. Mailer Error: %s', $this->Mailer->ErrorInfo));
			return false;
		}
	}
}
?>