#!/bin/bash
# coding:UTF-8

motionPath="/etc/motion/motion.conf"
# configuration de motion
#------------------------
printMessage "paramétrage" "motion"
# mode démon par défaut quand motion est lancé dans la console
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?daemon \(on\|off\)/daemon on/g"

# fichier PID déplacé pour permission d'écriture
sudo sed "$motionPath" -i -e "s:^\(#\|;\)\? \?process_id_file *\(on\|off\):process_id_file /home/pi/.motion/motion.pid:g"

# dimensions de l'image en pixel
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?width [0-9]*/width 640/g" -e "s/^\(#\|;\)\? \?height [0-9]*/height 480/g"

# nom de la camera
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?mmalcam_name .*$/mmalcam_name vc.ril.camera/g"

# durée maximale des films en secondes
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?max_movie_time [0-9]*$/max_movie_time 100/g"

# enregistrements de films mis off
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?ffmpeg_output_movies on$/ffmpeg_output_movies off/g"

# target directory
sudo sed "$motionPath" -i -e "s:^\(#\|;\)\? \?target_dir .*$:target_dir /var/www/html/public/cameraShots:g"

# stream port
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?stream_port [0-9]*$/stream_port 9081/g"

# stream only for local host
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?stream_localhost on$/stream_localhost off/g"

# Output frames at 1 fps when no motion is detected and increase to stream_maxrate when motion is detected
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?stream_motion off$/stream_motion on/g"

# Maximum framerate for stream streams (default: 1)
sudo sed "$motionPath" -i -e "s/^\(#\|;\)\? \?stream_maxrate .*$/stream_maxrate 12/g"

# Script to launch on motion detection, commented by default
sudo sed "$motionPath" -i -e "s:^\(#\|;\)\? \?on_motion_detected .*$:; on_motion_detected /var/www/html/public/bash/motionSendMail.sh:g";

# configuration du démon
sudo sed "/etc/default/motion" -i -e "s/^start_motion_daemon=no/start_motion_daemon=yes/g"

printMessage "activation de motion" "motion"
sudo systemctl enable motion
printError "$?"
