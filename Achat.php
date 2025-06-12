<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" >
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
    <title>Boutique</title>
    <link rel="stylesheet" href="StyleSheet1.css">
</head>
<body>
    <a href="Panier.php" class="link">Ajouter/Supprimer un bien<span>3</span></a>
    <section class="list_biens">
    <?php 
    //inclure la page de connexion
    include_once "con_dbb.php";
    //afficher la liste des produits
    $req = mysqli_query($conn, "SELECT * FROM bienimmobilier");
    while($row = mysqli_fetch_assoc($req)){
    ?>
        <form action="" class="biens">
            <div class="image_biens">
                <img src="<?= $row ['img']?>">
            </div>
            <div class="content">
                <h4 class="nom"><strong><?=$row['nom']?></strong>

                </h4>
                <h2><?=$row['prix']?></h2>
                <a href="Ajt_bien.php?id=<?=$row['id_property']?>"class="ajt">Ajouter au panier</a>
            </div>
        </form>
        <?php } ?>
    </section>
</body>
</html>
