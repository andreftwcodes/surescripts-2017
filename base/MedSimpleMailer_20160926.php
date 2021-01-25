<?php
require_once('phpmailer/class.phpmailer.php');


class MedSimpleMailer
{
	public $To;
	public $From;
	public $Subject;
	public $Body;
	
	public function sendEmail($To, $From, $Subject, $Body, $Transporter = 'ZIMBRA_WITH_GMAIL_AS_BACKUP')
	{
		$this->To = @explode(',', $To);
		$this->Subject = $Subject;
		$this->Body = $Body;

		switch(strtoupper($Transporter))
		{
			case 'GMAIL':
				$this->sendViaGmail();
				break;
			case 'ZIMBRA':
				$this->sendViaZimbra();
				break;
			case 'BOTH':
				$this->sendViaGmail();
				$this->sendViaZimbra();
				break;
			case 'ZIMBRA_WITH_GMAIL_AS_BACKUP':
				$this->sendViaZimbra(true);
				break;
		}
	}

	public function sendViaGmail()
	{
		$mail = new PHPMailer(true);
		$mail->IsSMTP();
		try 
		{
			$mail->SMTPAuth   = true;                  
			$mail->SMTPSecure = "ssl";                 
			$mail->CharSet = "UTF-8";
			$mail->Host       = "smtp.gmail.com";      
			$mail->Port       = 465;                   
			$mail->Username   = "meditab.surescripts@gmail.com";  
			$mail->Password   = "meditab1";            
			foreach($this->To as $To)
			{
				$mail->AddAddress($To);
			}
			$mail->SetFrom('meditab.surescripts@gmail.com', $this->From);
			$mail->Subject = $this->Subject;
			$mail->AltBody = $this->Body;
			$mail->MsgHTML($this->Body);
			$mail->Send();
		}
		catch (phpmailerException $e)
		{
			
		}
		catch (Exception $e) 
		{
			
		}
		unset($mail);
	}

	public function sendViaZimbra($blnSendViaGmailOnFail = false)
	{
		$mail = new PHPMailer(true);
		$mail->IsSMTP();
		try 
		{
			$mail->SMTPAuth   = true;                  
			$mail->SMTPSecure = "tls";                 
			$mail->CharSet = "UTF-8";
			$mail->Host       = "zimbra.meditab.com";      
			$mail->Port       = 25;                   
			$mail->Username   = "pmailer@meditab.com";  
			$mail->Password   = "medi2009";            
			foreach($this->To as $To)
			{
				$mail->AddAddress($To);
			}
			$mail->SetFrom('pmailer@meditab.com', $this->From);
			$mail->Subject = $this->Subject;
			$mail->AltBody = $this->Body;
			$mail->MsgHTML($this->Body);
			$mail->Send();
		}
		catch (phpmailerException $e)
		{
			
			if($blnSendViaGmailOnFail == true)
			{
				$this->sendViaGmail();
			}
		}
		catch (Exception $e) 
		{
			
			if($blnSendViaGmailOnFail == true)
			{
				$this->sendViaGmail();
			}
		}

		unset($mail);
	}
}

?>