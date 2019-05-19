<?php

class MotionManager {
	// Classe qui gère le software Motion : configuration, restart du  daemon
	//

//Attributs:
	private $_script = 'public/bash/sendMail.sh' ; //
	// private $_settingsTable = array(
	// 	on_motion_detected => array('string', '/var/www/html/public/bash/motionSendMail.sh', 'comment'),
	// 	width => array('discreet', array(480, 640, 1280), 640),
	// 	threshold => array('range', array(0, 100), 10), //pourcentage - valeur à modifier avec la définition de l'image
	// 	height => array('discreet', array(360, 480, 960), 480),
	// 	quality => array('range', array(0, 100), 75),
	// 	ffmpeg_timelapse => array('range', array(0, 3600), 0),
	// 	ffmpeg_timelapse_mode => array('discreet', array('hourly', 'daily', 'weekly-sunday', 'weekly-monday', 'monthly'), 'daily'),
	// );

	public function setSetting ($setting, $value) {
		// fonction générique pour valider et changer les settings de motion
		//
		$shellCmd='sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?'.$setting.'.*$:'.$setting.' '.$value.':g"';
		$output = shell_exec($shellCmd);
	}

	public function setSendMail($key, $email) {
		// configure l'envoi d'e-mail en cas de détection de mouvements
		//
		// 		Mail envoyé depuis l'adresse info@ebirds.be
		//		$output = shell_exec('sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?on_motion_detected *:on_motion_detected /home/pi/.motion/motion.pid:g"');

		//modification du fichier 'motionSendMail.sh' -> ajout de l'adresse mail
		$shellCmd='sudo sed "/var/www/html/public/bash/motionSendMail.sh" -i -e "s:^\(#\|;\)\? \?varMail=.*$:varMail='.$email.':g"';
		$output = shell_exec($shellCmd);

		//modification du fichier 'motion.conf'
		// $shellCmd='sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?'.$key.'.*$:'.$key.' /var/www/html/public/bash/motionSendMail.sh:g"';
		// $output = shell_exec($shellCmd);
	}

	public function clearSendMail($email) {
		// annule l'envoi d'e-mail en cas de détection de mouvements
		//

		$shellCmd='sudo sed "/var/www/html/public/bash/motionSendMail.sh" -i -e "s:^\(#\|;\)\? \?varMail=.*$:varMail=\"\":g"';
		$output = shell_exec($shellCmd);

		//modification du fichier 'motion.conf'
		$shellCmd='sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?on_motion_detected .*$:; on_motion_detected value:g"';
		$output = shell_exec($shellCmd);
	}

	private function modifyConfig($file, $parameter, $value="", $comment=False) {
		// annule l'envoi d'e-mail en cas de détection de mouvements
		//

		$shellCmd='sudo sed "'. $file .'" -i -e "s:^\(#\|;\)\? \?'.$parameter.'.*$:'.$parameter.'='.$value.':g"';
		$output = shell_exec($shellCmd);

		//modification du fichier 'motion.conf'
		$shellCmd='sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?on_motion_detected .*$:; on_motion_detected value:g"';
		$output = shell_exec($shellCmd);
	}


	public function restartMotion() {
		// redémarrage du daemon motion pour prendre en compte les modifySetting
		//

		$shellCmd='sudo /etc/init.d/motion restart';
		$output = shell_exec($shellCmd);
	}

	public function backUpMotion() {
		// copie du fichier motion.conf avant modification
		// teste également la présence du fichier de backup initial

		if ( ! file_exists ("/etc/motion/motion.conf.original")) {
			copy ("/etc/motion/motion.conf", "/etc/motion/motion.conf.original");
		}
		copy ("/etc/motion/motion.conf", "/etc/motion/motion.conf.back");
	}

	public function restoreMotion($origin=False) {
		// restaure les paramètres précédents du fichier motion.conf
		// ou du fichier original si $origin=True

		if ($origin) {
			copy ("/etc/motion/motion.conf.original", "/etc/motion/motion.conf");
		}
		else {
			copy ("/etc/motion/motion.conf.back", "/etc/motion/motion.conf");
		}
	}

}
// medium    		high		low
//-------------------------
// width 640 -> 1280		480
// height 480 -> 960		360
//
// compression
// -----------
// 0 à 100
// quality 75 -> 0-100
//
// sensibilité (nbre de pixels qui doivent bouger pour détecter l'image)
//------------
// threshold 1500 -> en pourcentage 1-99 changer en même temps que les dimensions
//
// event_gap 60 -> temps en seconde entre 2 événements
//
//  a inverser pour switch photo - video/Timelapse
// -----------------------------------------------
//	output_pictures on
//  ffmpeg_output_movies off
//
// time lapse -> activer la video
// ----------
// # Use ffmpeg to encode a timelapse movie
// # Default value 0 = off - else save frame every Nth second
// ffmpeg_timelapse 0

// # The file rollover mode of the timelapse video
// # Valid values: hourly, daily (default), weekly-sunday, weekly-monday, monthly, manual
// ffmpeg_timelapse_mode daily
//
//
// a faire par défaut -->> Done
// ------------------
// # Output frames at 1 fps when no motion is detected and increase to the
// # rate given by stream_maxrate when motion is detected (default: off)
// stream_motion off	-> on
// # Maximum framerate for stream streams (default: 1)
// stream_maxrate 1 -> 12 sur le nichoir 1
//
