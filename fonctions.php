<?php

require_once 'config.php';


function upload_originales($fichier,$destination,$ext){
    
    $sortie = array();
   $nom_origine = $fichier['name'];
   $extension_origine = substr(strtolower(strrchr($nom_origine,'.')),1);
   if(!in_array($extension_origine,$ext)){
        
        return "Erreur : Extension non autorisée";
        
    }
    if($extension_origine==="jpeg"){ $extension_origine = "jpg"; }
    
    $nom_final = date("YmdHis").chaine_hasard(36);
    
    $sortie['poids'] = filesize($fichier['tmp_name']);
    $sortie['largeur'] = getimagesize($fichier['tmp_name'])[0];
    $sortie['hauteur'] = getimagesize($fichier['tmp_name'])[1];
    $sortie['nom'] = $nom_final;
    $sortie['extension'] = $extension_origine;
    

    if(@move_uploaded_file($fichier['tmp_name'], $destination.$nom_final.".".$extension_origine)){
        return $sortie;
    }else{
        return "Erreur lors de l'upload d'image";
    }
    
}

function nom_hasard($nb_hasard){
    $ladate=date("YmdHis");
    for($i=0;$i<$nb_hasard;$i++){  
        if($i==0){
            $debut="1";
            $fin="9";
        }else{
        
        $debut.="0";
        $fin.="9";
        }
    }
    
    $hasard=mt_rand($debut,$fin);

    return $ladate.$hasard;
}
function chaine_hasard($nombre_caracteres){
    $caracteres = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,0,1,2,3,4,5,6,7,8,9";
    $tableau = explode(",", $caracteres);
    $nb_element_tab = count($tableau);
    $sortie ="";
    for($i=0;$i<$nombre_caracteres;$i++){
        $hasard = mt_rand(0, $nb_element_tab-1);
        $sortie .= $tableau[$hasard];
    }
    return $sortie;
}


function traite_chaine($chaine){
    $sortie = htmlentities(strip_tags(trim($chaine)),ENT_QUOTES);
    return $sortie;
}

function creation_img($chemin_org, $nom,$extension,$destination,$largeur_max,$hauteur_max,$qualite, $proportion = true){
    
   $chemin_image = $chemin_org.$nom.'.'.$extension;
    
    $param_image = getimagesize($chemin_image);
    
    $largeur_org = $param_image[0];
    $hauteur_org = $param_image[1];
    
    $ratio_l = $largeur_org / $largeur_max;
    $ratio_h = $hauteur_org / $hauteur_max;

    
    switch ($extension){
        case 'jpg':
            $image_finale = imagecreatefromjpeg($chemin_image);
            
            break;
        
        case 'png':
            $image_finale = imagecreatefrompng($chemin_image);

            break;
        default:
            return false;
    }
    
    if($proportion==true){
    
    if($ratio_l>$ratio_h){
        
        if($ratio_l < 1){
            $largeur_dest = $largeur_org;
            $hauteur_dest = $hauteur_org;
        }else{
            $largeur_dest = $largeur_max;
            $hauteur_dest = round($hauteur_org/$ratio_l);
        }
        
    }else{
        if($ratio_h < 1){
            $largeur_dest = $largeur_org;
            $hauteur_dest = $hauteur_org;
        }else{
            $largeur_dest = round($largeur_org/$ratio_h);
            $hauteur_dest = $hauteur_max;
        }
    }    
        
    $nouvelle_image = imagecreatetruecolor($largeur_dest, $hauteur_dest);

    
   imagecopyresampled($nouvelle_image, $image_finale, 0, 0, 0, 0, $largeur_dest, $hauteur_dest, $largeur_org, $hauteur_org);

    
    }else{
    
        if($ratio_l>$ratio_h){
        

            $largeur_dest = round($largeur_org/$ratio_h);
            $hauteur_dest = $hauteur_max;
            $centre_large = round(($largeur_dest-$largeur_max)/2);
            $centre_haut = 0;
            
        
    }else{
    
            $largeur_dest = $largeur_max;
            $hauteur_dest = round($hauteur_org/$ratio_l);
            $centre_large = 0;
            $centre_haut = round(($hauteur_dest-$hauteur_max)/2);
            
        }
     
    $img_temp = imagecreatetruecolor($largeur_dest, $hauteur_dest);    
   imagecopyresampled($img_temp, $image_finale, 0, 0, 0, 0, $largeur_dest, $hauteur_dest, $largeur_org, $hauteur_org);    
        
        
    
    $nouvelle_image = imagecreatetruecolor($largeur_max, $hauteur_max);    
   imagecopyresampled($nouvelle_image, $img_temp, 0, 0, $centre_large, $centre_haut, $largeur_dest, $hauteur_dest, $largeur_dest, $hauteur_dest);    
   imagedestroy($img_temp);
    }
    imagejpeg($nouvelle_image, $destination.$nom.'.jpg', $qualite);
    imagedestroy($nouvelle_image);

    
    return true;
}


function pagination($total, $page_actu = 1, $par_pg = 3, $var_get = "pgs") {
    $nombre_pg = ceil($total / $par_pg);
    if ($nombre_pg > 1) {
        $sortie = "Page ";
        for ($i = 1; $i <= $nombre_pg; $i++) {
            if ($i == 1) {
                if ($i == $page_actu) {
                    $sortie.= "<< < ";
                } else {
                    $sortie.= "<a href='?$var_get=$i'><<</a> <a href='?$var_get=" . ($page_actu - 1) . "'><</a> ";
                }
            }
            if ($i != $page_actu) {
                $sortie .= "<a href='?$var_get=$i'>$i</a>";
            } else {
                $sortie .= " $i ";
            }
            if ($i != $nombre_pg) {
                $sortie.= " - ";
            } else {
                if ($i == $page_actu) {
                    $sortie.=" > >>";
                } else {
                    $sortie.= " <a href='?$var_get=" . ($page_actu + 1) . "'>></a> <a href='?$var_get=$nombre_pg'>>></a> ";
                }
            }
        }
        return $sortie;
    } else {
        return "Page 1";
    }
}
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

