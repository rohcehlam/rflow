<?php

require_once("../inc/PHPMailer_v5.1/class.phpmailer.php");
		
class tEmail{
	
	private $mail;
	
	function __construct($from = ""){
		$this->mail = new PHPMailer();
		$this->mail->IsSMTP();
		$this->mail->SMTPSecure = 'ssl';
		$this->mail->SMTPAuth = true;
		$this->mail->Host = "smtp.gmail.com";
		$this->mail->Port = "465";
		$this->mail->Username = "mailer@markssystems.com";
		$this->mail->Password = "M@ilerMF";
		$this->mail->From = "mailer@markssystems.com";
		$this->mail->FromName = $from;
		$this->mail->WordWrap = 75;
		$this->mail->IsHTML(true);
	}
	
	function AddAddress($email, $text = ''){
		$this->mail->AddAddress($email, $text);
	}
	
	function set_subject($text){
		$this->mail->Subject = $text;
	}
	
	function set_body($text){
		$this->mail->Body = $text;
	}
	
	function set_alt_body($text){
		$this->mail->AltBody = $text;
	}
	
	function send(){
		return $this->mail->Send();
	}
	
	function get_error(){
		return $this->mail->ErrorInfo;
	}
	
}			
