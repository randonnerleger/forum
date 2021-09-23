<?php
$numheader = ( isset($_GET['numheader']) && (int) $_GET["numheader"] > 0 ) ? sprintf("%02d", (integer) $_GET['numheader'] ) : strftime("%U");
?>
<style>#header, #header .blur::before {background-image: url("<?php echo path_to_rl; ?>tpl/img/header_<?php echo $numheader; ?>.jpg");}</style>
