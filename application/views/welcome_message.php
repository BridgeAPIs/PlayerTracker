<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Modo Tracker</title>
		<link href="//samagames.net/modo/assets/css/bootstrap.min.css" rel="stylesheet">
	</head>
	<body>
<div class="container">
<div class="well" style="margin-top: 20%;">
	<h1>Players Tracker</h1>
	<?php
	if (isset($error)) {
		?><div class="alert alert-danger">
		<b>Erreur !</b> <?php echo $error; ?></div><?php
	}
	?>
	<?php
	if (isset($success)) {
		?><div class="alert alert-success">
		<?php echo $success; ?></div><?php
	}
	?>
	<div class="strack">
		<p>Identifiez vous pour continuer (Utilisez la commande en jeu pour vous générer un mot de passe) :</p>
		<form action="<?php echo site_url('realm/login'); ?>" method="get">
			<input type="text" placeholder="Votre pseudo" name="username" class="form-control" /><br/>
			<input type="password" name="password" placeholder="Votre mot de passe (/modpass)" class="form-control" /><br/>
			<input type="submit" class="btn btn-success" value="Connexion" style="width: 20%; margin-left:40%" />
		</form>
	</div>
</div>
</div>
</body>
</html>