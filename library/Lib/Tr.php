<?php
class Lib_Tr
{
    public static function document($document,$client)
    {
        $tr['timestamp'] = $document['date'];
        $tr['idElt'] = $document['idElt'];
        $tr['titre'] = Lib_Securite::visibiliteIndex($document,$client).$document['titre'];
        $tr['auteur'] = $document['pAuteur']." ".$document['nAuteur'];
        $tr['date'] = Lib_Date::duduDate($document['date']);
        $tr['tag'] = Lib_String::tag($document['tags'],"warning");
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return documents_voir('.$document['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="grey" data-rel="tooltip" data-placement="left" data-original-title="Télécharger l\élément" target="_blank" href="/documents/telecharger?id='.$document['idElt'].'" onclick="cancelBubble(event);">
                                <i class="icon-download-alt"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return documents_modifier(0 , '.$document['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return documents_supprimer('.$document['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

    public static function evenement($evenement,$client)
    {
        $tr['timestamp'] = $evenement['date'];
        $tr['start'] = $evenement['dateselect'];
        $tr['id'] = $evenement['idElt'];
        $tr['contenu'] = $evenement['contenu'];
        $tr['className'] = "label-success";
        $tr['title'] = Lib_Securite::visibiliteIndex($evenement,$client).$evenement['titre'];
        $tr['date'] = Lib_Date::ericDate($evenement['dateselect']);
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return agenda_voir('.$evenement['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return agenda_modifier(0 , '.$evenement['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return agenda_supprimer('.$evenement['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

    public static function message($message)
    {
        $tr['timestamp'] = $message['date'];
        $tr['idElt'] = $message['idElt'];
        $tr['titre'] = $message['titre'];
        $tr['participants'] = Lib_Messagerie::participants($message['participants']);
        $tr['last'] = $message['pAuteur']." ".$message['nAuteur'].", ".Lib_Date::duduDate($message['date']);
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return messagerie_voir('.$message['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return messagerie_inviter(0 , '.$message['idElt'].')">
                                <i class="icon-signin bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return messagerie_supprimer('.$message['idElt'].')">
                                <i class="icon-signout bigger-130"></i>
                            </a>';
        return $tr;
    }

   public static function post($post,$client)
    {
        $tr['timestamp'] = $post['date'];
        $tr['idElt'] = $post['idElt'];
        $tr['titre'] = Lib_Securite::visibiliteIndex($post,$client).$post['titre'];
        $tr['contenu'] = $post['contenu'];
        $tr['auteur'] = $post['pAuteur']." ".$post['nAuteur'];
        $tr['date'] = Lib_Date::duduDate($post['date']);
        $tr['tag'] = Lib_String::tag($post['tags'],"warning");
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return forum_voir('.$post['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return forum_modifier(0 , '.$post['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return forum_supprimer('.$post['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

   public static function incident($incident)
    {
        $tr['timestamp'] = $incident['date'];
        $tr['idElt'] = $incident['idElt'];
        $tr['titre'] = $incident['titre'];
        $tr['contenu'] = $incident['contenu'];
        if($incident['statut'] == 0)
            $tr['statut'] = Lib_String::tag("En cours","danger");
        else
            $tr['statut'] = Lib_String::tag("Résolu","success");
        $tr['auteur'] = $incident['pAuteur']." ".$incident['nAuteur'];
        $tr['date'] = Lib_Date::duduDate($incident['date']);
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return incidents_voir('.$incident['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return incidents_modifier(0 , '.$incident['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return incidents_supprimer('.$incident['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

   public static function sondage($sondage,$client)
    {
        $tr['timestamp'] = $sondage['date'];
        $tr['idElt'] = $sondage['idElt'];
        $tr['titre'] = Lib_Securite::visibiliteIndex($sondage,$client).$sondage['titre'];
        $tr['contenu'] = $sondage['contenu'];
        $tr['auteur'] = $sondage['pAuteur']." ".$sondage['nAuteur'];
        $tr['date'] = Lib_Date::duduDate($sondage['date']);
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return sondages_voir('.$sondage['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return sondages_supprimer('.$sondage['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

   public static function intervention($intervention,$client)
    {
        $tr['timestamp'] = $intervention['date'];
        $tr['idElt'] = $intervention['idElt'];
        if($intervention['statut'] == 0)
            $tr['statut'] = Lib_String::tag("En cours","warning");
        else
            $tr['statut'] = Lib_String::tag("Terminée","success");
        $tr['titre'] = Lib_Securite::visibiliteIndex($intervention,$client).$intervention['titre'];
        $tr['contenu'] = $intervention['contenu'];
        $tr['auteur'] = $intervention['pAuteur']." ".$intervention['nAuteur'];
        $tr['date'] = Lib_Date::ericDate($intervention['dateselect']);
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return interventions_voir('.$intervention['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return interventions_modifier(0 , '.$intervention['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return interventions_supprimer('.$intervention['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

   public static function client($client)
    {
        $tr['idElt'] = $client['idElt'];
        $tr['habitant'] = $client['nom']." ".$client['prenom'];
        $tr['profil'] = $client['nomProfil'];
        $tr['localisation'] = $client['localisation'];
        $tr['moderateur'] = "";
        if($client['moderateur']) $tr['moderateur'] = "Modérateur";
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return habitants_voir('.$client['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return habitants_modifier(0 , '.$client['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>';
        return $tr;
    }

    public static function syndic($syndic)
    {
        $tr['idElt'] = $syndic['idElt'];
        $tr['habitant'] = $syndic['nom']." ".$syndic['prenom'];
        $tr['groupe'] = $syndic['nomProfil'];
        $tr['actions'] = '  <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return syndic_modifier(0 , '.$syndic['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>';
        return $tr;
    }

    public static function promoteur($promoteur)
    {
        $tr['idElt'] = $promoteur['idElt'];
        $tr['habitant'] = $promoteur['nom']." ".$promoteur['prenom'];
        $tr['groupe'] = $promoteur['nomProfil'];
        $tr['actions'] = '  <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return syndic_modifier(0 , '.$promoteur['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>';
        return $tr;
    }

   public static function fournisseur($fournisseur)
    {
        $tr['idElt'] = $fournisseur['idElt'];
        $tr['nom'] = $fournisseur['nom'];
        $tr['metiers'] = Lib_String::tag($fournisseur['metiers'],"warning");
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return fournisseurs_voir('.$fournisseur['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return fournisseurs_modifier(0 , '.$fournisseur['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return fournisseurs_supprimer('.$fournisseur['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

   public static function album($album,$client)
    {
        $tr['timestamp'] = $album['date'];
        $tr['idElt'] = $album['idElt'];
        $tr['titre'] = Lib_Securite::visibiliteIndex($album,$client).$album['titre'];
        $tr['contenu'] = $album['contenu'];
        $tr['auteur'] = $album['pAuteur']." ".$album['nAuteur'];
        $tr['date'] = Lib_Date::duduDate($album['date']);
        $tr['actions'] = '   <a class="green tooltip-success" data-rel="tooltip" data-placement="left" data-original-title="Voir le détail" href="#" onclick="cancelBubble(event);return album_voir('.$album['idElt'].')">
                                <i class="icon-zoom-in bigger-130"></i>
                            </a>
                            <a class="orange tooltip-warning" data-rel="tooltip" data-placement="left" data-original-title="Modifier l\'élément" href="#" onclick="cancelBubble(event);return album_modifier(0 , '.$album['idElt'].')">
                                <i class="icon-pencil bigger-130"></i>
                            </a>
                            <a class="red tooltip-error" data-rel="tooltip" data-placement="left" data-original-title="Supprimer l\'élément" href="#" onclick="cancelBubble(event);return album_supprimer('.$album['idElt'].')">
                                <i class="icon-trash bigger-130"></i>
                            </a>';
        return $tr;
    }

    public static function photo($photo)
    {
        $tr='
           <li>
                <a href="/file/album/'.$photo['idCopro'].'/'.$photo['url'].'" data-rel="colorbox">
                    <img style="width:150px;height:150px;" src="/file/album/'.$photo['idCopro'].'/'.$photo['url'].'" />
                    <div class="text">
                        <div class="inner">'.$photo['titre'].'</div>
                    </div>
                </a>
                <div class="tools tools-bottom">
                    <a href="#" onclick="return album_photo_supprimer('.$photo['idAlbum'].','.$photo['idElt'].',this);">
                        <i class="icon-remove red"></i>
                    </a>
                </div>
            </li>';
        return $tr;
    }
}