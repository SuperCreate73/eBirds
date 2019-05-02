<?php

class MotionManager {
	// Classe qui gère le software Motion : configuration, restart du  daemon
	//

//Attributs:
	private $_script = 'public/bash/sendMail.sh' ; //

	public function setSendMail($email) {
		// configure l'envoi d'e-mail en cas de détection de mouvements
		//
		//TODO ne fonctionne pas-> envoyer une requête en BASH (CURL) pour
		//		lancer la commande au serveur central d'envoyer un mail
		// 		Mail envoyé depuis l'adresse info@ebirds.be
		//		$output = shell_exec('sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?on_motion_detected *:on_motion_detected /home/pi/.motion/motion.pid:g"');

		//modification du fichier 'motionSendMail.sh' -> ajout de l'adresse mail
		$shellCmd='sudo sed "/var/www/html/public/bash/motionSendMail.sh" -i -e "s:^\(#\|;\)\? \?varMail \?.*$:varMail='.$email.':g"';
		$output = shell_exec($shellCmd);

		//modification du fichier 'motion.conf'
		$shellCmd='sudo sed "/etc/motion/motion.conf" -i -e "s:^\(#\|;\)\? \?on_motion_detected .*$:on_motion_detected /var/www/html/public/bash/motionSendMail.sh:g"';
		$output = shell_exec($shellCmd);
	}

	public function clearSendMail($email) {
		// annule l'envoi d'e-mail en cas de détection de mouvements
		//

		$shellCmd='sudo sed "/var/www/html/public/bash/motionSendMail.sh" -i -e "s:^\(#\|;\)\? \?varMail \?.*$:# varMail=eMailAContacter:g"';
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
