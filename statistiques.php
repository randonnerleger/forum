<?php
define('PUN_ROOT', './');
define('PUN_QUIET_VISIT', 1);
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/common.php';

// Modificer cette condition pour permettre au membre de voir son évolution

if ($pun_user['g_id'] > PUN_MOD)
	message($lang_common['No permission']);

$page_title = pun_htmlspecialchars($pun_config['o_board_title']) . ' / Statistiques';
define('PUN_ALLOW_INDEX', 1);
require PUN_ROOT.'header.php';
require PUN_ROOT.'include/parser.php';
 
include_once 'ofc-library/open-flash-chart.php';
 
// On regarde si on nous donne l'identifiant d'un membre
if(isset($_GET['id']))
	{
	if(preg_match('#^[0-9]+$#', $_GET['id']))
		$req_where_id = 'poster_id = '.$_GET['id'];
	else
		unset($_GET['id']);
	}

// On regarde si on nous donne l'identifiant d'un topic
if(isset($_GET['tid']))
	{
	if(preg_match('#^[0-9]+$#', $_GET['tid']))
		{
		$req_where_id = 'topic_id = '.$_GET['tid'];
	
		$_GET['partie'] = "evo_post";
		
		if(!isset($_GET['date']))
			$_GET['date'] = "tout";
		}
	else
		unset($_GET['tid']);
	}
 
// Quelques tableaux de données
$lib_mois = array('', 'Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre');
$lib_jour = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
$lib_heure = array('00' => '00h', '01' => '01h', '02' => '02h', '03' => '03h', '04' => '04h', '05' => '05h', '06' => '06h', '07' => '07h', '08' => '08h', '09' => '09h', '10' => '10h', '11' => '11h', '12' => '12h', '13' => '13h', '14' => '14h', '15' => '15h', '16' => '16h', '17' => '17h', '18' => '18h', '19' => '19h', '20' => '20h', '21' => '21h', '22' => '22h', '23' => '23h');
$nb_jour_mois = array('', 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

// Récupération de la date de création du forum / d'inscription du membre
if(isset($_GET['id']))
	{
	if($_GET['id'] == 1)
		$result = $db->query('SELECT \''.$lang_common['Guest'].'\' AS username, registered FROM '. $db->prefix .'users WHERE id != 1 ORDER BY id LIMIT 0,1', true)or error('Database error', __FILE__, __LINE__, $db->error());
	else	
		$result = $db->query('SELECT username, registered FROM '. $db->prefix .'users WHERE id = '.$_GET['id'], true)or error('Database error', __FILE__, __LINE__, $db->error());
	
	}
else if(isset($_GET['tid']))
	$result = $db->query('SELECT subject as username, posted as registered FROM '. $db->prefix .'topics WHERE id = '.$_GET['tid'], true)or error('Database error', __FILE__, __LINE__, $db->error());
else
	$result = $db->query('SELECT username, registered FROM '. $db->prefix .'users WHERE id != 1 ORDER BY id LIMIT 0,1', true)or error('Database error', __FILE__, __LINE__, $db->error());

$reponse = $db->fetch_assoc($result);
$time_crea = $reponse['registered'];
$username = $reponse['username'];

// Si on fait des recherches sur les discussions, on passe par le pseudo
if($_GET['partie'] == 'rep_topic' OR $_GET['partie'] == 'record_topic')
	$req_where_id = 'poster = \''.$db->escape($username).'\'';

// Quelques fonctions
function set_max($tab_value)
	{
	$max = max($tab_value);
	
	if($max < 100)
		$barre = 5;
	else if($max < 500)
		$barre = 50;
	else if($max < 4000)
		$barre = 100;
	else 
		$barre = 1000;
	return ceil(max($tab_value)/$barre)*$barre;
	}

function norm_lib($variable)
	{
	$search = array ('@[éèêëÉÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[^a-zA-Z0-9 ]@');
	$replace = array ('e','a','i','u','o','c','');
	$variable = preg_replace($search, $replace, $variable);
	
	//$variable = strtolower($variable);	
	return $variable;
	}
	
function draw_line($tab_valeur, $tab_lib, $title)
	{
	$g = new graph();
	$g->title( '  ', '{font-size: 2px;}');

	$g->set_data( $tab_valeur );
	$g->line( 2, '0x9933CC', 'Page views', 0 );
	$g->set_x_labels( $tab_lib );
	
	if(count($tab_valeur) > 80)
		$g->set_x_label_style( 10, '#9933CC', 2, 3);
	else if(count($tab_valeur) > 40)
		$g->set_x_label_style( 10, '#9933CC', 2, 2);
	else	
		$g->set_x_label_style( 10, '#9933CC', 2 );
	$g->set_y_max( set_max($tab_valeur) );
	$g->y_label_steps( 5 );
	$g->set_is_decimal_separator_comma( true );
	$g->set_width( '100%' );
	$g->set_height( 400 );

	$g->set_output_type('js');
	?>
<div class="blockform">
	<h2><span><?php echo $title ; ?></span></h2>
	<div id="top20" class="block">
		<div class="box">
			<div class="inbox">
	<?php
	echo $g->render();
	?>
			</div>
		</div>
	</div>
</div>
			<?php
	
	}
	
function draw_lines($tab_valeur, $tab_valeur_prec, $tab_lib, $title, $legende)
	{
	$g = new graph();
	$g->title( '  ', '{font-size: 2px;}');

	$g->set_data( $tab_valeur );
	$g->set_data( $tab_valeur_prec );
	$g->line( 2, '0x9933CC', $legende.' en cours', 10 );
	$g->line( 2, '0xF694A4', $legende.($legende == 'Année' ? ' précédente' : ' précédent'), 10 );
	$g->set_x_labels( $tab_lib );
	
	if(count($tab_valeur) > 80)
		$g->set_x_label_style( 10, '#9933CC', 2, 3);
	else if(count($tab_valeur) > 40)
		$g->set_x_label_style( 10, '#9933CC', 2, 2);
	else	
		$g->set_x_label_style( 10, '#9933CC', 2 );
	$g->set_y_max( max(set_max($tab_valeur), set_max($tab_valeur_prec) ) );
	$g->y_label_steps( 5 );
	$g->set_is_decimal_separator_comma( true );
	$g->set_width( '100%' );
	$g->set_height( 400 );

	$g->set_output_type('js');
	?>
<div class="blockform">
	<h2><span><?php echo $title ; ?></span></h2>
	<div id="tab_1" class="block">
		<div class="box">
			<div class="inbox">
	<?php
	echo $g->render();
	?>
			</div>
		</div>
	</div>
</div>
			<?php
	
	}
	
function draw_bar($tab_valeur, $tab_lib, $title)
	{
	$bar = new bar_outline( 50, '#9933CC', '#8010A0' );
	$bar->data = $tab_valeur;
	
	$g = new graph();
	$g->title( ' ', '{font-size: 2px;}');
	$g->data_sets[] = $bar;
	$g->set_x_labels( $tab_lib );
	$g->set_x_label_style( 10, '#9933CC', 2, 1 );
	$g->set_y_max( set_max($tab_valeur) );
	$g->y_label_steps( 5 );
	$g->set_is_decimal_separator_comma( true );
	$g->set_width( '100%' );
	$g->set_height( 400 );
	
	$g->set_output_type('js');
	?>
<div class="blockform">
	<h2><span><?php echo $title ; ?></span></h2>
	<div id="tab_2" class="block">
		<div class="box">
			<div class="inbox">
	<?php
	echo $g->render();
	?>
			</div>
		</div>
	</div>
</div>
			<?php
	}

// Initialisation
if(!isset($_GET['partie']))
	$_GET['partie'] = 'evo_post';
else
	$_GET['partie'] = addslashes($_GET['partie']);
	
if(!isset($_GET['date']))
	{
	if($_GET['partie'] == 'evo_post' AND !isset($_GET['id']))
		$_GET['date'] = date('Y');
	else
		$_GET['date'] = 'tout';
	}
else
	$_GET['date'] = addslashes($_GET['date']);
	
// Gestion de l'affichage par défaut pour chaque partie
if(!isset($_GET['echelle']))
	$_GET['echelle'] = 'mois';
else
	{
	if($_GET['echelle'] != 'mois' AND $_GET['echelle'] != 'jour' AND $_GET['echelle'] != 'annee' AND $_GET['echelle'] != 'semaine')
		$_GET['echelle'] = 'mois';
	}
	
// Pour l'évolution
if($_GET['partie'] == 'evo_post' OR $_GET['partie'] == 'evo_topic' OR $_GET['partie'] == 'evo_user')
	{
	if(!isset($_GET['date_mois']))
		$_GET['date_mois'] = 0;
	else if(!preg_match('#^[0-9]+$#', $_GET['date_mois']))
		$_GET['date_mois'] = 0;
	
	if(!preg_match('#^[0-9]{4}-[0-9]{1,2}$#', $_GET['date']) && !preg_match('#^[0-9]{4}$#', $_GET['date']))
		$_GET['date'] = "tout";
	}
// Meilleurs posteurs
else if($_GET['partie'] == 'top_post' OR $_GET['partie'] == 'top_topic')
	{
	if(!preg_match('#^[0-9]{4}$#', $_GET['date']) AND $_GET['date'] != 'annee' AND $_GET['date'] != 'jour' AND $_GET['date'] != 'tout')
		$_GET['date'] = 'mois';
	}
// Répartition temporelle
else if(preg_match('#rep#', $_GET['partie']))
	{
	if(!in_array($_GET['date'], array('mois', 'jour', 'heure')))
		$_GET['date'] = 'mois';
	}
// Les records	
else if(preg_match('#record#', $_GET['partie']))
	{
	if($_GET['echelle'] == 'annee')
		$_GET['date'] = 'tout';
	}

?>
<div class="blockform">
	<h2><span>Statistiques du forum</span></h2>
	<div id="top20" class="block">
		<div class="box">
			<div class="inbox">
			<form action='' method='get'>
			<?php
			if(isset($_GET['id']))
				echo "<input type='hidden' name='id' value='".$_GET['id']."' />";
			if(isset($_GET['tid']))
				echo "<input type='hidden' name='tid' value='".$_GET['tid']."' />";
				?>
				<select name='partie'>
					<option value='evo_post'<?php echo $_GET['partie'] == 'evo_post' ? ' selected="selected"' : ''; ?>>Evolution du nombre de messages</option>
				<?php
				if(!isset($_GET['tid']))
					{
					if(!isset($_GET['id']))
						{
				?>
					<option value='evo_topic'<?php echo $_GET['partie'] == 'evo_topic' ? ' selected="selected"' : ''; ?>> Evolution du nombre de discussions</option>
					<option value='evo_user'<?php echo $_GET['partie'] == 'evo_user' ? ' selected="selected"' : ''; ?>>Evolution du nombre de membres</option>
					<option value='top_post'<?php echo $_GET['partie'] == 'top_post' ? ' selected="selected"' : ''; ?>>Meilleurs posteurs</option>
					<option value='top_topic'<?php echo $_GET['partie'] == 'top_topic' ? ' selected="selected"' : ''; ?>>Meilleurs créateurs</option>
				<?php
						}
				?>
					<option value='record_post'<?php echo $_GET['partie'] == 'record_post' ? ' selected="selected"' : ''; ?>>Record du nombre de messages postés</option>
					<option value='record_topic'<?php echo $_GET['partie'] == 'record_topic' ? ' selected="selected"' : ''; ?>>Record du nombre de discussions créées</option>
				<?php
					if(!isset($_GET['id']))
						{
				?>		
					<option value='record_user'<?php echo $_GET['partie'] == 'record_user' ? ' selected="selected"' : ''; ?>>Record du nombre de nouveaux membres</option>
				<?php
						}
				?>
					<option value='rep_post'<?php echo $_GET['partie'] == 'rep_post' ? ' selected="selected"' : ''; ?>>Répartition temporelle des messages</option>
					<option value='rep_topic'<?php echo $_GET['partie'] == 'rep_topic' ? ' selected="selected"' : ''; ?>>Répartition temporelle des discussions</option>
				<?php
					if(!isset($_GET['id']))
						{
				?>	
					<option value='rep_user'<?php echo $_GET['partie'] == 'rep_user' ? ' selected="selected"' : ''; ?>>Répartition temporelle des membres</option>
				<?php
						}
					}
				?>
				</select>
			
				<?php
				if(preg_match('#record#', $_GET['partie']))
					{
					?>
					Echelle : <select name='echelle'>
						<option value='jour'<?php echo $_GET['echelle'] == 'jour' ? ' selected="selected"' : ''; ?>>Jour</option>
						<option value='semaine'<?php echo $_GET['echelle'] == 'semaine' ? ' selected="selected"' : ''; ?>>Semaine</option>
						<option value='mois'<?php echo $_GET['echelle'] == 'mois' ? ' selected="selected"' : ''; ?>>Mois</option>
						<option value='annee'<?php echo $_GET['echelle'] == 'annee' ? ' selected="selected"' : ''; ?>>Année</option>
					</select>
					<?php
					}
				if(!(preg_match('#record#', $_GET['partie']) AND $_GET['echelle'] == 'annee'))
					{
				?>
				Période : <select name='date'>
					
					<?php
					$mois_avant = '';
					$mois_apres = '';
					$mois_centre = '';
					
					if(preg_match('#evo#', $_GET['partie']))
						{
						// Tableau contenant la liste des mois
						$tab_liste_mois = array();
						$tab_liste_mois_lib = array();
						$tab_liste_num_mois = array();
																
						$fin_annee = date('Y');
						$fin_mois = date('n');
						
						for($tmp_annee = date('Y', $time_crea), $tmp_mois = date('n', $time_crea); $tmp_annee < $fin_annee OR $tmp_mois <= $fin_mois; $tmp_mois++)
						    {
						    if($tmp_mois == 13)
						        {
						        $tmp_mois = 1;
						        $tmp_annee++;
						        }
						    
						    $tab_liste_mois[] = $tmp_annee."-".$tmp_mois;
						    $tab_liste_mois_lib[] = $tmp_annee.' '.$lib_mois[$tmp_mois];
							$tab_liste_num_mois[] = $tmp_mois;
							$tab_liste_num_annee[] = $tmp_annee;
						    }
							
						echo "\n\t\t\t\t\t<option value='tout'>Global</option>";
						
						// On affiche la dernirèe année
						if($_GET['date'] == $tab_liste_num_annee[count($tab_liste_mois)-1])
							echo "\n\t\t\t\t\t<option value='".$tab_liste_num_annee[count($tab_liste_mois)-1]."' selected='selected''>".$tab_liste_num_annee[count($tab_liste_mois)-1]."</option>";
						else
							echo "\n\t\t\t\t\t<option value='".$tab_liste_num_annee[count($tab_liste_mois)-1]."'>".$tab_liste_num_annee[count($tab_liste_mois)-1]."</option>";
							
						
						
						for($i = count($tab_liste_mois)-1; $i >= 0; $i--)
							{
							// Si la date en cours correspond, on séléctionne l'entrée dans la liste et on créé les liens pour le mois précédent et le mois suivant
							if($_GET['date'] == $tab_liste_mois[$i])
								{
								echo "\n\t\t\t\t\t<option value='".$tab_liste_mois[$i]."' selected='selected'>".$tab_liste_mois_lib[$i]."</option>";
								
								$mois_centre = $tab_liste_mois_lib[$i];
								
								// Lien mois précédent
								if(isset($tab_liste_mois[$i-1]))
									{
									if(isset($_GET['id']))
										$mois_avant = "<a href='statistiques.php?partie=".$_GET['partie']."&date=".$tab_liste_mois[$i-1]."&id=".$_GET['id']."'>".$tab_liste_mois_lib[$i-1]."</a> <<";
									else if(isset($_GET['tid']))
										$mois_avant = "<a href='statistiques.php?partie=".$_GET['partie']."&date=".$tab_liste_mois[$i-1]."&tid=".$_GET['tid']."'>".$tab_liste_mois_lib[$i-1]."</a> <<";
									else	
										$mois_avant = "<a href='statistiques.php?partie=".$_GET['partie']."&date=".$tab_liste_mois[$i-1]."'>".$tab_liste_mois_lib[$i-1]."</a> <<";
									
									$ref_mois = $tab_liste_mois[$i-1];
									}
									
								// Lien mois suivant
								if(isset($tab_liste_mois[$i+1]))
									{
									if(isset($_GET['id']))
										$mois_apres = ">> <a href='statistiques.php?partie=".$_GET['partie']."&date=".$tab_liste_mois[$i+1]."&id=".$_GET['id']."'>".$tab_liste_mois_lib[$i+1]."</a>";
									else if(isset($_GET['tid']))
										$mois_apres = ">> <a href='statistiques.php?partie=".$_GET['partie']."&date=".$tab_liste_mois[$i+1]."&tid=".$_GET['tid']."'>".$tab_liste_mois_lib[$i+1]."</a>";										else										
										$mois_apres = ">> <a href='statistiques.php?partie=".$_GET['partie']."&date=".$tab_liste_mois[$i+1]."'>".$tab_liste_mois_lib[$i+1]."</a>";
									}
								}
							else
								echo "\n\t\t\t\t\t<option value='".$tab_liste_mois[$i]."'>".$tab_liste_mois_lib[$i]."</option>";
							
							// On regarde si on en est au mois de janvier pour afficher l'année de l'année précédente
							if($tab_liste_num_mois[$i] == 1)
								{
								if($_GET['date'] == $tab_liste_num_annee[$i-1])
									echo "\n\t\t\t\t\t<option value='".$tab_liste_num_annee[$i-1]."' selected='selected'>".$tab_liste_num_annee[$i-1]."</option>";
								else
									echo "\n\t\t\t\t\t<option value='".$tab_liste_num_annee[$i-1]."'>".$tab_liste_num_annee[$i-1]."</option>";
								
								}
							}
						
						echo "\n\t\t\t\t\t<option value='tout'>Global</option>";
						
						// Si on regarde l'évolution sur l'année, on créer les liens années suivante et année précédente
						if(preg_match('#^[0-9]{4}$#', $_GET['date']))
							{
							// Lien année précédente précédent
							if(isset($_GET['id']))
								{
								$annee_avant = "<a href='statistiques.php?partie=".$_GET['partie']."&date=".($_GET['date']-1)."&id=".$_GET['id']."'>".($_GET['date']-1)."</a> <<";
								$annee_apres = ">> <a href='statistiques.php?partie=".$_GET['partie']."&date=".($_GET['date']+1)."&id=".$_GET['id']."'>".($_GET['date']+1)."</a>";
								}
							else if(isset($_GET['tid']))
								{
								$annee_avant = "<a href='statistiques.php?partie=".$_GET['partie']."&date=".($_GET['date']-1)."&tid=".$_GET['tid']."'>".($_GET['date']-1)."</a> <<";
								$annee_apres = ">> <a href='statistiques.php?partie=".$_GET['partie']."&date=".($_GET['date']+1)."&tid=".$_GET['tid']."'>".($_GET['date']+1)."</a>";								
								}
							else	
								{
								$annee_avant = "<a href='statistiques.php?partie=".$_GET['partie']."&date=".($_GET['date']-1)."'>".($_GET['date']-1)."</a> <<";
								$annee_apres = ">> <a href='statistiques.php?partie=".$_GET['partie']."&date=".($_GET['date']+1)."'>".($_GET['date']+1)."</a>";
								}						
							}
						}
					else if(preg_match('#rep#', $_GET['partie']))
						{
						?>
						<option value='mois'<?php echo $_GET['date'] == 'mois' ? ' selected="selected"' : ''; ?>>Par mois</option>
						<option value='jour'<?php echo $_GET['date'] == 'jour' ? ' selected="selected"' : ''; ?>>Par jour de la semaine</option>
						<option value='heure'<?php echo $_GET['date'] == 'heure' ? ' selected="selected"' : ''; ?>>Par heure</option>
						<?php
						}
					else
						{
						?>
						<option value='tout'>Global</option>
						<?php
						if(!preg_match('#record#', $_GET['partie']))
							{
						?>
						<option value='jour'<?php echo $_GET['date'] == 'jour' ? ' selected="selected"' : ''; ?>>Du jour</option>
						<option value='mois'<?php echo $_GET['date'] == 'mois' ? ' selected="selected"' : ''; ?>>Du mois</option>
						<?php	
							}
						?>
						<option value='annee'<?php echo $_GET['date'] == 'annee' ? ' selected="selected"' : ''; ?>>De l'année</option>
						<?php
						$debut_annee = date('Y', $time_crea);
						$tmp_annee = date('Y')-1;
						
						while($debut_annee <= $tmp_annee)
							{
							if($_GET['date'] == $tmp_annee)
								echo "\n\t\t\t\t\t<option value='".$tmp_annee."' selected='selected'>De l'année ".$tmp_annee."</option>";
							else
								echo "\n\t\t\t\t\t<option value='".$tmp_annee."'>De l'année ".$tmp_annee."</option>";
							
							$tmp_annee--;
							}
						echo "\n\t\t\t\t\t<option value='tout'>Global</option>";
						}
					?>
				</select>
			
				<?php
					}
				
				if(preg_match('#evo#', $_GET['partie']) AND preg_match('#^[0-9]{4}-[0-9]{1,2}$#', $_GET['date']))
					{
					echo "\n\t\t\t\tJour : <select name='date_mois'>";
					
					$tab_date = explode('-', $_GET['date']);
					
					if(0 == $_GET['date_mois'])
							echo "\n\t\t\t\t\t<option value='0' selected='selected'>Tous</option>";
						else	
							echo "\n\t\t\t\t\t<option value='0'>Tous</option>";
					
					for($i = 1; $i <= $nb_jour_mois[$tab_date[1]]; $i++)
						{
						if($i == $_GET['date_mois'])
							echo "\n\t\t\t\t\t<option value='".$i."' selected='selected'>".$i."</option>";
						else	
							echo "\n\t\t\t\t\t<option value='".$i."'>".$i."</option>";
						}
					
					echo "\n\t\t\t\t</select>";
					}
				?>
				<input type='submit' value='Afficher' />
				</form>
			
			<?php
			
			if(preg_match('#evo#', $_GET['partie']) AND preg_match('#^[0-9]{4}$#', $_GET['date']))
				echo "<p>".$annee_avant." ".$_GET['date']." ".$annee_apres."<br /></p>";
			else if(preg_match('#evo#', $_GET['partie']) AND $_GET['date_mois'] == 0)
				echo "<p>".$mois_avant." ".$mois_centre." ".$mois_apres."<br /></p>";
			
			if(isset($_GET['id']))
				echo "<strong>Membre : </strong>".$username;
				
			if(isset($_GET['tid']))
				echo "<strong>Discussion : </strong><a href='viewtopic.php?id=".$_GET['tid']."'>".$username."</a>";
			?>
			</div>
		</div>
	</div>
</div>
			<?php
			// Evolution du nombre de messages / discussions / membres
			if($_GET['partie'] == 'evo_post' OR $_GET['partie'] == 'evo_topic' OR $_GET['partie'] == 'evo_user')
				{
				$tab_valeur = array();
				$tab_valeur_prec = array();
				$tab_valeur_cum = array();
				$tab_valeur_cum_prec = array();
				$tab_lib = array();
				$tab_time = array();
							
				$cumul = 0;
				$cumul_prec = 0;
				$val_prec = 0;
				
								
				$evo_post = array(
					'table'	=> 'posts',
					'champ'	=> 'posted',
					'nom'	=> 'messages'
					);
					
				$evo_topic = array(
					'table'	=> 'topics',
					'champ'	=> 'posted',
					'nom'	=> 'discussions'
					);
					
				$evo_user = array(
					'table'	=> 'users',
					'champ'	=> 'registered',
					'nom'	=> 'membres'
					);
				
				// On regarde si un jour a été spécifié
				if(preg_match('#^[0-9]{4}-[0-9]{1,2}$#', $_GET['date']) AND $_GET['date_mois'] != 0)
					{
					$is_date = TRUE;
					
					$tab_date = explode('-', $_GET['date']);
					$lib_date = ' pour le '.$_GET['date_mois'].' de '.$lib_mois[$tab_date[1]].' '.$tab_date[0];
					
					if(!isset($req_where_id))
						$req_where = ' WHERE FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%c-%e") = \''.$_GET['date'].'-'.$_GET['date_mois'].'\' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%H")';
					else
						$req_where = ' WHERE FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%c-%e") = \''.$_GET['date'].'-'.$_GET['date_mois'].'\' AND '.$req_where_id.' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%H")';
					
					// On créer le tableau avec le bon nombre de lignes et le libellé des heures
					for($i = 0; $i < 24; $i++)
						{
						$tab_valeur[$i] = 0;
						$tab_valeur_cum[$i] = 0;
						$tab_lib[$i] = $i.'h';
						}
						
					$lib_date = ' pour le '.$_GET['date_mois'].' '.$lib_mois[$tab_date[1]].' '.$tab_date[0];
					}	
				// On regarde si un mois a été spécifié
				else if(preg_match('#^[0-9]{4}-[0-9]{1,2}$#', $_GET['date']))
					{
					$legende = 'Mois';
					if(!isset($req_where_id))
						$req_where = ' WHERE FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%c") = \''.$_GET['date'].'\' OR FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%c") = \''.$ref_mois.'\' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%m-%d")';
					else
						$req_where = ' WHERE (FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%c") = \''.$_GET['date'].'\' OR FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%c") = \''.$ref_mois.'\') AND '.$req_where_id.' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%m-%d")';
					
					$tab_date = explode('-', $_GET['date']);
					$lib_date = ' pour le mois de '.$lib_mois[$tab_date[1]].' '.$tab_date[0];
					
					// On créer le tableau avec le bon nombre de lignes et le libellé des jours
					$time_jour = mktime(0, 0, 0, $tab_date[1], 1, $tab_date[0]);
					$nb_jour = date('t', $time_jour);
					
					for($i = 1; $i <= $nb_jour; $i++)
						{
						$tab_valeur[$i] = 0;
						$tab_valeur_cum[$i] = 0;
						$tab_valeur_prec[$i] = 0;
						$tab_valeur_cum_prec[$i] = 0;
						$tab_lib[$i] = $lib_jour[date('w', $time_jour)].' '.date('j', $time_jour);
						$tab_time[$i] = 0;
						$time_jour += 86400;
						}
					}
				// Si on veut l'évolution sur l'année
				else if(preg_match('#^[[0-9]{4}$#', $_GET['date']))
					{
					$legende = 'Année';
					for($i = 1; $i <= 12; $i++)
						{
						$tab_valeur[$i] = 0;
						$tab_valeur_cum[$i] = 0;
						$tab_valeur_prec[$i] = 0;
						$tab_valeur_cum_prec[$i] = 0;
						$tab_lib[$i] = $lib_mois[$i];
						}
					
					if(isset($req_where_id))
						$req_where = ' WHERE '.$req_where_id.' AND (FROM_UNIXTIME('.${$_GET['partie']}['champ'].', "%Y") = '.$_GET['date'].' OR FROM_UNIXTIME('.${$_GET['partie']}['champ'].', "%Y") = '.($_GET['date'] - 1).') GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%m")';
					else
						$req_where = ' WHERE FROM_UNIXTIME('.${$_GET['partie']}['champ'].', "%Y") = '.$_GET['date'].' OR FROM_UNIXTIME('.${$_GET['partie']}['champ'].', "%Y") = '.($_GET['date'] - 1).' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%m")';
						
					$lib_date = " pour l'année ".$_GET['date'];
					}
				// Aucun jour ni mois ni année n'a pas été spécifié, on regarde l'évolution globale	
				else
					{
					foreach($tab_liste_mois AS $lib)
						{
						$tab_valeur[$lib] = 0;
						$tab_valeur_cum[$lib] = 0;
						$tab_lib[$lib] = '';
						$tab_time[$lib] = 0;
						}
					
					if(isset($req_where_id))
						$req_where = ' WHERE '.$req_where_id.' AND '.${$_GET['partie']}['champ'].' != 0 GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%m")';
					else
						$req_where = ' WHERE '.${$_GET['partie']}['champ'].' != 0 GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%m")';
					
					$lib_date = "";
					}
				
				
				$result = $db->query('SELECT count(*) AS num_type, '. ${$_GET['partie']}['champ'] .' AS time, FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "%Y-%c") AS type FROM '. $db->prefix . ${$_GET['partie']}['table'] . $req_where, true)or error('Database error', __FILE__, __LINE__, $db->error());
					
					// On parcourt les résultats
					while($reponse = $db->fetch_assoc($result))
						{
						// Si c'est l'évolution globale
						if($_GET['date'] == 'tout')
							{
							$cumul += $reponse['num_type'];
							$tab_lib[date('Y-n', $reponse['time'])] = $lib_mois[date('n', $reponse['time'])].' '.date('Y', $reponse['time']);
							$tab_valeur[date('Y-n', $reponse['time'])] = $reponse['num_type'];
							$tab_valeur_cum[date('Y-n', $reponse['time'])] = $cumul;
							$tab_time[date('Y-n', $reponse['time'])] = $reponse['time'];
							}
						// Si le jour est spécifié
						else if(isset($is_date))
							{
							$cumul += $reponse['num_type'];
							
							$tab_valeur[date('G', $reponse['time'])] = $reponse['num_type'];
							$tab_valeur_cum[date('G', $reponse['time'])] = $cumul;
							}
						// Si on étudie une année
						else if(preg_match('#^[0-9]{4}$#', $_GET['date']))
							{
							// C'est pour l'année en cours
							if(date('Y', $reponse['time']) == $_GET['date'])
								{
								$cumul += $reponse['num_type'];
								$tab_valeur[date('n', $reponse['time'])] = $reponse['num_type'];
								$tab_valeur_cum[date('n', $reponse['time'])] = $cumul;
								}
							// C'est pour l'année précédente
							else
								{
								$cumul_prec += $reponse['num_type'];
								$tab_valeur_prec[date('n', $reponse['time'])] = $reponse['num_type'];
								$tab_valeur_cum_prec[date('n', $reponse['time'])] = $cumul_prec;
								}
							
							}
						// Si on étudie un mois
						else
							{
							// On calcule la même date un mois plus tard pour avoir le bon libellé en cas d'absence d'information pour le mois en cours
							$time_mois = $reponse['time'] + 86400*date('t', $reponse['time']);
							
							// Informations sur le mois précédent mais inférieur au nombre de jour dans le mois en cours
							if(date('Y-n', $reponse['time']) == $ref_mois && date('j', $reponse['time']) <= $nb_jour)
								{
								$tab_time[date('j', $reponse['time'])] = $time_mois;
								$tab_lib[date('j', $reponse['time'])] = $lib_jour[date('w', $time_mois)].' '.date('j', $time_mois);
								$cumul_prec += $reponse['num_type'];
								$tab_valeur_prec[date('j', $reponse['time'])] = $reponse['num_type'];
								$tab_valeur_cum_prec[date('j', $reponse['time'])] = $cumul_prec;
								}
							// Informations sur le mois en cours
							else if(date('Y-n', $reponse['time']) != $ref_mois)
								{
								$tab_lib[date('j', $reponse['time'])] = $lib_jour[date('w', $reponse['time'])].' '.date('j', $reponse['time']);
								$tab_time[date('j', $reponse['time'])] = $reponse['time'];
								$cumul += $reponse['num_type'];
								$tab_valeur[date('j', $reponse['time'])] = $reponse['num_type'];
								$tab_valeur_cum[date('j', $reponse['time'])] = $cumul;
								}
							}
														
						$val_prec++;
						}
						
						// On comble les trous
						$prec = -1;
						foreach($tab_lib AS $id_l => $lib)
							{
							if($prec == -1)
								{
								$prec = $id_l;
								$prec_time = $tab_time[$id_l];
								}
							else
								{
								if($tab_valeur[$id_l] == 0)
									$tab_valeur_cum[$id_l] = $tab_valeur_cum[$prec];
								
								if($tab_valeur_prec[$id_l] == 0)
									$tab_valeur_cum_prec[$id_l] = $tab_valeur_cum_prec[$prec];								
									
								if($tab_lib[$id_l] == '')
									{
									if($_GET['date'] == 'tout')
										{
										$prec_time = $tab_time[$prec] + 86400*date('t', $prec_time);
										$tab_lib[$id_l] = $lib_mois[date('n', $prec_time)].' '.date('Y', $prec_time);
										$tab_time[$id_l] = $prec_time;
										}
									else
										{
										$prec_time = $tab_time[$prec] + 86400;
										$tab_lib[$id_l] = $lib_jour[date('w', $prec_time)].' '.date('j', $prec_time);
										$tab_time[$id_l] = $prec_time;
										}
									}
								}
							$prec = $id_l;								
							}
				 
					
				if(count($tab_lib) != 0)
					{
					// On affiche le graphique de l'évolution
					if(isset($is_date) OR $_GET['date'] == 'tout')
						draw_line($tab_valeur_cum, $tab_lib, 'Evolution du nombre de '.${$_GET['partie']}['nom'].$lib_date);
					else
						draw_lines($tab_valeur_cum, $tab_valeur_cum_prec, $tab_lib, 'Evolution du nombre de '.${$_GET['partie']}['nom'].$lib_date, $legende);
					
					echo "<br />";
					
					if($_GET['date'] == 'tout')
						draw_bar($tab_valeur, $tab_lib, 'Nombre de '.${$_GET['partie']}['nom'].' par mois');
					else if(preg_match('#^[0-9]{4}$#', $_GET['date']))
						draw_bar($tab_valeur, $tab_lib, 'Nombre de '.${$_GET['partie']}['nom'].' par mois'.$lib_date);
					else	
						draw_bar($tab_valeur, $tab_lib, 'Nombre de '.${$_GET['partie']}['nom'].' par jour'.$lib_date);
					}
				}
			// Meilleurs posteurs
			else if($_GET['partie'] == 'top_post' OR $_GET['partie'] == 'top_topic')
				{
				$top_post = array(
					'table'	=> 'posts',
					'champ'	=> 'poster_id',
					'nom'	=> 'posteurs'
					);
					
				$top_topic = array(
					'table'	=> 'topics',
					'champ'	=> 'poster',
					'nom'	=> 'createurs de discussions'
					);
				
				$tab_valeur = array();
				$tab_lib = array();
				
				if($_GET['date'] == 'tout')
					{
					if($_GET['partie'] == 'top_post')
						$result = $db->query('SELECT poster, COUNT(*) AS num_posts FROM '. $db->prefix .'posts GROUP BY poster ORDER BY num_posts DESC LIMIT 15', true)or error('Database error', __FILE__, __LINE__, $db->error());
					else
						$result = $db->query('SELECT count(*) AS num_posts, poster FROM '. $db->prefix .'topics GROUP BY poster ORDER BY num_posts DESC, poster LIMIT 15', true)or error('Database error', __FILE__, __LINE__, $db->error());
					}
				else
					{
					if(preg_match('#^[0-9]{4}$#', $_GET['date']))
						{
						$req_where = 'FROM_UNIXTIME(posted, "%Y") = \''.$_GET['date'].'\'';
						$title = ' de l\'annee '.$_GET['date'];
						}
					else if($_GET['date'] == 'annee')
						{
						$req_where = 'FROM_UNIXTIME(posted, "%Y") = \''.date('Y').'\'';
						$title = ' de l\'annee';
						}
					else if($_GET['date'] == 'jour')
						{
						$req_where = 'FROM_UNIXTIME(posted, "%Y-%m-%d") = \''.date('Y-m-d').'\'';
						$title = ' du jour';
						}
					else
						{
						$req_where = 'FROM_UNIXTIME(posted, "%Y-%m") = \''.date('Y-m').'\'';
						$title = ' du mois';
						}
						
					$result = $db->query('SELECT count(*) AS num_posts, poster FROM '. $db->prefix . ${$_GET['partie']}['table'] .' WHERE '. $req_where .' GROUP BY '. ${$_GET['partie']}['champ'] .' ORDER BY num_posts DESC, poster LIMIT 15', true)or error('Database error', __FILE__, __LINE__, $db->error());
					}
				
				while($reponse = $db->fetch_assoc($result))
						{
						$tab_valeur[] = $reponse['num_posts'];
						$tab_lib[] = norm_lib($reponse['poster']);
						}
				
				if(count($tab_lib) != 0)
					{				
					draw_bar($tab_valeur, $tab_lib, 'Meilleurs '.${$_GET['partie']}['nom'].' '.$title);
					}
				}
			// Répartition temporelle
			else if(preg_match('#rep#', $_GET['partie']))
				{
				$tab_valeur = array();
				$tab_lib = array();
				
				$format_unix = array(
					'mois'	=> "%m",
					'jour'	=> "%w",
					'heure'	=> "%H"
					);
					
				$format_date = array(
					'mois'	=> "n",
					'jour'	=> "w",
					'heure'	=> "H"
					);
				
				$titre_date = array(
					'mois'	=> "mois",
					'jour'	=> "jour de la semaine",
					'heure'	=> "heure"
					);
				
				$rep_post = array(
					'table'	=> 'posts',
					'champ'	=> 'posted'
					);
					
				$rep_topic = array(
					'table'	=> 'topics',
					'champ'	=> 'posted'
					);
					
				$rep_user = array(
					'table'	=> 'users',
					'champ'	=> 'registered'
					);
					
				if($req_where_id != '')
					$result = $db->query('SELECT count(*) AS num_type, '. ${$_GET['partie']}['champ'] .' AS type FROM '. $db->prefix . ${$_GET['partie']}['table'] .' WHERE '.$req_where_id.' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "'.$format_unix[$_GET['date']].'")', true)or error('Database error', __FILE__, __LINE__, $db->error());	
				else
					$result = $db->query('SELECT count(*) AS num_type, '. ${$_GET['partie']}['champ'] .' AS type FROM '. $db->prefix . ${$_GET['partie']}['table'] .' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "'.$format_unix[$_GET['date']].'")', true)or error('Database error', __FILE__, __LINE__, $db->error());	
				
				
				$tab_lib = ${'lib_'.$_GET['date']};
				
				if($_GET['date'] == 'mois')
					unset($tab_lib[0]);
				
				if($_GET['date'] == 'jour')
					unset($tab_lib[7]);
				
				foreach($tab_lib AS $ident=>$lib)
					$tab_valeur[$ident] = 0;
									
				while($reponse = $db->fetch_assoc($result))
						{
						$tab_valeur[date($format_date[$_GET['date']], $reponse['type'])] = $reponse['num_type'];
						//$tab_lib[] = ${'lib_'.$_GET['date']}[date($format_date[$_GET['date']], $reponse['type'])];
						}
				
				if(count($tab_lib) != 0)
					{				
					draw_bar($tab_valeur, $tab_lib, 'Repartition par '.$titre_date[$_GET['date']].' '.$title);
					}
				}
			// Les records	
			else if(preg_match('#record#', $_GET['partie']))
				{
				$unix = array(
					'jour'	 => "%Y-%m-%d",
					'semaine' => "%x-%v",
					'mois'	=> "%Y-%m",
					'annee'	=> "%Y"
					);
					
				$record_post = array(
					'table'	=> 'posts',
					'champ'	=> 'posted',
					'nom'	=> 'de messages postés'
					);
					
				$record_topic = array(
					'table'	=> 'topics',
					'champ'	=> 'posted',
					'nom'	=> 'de discussions créées'
					);
					
				$record_user = array(
					'table'	=> 'users',
					'champ'	=> 'registered',
					'nom'	=> 'de nouveaux membres'
					);
				
				$tab_valeur = array();
				$tab_lib = array();
				
				if(preg_match('#^[0-9]{4}$#', $_GET['date']))
					{
					$req_where = 'WHERE FROM_UNIXTIME('.${$_GET['partie']}['champ'].', "%Y") = \''.$_GET['date'].'\'';
					
					if($req_where_id != '')
						$req_where .= 'AND '.$req_where_id;
						
					$title = ' pour l\'annee '.$_GET['date'];
					}
				else if($_GET['date'] == 'annee')
					{
					$req_where = 'WHERE FROM_UNIXTIME('.${$_GET['partie']}['champ'].', "%Y") = \''.date('Y').'\'';
					$title = ' pour  l\'annee '.date('Y');
					
					if($req_where_id != '')
						$req_where .= 'AND '.$req_where_id;
					}
				else if($req_where_id != '')
					$req_where = 'WHERE '.$req_where_id;
					
				$result = $db->query('SELECT count(*) AS num_posts, '. ${$_GET['partie']}['champ'] .' AS time FROM '. $db->prefix . ${$_GET['partie']}['table'] .' '. $req_where .' GROUP BY FROM_UNIXTIME('. ${$_GET['partie']}['champ'] .', "'. $unix[$_GET['echelle']] .'") ORDER BY num_posts DESC, '. ${$_GET['partie']}['champ'] .' DESC LIMIT 15', true)or error('Database error', __FILE__, __LINE__, $db->error());
								
				while($reponse = $db->fetch_assoc($result))
						{
						$tab_valeur[] = $reponse['num_posts'];
						
						if($_GET['echelle'] == 'mois')
							$tab_lib[] = $lib_mois[date('n',$reponse['time'])].' '.date('Y',$reponse['time']);
						else if($_GET['echelle'] == 'annee')
							$tab_lib[] = date('Y',$reponse['time']);
						else if($_GET['echelle'] == 'semaine')
							{
							if(date('w',$reponse['time']) == 0)
								$dec_jour = 6;
							else
								$dec_jour = date('w',$reponse['time']) - 1;
								
							$new_date = mktime(0, 0, 0, date('m', $reponse['time']), date('d', $reponse['time'])-$dec_jour, date('Y', $reponse['time']));
							$tab_lib[] = 'Sem. du '.date('d',$new_date).' '.$lib_mois[date('n',$new_date)].' '.date('y',$new_date);
							}
						else
							{
							$tab_lib[] = date('j',$reponse['time']).' '.$lib_mois[date('n',$reponse['time'])].' '.date('Y',$reponse['time']);
							}
						}
				
				if(count($tab_lib) != 0)
					{
					draw_bar($tab_valeur, $tab_lib, 'Record du nombre '.${$_GET['partie']}['nom'].' par '.$_GET['echelle'].' '.$title);
					}
				}
	 
require PUN_ROOT.'footer.php';