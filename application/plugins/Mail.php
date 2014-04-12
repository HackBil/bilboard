<?php

class Plugin_Mail
{
	protected $_sender_name = null;
	protected $_sender_mail = null;
	protected $_recipient_name = null;	// actuellement inutilisé
	protected $_recipient_mail = null;
	protected $_subject = null;
	protected $_txt_msg = null;			// partie texte du message
	protected $_html_msg = null;		// partie HTML du message
	protected $_included_files = null;	// pièces jointes
	
	protected $_header;
	protected $_msg;					// message global
	protected $_boundary;				// séparateur principal dans le message
	protected $_sub_boundary;			// séparateur secondaire dans le message
	protected $_encode = "ISO-8859-1";
	protected $_endl = "\r\n";
	
	protected $_converter = null;

	public function __construct()
	{
		$this->_boundary     = rand(0,9)."-".rand(10000000000,9999999999)."-".rand(10000000000,9999999999)."=:".rand(10000,99999);
		$this->_sub_boundary = rand(0,9)."-".rand(10000000000,9999999999)."-".rand(10000000000,9999999999)."=:".rand(10000,99999);
		$this->_converter = new Plugin_Converter();
	}
    public function setSubject($subject)
	{
		$this->_subject = $subject;
	}
    public function setSender($sender_mail, $sender_name)
    {
		$this->_sender_name = $sender_name;
		$this->_sender_mail = $sender_mail;
    }
	public function setRecipient($recipient_mail, $recipient_name)
	{
		$this->_recipient_name = $recipient_name;
		$this->_recipient_mail = $recipient_mail;
	
		$this->constructEndl();
	}
	public function setMsg($txt, $html)
	{
		$this->_txt_msg = $txt;
		$this->_html_msg = $this->_converter->convert_accents($html);
	}
	public function addFile($path, $name)
	{
		if(empty($path) || empty($name))
			return false;
			
		$file        = fopen($path, "r");
		$attachement = fread($file, filesize($path));
		$attachement = chunk_split(base64_encode($attachement));
		fclose($file);
		
		// TODO: extraire le bon MIME
		$mime_type = "application/force-download";
		
		$this->_included_files[] = array(
									"file_contents" => $attachement,
									"name" 			=> $this->_converter->convert_accents($name),
									"content-type"	=> $mime_type
										);
		
		return true;
	}
	public function send()
	{
		if($this->_recipient_mail == null || $this->_subject == null || $this->_txt_msg == null || $this->_html_msg == null)
			return false;
		
		$this->constructHeader();
		$this->constructMsg();
		
		mail($this->_recipient_mail, $this->_subject, $this->_msg, $this->_header);
		return true;
	}
	
	protected function constructEndl()
	{
		/*
			Tous les serveurs qui reçoivent les e-mails ne suivent pas obligatoirement la norme.
			Selon la norme, un passage à la ligne dans le code source d'un e-mail est « \r\n ».
			Certains hébergeurs remplacent le « \n » automatiquement par « \r\n », ce qui fait que l'on se retrouve avec « \r\r\n »,
			qui occasionne certains bogues au niveau de l'affichage des e-mails.
			Cette fonction essaie de palier à ce problème.
		*/
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $this->_recipient_mail))
		{
			$this->_endl = "\r\n";
		}
		else
		{
			$this->_endl = "\n";
		}
	}
	protected function constructHeader()
	{
		$this->_header = "From: \"".$this->_sender_name."\" <".$this->_sender_mail.">".$this->_endl;
		$this->_header .= "Reply-to: \"".$this->_sender_name."\" <".$this->_sender_mail.">".$this->_endl;
		$this->_header .= "MIME-Version: 1.0".$this->_endl;
		if(is_array($this->_included_files))
			$this->_header .= "Content-Type: multipart/mixed; ".$this->_endl."	boundary=\"".$this->_boundary."\"".$this->_endl;
		else
			$this->_header .= "Content-Type: multipart/alternative;".$this->_endl."	boundary=\"".$this->_boundary."\"".$this->_endl;
	}
	
	protected function constructMsg()
	{
		if(is_array($this->_included_files))
			$this->constructMixedMsg();
		else
			$this->constructSimpleMsg();
	}
	
	protected function constructSimpleMsg()
	{
		$this->_msg = "MIME-Version: 1.0".$this->_endl;
		$this->_msg .= "Content-Type: multipart/alternative;".$this->_endl;
		$this->_msg .= "	boundary=\"".$this->_boundary."\"".$this->_endl;
		$this->_msg .= $this->_endl;
		$this->_msg .= "This is a multi-part message in MIME format.".$this->_endl;
		$this->_msg .= $this->_endl;
		$this->_msg .= "--".$this->_boundary.$this->_endl;
		$this->_msg .= "Content-Type: text/plain; charset=\"".$this->_encode."\"\n".$this->_endl;
//		$this->_msg .= "Content-Transfer-Encoding: 8bit\n\n".$this->_endl;
		$this->_msg .= $this->_endl;
		$this->_msg .= $this->_txt_msg.$this->_endl;
		$this->_msg .= $this->_endl;
		$this->_msg .= "--".$this->_boundary.$this->_endl;
		$this->_msg .= "Content-Type: text/html; charset=\"".$this->_encode."\"\n".$this->_endl;
//		$this->_msg .= "Content-Transfer-Encoding: quoted-printable\n".$this->_endl;
		$this->_msg .= $this->_endl;		
		$this->_msg .= $this->injectStandardHtml($this->_html_msg).$this->_endl;
		$this->_msg .= $this->_endl;
		$this->_msg .= "--".$this->_boundary."--".$this->_endl;
		$this->_msg .= $this->_endl;
	}
	
	protected function constructMixedMsg()
	{
		foreach($this->_included_files as $current_file)
		{
			$attachments  = "--".$this->_boundary.$this->_endl;
			$attachments .= "Content-Type: ".$current_file['content-type'].";".$this->_endl;
			$attachments .= "	name=\"".$current_file['name']."\"".$this->_endl;
			$attachments .= "Content-Transfer-Encoding: base64".$this->_endl;
			$attachments .= "Content-Disposition: attachment;".$this->_endl;
			$attachments .= "	filename=\"".$current_file['name']."\"".$this->_endl;
			$attachments .= $this->_endl;
			$attachments .= $current_file['file_contents'].$this->_endl;
			$attachments .= $this->_endl;
		}
		
		$this->_msg  = "This is a multi-part message in MIME format.".$this->_endl;
		$this->_msg  .= 
		$this->_msg  .= "--".$this->_boundary.$this->_endl;
		$this->_msg  .= "Content-Type: multipart/alternative;".$this->_endl;
 		$this->_msg  .=  "  boundary=\"".$this->_sub_boundary."\"".$this->_endl;
		$this->_msg  .= $this->_endl;
		$this->_msg  .= "--".$this->_sub_boundary.$this->_endl;
		$this->_msg  .= "Content-Type: text/plain; charset=\"".$this->_encode."\"\n".$this->_endl;
//		$this->_msg  .= "Content-Transfer-Encoding: quoted-printable".$this->_endl;
		$this->_msg  .= $this->_endl;
		$this->_msg  .= $this->_txt_msg.$this->_endl;
		$this->_msg  .= "--".$this->_sub_boundary.$this->_endl;
		$this->_msg  .= "Content-Type: text/html; charset=\"".$this->_encode."\"\n".$this->_endl;
//		$this->_msg  .= "Content-Transfer-Encoding: quoted-printable".$this->_endl;
		$this->_msg  .= $this->_endl;
		$this->_msg  .= $this->injectStandardHtml($this->_html_msg).$this->_endl;
		$this->_msg  .= $this->_endl;
		$this->_msg  .= "--".$this->_sub_boundary."--".$this->_endl;
		$this->_msg  .= $this->_endl;
		$this->_msg  .= $attachments.$this->_endl;
		$this->_msg  .= "--".$this->_sub_boundary."--".$this->_endl;
		$this->_msg  .= $this->_endl;
	}
	
	protected function injectStandardHtml($html_msg)
	{
		$msg  = '<style'.
					'p{'.
						'display: block; '.
						'width: 350px; '.
						'font-family: Tahoma; '.
						'font-size: 11px; '.
						'color: rgb(81, 81, 81); '.
						'line-height: 18px; '.
						'margin: 0pt; '.
						'padding: 0pt; '.
					'}'.
				'</style>'.
				'<table align="center" border="0" cellpadding="0" cellspacing="0" width="590px">'.
					'<tbody>'.
						'<tr>'.
							'<td>'.
								'<table border="0" cellpadding="0" cellspacing="0">'.
									'<tbody>'.
										'<tr>'.
											'<td height="137" width="275">'.
												'<a href="http://resideclic.com" target="_blank">'.
													'<img src="http://resideclic.com/src/mail/img_03.png" alt="" style="display: block;" border="0" height="137" width="275">'.
												'</a>'.
											'</td>'.
											'<td height="137" width="315">'.
												'<a href="http://resideclic.com" target="_blank">'.
													'<img src="http://resideclic.com/src/mail/img_04.png" alt="" style="display: block;" border="0" height="137" width="315">'.
												'</a>'.
											'</td>'.
										'</tr>'.
									'</tbody>'.
								'</table>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td style="margin: 0pt; padding: 0pt;" width="590">'.
								'<table border="0" cellpadding="0" cellspacing="0">'.
									'<tbody>'.
										'<tr valign="top">'.
											'<td style="border-left: 3px solid rgb(203, 190, 178);" width="80">'.
												'&nbsp;'.
											'</td>'.
											'<td style="padding-top: 50px;" width="520">'.
												'<div style="margin: 0pt; padding: 0pt 0pt 0pt 30px;">'.
													'<p style="display: block; width: 350px; font-family: Tahoma; font-size: 12px; color: rgb(97, 82, 121); font-weight: bold; margin: 0pt; padding: 0pt;">'.
														$this->_converter->convert_accents($this->_subject).
													'</p>'.
													'<p style="display: block; width: 335px; font-family: Tahoma; font-size: 9px; color: rgb(232, 97, 55); margin: 0pt; padding: 0pt;">'.
														$this->_converter->convert_accents($this->getFrDate()).
													'</p>'.
													'<div style="display: block; width: 430px; font-family: Tahoma; font-size: 11px; color: rgb(81, 81, 81); line-height: 18px; margin: 0pt; padding: 0pt;">'.
														$html_msg.
													'</div>'.
												'</div>'.
											'</td>'.
											'<td style="border-right: 3px solid rgb(203, 190, 178);" width="80">'.
												'&nbsp;'.
											'</td>'.
										'</tr>'.
									'</tbody>'.
								'</table>'.
							'</td>'.
						'</tr>'.
						'<tr>'.
							'<td>'.
								'<table border="0" cellpadding="0" cellspacing="0">'.
									'<tbody>'.
										'<tr>'.
											'<td height="133" width="590">'.
												'<img src="http://resideclic.com/src/mail/basemailenvoi.png" alt="" style="display: block;" border="0" height="133" width="590">'.
											'</td>'.
										'</tr>'.
									'</tbody>'.
								'</table>'.
							'</td>'.
						'</tr>'.
					'</tbody>'.
				'</table>';
				
		return $msg;
	}
	protected function getFrDate()
	{
		$months = array (
							1	=>	"Janvier",
							3	=>	"Février",
							4	=>	"Mars",
							5	=>	"Avril",
							6	=>	"Mai",
							7	=>	"Juin",
							8	=>	"Juillet",
							9	=>	"Août",
							10	=>	"Septembre",
							11	=>	"Octobre",
							12	=>	"Décembre"
						);
						
		$d = date("j");
		$m = $months[date("n")];
		$y = date("Y");
		
		return "$d $m $y";
	}
}