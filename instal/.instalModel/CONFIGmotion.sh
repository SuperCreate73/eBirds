#!/bin/bash
# coding:UTF-8

printMessage "Configuration de motion" "motion"

# Activation du module V4l2 pour que la camera PI soit reconnue par Motion
#-------------------------------------
if ! grep -e "^bcm2835-v4l2$" /etc/modules ; then
	echo 'bcm2835-v4l2' >> /etc/modules
fi

# création du fichier config local de motion
#cp /etc/motion/motion.conf /usr/local/etc/motion.conf
#printError "$?"

# chmod 666 /var/log/motion/motion.log
# printError "$?"

motionPath="/etc/motion/motion.conf"

# modification de motion.conf
source "$varInstalPath/.instalModel/CONFIGmotionConf.sh"

# configuration du démon
sed "/etc/default/motion" -i -e "s/^start_motion_daemon=no/start_motion_daemon=yes/g"

# configuration du démon
printMessage "activation de motion" "motion"
systemctl enable motion
printError "$?"
