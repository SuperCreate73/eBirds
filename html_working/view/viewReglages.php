<?php
	$nom_hote = gethostname();
	$title = 'Mon nichoir: Réglages et paramètres';
	$styles = '<link href="public/css/styleReglages.css" rel="stylesheet" type="text/css" />';
	$javaScripts = 	'<script src="public/js/assistants_reglages.js"></script>';

?>


<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<h1>Réglages</h1>
	<div id="accordion">
		<form method="POST" action="index.php?page=reglages&action=doreglages">
			<div class="cadre systeme">
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
				<?php } ?>

			</div> <!--    FIN CADRE II -->

			<div class="cadre">
				<div class="row">
					<div class="two columns">
						<h2>Mode</h2>
					</div>

					<div class="five columns">
						<label for="modeNichoir">Mode de fonctionnement du nichoir:</label>
						<select class="u-full-width" id="modeNichoir" onchange="changeMode(event);">
							<option value="Découverte">Découverte</option>
							<option value="Occupation">Occupation</option>
						</select>
					</div>

					<div class="five columns">
						<label for="imageSize">Dimensions de l'image:</label>
						<select class="u-full-width" id="imageSize" name="imageSize">
							<option value="low" <?php ($imageSize[0]=='low') ? selected : "" ?> >Petite</option>
							<option value="medium" <?php ($imageSize[0]=='medium') ? selected : "" ?> >Moyenne</option>
							<option value="high" <?php ($imageSize[0]=='high') ? selected : "" ?> >Grande</option>
						</select>
					</div>

					<div class="five columns">
						<label for="ffmpeg_timelapse_mode">fréquence de sauvegarde (timelapse):</label>
						<select class="u-full-width" id="ffmpeg_timelapse_mode" name="ffmpeg_timelapse_mode">
							<option value="hourly" <?php ($ffmpeg_timelapse_mode[0][0]=='hourly') ? 'selected' : "" ?> >Toute les heures</option>
							<option value="daily" <?php ($ffmpeg_timelapse_mode[0][0]=='daily') ? 'selected' : "" ?> >Tous les jours</option>
							<option value="weekly-sunday" <?php ($ffmpeg_timelapse_mode[0][0]=='weekly-sunday') ? 'selected' : "" ?> >Toutes les semaines (dimanche)</option>
							<option value="weekly-monday" <?php ($ffmpeg_timelapse_mode[0][0]=='weekly-monday') ? 'selected' : "" ?> >Toutes les semaines (lundi)</option>
							<option value="monthly" <?php ($ffmpeg_timelapse_mode[0][0]=='monthly') ? 'selected' : "" ?> >Tous les mois</option>
						</select>
					</div>

					<div class="five columns">
						<label>Intervalle en seconde entre les photos (0-3200):</label>
						<input class="u-full-width" type="number" min="0" max="3200" placeholder="<?= $ffmpeg_timelapse[0][0] ; ?>" name="ffmpeg_timelapse" id="ffmpeg_timelapse" onchange="">
					</div>

					<div class="five columns">
						<label>Qualité de l'image (0-100):</label>
						<input class="u-full-width" type="number" min="0" max="100" placeholder="<?= $quality[0][0] ; ?>" name="quality" id="quality" onchange="">
					</div>

					<div class="five columns">
						<label>Seuil de détection en % (5-50):</label>
						<input class="u-full-width" type="number" min="5" max="50" placeholder=<?= $threshold[0][0] ; ?> name="threshold" id="threshold" onchange="">
					</div>


					<div class="five columns">
						<label>Contact en cas de détection de mouvement:</label>
						<input class="u-full-width" type="text" placeholder="<?= $on_motion_detected[0][0] ; ?>" name="on_motion_detected" id="on_motion_detected" onchange="">
					</div>
				</div>
			</div> <!--    FIN CADRE II -->

			<div class="cadre">
				<div class="row">
					<div class="two columns">
						<h2>Caméra</h2>
					</div>

					<div class="five columns">
						<label for="modeCamera">Mode d'enregistrement images:</label>
						<select class="u-full-width" id="modeCamera" onchange="changeModeCamera(event);">
							<option value="Detection">Photo - Détection mouvement</option>
							<option value="Timelapse">Photo - Timelapse</option>
							<option value="Video">Video</option>
						</select>
					</div>

					<div class="five columns">
						<label for="definitionCamera">Définition de l'image:</label>
						<select class="u-full-width" id="definitionCamera" onchange = "changeDefinitionCamera(event);">
							<option value="High">High</option>
							<option value="Medium">Medium</option>
							<option value="Low">Low</option>
						</select>
					</div>
				</div>
			</div> <!--    FIN CADRE III -->

			<!--    FIN CADRE IV -->


			<div class="cadre">
				<div class="row">
					<div class="two columns">
						<h2>Général</h2>
					</div>

					<div class="ten columns">
						<h3>Localisation</h3>
					</div>
				</div>

				<div class="row">
					<div class="offset-by-two columns">
						<div class="seven columns">
							<label>Rue et numéro</label>
							<input class="u-full-width" type="text" placeholder="Rue" name="rue">
							<div class="row">
								<div class="four columns">
									<label>C.P.</label>
									<input class="u-full-width" type="text" placeholder="Code postal" name="CP">
								</div>

								<div class="eight columns">
									<label>Localité</label>
									<input class="u-full-width" type="text" placeholder="Localité" name="localite">
								</div>
							</div>
							<label>Pays</label>
							<input class="u-full-width" type="text" placeholder="Pays" name="pays">
						</div>

						<div class="three columns">
							<label>Latitude</label>
							<input class="u-full-width" type="text" placeholder="Latitude" name="latitude">
							<label>Longitude</label>
							<input class="u-full-width" type="text" placeholder="Longitude" name="longitude">
							<label>Altitude</label>
							<input class="u-full-width" type="text" placeholder="Altitude" name="altitude">
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


			</div> <!--    FIN CADRE V -->


		</form>
	</div>

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>
