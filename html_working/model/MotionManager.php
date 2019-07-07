<?php

class MotionManager {
	// Classe qui gère le software Motion : configuration, restart du  daemon
	//
	private $_script = 'public/bash/sendMail.sh' ;


	public function setAllSettings($inputArray) {
		$output = shell_exec('echo "MotionManager : '. json_encode($inputArray) .'" >> /var/www/debug.log');
		foreach ($inputArray as $key => $value)
		{
			if ($key == 'on_motion_detected')
			{
				$this -> setSendMail($value);
			}
			else
			{
				$this -> setSetting($key, $value);
			}
		}
	}

	public function setSetting ($setting, $value) {
		// fonction générique pour valider et changer les settings de motion
		//
		$shellCmd = 'sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?'.$setting.' .*$:'.$setting.' '.$value.':g"';
		$output = shell_exec($shellCmd);
	}


	public function setSendMail($email)
	// configure l'envoi d'e-mail en cas de détection de mouvements
	// Mail envoyé depuis l'adresse info@ebirds.be
	{
		if (! isset($email) || $email == "")
		// réinitialisation de l'adresse mail
		{
			$shellCmd = 'sudo sed "/var/www/html/public/bash/motionSendMail.sh" -i -e "s:^\(#\|;\)\? \?varMail=.*$:varMail= :g" ';
			$output = shell_exec($shellCmd);
			$shellCmd = 'sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?on_motion_detected .*$:; on_motion_detected email:g"';
			$output = shell_exec($shellCmd);
		}
		else
		{
			// modification du fichier 'motionSendMail.sh' -> ajout de l'adresse mail
			$shellCmd = 'sudo sed "/var/www/html/public/bash/motionSendMail.sh" -i -e "s:^\(#\|;\)\? \?varMail=.*$:varMail='.$email.':g" ';
			$output = shell_exec($shellCmd);
			// modification de motion
			$this -> setSetting('on_motion_detected', $email);
		}
	}


	public function restartMotion() {
		// redémarrage du daemon motion pour prendre en compte les modifySetting
		//
		$shellCmd = 'sudo /etc/init.d/motion restart';
		$output = shell_exec($shellCmd);
	}


	public function backUpMotion() {
		// copie du fichier motion.conf avant modification
		// teste également la présence du fichier de backup initial

		if ( ! file_exists ("/etc/motion/motion.conf.original")) {
			$shellCmd = 'sudo cp /etc/motion/motion.conf /etc/motion/motion.conf.original';
			$output = shell_exec($shellCmd);
			// copy ("/etc/motion/motion.conf", "/etc/motion/motion.conf.original");
		}
		$shellCmd = 'sudo cp /etc/motion/motion.conf /etc/motion/motion.conf.back';
		$output = shell_exec($shellCmd);
		// copy ("/etc/motion/motion.conf", "/etc/motion/motion.conf.back");
	}


	public function restoreMotion($origin=False) {
		// restaure les paramètres précédents du fichier motion.conf
		// ou du fichier original si $origin=True

		if ($origin) {
			$shellCmd = 'sudo cp /etc/motion/motion.conf.original /etc/motion/motion.conf';
			$output = shell_exec($shellCmd);
			// copy ("/etc/motion/motion.conf.original", "/etc/motion/motion.conf");
		}
		else {
			$shellCmd = 'sudo cp /etc/motion/motion.conf.back /etc/motion/motion.conf';
			$output = shell_exec($shellCmd);
			// copy ("/etc/motion/motion.conf.back", "/etc/motion/motion.conf");
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
