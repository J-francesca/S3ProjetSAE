<?php

	require_once("./modele/proposition.php");
    require_once("./modele/groupe.php");
	require_once("./modele/participation.php");
	require_once("./modele/membre.php");
	require_once("./modele/theme.php");
	require_once("./modele/groupetheme.php");
	
    class ControleurParametre {
        public static function lireParametre($id,$idGroupe) {
            
			
			$titre = "Page de parametre";
			$nbMembre = Participation::nbMembresDansGroupe($idGroupe);
			$tab_m = Participation::getMembreByIdGroupe($idGroupe);
            $Style = "StyleParametre"; 
		
            include("vue/debut.php");
            include("vue/pageDeParametre/parametrePartie1.html");
			include("vue/membre/lesMembres.php");
			include("vue/pageDeParametre/parametrePartie2.html");
			include("vue/pageDeParametre/parametrePartie3.html");
            include("vue/fin.html");
			
        }
		
		public static function lireParametreGroupe($id,$idGroupe) {
			$titre = "Page de parametre";
			$nbMembre = Participation::nbMembresDansGroupe($idGroupe);
			$tab_m = Participation::getMembreByIdGroupe($idGroupe);
            $Style = "StyleParametre"; 
			$groupe = Groupe::getGroupeById($idGroupe);
			$parametre = $_GET['parametre'];
			$classe = $_GET['classe'];
			$role = Participation::getRoleByIds($id, $idGroupe);
			$ext = $_GET['ext'];
			if($role == 1 || $role == 2) {
			
				include("vue/debut.php");
				include("vue/pageDeParametre/parametrePartie1.html");
				include("vue/membre/lesMembres.php");
				include("vue/pageDeParametre/parametrePartie2.html");
				include("vue/$classe/$parametre.$ext");
				include("vue/pageDeParametre/parametrePartie3.html");
				include("vue/fin.html");
			}else{
				include("vue/debut.php");
				include("vue/pageDeParametre/parametrePartie1.html");
				include("vue/membre/lesMembres.php");
				include("vue/pageDeParametre/parametrePartie2.html");
				include("vue/pageDeParametre/accesInterdit.html");
				include("vue/pageDeParametre/parametrePartie3.html");
				include("vue/fin.html");
			}
        }
		
		
	}
?>

