<?php 
include("colors.php");
$this->load->helper('url');
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Modo Tracker</title>
		<link href="<?php echo base_url("assets/css/bootstrap.min.css"); ?>" rel="stylesheet">
		<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.min.js"); ?>"></script> 
		<script type="text/javascript" src="<?php echo base_url("assets/js/bootstrap.min.js"); ?>"></script> 
	</head>
	<body>
<?php

function getIconType($sanction) {
	switch ($sanction) {
		case "Bannissement":
			return "glyphicon-ban-circle";
		case "Mute":
			return "glyphicon-volume-off";
		case "Kick":
			return "glyphicon-off";
		case "Remarque":
			return "glyphicon-tag";
	}
	return null;
}

function getIcon($sanction) {
	$type = getIconType($sanction);
	if ($type == null)
		return "";
	else
		return '<span class="glyphicon '.$type.'"> </span>';
}

?>
<?php $col = new MinecraftColors(); ?>
<div class="container">
<div class="well">

<h1>Casier du joueur <?php echo $pseudo; ?></h1>
<h2>Données de l'utilisateur : </h2>
<ul>
	<li>Nombre de coins : <b><?php echo $coins; ?></b></li>
	<li>Nombre de stars : <b><?php echo $stars; ?></b></li>
	<li>Dernière connexion : <b><?php echo $lastlogin; ?></b></li>
	<li>Temps de connexion total : <b><?php echo $logtime; ?></b></li>
</ul>

<table class="table table-striped table-bordered table-hovered">
	<thead>
		<tr>
				<th>Type</th>
				<th>Durée</th>
				<th>Raison</th>
				<th>Ajouté par</th>
				<th>Timecode</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$hasCase = FALSE;
			foreach ($case as $caseLine) {
				$data = json_decode($caseLine);
				if ($data == null) {
					?>
					<tr>
						<td colspan="5" class="danger"><?php echo $caseLine; ?></td>
						<td>
							<form class="form-inline" method="post">
							  	<input type="hidden" name="remove" value='<?php echo addslashes($caseLine); ?>' />
							  	<input type="hidden" name="uuid" value="<?php echo addslashes($uuid); ?>" />
							  	<input type="submit" class="btn btn-danger btn-xs" value="Supprimer" />
							</form>
						</td>
					</tr>
					<?php 
				} else {
					$reason = (isset($data->motif)) ? $data->motif : "Aucun motif fourni.";
					$reason = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $reason);
					$reason = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $reason);
					?>
					<tr>
						<td <?php if (!isset($data->duration)) echo 'colspan="2" style="text-align: center;"'; ?>><?php echo getIcon($data->type); ?> <?php echo $data->type; ?></td>
						<?php if (isset($data->duration)) {
							?><td><?php echo $data->duration; ?></td><?php
						} ?>
						<td><?php echo $reason; ?></td>
						<td><img src="<?php echo base_url("assets/heads.php?cache=16&size=16&name=".$data->addedBy); ?>" /> <?php echo $data->addedBy; ?></td>
						<td><?php echo date('l j F Y H:i:s', $data->timestamp/1000); ?></td>
					</tr>
					<?php
				}
				$hasCase = TRUE;
			}

			if (!$hasCase) {
				?>
				<tr class="success">
					<td colspan="5" style="text-align: center;">Le dossier du joueur est vide !</td>
				</tr>
				<?php 
			}
			?>
		</tbody>
	</table>
 
</div>
</div>
</body>
</html>