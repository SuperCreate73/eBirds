<?php

function setFocus($position) {

	$tabFocus[0]='prems menuitem';
	for ($nbre = 1; $nbre<=2; $nbre++) {
		$tabFocus[$nbre]='menuitem';
	}
	$tabFocus[3]='der menuitem';
	$tabFocus[$position].=' selected';
	for ($nbre = 0; $nbre<=3; $nbre++) {
		$tabFocus[$nbre]="'".$tabFocus[$nbre]."'";
	}
	return $tabFocus;
}

function scanDirectory($directory,$limit=100){
// fonction qui renvoie un tableau des fichiers du répertoire en paramètre 
	$files = array_filter(scandir($directory), function($file) { return !($file=='.' || $file =='..'); });
	return $files;
}
 
function layoutPane($arrayData,$colnum){
	// fonction de mise en forme en '$colnum' nombre de colonne
	//
	$arrayLayout = array();
	for ($iter=1; $iter<=$colnum; $iter++) {
		$arrayLayout[]= " ";
	}
	foreach ($arrayData as $key => $value) {
		$arrayColumn = $key % $colnum;
		$arrayLayout[$arrayColumn]=layoutDisplay($arrayLayout[$arrayColumn],$value);
	}
	return $arrayLayout;

}

function layoutDisplay($strInput,$addString) {
	$strInput .= PHP_EOL;
	return ($strInput.'<div class="fileList">'.$addString.'</div>');
}

//-----------------------------------------------------------------------------------------------------------------------------
function imagethumb( $image_src , $image_dest = NULL , $max_size = 100, $expand = FALSE, $square = FALSE ) {

    if (!file_exists($image_src)) return FALSE;
	
	// Récupère les infos de l'image
    $fileinfo = getimagesize($image_src);

    if (!$fileinfo) return FALSE;

    $width     = $fileinfo[0];
    $height    = $fileinfo[1];
    $type_mime = $fileinfo['mime'];
    $type      = str_replace('image/', '', $type_mime);

    if (!$expand && max($width, $height)<=$max_size && (!$square || ($square && $width==$height) ) ) {
        // L'image est plus petite que max_size
        if($image_dest) {
            return copy($image_src, $image_dest);
        }
        else {
            header('Content-Type: '. $type_mime);
            return (boolean) readfile($image_src);
        }
    }

    // Calcule les nouvelles dimensions
    $ratio = $width / $height;
    if( $square ) {
        $new_width = $new_height = $max_size;
        if( $ratio > 1 ) {
            // Paysage
            $src_y = 0;
            $src_x = round( ($width - $height) / 2 );
            $src_w = $src_h = $height;
        }
        else {
            // Portrait
            $src_x = 0;
            $src_y = round( ($height - $width) / 2 );
            $src_w = $src_h = $width;
        }
    }
    else {
        $src_x = $src_y = 0;
        $src_w = $width;
        $src_h = $height;
        if ( $ratio > 1 ) {
            // Paysage
            $new_width  = $max_size;
            $new_height = round( $max_size / $ratio );
        }
        else {
            // Portrait
            $new_height = $max_size;
            $new_width  = round( $max_size * $ratio );
        }
    }

    // Ouvre l'image originale
    $func = 'imagecreatefrom' . $type;
    if( !function_exists($func) ) return FALSE;
    $image_src = $func($image_src);
    $new_image = imagecreatetruecolor($new_width,$new_height);

    // Gestion de la transparence pour les png
    if( $type=='png' ) {
        imagealphablending($new_image,false);
        if( function_exists('imagesavealpha') ){
            imagesavealpha($new_image,true);
		}
    }

    // Gestion de la transparence pour les gif
    elseif( $type=='gif' && imagecolortransparent($image_src)>=0 ) {
        $transparent_index = imagecolortransparent($image_src);
        $transparent_color = imagecolorsforindex($image_src, $transparent_index);
        $transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($new_image, 0, 0, $transparent_index);
        imagecolortransparent($new_image, $transparent_index);
    }

    // Redimensionnement de l'image
    imagecopyresampled($new_image, $image_src, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h );

    // Enregistrement de l'image
    $func = 'image'. $type;
    if($image_dest){
        $func($new_image, $image_dest);
    }
    else {
        header('Content-Type: '. $type_mime);
        $func($new_image);
    }
	
	// Libération de la mémoire
    imagedestroy($new_image);
    return TRUE;
}




//-----------------------------------------------------------------------------------------------------------------------------
<?php
 
    // On indique au script ou se trouve les images a lister par rapport a l emplacement de ce script
    $dir = opendir("photos/Divers/humour/");
 
     // remplacer 10 par le nombre d'images par page souhaité
     $nbimages = 102;
 
 
// On donne le nom du dossier ou sont présente les images par rapport au script en précisant l extension des fichier à lister
$images_sur_le_serveur = glob("photos/Divers/humour/*.jpg");
 
// On compte le nombre d image
$combien_d_images_sur_le_serveur = count($images_sur_le_serveur) + 2;  
 
echo '<p align="center">';
 
// On déclare la pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($_GET['page'] == "") { $_GET['page'] = 1; }
$debut = ($_GET['page'] - 1)  * $nbimages + 2;
$i = $debut;
$j = 1;
 
// remplacer par la fonction scandir	
     while ($Fichier = readdir($dir))
     {
     $files[] = $Fichier;
 
     }

	// remplacer par une boucle for
	 // for ($conteur=($page-1)*$FilesParPage;$conteur-($page-1)*$FilesParPage<$Filesparpage;$conteur++)
     while ($i >= $debut and $j <= $nbimages)
     {
         if ( $files[$i] != ".." && $files[$i] != "." && $files[$i] != "" && ereg("(.jpg)",$files[$i]) )
         {
 
         echo "<img src='photos/Divers/humour/$files[$i]' border=\"0\">";
 
         }
     $i++;
     $j++;
 
     }
 
 echo '</p>';
 
 
 
 
echo '<p align="center">';
 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////Pagination des résultats/////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
    $derniere_page = ceil($combien_d_images_sur_le_serveur / $nbimages);
    if ($page > 1) {
        echo '<a href="'.$_SERVER['PHP_SELF'].'" >Début</a>&nbsp;-&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?page=' . ($page - 1) . '" >Précédent</a>';
    }
 
// MAX_NB_PAGE est égal au nombre de lien vers les pages a afficher au maximum (ex: pour 10 pages a afficher il y aura <<<< - 1 - 2 - 3 - 4 - 5 - 6 - >>>>)
define('MAX_NB_PAGES', 10); // Nombre maximal de pages apparaissant pour la navigation    
for ($i = max(1, min(max($page - MAX_NB_PAGES / 2, 1), $derniere_page - MAX_NB_PAGES)), $j = 0; $j <= MAX_NB_PAGES && $i <= $derniere_page; $i++, $j++) {
 
 if ($i == $page) {
        if ($page > 1) {echo '&nbsp;-&nbsp;'; }
            echo '' . $i . '';
        } else {
            echo '&nbsp;-&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?page=' . $i . '" >' . $i . '</a>';
        }
    }
    if ($page < $derniere_page) {
        echo '&nbsp;-&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?page=' . ($page + 1) . '" >Suivant</a>&nbsp;-&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?page=' . ($derniere_page) . '" >Fin</a>';
    }
 
 
    echo '</p>';
 
     closedir($dir);
?>
