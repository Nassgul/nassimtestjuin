<?php
session_start();

require_once 'config.php';
require_once 'connect.php';
require_once 'fonctions.php';


// si on est pas (ou plus) connecté
if (!isset($_SESSION['sid']) || $_SESSION['sid'] != session_id()) {
    header("location: deconnect.php");
}

// si il existe un id de type get et qu'il est numérique
if(isset($_GET['id'])&&  ctype_digit($_GET['id'])){
    $idphoto = $_GET['id'];
}else{
   header("location: membre.php");
}

// si on a envoyé le formulaire et qu'un fichier est bien attaché
if(isset($_POST['letitre'])){
    
    // traitement des chaines de caractères
    $letitre = traite_chaine($_POST['letitre']);
    $ladesc = traite_chaine($_POST['ladesc']);
    
    // mise à jour du titre et du texte
    mysqli_query($mysqli,"UPDATE photo SET letitre='$letitre', ladesc='$ladesc' WHERE id = $idphoto");
    
    // supression dans la table photo_has_rubrique (sans l'utilisation de la clef étrangère)
    $sql2="DELETE FROM photo_has_rubriques WHERE photo_id = $idphoto";
    mysqli_query($mysqli,$sql2);
    
    // vérification de l'existence des sections cochées dans le formulaire
            if(isset($_POST['section'])){
            foreach($_POST['section'] AS $clef => $valeur){
                if(ctype_digit($valeur)){
                    // insertion dans la table photo_has_rubrique
                    mysqli_query($mysqli,"INSERT INTO photo_has_rubriques VALUES ($idphoto,$valeur);")or die(mysqli_error($mysqli));
                }
            }
            }
            header("Location: membre.php");
}


// récupérations des images de l'utilisateur connecté dans la table photo avec leurs sections même si il n'y a pas de sections sélectionnées (jointure externe avec LEFT)
$sql = "SELECT p.*, GROUP_CONCAT(r.id) AS idrub, GROUP_CONCAT(r.lintitule SEPARATOR '|||' ) AS lintitule
    FROM photo p
	LEFT JOIN photo_has_rubriques h ON h.photo_id = p.id
    LEFT JOIN rubriques r ON h.rubriques_id = r.id
        WHERE p.utilisateur_id = ".$_SESSION['id']." 
            AND p.id = $idphoto
        GROUP BY p.id
        ORDER BY p.id DESC;
    ";
$recup_sql = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

$recup_photo = mysqli_fetch_assoc($recup_sql);

// récupération de toutes les rubriques pour le formulaire d'insertion
$sql="SELECT * FROM rubriques ORDER BY lintitule ASC;";
$recup_section = mysqli_query($mysqli, $sql);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="style.css">
        <title></title>
    </head>
    <body>
        	<nav>
        <ul>
             <li><a href="index.php">Accueil</a></li>            
             <li><a>Catégories</a>
                    <ul>         
                        <?php
                        $sqlrub = "SELECT * FROM rubriques";
    $queryrub = mysqli_query($mysqli,$sqlrub);


    while($row = mysqli_fetch_assoc($queryrub))
    {
    echo "<li><a href='categories.php?idsection=".$row['id']."'>".$row['lintitule']."</a></li>";
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
             <div>
                 
                <form action="" method="POST" name="">
                    <input type="text" name="letitre" value="<?php echo $recup_photo['letitre'] ?>" required /><br/>
 
                    <textarea name="ladesc"><?php echo $recup_photo['ladesc'] ?></textarea><br/>
                    
                    <input type="submit" value="Envoyer le fichier" /><br/>
                    Sections : <?php
                    
                    // récupération des sections de l'image dans un tableau
                    $recup_sect_img = explode(',',$recup_photo['idrub']);
                    
                    
                    // affichage des sections
                    while($ligne = mysqli_fetch_assoc($recup_section)){
                        if(in_array($ligne['id'], $recup_sect_img)){
                            $box = "checked";
                        }else{
                            $box = "";
                        }
                        echo $ligne['lintitule']." : <input type='checkbox' name='section[]' value='".$ligne['id']."' $box > - ";
                    }
                    
                    echo "<br/><img src='".CHEMIN_RACINE.$dossier_mini.$recup_photo['idrub'].".jpg' alt='' />";
                    ?>
                </form>
            </div>
    </body>
</html>
