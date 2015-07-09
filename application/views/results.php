<?php 
include("colors.php");
$this->load->view("head"); 

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

<h1>Résultat de la recherche pour <?php echo $pseudo; ?></h1>
<ul class="nav nav-tabs">
  <li class=""><a aria-expanded="false" href="#user" data-toggle="tab">Utilisateur</a></li>
  <li class="active"><a aria-expanded="true" href="#case" data-toggle="tab">Dossier</a></li>
  <li class=""><a aria-expanded="false" href="#oldcase" data-toggle="tab">Dossier (ancien format)</a></li>
  <li class=""><a aria-expanded="false" href="#stats" data-toggle="tab">Statistiques</a></li>
  <li class=""><a aria-expanded="false" href="#shops" data-toggle="tab">Shops</a></li>
</ul>
<div id="tabContent" class="tab-content">
  <div class="tab-pane fade" id="user">
  	<h2>Données de l'utilisateur : </h2>
  	<ul>
		<li>Nombre de coins : <b><?php echo $coins; ?></b></li>
		<li>Nombre de stars : <b><?php echo $stars; ?></b></li>
		<li>Nombre d'amis : <b><?php echo $friends; ?></b></li>
		<li>Dernière connexion : <b><?php echo $lastlogin; ?></b></li>
		<li>Temps de connexion total : <b><?php echo $logtime; ?></b></li>
		<li>UUID du joueur : <b><?php echo $uuid; ?></b></li>
	</ul>
	<?php
	if ($history != null) {
	?>
		<h3>Historique des noms :</h3>
		<ul>
			<?php foreach ($history as $key => $name) {
				/*if ($key == 0)
					echo "<li>Pseudo initial : "..$name["name"].."</li>";
				else
					echo "<li>Pseudo $key : "..$name["name"]..", changé le "..date('l j F Y \à H:i:s', $name["changedToAt"]).."</li>";*/
				?>
				<li><?php if ($key == 0) { echo "Pseudo initial"; } else { $k = $key+1; echo $k."ème pseudo"; } ?> : <?php echo $name["name"]; ?></li>
				<?php
			} ?>
		</ul>
	<?php
	}
	?>
	
  </div>
  <div class="tab-pane fade active in" id="case">
<!--     private String addedBy = null;
    private String type = null;
    private String motif = null;
    private String duration = null;
    private Long timestamp = null; -->

    <br />

	<form class="form-inline" method="post" style="width: 100%;">
				  <div class="form-group" style="width: 100%;">
				    <div class="input-group" style="width: 100%;">
				    	<div class="input-group-addon" style="width: 10%;">Ajouter : </div>
				   		<input type="text" class="form-control" id="line" placeholder="Votre texte ici..." name="addtofile">
				   		<span class="input-group-btn" style="width: 10%;">
				  			<button type="submit" class="btn btn-success">Ajouter</button>
					    </span>
				   	</div>
				  </div>
				</form>

 <br />

    <table class="table table-striped table-bordered table-hovered">


		<thead>
			<tr>
				<th>Type</th>
				<th>Durée</th>
				<th>Raison</th>
				<th>Ajouté par</th>
				<th>Timecode</th>
				<th><span class="glyphicon glyphicon-trash"></span></th>
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
						<td><img src="http://samagames.net/modo/assets/heads.php?cache=16&size=16&name=<?php echo $data->addedBy; ?>" /> <?php echo $data->addedBy; ?></td>
						<td><?php echo date('l j F Y H:i:s', $data->timestamp/1000); ?></td>
						<td>
							<form class="form-inline" method="post">
							  	<input type="hidden" name="remove" value='<?php echo addslashes($caseLine); ?>' />
							  	<input type="hidden" name="uuid" value="<?php echo addslashes($uuid); ?>" />
							  	<button type="submit" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-trash"> </span></button>
							</form>
						</td>
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
  <div class="tab-pane fade" id="oldcase">

  				<table class="table table-striped table-bordered table-hovered">
					<thead>
						<tr>
						<th>Raison</th>
						<th>Auteur</th>
						<th>Timecode</th>
						<th>Points</th>
						</tr>
					</thead>
					<tbody>
				<?php
				foreach ($flags as $flag) {
					$split = explode(" -- ", $flag);
					$reason = $split[0];
					$byTime = substr($split[1], 16);
					$by = explode(" le ", $byTime)[0];
					$time = explode(" le ", $byTime)[1];
					$points = substr($split[2], 17);
					$reason = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $reason);
					$reason = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $reason);
					?>
					<tr>
						<td><?php echo $reason; ?></td>
						<td><?php echo $col->convertToHTML($by); ?></td>
						<td><?php echo $time; ?></td>
						<td><?php echo $points; ?></td>
					</tr>
					<?php
					// 17
				} 
				?></tbody>
				</table>
				
				<h3>Sanctions</h3>
				<table class="table table-striped table-bordered table-hovered">
					<thead>
						<tr>
						<th>Type</th>
						<th>Raison</th>
						<th>Durée</th>
						<th>Appliqué par</th>
						</tr>
					</thead>
					<tbody>
				<?php $col = new MinecraftColors(); ?>
				<?php 
				foreach ($sanctions as $flag) {
					try {
						$parts1 = explode(" d'une durée de ", $flag);
						$type = $parts1[0];
						$parts2 = explode(" appliquée par ", $parts1[1]);
						$time = $parts2[0];
						$parts3 = explode("RAISON : ", $parts2[1]);
						$by = $parts3[0];
						$motif = substr($parts3[1], 3);
						?>
						<tr>
							<td><?php echo $type; ?></td>
							<td><?php echo $motif; ?></td>
							<td><?php echo $time; ?></td>
							<td><?php echo $col->convertToHTML($by); ?></td>
						</tr> <?php
					} catch (Exception $e) {
						echo "<li>".$flag."</li>";
					}
				}
				?></tbody>
				</table>
  </div>
  <div class="tab-pane fade" id="stats">
    <h2>Statistiques</h2>
		<?php
		foreach ($stats as $key => $game) {
			echo "<h3>".$key."</h3>";
			echo "<ul>";
			foreach ($game as $stat => $val)
				echo "<li>".$stat." : <b>".$val."</b></li>";
			echo "</ul>";
		}
		?>
  </div>
  <div class="tab-pane fade" id="shops">
  	<h2>Shops</h2>
	<p>Ceci est la liste des items possédés par le joueur.</p>
	<?php
	foreach ($shops as $key => $game) {
		echo "<h3>".$key."</h3><ul>";
		foreach ($game as $k => $c) {
			echo "<li><b>".$k." : </b> ".join(", ", $c);
		}
		echo "</ul>";
	}
	?>  
  </div>
</div>
</div>
</div>
</body>
</html>