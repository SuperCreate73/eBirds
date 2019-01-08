<?php

require_once("model/FileManager.php");

class ImageFileManager extends FileManager {

//	private $_tempExt;

//	public function __construct() {
	
//		$this->setTempHum();
//		$this->setEntrees();
//		$this->setSorties();
//		$this->setVisites();
//	}
	
  // Liste des getters : fonctions permettant de récupérer les valeurs des attributs privés 
  
//  	public function tempExt() {return $this->_tempExt;}
//  	public function tempInt() {return $this->_tempInt;}
//  	public function humExt() {return $this->_humExt;}
//  	public function humInt() {return $this->_humInt;}
//		public function dateHeure() {return $this->_dateHeure;}
//		public function entrees() {return $this->_entrees;}
//		public function sorties() {return $this->_sorties;}
//		public function visites() {return $this->_visites;}

// Méthodes :
// Créer Miniatures
// Effacer Miniatures
//
	private function imageThumb( $image_src , $image_dest = NULL , $max_size = 100, $expand = FALSE, $square = FALSE ) {

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

}

