<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<title>Modo Tracker</title>
		<link href="//samagames.net/modo/assets/css/bootstrap.min.css" rel="stylesheet">
		<script type="text/javascript" src="//samagames.net/modo/assets/js/jquery.min.js"></script> 
		<script type="text/javascript" src="//samagames.net/modo/assets/js/bootstrap.min.js"></script> 

		<style>
			background-image: {
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
			}
		</style>
	</head>
  <body>

	<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">SamaGames</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Résultats</span></a></li>
      </ul>
      <form class="navbar-form navbar-left" role="search" action="<?php echo site_url('lookup/results'); ?>" method="post">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Pseudo" name="pseudo">
        </div>
        <button type="submit" class="btn btn-primary">Aller</button>
      </form>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="<?php echo site_url('realm/logout'); ?>"><b class="glyphicon glyphicon-off"> </b> Déconnexion</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>