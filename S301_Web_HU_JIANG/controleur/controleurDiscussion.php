<?php

	require_once("./modele/proposition.php");
    require_once("./modele/groupe.php");
	require_once("./modele/participation.php");
	require_once("./modele/membre.php");
		
    class ControleurDiscussion {
        public static function lireDiscussion($id) {
            $titre = "Page de discussion";
			$membre = Membre::getMembreById($id);
            $nom = $membre->getNomMembre();
            $prenom = $membre->getPrenomMembre();
			$tab_groupe = Groupe::getSesGroupes($id) ;
            $Style = "StyleDiscussion"; 
			
			
            include("vue/debut.php");
            include("vue/pageDeDiscussion/Partie1Sans.html");
			include("vue/pageDeDiscussion/lesGroupes.php");
            include("vue/pageDeDiscussion/Partie2.html");
            include("vue/pageDeDiscussion/Partie3Sans.html");
            include("vue/fin.html");
        }
		
		public static function lireDiscussionAvecPropositions($id,$idGroupe) {
            $titre = "Page de discussion";
            $membre = Membre::getMembreById($id);
            $nom = $membre->getNomMembre();
            $prenom = $membre->getPrenomMembre();
			$groupe = Groupe::getGroupeById($idGroupe);
			$nGroupe = $groupe->getNomGroupe();
			$tab_groupe = Groupe::getSesGroupes($id);
			$tab_p = Proposition::getAllPropositionsByidGroupe($idGroupe);
			$role = Participation::getRoleByMembreId($id, $idGroupe);
			$Style = "StyleDiscussion"; 
			
            include("vue/debut.php");
            include("vue/pageDeDiscussion/Partie1.html");
			include("vue/pageDeDiscussion/lesGroupes.php");
            include("vue/pageDeDiscussion/Partie2.html");
			include("vue/pageDeDiscussion/sesPropositions.php");
            include("vue/pageDeDiscussion/Partie3.html");
            include("vue/fin.html");
        }

    }
?>
