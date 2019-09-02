<?php
if ( pun_user_guest ) {
	require_once('../../redirect.php');
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

echo '<link rel="stylesheet" type="text/css" href="' . path_to_forum . 'style/' . $pun_user['style'] . '.css?version=' . current_theme . '" />
</head>
<body id="uploads">

<!-- BEGIN HEADER RL -->';
require '../include/user/header.php';
require '../include/user/menuG.php';
