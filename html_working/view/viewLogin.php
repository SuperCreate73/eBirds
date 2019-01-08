
<?php 
	$title = 'Mon nichoir: Login';
	$styles ='<link rel="stylesheet" type="text/css" href="public/css/styleLogin.css">';
	$javaScripts = ''; 
?>

<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<h1>Login</h1>
	<form class="loginform cadre" action="index.php?page=loginVerify" method="POST">
		<div class="presentation" id="presentation">
			<?= $message; ?>
		</div>

		<div class="inputField">
			<input type="text" placeholder="Votre login" name="login">
		
			<input type="password" placeholder="Votre mot de passe" name="passe">
		</div>

		<div class="controls">
			<input type="submit" class="textButton" value="OK !">
		</div>
	</form>
	
<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>

