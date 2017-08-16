<!DOCTYPE html>
<html>
<head>
	<title>Tableau RL</title>
	<link rel="stylesheet" type="text/css" href="../../style/RL_Clair.css" />
	<link rel="stylesheet" type="text/css" href="table_editor.css" />
</head>

<body>

<div class="pun">
<div class="punwrap">
<div id="brdmain">

	<h3><label for="table_select">Tableau à modifier</label></h3>
	<p>
		<select id="table_select">
			<option value="null">Sélectionner un tableau existant ou créer un nouveau tableau</option>
			<option value="new">Nouveau tableau, ajouté à la fin du post</option>
		</select>
	</p>

	<table class="rl-table edit"></table>

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