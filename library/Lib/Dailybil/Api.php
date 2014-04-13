<?php
class Lib_Dailybil_Api
{
	public function create($post)
	{
		$url = 'http://dailybil.herokuapp.com/tweeter';
		$fields = array(
						'token' => $post['token'],
						'categories'=> implode(",",$post['categories'])
		);
		$fields_string = "";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; } 
		rtrim($fields_string,'&');
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_exec($ch);
		//close connection 
		curl_close($ch);
	}

	public function categories()
	{
		$url = 'http://dailybil.herokuapp.com/categories';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		if($categoriesStr = curl_exec($ch)) {
			$categories = explode(',',Zend_Json::decode($categoriesStr));
		}
		foreach($categories as $key => $categorie)
		{
			if($categorie == "")
				unset($categories[$key]);
		}
		//close connection 
		curl_close($ch);
		return $categories;
	}

	public function searches($token)
	{
		$url = 'http://dailybil.herokuapp.com/tweeter';
		$get = '?token='.$token;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url.$get);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		if($searchesStr = curl_exec($ch)) {
			$searches = Zend_Json::decode($searchesStr);
		}
		//close connection 
		curl_close($ch);
		return $searches;
	}
}