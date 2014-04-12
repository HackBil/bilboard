<?php
class Model_Client extends Zend_Db_Table
{
	// RD - Login
	public function login($mail,$pass)
	{
		if (!empty($mail) && !empty($pass))
		{
			$pass = Lib_Pass::encode($pass);
			$select = $this->_db->select()
						->from('client')
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

	// RD - Login
	public function mailuniq($mail)
	{
		$select = $this->_db->select()
					->from('client')
					->where('mail = ?', $mail);
		$resultat = $select->query();
		return $resultat->fetch();
	}

    // RD - Génération du client
    public function client($id)
    {
            $select = $this        ->_db->select()
                                            ->from('client',array('*','idElt'=>'id'))
                                            ->where('id = ?', $id)
                                            ->orWhere('mail = ?', $id);
            $resultat = $select->query();
            $client = $resultat->fetch();

            $select = $this        ->_db->select()
                                            ->from('client_copro')
                                            ->joinLeft('copro','copro.id = client_copro.idCopro')
                                            ->joinLeft('y_gu','y_gu.id = client_copro.profil',array('gu','famille'))
                                            ->where('idClient = ?', $client['id']);
            $resultat = $select->query();
            $client['copros'] = $resultat->fetchAll();

            $client['infos'] = array();
            $select = $this        ->_db->select()
                                            ->from('client_infos')
                                            ->where('idClient = ?', $id);
            $resultat = $select->query();
            $client['infos'] = $resultat->fetch();

            $client['param'] = array();
            $select = $this        ->_db->select()
                                            ->from('client_param')
                                            ->where('idClient = ?', $id);
            $resultat = $select->query();
            $client['param'] = $resultat->fetch();

            return $client;
    }

	public function index($idCopro)
	{
		$select = $this	->_db->select()
						->from('client',array('*','idElt'=>'id'))
						->joinLeft('client_copro','client_copro.idClient = client.id',array('activ','moderateur','localisation'))
						->joinLeft('y_gu','client_copro.profil = y_gu.id')
						->where('client_copro.idCopro = ?', $idCopro)
						->where('client_copro.activ >= ?',0 );
		$resultat = $select->query();
		return $resultat->fetchAll();
	}

	public function indexHabitants($idCopro)
	{
		$select = $this	->_db->select()
						->from('client',array('*','idElt'=>'id'))
						->joinLeft('client_copro','client_copro.idClient = client.id',array('activ','moderateur','localisation'))
						->joinLeft('y_gu','client_copro.profil = y_gu.id')
						->where('client_copro.idCopro = ?', $idCopro)
						->where('client_copro.activ >= ?',0 )
						->where('y_gu.id < ?',100 );
		$resultat = $select->query();
		return $resultat->fetchAll();
	}

	public function get($idHabitant,$idCopro)
	{
		$select = $this	->_db->select()
						->from('client',array('*','idElt'=>'id'))
						->joinLeft('client_copro','client_copro.idClient = client.id')
						->joinLeft('y_gu','client_copro.profil = y_gu.id',array('nomProfil'=>'profil'))
						->where('client.id = ?', $idHabitant)
						->where('client_copro.idCopro = ?',$idCopro);
		$resultat = $select->query();
		$client = $resultat->fetch();

		$select = $this	->_db->select()
						->from('client')
						->where('id = ?', $client['validateur']);
		$resultat = $select->query();
		$client['validateur'] = $resultat->fetch();

		$client['infos'] = array();
		$select = $this	->_db->select()
						->from('client_infos')
						->joinLeft('y_c_profession','client_infos.profession = y_c_profession.id',array('professionLibelle'=>'libelle'))
						->joinLeft('y_c_marital','client_infos.marital = y_c_marital.id',array('maritalLibelle'=>'libelle'))
						->where('idClient = ?', $idHabitant);
		$resultat = $select->query();
		$client['infos'] = $resultat->fetch();

		$client['param'] = array();
		$select = $this	->_db->select()
						->from('client_param')
						->where('idClient = ?', $idHabitant);
		$resultat = $select->query();
		$client['param'] = $resultat->fetch();

		return $client;
	}

	// RD - Fonction d'ajout d'un client
	public function add($post)
	{
		if(isset($post['mail']))
		{
			$data['mail'] =  htmlspecialchars($post['mail']);
			if(isset($post['pass'])) $data['pass'] = Lib_Pass::encode($post['pass']);
			if(isset($post['civ'])) $data['civ'] =  $post['civ'];
			if(isset($post['nom'])) $data['nom'] = htmlspecialchars(Lib_String::ucfirstall(strtolower($post['nom']),"-",null));
			if(isset($post['prenom'])) $data['prenom'] =  htmlspecialchars(Lib_String::ucfirstall(strtolower($post['prenom']),"-",null));
			$data['dateInscription'] =  time();
			$result = $this->_db->insert('client',$data);
			$last_client_id = $this->_db->lastInsertId();
			return $last_client_id;
		}
		else
		{
			return	false;
		}
	}
	
	// RD - Fonction de mise à jour d'un client
	public function update($post,$idClient)
	{
		if(isset($post['mail'])) $data['mail'] =  htmlspecialchars($post['mail']);
		if(isset($post['pass'])) $data['pass'] =  Lib_Pass::encode($post['pass']);
		if(isset($post['keypass'])) $data['keypass'] =  $post['keypass'];
		if(isset($post['civ'])) $data['civ'] =  $post['civ'];
		if(isset($post['nom'])) $data['nom'] =  htmlspecialchars(Lib_String::ucfirstall(strtolower($post['nom']),"-",null));
		if(isset($post['prenom'])) $data['prenom'] =  htmlspecialchars(Lib_String::ucfirstall(strtolower($post['prenom']),"-",null));
		if(isset($post['pic'])) $data['pic'] = $post['pic'];
		if(isset($post['lastActivity'])) $data['lastActivity'] = $post['lastActivity'];
		if(isset($data))
		{
			$where = array('id = ?' => $idClient);
			$this->_db->update('client',$data,$where);
		}
		// Envoi de la mise à jour des infos personnelles sous condition :
		$this->updateInfos($idClient,$post);
		// Envoi de la mise à jour des paramètres sous condition :
		$this->updateParam($idClient,$post);

		// Envoi de la mise à jour de la table client_copro :
		if(isset($post['idCopro']))
			$this->updateClientCopro($idClient,$post);
	}
	
	// RD - Fonction qui va set les infos persos du client : création de la ligne si elle n'existe pas, update sinon
	public function updateInfos($idClient,$post)
	{
		if(isset($post['phone']))$infos['phone']= htmlspecialchars($post['phone']);
		if(isset($post['profession']))$infos['profession']=$post['profession'];
		if(isset($post['marital']))$infos['marital']=$post['marital'];
		if(isset($post['mailSecondaire']))$infos['mailSecondaire']=$post['mailSecondaire'];
		if(isset($post['enfants']))$infos['enfants']=$post['enfants'];
		if(isset($post['naissance']))$infos['naissance']= htmlspecialchars($post['naissance']);
		if(isset($infos))
		{
			$select = $this->_db->select()
				->from('client_infos')
				->where('client_infos.idClient = ?',$idClient);
			$resultat = $select->query();
			if($resultat->fetch()!=false)
			{		
				$where = array('idClient=?'=>$idClient);
				$this->_db->update('client_infos', $infos, $where);
			}
			else
			{
				$infos['idClient']=$idClient;
				$this->_db->insert('client_infos', $infos);
			}
		}
		
	}	

	// RD - Fonction qui va set les params du client : création de la ligne si elle n'existe pas, update sinon
	public function updateParam($idClient,$post)
	{
		if(isset($post['coordonnees']))$param['coordonnees']=$post['coordonnees'];
		if(isset($post['infos']))$param['infos']=$post['infos'];
		if(isset($post['frequence']))$param['frequence']=$post['frequence'];
		if(isset($post['compta']))$param['compta']=$post['compta'];
		if(isset($post['nouveaute']))$param['nouveaute']=$post['nouveaute'];
		if(isset($param))
		{
			$select = $this->_db->select()
				->from('client_param')
				->where('client_param.idClient = ?',$idClient);
			$resultat = $select->query();
			if($resultat->fetch()!=false)
			{
				$where = array('idClient=?'=>$idClient);
				$this->_db->update('client_param', $param, $where);
			}
			else
			{
				$param['idClient']=$idClient;
				$this->_db->insert('client_param', $param);
			}
		}
	}

	// RD - Fonction qui va set les params du client : création de la ligne si elle n'existe pas, update sinon
	public function updateClientCopro($idClient,$post)
	{
		$param['idCopro']=$post['idCopro'];
		if(isset($post['validateur']))$param['validateur']=$post['validateur'];
		if(isset($post['moderateur']))$param['moderateur']=$post['moderateur'];
		if(isset($post['activ']))$param['activ']=$post['activ'];
		if(isset($post['profil']))$param['profil']=$post['profil'];
		if(isset($post['localisation']))$param['localisation']= htmlspecialchars($post['localisation']);
		$select = $this->_db->select()
			->from('client_copro')
			->where('client_copro.idClient = ?',$idClient)
			->where('client_copro.idCopro = ?',$param['idCopro']);
		$resultat = $select->query();
		if($resultat->fetch()!=false)
		{
			$where = array('idClient=?'=>$idClient,'idCopro = ?'=>$param['idCopro']);
			$this->_db->update('client_copro', $param, $where);
		}
		else
		{
			$param['idClient']=$idClient;
			$this->_db->insert('client_copro', $param);
		}
	}

	// RD - Fonction d'enregistrement de la clé temporaire pour la réinitialisation du mot de passe
	public function keypass($mail)
	{
		// Génération de la clé
		$cle = rand(0,9999)."-".rand(0,9999)."-".rand(0,9999)."-".rand(0,9999);
		
		// On ajoute une ligne dans la BDD.
		$data = array ('keypass' => $cle);
		$where = array('mail = ?' => $mail);
		$result = $this->_db->update('client', $data, $where);
		return $cle;
	}

	// RD - Fonction d'enregistrement de la clé temporaire pour la réinitialisation du mot de passe
	public function newpass($post)
	{
		$select = $this->_db->select()
					->from('client')
					->where('keypass = ?', $post['keypass']);
		$resultat = $select->query();
		$client = $resultat->fetch();

		$data['pass'] =  Lib_Pass::encode($post['pass']);
		$data['keypass'] = "";
		$where = array('id = ?' => $client['id']);
		$result = $this->_db->update('client', $data, $where);
		return $result;
	}

	public function inactifs($idCopro)
	{
		$select = $this	->_db->select()
						->from('client',array('*','idElt'=>'id'))
						->joinLeft('client_copro','client_copro.idClient = client.id',array('moderateur'))
						->joinLeft('y_gu','client_copro.profil = y_gu.id')
						->where('client_copro.activ = 0')
						->where('client_copro.validateur = 0')
						->where('client_copro.idCopro = ?', $idCopro);
		$resultat = $select->query();
		return $resultat->fetchAll();
	}

	public function activer($idCopro,$idClient,$idValidateur)
	{
		$data['activ'] = 1;
		$data['validateur'] = $idValidateur;
		$where = array('idClient = ?' => $idClient,'idCopro = ?' => $idCopro);
		$this->_db->update('client_copro',$data,$where);
	}

	public function newsletter($freq)
	{
		$select = $this	->_db->select()
						->from('client',array('*','idElt'=>'id'))
						->joinLeft('client_copro','client_copro.idClient = client.id',array('moderateur'))
						->joinLeft('client_param','client_param.idClient = client.id',array('frequence'))
						->where('client_copro.activ = 1')
						->where('client_param.frequence <= ?',$freq);
		$resultat = $select->query();
		return $resultat->fetchAll();
	}

	public function moderateurs($idCopro)
	{
		$select = $this	->_db->select()
						->from('client',array('*','idElt'=>'id'))
						->joinLeft('client_copro','client_copro.idClient = client.id',array('moderateur'))
						->where('client_copro.activ = 1')
						->where('client_copro.moderateur = 1')
						->where('client_copro.idCopro = ?',$idCopro);
		$resultat = $select->query();
		return $resultat->fetchAll();
	}
}