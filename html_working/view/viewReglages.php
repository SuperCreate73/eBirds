<?php
$nom_hote = gethostname();
$title = 'Mon nichoir: Réglages et paramètres';
$styles = '<link href="public/css/styleReglages.css" rel="stylesheet" type="text/css" />';
$javaScripts = 	'<script src="public/js/assistants_reglages.js"></script>';

?>


<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

<h1>Réglages</h1>
<!--div id="accordion"-->
<form method="POST" action="index.php?page=reglages&action=doreglages">


	<!-- ############################################################################# -->
	<!-- ############################################################################# -->
	<!-- #########  Cadre de réglages système  ####################################### -->
	<!-- ############################################################################# -->
	<!-- ############################################################################# -->

	<div class="cadre">

		<div class="row spaced">
			<div class="eight columns">
				<h2>Système</h2>
			</div>

			<div class="offset-by-four eight columns">
				<div id="btn_shutdown" class='my_button' value="Shutdown" title="Shutdown" onclick="shutdown()"></div>
				<div id="btn_restart" class='my_button' value="Redémarrer" title="Reboot" onclick="reboot()"></div>
				<div id="btn_update" class='my_button' value="Mise à jour logiciels" title="Software update" onclick="upgrade()"></div>
				<div id="btn_upgrade" class='my_button' value="Mise à jour système" title="OS update" onclick="distupgrade()"></div>
				<!--input class="button u-full-width boutonLogin" value="Redémarrer" onclick="reboot()"-->
			</div>
		</div>

		<div class="row">
			<div class="offset-by-two columns">
				<div class="two columns">
					<h3>Nom :</h3>
				</div>

				<div class="seven columns">
					<input class="u-full-width" type="text" placeholder="<?php echo $nom_hote ?>" id="nom_nichoir">
				</div>

				<div class="one column">
					<div id="save_name" class='my_button save' value="Enregistrer" onclick="changerNom()"></div>
					<!--input class="button u-full-width boutonLogin" value="Modifier"-->
				</div>
			</div>
		</div>

		<!-- ############################# -->
		<!-- identifiant -->
		<!-- ############################# -->
		<div class="row">
			<div class="offset-by-two columns">
				<div class="two columns">
					<h3>Identifiant :</h3>
				</div>

				<div class="seven columns">
					<input class="u-full-width" type="text" placeholder="Identifiant" name="id">
				</div>

				<div class="one column">
					<div id="save_id" class='my_button save' value="Enregistrer id" onclick="save_id()"></div>

					<!--input class="button u-full-width boutonLogin" value="Modifier"-->
				</div>
			</div>
		</div>

		<!-- ############################# -->
		<!-- gestion des utilisateurs -->
		<!-- ############################# -->
		<div class="row">
			<div class="offset-by-two columns">
				<div class="seven columns">
					<h3>Utilisateurs :</h3>
				</div>

				<div class="three columns">
					<div id="add_user" class='my_button' value="ajouter utilisateur" onclick="addUser()"></div>
					<!--input class="button u-full-width boutonLogin" value="Ajouter" onclick="addUser()"-->
				</div>
			</div>
		</div>

		<?php foreach ($users as $key => $userItem) { ?>
			<div class='row'>
				<div class='offset-by-four columns' id='<?= $userItem[0] ; ?>' >
					<div class='seven columns'><?= $userItem[0] ; ?></div><div class="my_button deleteUser" onclick="removeUser('<?= $userItem[0]; ?>');">
					</div>
				</div>
			</div>
		<?php } ?>

	</div>

	<!-- ############################################################################# -->
	<!-- ############################################################################# -->
	<!-- #########  Cadre de la Localisation  ######################################## -->
	<!-- ############################################################################# -->
	<!-- ############################################################################# -->

	<div class="cadre">
		<div class="row">
			<div class="two columns">
				<h2>Localisation</h2>
			</div>

			<div class="offset-by-nine three columns">
				<h3>Localisation</h3><input class="button" type="button" value="Me géolocaliser" onclick="geolocaliser();">
			</div>
		</div>

		<div class="row">
			<div class="offset-by-two columns">
				<div class="seven columns">

					<label>Rue et numéro</label>
					<div class="row">
						<div class="nine columns">
							<input id="street" class="u-full-width" type="text" value="<?= ($locationInterface -> street) ?>" name="street">
						</div>
						<div class="three columns">
							<input id="houseNumber" class="u-full-width" type="text" value="<?= ($locationInterface -> houseNumber) ?>" name="houseNumber">
						</div>


				</div>
					<div class="row">
						<div class="four columns">
							<label>C.P.</label>
							<input id="postalCode" class="u-full-width" type="text" value="<?= ($locationInterface -> postalCode) ?>" name="postalCode">
						</div>

						<div class="eight columns">
							<label>Localité</label>
							<input id="city" class="u-full-width" type="text" value="<?= ($locationInterface -> city) ?>" name="city">
						</div>
					</div>
					<label>Pays</label>
					<input id="country" class="u-full-width" type="text" value="<?= ($locationInterface -> country) ?>" name="country">
				</div>

				<div class="three columns">
					<label>Latitude</label>
					<input id="latitude" class="u-full-width" type="text" value="<?= ($locationInterface -> xCoord) ?>" name="xCoord">
					<label>Longitude</label>
					<input id="longitude" class="u-full-width" type="text" value="<?= ($locationInterface -> yCoord) ?>" name="yCoord">
					<label>Altitude</label>
					<input class="u-full-width" type="text" value="<?= ($locationInterface -> zCoord) ?>" name="zCoord">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="offset-by-seven columns">
				<div class="five columns">
					<input class="button u-full-width boutonLogin" value="Enregistrer les modifications" type="submit">
				</div>
			</div>
		</div>
	</div>

	<!-- ############################################################################# -->
	<!-- ############################################################################# -->
	<!-- #########  Cadre des réglages caméra  ####################################### -->
	<!-- ############################################################################# -->
	<!-- ############################################################################# -->

	<div class="cadre">

		<div class="row">
			<div class="two columns">
				<h2>Caméra</h2>
			</div>
		</div>


		<div class="two columns">
			<h3>Général</h3>
		</div>
		<div class="row">
			<div class="five columns">
				<label for="imageSize">Dimensions de l'image:</label>
				<select class="u-full-width" id="imageSize" name="imageSize">
					<option value="low" <?= ($settingsInterface->imageSize == 'low') ? 'selected' : "" ?> >Petite</option>
					<option value="medium" <?= ($settingsInterface->imageSize == 'medium') ? 'selected' : "" ?> >Moyenne</option>
					<option value="high" <?= ($settingsInterface->imageSize == 'high') ? 'selected' : "" ?> >Grande</option>
				</select>
			</div>

			<div class="five columns">
				<label>Qualité de l'image (0-100):</label><div id="texteQualite" ><?= $settingsInterface -> quality ?></div>
				<input class="u-full-width" type="range" min="0" max="100" value="<?= $settingsInterface -> quality ?>" placeholder="<?= $settingsInterface -> quality ?>" name="quality" id="quality" oninput="updateQualite(this.value);">
			</div>
		</div>
		<hr style="border: .5px solid white;" />



		<div class="two columns">
			<h3>Détection de mouvement</h3>
		</div>
		<div class="row">
			<div class="five columns">
				<label for="modeCamera">Mode d'enregistrement images:</label>
				<select class="u-full-width" id="modeCamera" onchange="" name="imageTypeDetection">
					<option value="off" <?= ($settingsInterface -> imageTypeDetection=='off') ? 'selected' : "" ?> >Off</option>
					<option value="picture" <?= ($settingsInterface -> imageTypeDetection=='picture') ? 'selected' : "" ?> >Photo</option>
					<option value="video" <?= ($settingsInterface -> imageTypeDetection=='video') ? 'selected' : "" ?> >Video</option>
				</select>
			</div>
			<div class="five columns">
				<label>Quel pourcentage de l'image doit changer pour détecter un mouvement (5-99):</label><div id="texteDetection" ><?= $settingsInterface -> threshold ?></div>
				<input class="u-full-width" type="range" min="5" max="99" value="<?= $settingsInterface -> threshold ?>" placeholder="<?= $settingsInterface -> threshold ?>" name="threshold" id="threshold" oninput="updateDetection(this.value);">
			</div>

			<div class="five columns offset-by-seven">
				<label>Email de contact en cas de détection de mouvement:</label>
				<input class="u-full-width" type="text" value="<?= $settingsInterface -> on_motion_detected ?>" name="on_motion_detected" id="on_motion_detected" onchange="">
			</div>
		</div>


		<hr style="border: .5px solid white;" />

		<div class="two columns">
			<h3>Intervalles réguliers</h3>
		</div>


		<div class="row">

			<div class="five columns">
				<label for="modeCamera_tl">Mode d'enregistrement images:</label>
				<select class="u-full-width" id="modeCamera_tl" onchange="" name="imageTypeInterval">
					<option value="off" <?= ($settingsInterface -> imageTypeInterval=='off') ? 'selected' : "" ?> >Off</option>
					<option value="picture" <?= ($settingsInterface -> imageTypeInterval=='picture') ? 'selected' : "" ?> >Photo</option>
					<option value="video" <?= ($settingsInterface -> imageTypeInterval=='video') ? 'selected' : "" ?> >Video</option>
				</select>
			</div>

		<div class="five columns">
			<label for="ffmpeg_timelapse_mode">fréquence de sauvegarde du film:</label>
			<select class="u-full-width" id="ffmpeg_timelapse_mode" name="ffmpeg_timelapse_mode">
				<option value="hourly" <?= ($settingsInterface -> ffmpeg_timelapse_mode=='hourly') ? 'selected' : "" ?> >Toute les heures</option>
				<option value="daily" <?= ($settingsInterface -> ffmpeg_timelapse_mode=='daily') ? 'selected' : "" ?> >Tous les jours</option>
				<option value="weekly-sunday" <?= ($settingsInterface -> ffmpeg_timelapse_mode=='weekly-sunday') ? 'selected' : "" ?> >Toutes les semaines (dimanche)</option>
				<option value="weekly-monday" <?= ($settingsInterface -> ffmpeg_timelapse_mode=='weekly-monday') ? 'selected' : "" ?> >Toutes les semaines (lundi)</option>
				<option value="monthly" <?= ($settingsInterface -> ffmpeg_timelapse_mode=='monthly') ? 'selected' : "" ?> >Tous les mois</option>
			</select>
		</div>

		<div class="five columns offset-by-two">
			<label>Intervalle en seconde entre les prises de vue (0-3600):</label><div id="texteIntervalle" ><?= $settingsInterface -> snapshot_interval ?></div>
			<input class="u-full-width" type="range" min="0" max="3600" value="<?= $settingsInterface -> snapshot_interval ?>" placeholder="<?= $settingsInterface -> snapshot_interval ?>" name="snapshot_interval" id="snapshot_interval" oninput="updateSnapInterval(this.value);">
		</div>
	</div>

	<div class="row">
		<div class="offset-by-seven columns">
			<div class="five columns">
				<input class="button u-full-width boutonLogin" value="Enregistrer les modifications" type="submit">
			</div>
		</div>
	</div>
</div>

<!-- ############################################################################# -->
<!-- ############################################################################# -->
<!-- #########  Cadre des capteurs  ############################################## -->
<!-- ############################################################################# -->
<!-- ############################################################################# -->

	<div class="cadre">

		<div class="row spaced">
			<div class="eight columns">
				<h2>Capteurs</h2>
			</div>
		</div>


		<div class="row">
			<div class="settingFrame">
					<h3>Humidité / Température :</h3>
					<div class="rangeLine">
						<div class="verticalFrame">
							<label for="sensor_name" class="sensorElement">capteur à ajouter :</label>
							<select id="sensor_name" name="sensor_name" class="sensorElement">
								<option value="DHT-11" >DHT-11</option>
								<option value="DHT-22" >DHT-22</option>
								<option value="HX711" >HX711</option>
								<option value="SI-4523" >SI-4523</option>
							</select>
						</div>
						<div class="verticalFrame">
							<label for="sensor_location">position :</label>
							<select id="sensor_location" name="sensor_location">
								<option value="in" >intérieur</option>
								<option value="out" >extérieur</option>
							</select>
						</div>

						<div class="verticalFrame">
							<label for="pin_id">pin :</label>
							<input class="inputSensor" type="text" value="" name="pin_id" id="pin_id" onchange="">
						</div>

						<div class="verticalFrame">
							<label for="sensorNicName">nom :</label>
							<input class="inputSensor" type="text" value="" name="sensorNicName" id="sensorNicName" onchange="">
						</div>

						<div id="add_sensor" class="my_button" value="ajouter capteur" onclick="addSensor()"></div>
					</div>

			</div>

					<br>
					<br>
					<br>
					<div id="checkbox_dht11" class="checkbox toggleUnSelect" value="DHT11" title="DHT11 sensor" onclick="toggleSelectMode(this)"></div>
					<label for="checkbox_dht11">DHT11</label><br>
				  <input type="chekbox" id="DHT22" name="DHT22" value="DHT22">
				  <label for="DHT22"> DHT22</label><br>

					<div class="offset-by-seven columns">
						<div class="five columns">
							<input class="button u-full-width boutonLogin" value="Enregistrer les modifications" type="submit">
						</div>
					</div>

				<div class="three columns">
					<input type="checkbox" id="in11" name="in11" value="in11">
					<label for="in11"> intérieur </label>
				</div>

				<div class="one column">
					<div id="save_name" class='my_button save' value="Enregistrer" onclick="changerNom()"></div>
					<!--input class="button u-full-width boutonLogin" value="Modifier"-->
				</div>
		</div>

		<!-- ############################# -->
		<!-- identifiant -->
		<!-- ############################# -->
		<div class="row">
			<div class="offset-by-two columns">
				<div class="two columns">
					<h3>Identifiant :</h3>
				</div>

				<div class="seven columns">
					<input class="u-full-width" type="text" placeholder="Identifiant" name="id">
				</div>

				<div class="one column">
					<div id="save_id" class='my_button save' value="Enregistrer id" onclick="save_id()"></div>

					<!--input class="button u-full-width boutonLogin" value="Modifier"-->
				</div>
			</div>
		</div>

		<!-- ############################# -->
		<!-- gestion des utilisateurs -->
		<!-- ############################# -->
		<div class="row">
			<div class="offset-by-two columns">
				<div class="seven columns">
					<h3>Utilisateurs :</h3>
				</div>

				<div class="three columns">
					<div id="add_user" class='my_button' value="ajouter utilisateur" onclick="addUser()"></div>
					<!--input class="button u-full-width boutonLogin" value="Ajouter" onclick="addUser()"-->
				</div>
			</div>
		</div>

		<?php foreach ($users as $key => $userItem) { ?>
			<div class='row'>
				<div class='offset-by-four columns' id='<?= $userItem[0] ; ?>' >
					<div class='seven columns'><?= $userItem[0] ; ?></div><div class="my_button deleteUser" onclick="removeUser('<?= $userItem[0]; ?>');">
					</div>
				</div>
			</div>
		<?php } ?>

	</div>


 <!--    FIN CADRE V -->


</form>

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>
