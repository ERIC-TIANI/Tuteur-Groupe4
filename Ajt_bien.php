<?php 
//inclure la page de connexion
include_once "con_dbb.php";
//verifier si une sessio existe
if(!isset($_SESSION)){
	//sinon demarrer la session
	session_start();
}

//creer la session
if(!isset($_SESSION['panier'])){
	//sinon
	$_SESSION['panier']=array();
}
if (isset($_SESSION['id_property'])) {
    $id_property = $_SESSION['id_property'];
} else {
    $id_property = null; // ou une valeur par défaut
}
print_r($_SESSION);

echo $_GET['id_property'];
?>
