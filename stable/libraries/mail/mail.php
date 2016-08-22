<?php

require_once dirname(__FILE__)."/class.smtp.php";
require_once dirname(__FILE__)."/class.pop3.php";
require_once dirname(__FILE__)."/class.phpmailer.php";

class Mail
{
	function send_mail($to, $subject, $message, $from, $fromname, $reply_to='', $reply_name='',$attachments=array(), $mail_in='to')
	{
		global $page;

		$mail = new PHPMailer();
		$mail->CharSet = "UTF-8";
		switch(strtolower(isset($page->mail_type)?$page->mail_type:""))
		{
			case "qmail":
				$mail->IsQmail();
			break;
			case "sendmail":
				$mail->IsSendmail();
			break;
			case "smtp":
				$mail->IsSMTP();
				$mail->Host       = $page->mail_host; // SMTP server
				$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
				$mail->SMTPAuth   = true;                  // enable SMTP authentication
				$mail->Port       = $page->mail_port;                    // set the SMTP port for the GMAIL server
				$mail->Username   = $page->mail_user; // SMTP account username
				$mail->Password   = $page->mail_password;        // SMTP account password
				$mail->SMTPSecure = $page->mail_ssl?'ssl':'';
			break;
			case 'queue':
				$query='insert into `x_conf_email_queue` (
					`hostname`,
					`to`,
					`from`,
					`from_name`,
					`mail_in`,
					`subject`,
					`message`,
					`reply`,
					`reply_name`,
					`attachments`,
					`add_datetime`					
				) values (';
				$query.=$page->db_conn->stringEscape(isset($page->hostname)?$page->hostname:(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost')).',';
				$query.=$page->db_conn->stringEscape(ser(array($to=>array('email'=>$to)))).',';
				$query.=$page->db_conn->stringEscape($from).',';
				$query.=$page->db_conn->stringEscape($fromname).',';
				$query.=$page->db_conn->stringEscape($mail_in).',';
				$query.=$page->db_conn->stringEscape($subject).',';
				$query.=$page->db_conn->stringEscape($message).',';
				$query.=$page->db_conn->stringEscape($reply_to).',';
				$query.=$page->db_conn->stringEscape($reply_name).',';
				$query.=$page->db_conn->stringEscape(ser($attachments)).',';
				$query.=$page->db_conn->stringEscape(nowfull()).'';
				$query.=')';
				$page->db_conn->query($query);
				return 1;
			break;
			case "mail":
			default:
				$mail->IsMail();
		}

		try {
			if ($reply_to != '')
			{
				$mail->AddReplyTo($reply_to, $reply_name);
			}
			$mail->AddAddress($to);
			$mail->SetFrom($from, $fromname);
			$mail->Subject = $subject;
			$mail->AltBody = strip_tags($message); // optional - MsgHTML will create an alternate automatically
			$mail->MsgHTML($message);
			// add attachments
			if(is_array($attachments) && count($attachments)>0)
				foreach($attachments as $k=>$v)
				{
					$mail->AddAttachment($v,$k);
				}
			$mail->Send();
			unset($mail);
			return true;
		} catch (phpmailerException $e) {
			unset($mail);
			return false;
		} catch (Exception $e) {
			unset($mail);
			return false;
		}
    }

	function send_mail_multiple($to, $subject, $message, $from, $fromname, $reply_to='', $reply_name='',$attachments=array(),$mail_in='to')
	{
		global $page;

		$mail = new PHPMailer();
		$mail->CharSet = "UTF-8";
		switch(strtolower(isset($page->mail_type)?$page->mail_type:""))
		{
			case "qmail":
				$mail->IsQmail();
			break;
			case "sendmail":
				$mail->IsSendmail();
			break;
			case "smtp":
				$mail->IsSMTP();
				$mail->SMTPAuth=true;
			break;
			case 'queue':
				$query='insert into `x_conf_email_queue` (
					`hostname`,
					`to`,
					`from`,
					`from_name`,
					`mail_in`,
					`subject`,
					`message`,
					`reply`,
					`reply_name`,
					`attachments`,
					`add_datetime`					
				) values (';
				$query.=$page->db_conn->stringEscape(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost').',';
				$query.=$page->db_conn->stringEscape(ser($to)).',';
				$query.=$page->db_conn->stringEscape($from).',';
				$query.=$page->db_conn->stringEscape($fromname).',';
				$query.=$page->db_conn->stringEscape($mail_in).',';
				$query.=$page->db_conn->stringEscape($subject).',';
				$query.=$page->db_conn->stringEscape($message).',';
				$query.=$page->db_conn->stringEscape($reply_to).',';
				$query.=$page->db_conn->stringEscape($reply_name).',';
				$query.=$page->db_conn->stringEscape(ser($attachments)).',';
				$query.=$page->db_conn->stringEscape(nowfull()).'';
				$query.=')';
				$page->db_conn->query($query);
				return 1;
			break;
			case "mail":
			default:
				$mail->IsMail();
		}
		if ($mail_in == 'bcc'){
			foreach($to as $value){
				$mail->AddBCC($value['email'],isset_or($value['name']));
			}
		}else{
			foreach($to as $value){
				$mail->AddAddress($value['email'],isset_or($value['name']));
			}
		}
		$mail->IsHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->From  = $from;
		$mail->Sender = $from;
		$mail->FromName = $fromname;

		$mail->Host=$page->mail_host;
		$mail->Username=$page->mail_user;
		$mail->Password=$page->mail_password;

		// add attachments
		if(count($attachments)>0)
			foreach($attachments as $k=>$v)
			{
				$mail->AddAttachment($v,$k);
			}

		if ($reply_to != '')
		{
			$mail->AddReplyTo($reply_to, $reply_name);
		}
		$message = str_replace("<br>", "\n", $message);
		$mail->AltBody  =  $message;
		$send = $mail->Send();

		unset($mail);
		return $send;

	}

	public function __destruct() {

	}

}
?>