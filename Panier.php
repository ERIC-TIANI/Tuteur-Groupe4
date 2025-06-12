<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achat de biens</title>
    <link rel="stylesheet" href="StyleSheet1.css">
    <title>Panier</title>
</head>
<body class="panier">
    <a href="Achat.php" class="link">Boutique</a>
    <section>
         
        <table>
        
            <tr>
                <th>Image</th>
              <th>Nom</th>
                <th>Description</th>
                <th>Ville</th>
                <th>Prix</th>
                <th>Action</th>
            </tr>
            <?php 
            //inclure la page de connexion
            include_once "con_dbb.php";
            //afficher la liste des produits
            $req = mysqli_query($conn, "SELECT * FROM bienimmobilier");
            while($row = mysqli_fetch_assoc($req)){
            ?>
            <tr>
            
                <td><img src="<?= $row ['img']?>"></td>
                <td><?= $row ['nom']?></td>
                <td><?= $row ['description']?></td>
                <td><?= $row ['ville']?></td>
                <td><?= $row ['prix']?></td>
                <td><img src="<?= $row ['action']?>"></td>
                <?php } ?>
            </tr> 
         
        </table>
       
    </section>
</body>
</html>

