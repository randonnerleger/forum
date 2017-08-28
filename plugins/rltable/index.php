<!DOCTYPE html>
<html>
<head>
	<title>Tableau RL</title>
	<link rel="stylesheet" type="text/css" href="../../style/RL_Clair.css" />
	<link rel="stylesheet" type="text/css" href="table_editor.css" />
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>

<div class="pun">
<div class="punwrap">
<div id="brdmain">

	<h3><label for="table_select">Éditeur de tableau</label></h3>
	<p>
		<select id="table_select">
			<option value="null">Que souhaitez-vous faire ?</option>
			<option value="new"> Créer un nouveau tableau (ajouté à la fin du post)</option>
		</select>
	</p>

	<table class="rl-table edit"></table>

	<div id="importModal" class="hidden">
		<p>Collez le tableau ci-dessous. Les tabulations seront transformées en colonnes automatiquement.</p>
		<p><textarea id="importText" cols="50" rows="7"></textarea></p>
		<p class="buttons">
			<input type="button" value="Importer" id="importModalBtn" />
			<input type="button" value="Annuler" id="cancelModalBtn" />
		</p>
	</div>

	<p class="buttons">
		<input type="button" value="Valider" id="saveBtn" />
		<input type="button" value="Annuler" id="cancelBtn" />
	</p>

	<script type="text/javascript" src="table_editor.js"></script>
	<script type="text/javascript" src="table_form.js"></script>
</div>
</div>
</div>

</body>
</html>