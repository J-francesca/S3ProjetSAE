<?php
set_time_limit(10); // 限制脚本运行时间为 10 秒
ini_set('memory_limit', '128M'); // 限制内存使用为 128MB
	session_start();

	if (isset($_SESSION['expire_time']) && time() > $_SESSION['expire_time']) {
		session_unset();  
		session_destroy(); 
		if (isset($_COOKIE['user_id'])) {
			setcookie('user_id', '', time() - 3600, '/');  
	
		$_SESSION['erreur'] = "Votre session a expiré. Veuillez vous reconnecter.";
		header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion'); 
		exit();
	}

}


	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	include("config/Connection.php");
	Connexion::connect(); 

	$tableauControleurs = ["controleurMembre", "controleurDiscussion", 
	"controleurProposition", "controleurCommentaire", "controleurVote", 
	"controleurParametre","controleurDecideur","controleurSignalement",
	"controleurGroupe","controleurNotification","controleurInvitation",
	"controleurGroupeTheme", "controleurRole"];
	$actionMap = [
		"connexion" => "traiterConnexion", 
		"afficherWelcome" => "afficherWelcome",
		"afficherFormulaireConnexion" => "afficherFormulaireConnexion"
	];

	$controleur = "controleurMembre";
	$action = "afficherWelcome";

	// contrôleur valide est passé dans l'URL
	if (isset($_GET['controleur']) && in_array("controleur" . ucfirst($_GET['controleur']), $tableauControleurs)) {
		$controleur = "controleur" . ucfirst($_GET['controleur']);
	}



	$cheminControleur = "controleur/$controleur.php";
	if (!file_exists($cheminControleur)) {
		die("Le fichier $cheminControleur est introuvable.");
	}

	require_once($cheminControleur);

	//action valide est passée dans l'URL
	if (isset($_GET['action'])) {
		$actionProposee = $_GET['action'];
		if (method_exists($controleur, $actionProposee)) {
			$action = $actionProposee;
		} else {
			die("L'action demandée '$actionProposee' n'existe pas dans le contrôleur $controleur.");
		}
	}

	if (isset($_GET['format']) && $_GET['format'] === 'json') {
		if (ob_get_length()) ob_clean();
		header('Content-Type: application/json');
	}
	

	$id = isset($_GET['id']) ? $_GET['id'] : null;
	$idGroupe = isset($_GET['idGroupe']) ? $_GET['idGroupe'] : null;

	if ($id && $idGroupe && method_exists($controleur, $action)) {
		$controleur::$action($id, $idGroupe);
	} elseif ($id && method_exists($controleur, $action)) {
		$controleur::$action($id);
	} elseif (method_exists($controleur, $action)) {
		$controleur::$action();
	} else {
		die("L'action demandée n'existe pas ou l'identifiant est invalide.");
	}

?>
