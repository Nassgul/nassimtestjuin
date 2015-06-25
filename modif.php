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
    $sql2="DELETE FROM photo_has_rubrique WHERE photo_id = $idphoto";
    mysqli_query($mysqli,$sql2);
    
    // vérification de l'existence des sections cochées dans le formulaire
            if(isset($_POST['section'])){
            foreach($_POST['section'] AS $clef => $valeur){
                if(ctype_digit($valeur)){
                    // insertion dans la table photo_has_rubrique
                    mysqli_query($mysqli,"INSERT INTO photo_has_rubrique VALUES ($idphoto,$valeur);")or die(mysqli_error($mysqli));
                }
            }
            }
            header("Location: membre.php");
}


// récupérations des images de l'utilisateur connecté dans la table photo avec leurs sections même si il n'y a pas de sections sélectionnées (jointure externe avec LEFT)
$sql = "SELECT p.*, GROUP_CONCAT(r.id) AS idrub, GROUP_CONCAT(r.lintitle SEPARATOR '|||' ) AS lintitule
    FROM photo p
	LEFT JOIN photo_has_rubrique h ON h.photo_id = p.id
    LEFT JOIN rubrique r ON h.rubrique_id = r.id
        WHERE p.utilisateur_id = ".$_SESSION['id']." 
            AND p.id = $idphoto
        GROUP BY p.id
        ORDER BY p.id DESC;
    ";
$recup_sql = mysqli_query($mysqli,$sql) or die(mysqli_error($mysqli));

$recup_photo = mysqli_fetch_assoc($recup_sql);

// récupération de toutes les rubriques pour le formulaire d'insertion
$sql="SELECT * FROM rubrique ORDER BY lintitle ASC;";
$recup_section = mysqli_query($mysqli, $sql);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
         <div id="content">
             <div id="haut"><h1>Modifier la photo <a href="./">photos.be</a></h1> 
                <div id="connect"><?php // texte d'accueil
                        echo "<h3>Bonjour ".$_SESSION['lenom'].'</h3>';
                        echo "<p>Vous êtes connecté en tant que <span title='".$_SESSION['ladesc']."'>".$_SESSION['nom_perm']."</span></p>";
                        echo "<h5><a href='deconnect.php'>Déconnexion</a></h5>";
                        
                        // liens  suivant la permission utilisateur
                        switch($_SESSION['laperm']){
                            // si on est l'admin
                            case 0 :
                               echo "<a href='admin.php'>Administrer le site</a> - <a href='membre.php'>Espace membre</a>";
                                break;
                            // si on est modérateur
                            case 1:
                                echo "<a href='modere.php'>Modérer le site</a> - <a href='membre.php'>Espace membre</a>";
                                break;
                            // si autre droit (ici simple utilisateur)
                            default :
                                echo "<a href='membre.php'>Espace membre</a>";
                        }?></div>
            </div>
             <div id="milieu">
                 <div id="formulaire">
                <form action="" method="POST" name="onposte">
                    <input type="text" name="letitre" value="<?php echo $recup_photo['letitre'] ?>" required /><br/>
 
                    <textarea name="ladesc"><?php echo $recup_photo['ladesc'] ?></textarea><br/>
                    
                    <input type="submit" value="Envoyer le fichier" /><br/>
                    Sections : <?php
                    
                    // récupération des sections de l'image dans un tableau
                    $recup_sect_img = explode(',',$recup_photo['idrub']);
                    
                    
                    // affichage des sections
                    while($ligne = mysqli_fetch_assoc($recup_section)){
                        if(in_array($ligne['id'], $recup_sect_img)){
                            $coche = "checked";
                        }else{
                            $coche = "";
                        }
                        echo $ligne['lintitle']." : <input type='checkbox' name='section[]' value='".$ligne['id']."' $coche > - ";
                    }
                    echo "<br/><img src='".CHEMIN_RACINE.$dossier_mini.$recup_photo['lenom'].".jpg' alt='' />";
                    ?>
                </form>
            </div>
    </body>
</html>
