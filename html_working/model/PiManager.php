<?php
class PiManager
// Classe regroupant les fonctions propres à la gestion du raspberry (sous linux) 
{
	public function reboot()
	{
	// Redémarrage du raspberry en envoyant une instruction 'reboot' au shell
		$output = shell_exec('sudo reboot 2>&1 > /dev/null');
		return $output;
	}

	public function shutdown()
	// Arrêt du raspberry en envoyant une instruction 'shutdown' au shell
	{
		$output = shell_exec('sudo shutdown now 2>&1 > /dev/null');
		return $output;
	}

	public function upgrade()
	// Arrêt du raspberry en envoyant une instruction 'shutdown' au shell
	{
		$output = shell_exec('sudo apt-get -y update && sudo apt-get -y upgrade');
		return $output;
	}

	public function distUpgrade()
	// Arrêt du raspberry en envoyant une instruction 'shutdown' au shell
	{
		$output = shell_exec('sudo apt-get -y update && sudo apt-get -y dist-upgrade');
		return $output;
	}
	public function changeName($nom)
	{
		//	TODO	$reboot à joindre à la string de sortie style :
		//	$output.' && '.$reboot  
		$output = shell_exec('sudo echo '.$nom.' > /etc/hostname');
		$reboot = $this->reboot();
		return $output;
	}
}
