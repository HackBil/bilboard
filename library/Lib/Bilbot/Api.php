<?php
class Lib_Bilbot_Api
{
	public function create($post)
	{
		$url = 'http://bilbot.herokuapp.com/create';
		$tokens = Lib_Socialconnect::twitterDecode($post['connectionObject']);
		$fields = array(
						'access_token' => $tokens['oauth_token'],
						'access_token_secret' => $tokens['oauth_token_secret'],
						'categories'=> $post['users']
		);
		$fields_string = Zend_Json::encode($fields);
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_exec($ch);
		//close connection 
		curl_close($ch);
	}
}