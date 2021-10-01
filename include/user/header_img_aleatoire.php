<?php
global $pun_user;

$numheader = ( isset($_GET['numheader']) && (int) $_GET["numheader"] > 0 ) ? sprintf("%02d", (integer) $_GET['numheader'] ) : strftime("%U");

$style = array(
	'alone',
	'ciel_et_terre',
	'cuben',
	'eclaircie',
	'herbage',
	'islande',
	'montagne',
	'old_school',
	'un_lac',
	'zen'
);
$pun_user_style = str_replace( array ('RL_Clair_', 'RL_Sombre_' ), '', $pun_user['style'] );
?>
<style>#header, #header .blur::before {
	background-image: url("<?php echo path_to_rl; ?>tpl/img/header_<?php echo strtolower( in_array( $pun_user_style, $style ) ? $pun_user_style : $numheader ); ?>.jpg");}
</style>
