<?php
session_start();
require_once 'config.php';
require_once 'connect.php';
require_once 'fonctions.php';

if (isset($_GET['idsection']) && ctype_digit($_GET['idsection'])){
    
    $recupget = "WHERE rubriques_id= ".$_GET['idsection'];
}
$recup_nb_test = "select count(*) as nb from photo_has_rubriques $recupget";
// requete de récupération
$tot = mysqli_query($mysqli,$recup_nb_test);
// transformation du résultat en tableau associatif
$maligne = mysqli_fetch_assoc($tot);
// variable contenant le nombre total de proverbes
$nb_total = $maligne['nb'];



if(isset($_GET[$get_pagination])){
  
    if(ctype_digit($_GET[$get_pagination])){
     
        $page_actu = $_GET[$get_pagination];
    }else{ 
       $page_actu = 1; 
    }
}else{ 
    $page_actu = 1;
}

$debut = ($page_actu -1) * $elements_par_page;
if (isset($_GET['idsection']) && ctype_digit($_GET['idsection'])){
    
    $ajout_requete = "WHERE r.id = ".$_GET['idsection'];
}else {
    $ajout_requete = "";
}

if (isset($_GET['idsection']) && ctype_digit($_GET['idsection'])){
    
    $ajout_requete = "WHERE r.id = ".$_GET['idsection'];
}else {
    $ajout_requete = "";
}


$sql = "SELECT p.lenom,p.lextension,p.ladesc,p.letitre, u.lelogin, 
    
    GROUP_CONCAT(r.lintitule SEPARATOR '~~')
    FROM photo p
    INNER JOIN utilisateur u ON u.id = p.utilisateur_id
    LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id
    $ajout_requete
    GROUP BY p.id
    ORDER BY p.id DESC
    LIMIT $debut,$elements_par_page;
    ";
$recup_sql = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));      



?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Accueil</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
		<nav>
        <ul>
             <li><a href="index.php">Accueil</a></li>            
             <li><a>Catégories</a>
                    <ul>         
                        <?php
                        $sqlr = "SELECT * FROM rubriques";
    $q = mysqli_query($mysqli,$sqlr);


    while($r = mysqli_fetch_assoc($q))
    {
    echo "<li><a href='categories.php?idsection=".$r['id']."'>".$r['lintitule']."</a></li>";
    }

                                         ?>
                    </ul>
 </li>
            <li><a href="contact.php">Nous contacter</a></li>
           <?php 
		   
		   if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {echo " ";}else{switch ($_SESSION['laperm']) {
                            // si on est l'admin
                            case 0 :
                                echo "<li><a href='admin.php'>Administration</a></li><li><a href='membre.php'>Espace client</a></li><li><a href='deconnect.php'>Déconnexion</a></li>";
                                break;
                            // si on est modérateur
                            case 1:
                                echo "<li><a href='modere.php'>Modération</a></li><li><a href='membre.php'>Espace client</a></li><li><a href='deconnect.php'>Déconnexion</a></li>";
                                break;
                            // si autre droit (ici simple utilisateur)
                            case 2 :
                        echo "<li><a href='membre.php'>Espace client</a></li><li><a href='deconnect.php'>Déconnexion</a></li>";};} ?>
       </ul>
</nav>
	<div id="content">
                         <?php
        if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
    ?><h1>Telepro-photos.fr</h1>
                        <form action="" name="connection" method="POST">
                            <input type="text" name="lelogin" required />
                            <input type="password" name="lepass" required />
                            <input type="submit" value="Connexion" />
                        </form>
                       
                        <?php
}else{
echo "<h1>Telepro-photos.fr</h1>";
    
                            echo "<h3>Bonjour ".$_SESSION['lenom'].'</h3>';
                        echo "<p>Vous êtes connecté en tant que <span >".$_SESSION['nom_perm']."</span></p>";
                        
                        
      }
        ?>

            </div>
        <div id="milieu">
            <div id="pagination">
                  <?php
            echo pagination($nb_total, $page_actu, $elements_par_page, $get_pagination)
            ?>  
        </div>
            <div id="bloc">
            <?php
                // affichez les miniatures de chaques photos dans la db par id Desc, avec le titre au dessus et la description en dessous, et affichage de la grande photo dans une nouvelle fenêtre lors du clic, Bonus : afficher lelogin de l'auteur de l'image
               while($ligne = mysqli_fetch_assoc($recup_sql)){
                 echo "<div class='miniatures'>";
                 echo "<h4>".$ligne['letitre']."</h4>";
                 echo "<a href='".CHEMIN_RACINE.$dossier_gd.$ligne['lenom'].".jpg' target='_blank'><img src='".CHEMIN_RACINE.$dossier_mini.$ligne['lenom'].".jpg' alt='' /></a><br/>";

                 echo "<p>".$ligne['ladesc']."<br /> par <strong>".$ligne['lelogin']."</strong></p>";
                 echo "</div>";
               }                
               ?> 
            </div>
        </div>
    </body>
</html>
