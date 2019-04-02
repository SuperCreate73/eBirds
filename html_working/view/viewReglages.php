<?php
	$nom_hote = gethostname();
	$title = 'Mon nichoir: Réglages et paramètres';
	$styles = '<link href="public/css/styleReglages.css" rel="stylesheet" type="text/css" />';
	$javaScripts = 	'<script src="public/js/assistants_reglages.js"></script>';

?>


<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<h1>Réglages</h1>
	<div id="accordion">
		<form method="POST" action="index.php?action=doreglages">
			<div class="cadre systeme">
				<div class="row spaced">
					<div class="eight columns">
						<h2>Système</h2>
					</div>

					<div class="eight columns">
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
						<div class='offset-by-four columns'>
							<div class='five columns'><?= $userItem[0] ; ?></div>
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
						<select class="u-full-width" id="modeNichoir">
							<option value="Option 1">Découverte</option>
							<option value="Option 2">Occupation</option>
						</select>
					</div>

					<div class="five columns">
						<label>Email(s) à contacter en cas d'occupation:</label>
						<input class="u-full-width" type="text" placeholder="email(s)" name="emails">
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
						<select class="u-full-width" id="modeCamera">
							<option value="Option 1">Photo</option>
							<option value="Option 2">Video</option>
						</select>
					</div>

					<div class="five columns">
						<label for="definitionCamera">Définition de l'image:</label>
						<select class="u-full-width" id="definitionCamera">
							<option value="Option 1">High</option>
							<option value="Option 2">Medium</option>
							<option value="Option 2">Low</option>
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
			</div> <!--    FIN CADRE V -->

			<div class="row">
				<div class="offset-by-seven columns">
					<div class="five columns">
						<input class="button u-full-width boutonLogin" value="Enregistrer les modifications" type="submit">
					</div>
				</div>
			</div>
		</form>
	</div>

<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>
