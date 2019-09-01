<?php
define('PUN_ROOT', '../../');
require PUN_ROOT.'include/common.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Tableau RL</title>
	<link rel="stylesheet" type="text/css" href="<?php echo PUN_ROOT.'style/'.$pun_user['style'].'.css' ?>" />
	<link rel="stylesheet" type="text/css" href="table_editor.css" />
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>

<div class="pun">
<div class="punwrap">
<div id="ezbbclink">

	<div class="rl-table-form">
		<select id="table_select">
			<option value="null">Que souhaitez-vous faire ?</option>
			<option value="new"> Créer un nouveau tableau (ajouté à la fin du post)</option>
		</select>
		<input type="button" value="Valider" id="saveBtn" />
		<input type="button" value="Annuler" id="cancelBtn" />
	</div>

	<div class="rl-table-container">
		<table class="rl-table edit"></table>
	</div>

	<div id="importModal" class="hidden">
		<p>Collez le tableau ci-dessous. Les tabulations seront transformées en colonnes automatiquement.</p>
		<p><textarea id="importText" cols="50" rows="7"></textarea></p>
		<p class="buttons">
			<input type="button" value="Importer" id="importModalBtn" />
			<input type="button" value="Annuler" id="cancelModalBtn" />
		</p>
	</div>

	<script type="text/javascript" src="table_editor.js"></script>
	<script type="text/javascript" src="table_form.js"></script>

</div>
</div>
</div>

</body>
</html>
