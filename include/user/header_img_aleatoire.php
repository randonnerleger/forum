<?php
if ($_GET["numheader"] > 0) {
	if( isset($_GET['numheader']) )
	 $numheader = sprintf("%02d", (integer) $_GET['numheader'] ) ;

} else {
// Image alétatoire
// $numheader=rand(0, 137);

// Image par numéro de semaine
$numheader = strftime("%U");
}

?>
<style>#header {background:#89969F url("<?php echo path_to_rl; ?>tpl/img/header_<?php echo $numheader; ?>.jpg");}</style>
