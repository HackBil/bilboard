<?php

/*
 *	Fonction "rechercher_copro()" :
 *
 *	Fonction de recherche de copropri�t�.
 *	Utilis�e sur la page /frontend/inscription/index.
 *	Retourne un tableau contenant les copropri�t�s dont l'adresse
 *	ressemble le plus � celle recherch�e.
 *
 *	Auteur : QL.
*/

class View_Helper_Search
{
	const pourcentage_ressemblance_minimum = 65; // %
	const nbr_max_copro_retournee = 10;
	const nbr_max_syndic_retournes = 10;
	protected $accents = array ("�","�","�","�","�","�","�","�","'","&#039;");
	protected $sans_accents = array ("e","e","e","a","a","i","o","c"," "," ");
	
	function preparer_adresse($adresse)
	{
		// Pr�paration de la copropri�t� recherch�e :
		// - on enregistre chaque mot signifiant (pas les mots de liaison type le/la/les/de/des/...) dans un tableau
		// - on passe tous les caract�res en minuscule
		// - on retire les espaces en trop
		// - on retire les mots d'une seule lettre
		// - on remplace les caract�re accentu�s
		// - on retire les nombres, nous les testerons plus tard
		$significatif = strtolower($adresse);
		$significatif = str_replace($this->accents,$this->sans_accents, utf8_decode($significatif));
		$significatif = str_replace('-', ' ', $significatif);
		$significatif = str_replace(' les ', ' ', $significatif);
		$significatif = str_replace(' le ', ' ', $significatif);
		$significatif = str_replace(' la ', ' ', $significatif);
		$significatif = str_replace(' des ', ' ', $significatif);
		$significatif = str_replace(' de ', ' ', $significatif);
		$significatif = str_replace(' du ', ' ', $significatif);
		$significatif = preg_replace('#( . )|(^. )|( .$)#', ' ', $significatif);
		$significatif = preg_replace('#[0-9]#', '', $significatif);
		$significatif = trim(preg_replace('#( +)#',' ',$significatif));
		return explode(' ', $significatif);
	}
	
	function preparer_nombres($adresse)
	{
		// On retourne un tableau contenant les nombres contenus dans l'adresse.
		preg_match_all("#([0-9]+)#", $adresse, $match);
		return $match[0];
	}

	function rechercher_copro($copro)
	{		
		$resultats = array();
		$indices = array();

		/* --------------------- */
		/* Etape 1 : code postal */
		/* --------------------- */
		
		// On extrait le code postal de l'adresse s'il existe.
		$cp = null;
		if(preg_match("#([0-9]{5})#", $copro, $match))
		{
			$cp =  $match[0];
			// On ne garde que les 2 premiers chiffres
			$cp = substr($cp, 0, 2);
		}
		// Zend_Debug::dump($copro);
		// Zend_Debug::dump($cp);
		
		// R�cup�ration des copropri�t�s enregistr�s.
		$db_copro = new Model_Copro();
		$tab_copropriete = $db_copro->recup_info_copropriete_search($cp);
		$copro = Libresideclic_String::clean_SEARCH($copro);
		$copro = strtolower($copro);
		if(empty($tab_copropriete))
		{
			return null;
		}

		// Pr�paration de l'adresse recherch�e.
		$expl_copro_recherchee = $this->preparer_adresse($copro);
		$nbr_copro_recherchee = $this->preparer_nombres($copro);

		// Boucle sur l'ensemble des copropri�t�s enregistr�es.
		foreach($tab_copropriete as $tc)
		{
			$indice = 0;
			
			// Pr�paration de l'adresse en cours de comparaison.
			$id = $tc['id'];
			$adresse = $tc['nom'].' '.$tc['rue'].' '.$tc['cp'].' '.$tc['ville'];
			$adresse = strtolower($adresse);
			$expl_copro_en_cours = $this->preparer_adresse($adresse);
			$nbr_copro_en_cours = $this->preparer_nombres($adresse);
			
			// Comparaison des mots des dadresses.
			foreach($expl_copro_recherchee as $mot_recherchee)
			{
				foreach($expl_copro_en_cours as $mot_en_cours)
				{
					if(levenshtein($mot_recherchee, $mot_en_cours) <= 1)
					{
						$indice++;
						break;
					}
				}
			}
			
			// Comparaison des nombres des adresses.
			foreach($nbr_copro_recherchee as $nbr)
			{
				if(strpos($adresse, $nbr) !== FALSE)
				{
					$indice++;
				}
			}
			
			// S�lection des adresses ressemblant � l'adresse recherch�e :
			// on consid�re que la recherche est valide si l'indice est suffisant par rapport
			// au nombre de mots de l'adresse, le rapport est d�fini par la variable $pourcentage
			// (on utilise l'adresse la pllus courte entre celle entr�e et celle de la base dde donn�e)
			$len_adresse_reelle = count($expl_copro_en_cours) + count($nbr_copro_en_cours);
			$len_adresse_testee = count($expl_copro_recherchee) + count($nbr_copro_recherchee);
			$length = min($len_adresse_reelle, $len_adresse_testee);
			$indice_minimum = $length*self::pourcentage_ressemblance_minimum/100;
			if($indice > $indice_minimum)
			{
				$resultats[$id] = $tc;		// contient l'adresse compl�te
				$indices[$id] = $indice;	// contient l'indice pour futur tri
			}
			
		} // finb de boucle (sur les copropri�t�s)

		if(empty($resultats))
		{
			return null;
		}
		
		// On garde les X meilleurs r�sultats avec X = nbr_max_copro_retournee.
		array_multisort($resultats, $indices);
		$resultats = array_chunk($resultats, self::nbr_max_copro_retournee);
		$resultats = $resultats[0];

		return $resultats;
	}
	
	function rechercher_societe($societe)
	{		
		$resultats = array();
		$indices = array();

		// Récupération des copropriétés enregistrés.
		$model_societe = new Model_Societe();
		$societes = $model_societe->liste_syndics();
		$societe = Libresideclic_String::clean_SEARCH($societe);
		$societe = strtolower($societe);
		if(empty($societes))
		{
			return null;
		}

		// Boucle sur l'ensemble des copropriétés enregistrées.
		foreach($societes as $syndic)
		{
			$tmpNom = strtolower($syndic['nom']);
			$lev = levenshtein($societe, $tmpNom);
			if($lev/strlen($tmpNom) <= 0.2)
			{
				$resultats[] = $syndic;
				$indices[] = $lev;
			}
			
		}
		if(empty($resultats))
		{
			return null;
		}
		
		// On garde les  meilleurs résultats avec X = nbr_max_copro_retournee.
		array_multisort($resultats, $indices);
		$resultats = array_chunk($resultats, self::nbr_max_syndic_retournes);
		return $resultats[0];
	}

}