<?php
class Model_User extends Zend_Db_Table
{
	// RD - Login
	public function login($mail,$pass)
	{
		if (!empty($mail) && !empty($pass))
		{
			$pass = Lib_Pass::encode($pass);
			$select = $this->_db->select()
						->from('user')
						->where('mail = ?', $mail);
			$resultat = $select->query();
			$result = $resultat->fetch();
			if(!$result)
				return ("Adresse électronique incorrecte !");
			if($result['pass'] != $pass)
				return ('Mot de passe incorrect !');
			else
				return $result['id'];
		}
		else
			return ("Le formulaire n'est pas renseigné correctement.");
	}

	public function get($idUser)
	{
		$select = $this	->_db->select()
						->from('user',array('*','idElt'=>'id'))
						->where('user.id = ?', $idUser);
		$resultat = $select->query();
		return $resultat->fetch();
	}

	public function getFromMail($mailUser)
	{
		$select = $this	->_db->select()
						->from('user',array('*','idElt'=>'id'))
						->where('user.mail = ?', $mailUser);
		$resultat = $select->query();
		return $resultat->fetch();
	}

	public function add($user)
	{
		$data['lastname'] = $user['lastname'];
		$data['firstname'] = $user['firstname'];
		$data['mail'] = $user['mail'];
		$data['pass'] = Lib_Pass::encode($user['pass']);
		$result = $this->_db->insert('user',$data);
		return $this->_db->lastInsertId();
	}

	public function update($user,$userId)
	{
		$data['lastname'] = $user['lastname'];
		$data['firstname'] = $user['firstname'];
		$data['mail'] = $user['mail'];
		$data['pass'] = Lib_Pass::encode($user['pass']);
		$where =  array ('id = ?' => $userId);
		$this->_db->update('user', $data, $where);
	}
}