<?php
session_start();
require_once 'config.php';
require_once 'connect.php';
require_once 'fonctions.php';


if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
    header("location: connection.php");
}
if (!isset($_SESSION['laperm']) !=0 ) {
    header("location: membre.php");
}

if(isset($_POST['letitre'])&&isset($_FILES['lefichier'])){
    
 
    $letitre = traite_chaine($_POST['letitre']);
    $ladesc = traite_chaine($_POST['ladesc']);

    $limage = $_FILES['lefichier'];

  
    $upload = upload_originales($limage,$dossier_ori,$formats_acceptes);
    
    if(!is_array($upload)){
        // on affiche l'erreur
        echo $upload;
        
     }else{
        $gd_ok = creation_img($dossier_ori, $upload['nom'],$upload['extension'],$dossier_gd,$grande_large,$grande_haute,$grande_qualite);
        
        $min_ok = creation_img($dossier_ori, $upload['nom'],$upload['extension'],$dossier_mini,$mini_large,$mini_haute,$mini_qualite,false);
        
       if($gd_ok==true && $min_ok==true){
   
           $sql= "INSERT INTO photo (lenom,lextension,lepoids,lahauteur,lalargeur,letitre,ladesc,utilisateur_id) 
	VALUES ('".$upload['nom']."','".$upload['extension']."',".$upload['poids'].",".$upload['hauteur'].",".$upload['largeur'].",'$letitre','$ladesc',".$_SESSION['id'].");";
            
            mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));
            
            $id_photo = mysqli_insert_id($mysqli);
            
             if(isset($_POST['section'])){
            foreach($_POST['section'] AS $clef => $valeur){
                if(ctype_digit($valeur)){
                    mysqli_query($mysqli,"INSERT INTO photo_has_rubriques VALUES ($id_photo,$valeur);")or die(mysqli_error($mysqli));
                }
            }
            }
            
        }else{
            echo 'Erreur lors de la création des images redimenssionnées';
        }
        
    }    
}


if(isset($_GET['delete'])&& ctype_digit($_GET['delete'])){
    $idphoto = $_GET['delete'];
    $idutil = $_SESSION['id'];
    
  
    $sql1="SELECT lenom, lextension FROM photo WHERE id=$idphoto;";
    $nom_photo = mysqli_fetch_assoc(mysqli_query($mysqli,$sql1));
    
  
    $sql2="DELETE FROM photo_has_rubriques WHERE photo_id = $idphoto";
    mysqli_query($mysqli,$sql2);
    
 
    $sql3="DELETE FROM photo WHERE id = $idphoto;";
    mysqli_query($mysqli,$sql3);
    echo $dossier_ori.$nom_photo['lenom'].".".$nom_photo['lextension'];
    
   
    unlink($dossier_ori.$nom_photo['lenom'].".".$nom_photo['lextension']);
    unlink($dossier_gd.$nom_photo['lenom'].".jpg");
    unlink($dossier_mini.$nom_photo['lenom'].".jpg");
}






$sql = "SELECT p.*, GROUP_CONCAT(r.id) AS idrub, GROUP_CONCAT(r.lintitule SEPARATOR '|||' ) AS lintitule
    FROM photo p
	LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id
        GROUP BY p.id
        ORDER BY p.id DESC
        LIMIT $debut,$elements_par_page;
    ";
$recup_sql = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));


$sql="SELECT * FROM rubriques ORDER BY lintitule ASC;";
$recup_section = mysqli_query($mysqli, $sql);



$recup_nb_test = "SELECT COUNT(*) AS nb FROM photo;";

$tot = mysqli_query($mysqli,$recup_nb_test);

$maligne = mysqli_fetch_assoc($tot);

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


?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Accueil</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="monjs.js"></script>
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
                
             
                                  <div id="pagination">
                  <?php
            echo pagination($nb_total, $page_actu, $elements_par_page, $get_pagination)
            ?>  
       
                 <div id="bloc">
                     <?php
                     while($ligne = mysqli_fetch_assoc($recup_sql)){
                 echo "<div class='miniatures'>";
                 echo "<h4>".$ligne['letitre']."</h4>";
                 echo "<a href='".CHEMIN_RACINE.$dossier_gd.$ligne['lenom'].".jpg' target='_blank'><img src='".CHEMIN_RACINE.$dossier_mini.$ligne['lenom'].".jpg' alt='' /></a>";
                 echo "<p>".$ligne['ladesc']."<br /><br />";
                 // affichage des sections
                 $sections = explode('|||',$ligne['lintitule']);
                 //$idsections = explode(',',$ligne['idrub']);
                 foreach($sections AS $key => $valeur){
                     echo " $valeur<br/>";
                 }
                 echo"<br/><a href='modif.php?id=".$ligne['id']."'><img src='img/modifier.png' alt='modifier' /></a> <img onclick='supprime(".$ligne['id'].");' src='img/supprimer.png' alt='supprimer' />
                     </p>";
                 echo "</div>";
               }
               ?>

                 </div>
             </div>
           
    </body>
</html>
