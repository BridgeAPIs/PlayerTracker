<?php 
include("colors.php");
$this->load->view("head"); ?>
	<?php
	if (isset($error)) {
		?><div class="alert alert-danger">
		<b>Erreur !</b> <?php echo $error; ?></div><?php
	}
	?>
	<div class="container">
	<div class="well">
	<div class="strack">
		<p>Entrez le pseudo recherché :</p>
		<form action="<?php echo site_url('lookup/results'); ?>" method="post" class="form-inline">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Pseudo de la cible" name="pseudo" class="form-control" />
			</div>
			<button type="submit" class="form-control" class="btn btn-success">Rechercher</button>
		</form>
	</div></div>
</div></div>


</body>
</html>