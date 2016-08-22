<?php
	/**
	 * Email Manager Class
	 */
	/**
	 * E-mails Send Manager Class
	 * @package WebLauncher\Managers
	 * @example $this->system->mail
	 */
	class EmailManager{
		/**
		 * @var /PHPMailer $mailer PHPMailer object
		 */
		public $mailer=null;
		
		/**
		 * Constructor
		 */
		function __construct(){
			
		}
		
		/**
		 * Compose message
		 * @param array|string $to
		 * @param string $subject
		 * @param string $message
		 * @param string $from 
		 * @param string $fromname
		 * @param string $reply_to
		 * @param string $reply_name
		 * @param array $attachments
		 * @param string $mail_in
		 * @param string $sender
		 * @param array $others
		 */
		function compose($to, $subject, $message, $from, $fromname, $reply_to='', $reply_name='',$attachments=array(), $mail_in='to', $sender='', $others=array()){
			global $page;
			$to=$this->clean_receivers($to);
			if(strtolower(isset($page->mail_type)?$page->mail_type:'')=='queue')
				return $this->queue($to, $subject, $message, $from, $fromname, $reply_to, $reply_name,$attachments, $mail_in, $sender, $others);

			$this -> mailer = new PHPMailer();
			$this -> mailer -> CharSet = 'UTF-8';
			switch(strtolower(isset($page->mail_type)?$page->mail_type:''))
			{
				case "qmail":
					$this -> mailer ->IsQmail();
				break;
				case "sendmail":
					$this -> mailer ->IsSendmail();
				break;
				case "smtp":
					$this -> mailer ->IsSMTP();
					$this -> mailer ->SMTPAuth=true;
				break;
				case "mail":
				default:
					$this -> mailer ->IsMail();
			}
			
			if(is_array($to)){
				foreach($to as $value){
					if ($mail_in == 'bcc')
						$this -> mailer ->AddBCC(is_array($value)?$value['email']:$value,is_array($value)?isset_or($value['name']):'');
					else
						$this -> mailer ->AddAddress(is_array($value)?$value['email']:$value,is_array($value)?isset_or($value['name']):'');
				}
			}else{
				if ($mail_in == 'bcc')
					$this -> mailer ->AddBCC($to,isset_or($to));
				else
					$this -> mailer ->AddAddress($to,isset_or($to));
			}
			if(isset($others['headers']) && is_array($others['headers']))
				foreach($others['headers'] as $k=>$v)
					$this -> mailer -> addCustomHeader($k.': '.$v);
			
			$this -> mailer ->IsHTML(true);
			$this -> mailer ->Subject = $subject;
			$this -> mailer ->Body = $message;
			$this -> mailer ->From  = $from;
			$this -> mailer ->Sender = $from;
			if($sender){
				$this -> mailer ->Sender = $sender;
				$this -> mailer -> addCustomHeader('Errors-To: '.$sender);
			}
			$this -> mailer ->FromName = $fromname;
	
            if(isset($page)){
    			$this -> mailer ->Host=$page->mail_host;
    			$this -> mailer ->Username=$page->mail_user;
    			$this -> mailer ->Password=$page->mail_password;
            }
			// add attachments
			if(count($attachments)>0)
				foreach($attachments as $k=>$v)
					$this -> mailer ->AddAttachment($v,$k);				
	
			if ($reply_to != '')			
				$this -> mailer ->AddReplyTo($reply_to, $reply_name);
			
			$message=preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $message);
			$message = str_replace("<br>", "\n", $message);			
			$this -> mailer ->AltBody  =  strip_tags($message);
			
			return $this;
		}
		
		/**
		 * Clean recievers 
		 * @param array $to
		 */
		function clean_receivers($to){			
			if(is_array($to)){
				$arr=array();
				foreach($to as $value)
					if(!isset($arr[is_array($value)?$value['email']:$value]))
						$arr[is_array($value)?$value['email']:$value]=$value;
				return $arr;
			}
			return $to;
		}
		
		/**
		 * Queue mail message
		 * @param array|string $to
		 * @param string $subject
		 * @param string $message
		 * @param string $from 
		 * @param string $fromname
		 * @param string $reply_to
		 * @param string $reply_name
		 * @param array $attachments
		 * @param string $mail_in
		 * @param string $sender
		 * @param array $others
		 */
		function queue($to, $subject, $message, $from, $fromname, $reply_to='', $reply_name='',$attachments=array(), $mail_in='to', $sender='', $others=array()){
			global $page;	
			if(!is_array($to))
				$to=ser(array($to=>array('email'=>$to)));
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
						`sender`,
						`others`,
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
					$query.=$page->db_conn->stringEscape($sender).',';
					$query.=$page->db_conn->stringEscape(ser($others)).',';
					$query.=$page->db_conn->stringEscape(ser($attachments)).',';
					$query.=$page->db_conn->stringEscape(nowfull()).'';
					$query.=')';
			$page->db_conn->query($query);
			return $this;
		}
		
		/**
		 * Send mail message
		 */
		function send(){
			$send=true;
			if(!is_null($this->mailer))
			{
				$send = $this -> mailer ->Send();	
			}
			return $send;
		}
		
		/**
		 * Process object from queue
		 * @param array $obj
		 * @param array $hosts
		 */
		function process_queue_obj($obj,$hosts){
			$this->mailer=null;
			$this->compose(unser($obj['to']), $obj['subject'], $obj['message'], $obj['from'], $obj['from_name'],$obj['reply'],$obj['reply_name'],unser($obj['attachments']),$obj['mail_in'],$obj['sender'],unser($obj['others']));			
			switch(strtolower(isset($hosts[$obj['hostname']]['mail_type'])?$hosts[$obj['hostname']]['mail_type']:""))
			{
				case "qmail":
					$this->mailer->IsQmail();
				break;
				case "sendmail":
					$this->mailer->IsSendmail();
				break;
				case "smtp":
					$this->mailer->IsSMTP();
					$this->mailer->SMTPAuth=true;
					$this->mailer->Host=isset_or($hosts[$obj['hostname']]['mail_host']);
					$this->mailer->Username=isset_or($hosts[$obj['hostname']]['mail_user']);
					$this->mailer->Password=isset_or($hosts[$obj['hostname']]['mail_password']);
                    if(isset_or($hosts[$obj['hostname']]['mail_port'])){
                        $this->mailer->Port=isset_or($hosts[$obj['hostname']]['mail_port']);
                    }
				break;			
				case "mail":
				default:
					$this->mailer->IsMail();
			}		
			$this -> mailer -> addCustomHeader('X-MessageID: '.$obj['id']); 
			return $this->send();
		}
	}
?>