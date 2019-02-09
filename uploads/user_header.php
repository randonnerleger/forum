<?php
if ( pun_user_guest ) {

  header('HTTP/1.1 404 Not Found', true, 404);
  echo '
		<html>
		<head>
		<title>Redirection</title>
		<meta http-equiv="refresh" content="2;URL=' . folder_rl . '/forum/login.php" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="' . folder_rl . '/forum/style/RL_Clair.css?' . current_theme . '" />
		</head>
		<body>

		<div id="punredirect" class="pun">
		<!-- BEGIN HEADER RL -->
		<img src="' . folder_rl . '/tpl/img/logo.png" />
		<!-- END RL -->
		<div class="top-box"></div>
		<div class="punwrap">
		<div id="brdmain">
		<div class="block">
			<h2>Redirection</h2>
			<div class="box">
				<div class="inbox">
					<p>
					Vous devez être identifié au forum pour accéder à ces pages.<br />
					Redirection&#160;…<br /><br />
					<a href="' . folder_rl . '/forum/login.php">Cliquez ici si vous ne voulez pas attendre (ou si votre navigateur ne vous redirige pas automatiquement).</a>
					</p>
				</div>
			</div>
		</div>
		</div>
		</div>
		<div class="end-box"></div>
		</div>

		</body>
		</html>';
  exit;
}

echo '<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>'.($title ? escape($title) . ' - ' : '').$config->title.'</title>
    <meta name="viewport" content="width=device-width" />

<!-- BEGIN MODIF META RL -->';
require '../include/user/header_favicon.php';
require '../include/user/header_img_aleatoire.php';

echo '<link rel="stylesheet" type="text/css" href="' . path_to_forum . 'style/' . $pun_user['style'] . '.css?' . current_theme . '" />
</head>
<body id="uploads">

<!-- BEGIN HEADER RL -->';
require '../include/user/header.php';
require '../include/user/menuG.php';
