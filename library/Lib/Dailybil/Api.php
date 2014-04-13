<?php
class Lib_Dailybil_Api
{
	protected static $apiKey = "bYyv4qKwxwC10J7mYA";

	public function categories()
	{
		$url = 'http://dailybil.herokuapp.com/categories';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		if($categoriesStr = curl_exec($ch)) {
			$categories = explode($categoriesStr,",");
		}
		//close connection 
		curl_close($ch);
		return $categories;
	}
}