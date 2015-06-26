<?php
header('Content-Type:text/html;charset=UTF-8');
session_start();
require_once 'config.php';
require_once 'connect.php';
require_once 'fonctions.php';

if (isset($_POST['lelogin'])) {
    $lelogin = traite_chaine($_POST['lelogin']);
    $lemdp = traite_chaine($_POST['lepass']);

    // vérification de l'utilisateur dans la db
    $sql = "SELECT  u.id, u.lemail, u.lenom, 
		d.lenom AS nom_perm, d.laperm 
	FROM utilisateur u
		INNER JOIN droit d ON u.droit_id = d.id
    WHERE u.lelogin='$lelogin' AND u.lepass = '$lemdp';";
    $requete = mysqli_query($mysqli, $sql)or die(mysqli_error($mysqli));
    $recup_user = mysqli_fetch_assoc($requete);

    // vérifier si on a récupèré un utilisateur
    if (mysqli_num_rows($requete)) { // vaut true si 1 résultat (ou plus), false si 0

        // si l'utilisateur est bien connecté

        $_SESSION = $recup_user; // transformation des résultats de la requête en variable de session
        $_SESSION['sid'] = session_id(); // récupération de la clef de session
        $_SESSION['lelogin'] = $lelogin; // récupération du login (du POST après traitement)
        // var_dump($_SESSION);
        // redirection vers la page d'accueil (pour éviter les doubles connexions par F5)
        header('./contact.php');
    }
}

$recupmail = mysqli_query($mysqli,"SELECT lemail FROM utilisateur WHERE id=1");
$result = mysqli_fetch_assoc($recupmail);

if (isset($_POST)) {
    $nom = strip_tags(trim($_POST['nom']));
    $titre = strip_tags(trim($_POST['titre']));
    $mail = strip_tags(trim($_POST['lemail']));
    $texte = strip_tags(trim($_POST['lemessage']));
    $contactmail = $result['lemail'];
    $message = 'From: '.$mail;
    mail($contactmail, $nom, $titre, $texte, $message);
}


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
        
       
                    <div id="contact">
                        
                <form name="form" method="POST">
                <input name="nom" type="text" placeholder="Nom" required/><br/>
                <input name="titre" type="text" placeholder="Titre" /><br/>
                <input  name="lemail" type="email" placeholder="Votre adresse email" required /><br/>
                <textarea  name="lemessage" placeholder="Votre message" maxlength="500" required></textarea> <br/>
                <input type="submit" value="Envoyer"/>
                
            </form>
      </div>   
 
        
        
        
    </body>
</html>
