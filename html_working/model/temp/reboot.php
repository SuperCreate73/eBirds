<?php
//$output = shell_exec('cd images_camera; rm -rf * 2>&1');
$output = shell_exec('sudo reboot 2>&1');
echo "<pre>$output</pre>";
?>
