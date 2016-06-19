<div id="top-page"><a href="#wrapper-rl"></a></div>
<div id="wrapper-rl">
	<input type="checkbox" id="menu-left-checkbox" class="menu-left-checkbox" role="button">
	<input type="checkbox" id="menu-search-checkbox" class="menu-search-checkbox">
	<input type="checkbox" id="menu-forum-checkbox" class="menu-forum-checkbox">

<?php

function get_browsername() {
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE){
$browser = 'Microsoft Internet Explorer';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE) {
$browser = 'Google Chrome';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
$browser = 'Mozilla Firefox';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== FALSE) {
$browser = 'Opera';
}elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE) {
$browser = 'Apple Safari';
}else {
$browser = 'error'; //<-- Browser not found.
}
return $browser;
}

#echo '. ' . get_browsername() . ' .'; //<-- Display the browser name

#if(get_browsername() == 'Mozilla Firefox') { 
if(get_browsername() == 'Opera') { 
	$hackOperaMini = 'opera';
}
?>

	<div id="wrapper-inner" class="<?php echo $hackOperaMini ?>">
		<div id="header">
			<label for="menu-left-checkbox" class="slide-left-toggle" onClick="CloseOtherMenu('forum','search');"></label>
			<label for="menu-forum-checkbox" class="menu-forum-toggle" onClick="CloseOtherMenu('left','search');"></label>
			<label for="menu-search-checkbox" class="menu-search-toggle" onClick="CloseOtherMenu('left','forum');"></label>
			<div class="inner">
				<div id="logo" class="inbl">
					<a href="<?php echo path_to_home ?>" rel="nofollow"><img src="<?php echo path_to_rl.'tpl/img/logo.png'?>" /></a>
				</div><!-- #logo -->

				<div id="RL" class="inbl">
					<a href="<?php echo path_to_home ?>">Randonner-leger.org</a>
				</div><!-- #logo -->

				<div id="menu-search">
					<div id="menu-search-inner">
						<script type="text/javascript">

						function CheckSearchRL(){
							var domainroot=document.SearchRL.domainroot[document.SearchRL.domainroot.selectedIndex].value;
							var txtRecherche_mod = document.getElementById('qfront').value.replace(/ /g, '+');
							var form_valid = (txtRecherche_mod == 'fermeture+rl');
								if(!form_valid){
									if (domainroot=="interne") {
										document.location.href="http://www.randonner-leger.org/wiki/doku.php?do=search&id="+txtRecherche_mod;
										return false;
									} else {
										document.location.href="https://duckduckgo.com/?q=site%3A"+domainroot+"+"+txtRecherche_mod;
										return false;
									}
								}
								alert('Aïe !! \nVotre requête vous entraîne dans les tréfonds du forum ;)');
								document.location.href="http://www.randonner-leger.org/forum/uploads/2_bd_annivrl_7ans.gif";
								return false;
						}
						</script>

						<form onsubmit="return CheckSearchRL()" method="get" name="SearchRL" action="" class="search-form">
							<input name="q" type="hidden" />
							<input class="search-field" id="qfront" value="" title="Saisissez les mots-cles à rechercher" type="search" placeholder="Rechercher" />
							<button class="reset-btn" type="reset"></button>

							<select class="search-select" name="domainroot">
							<option value="www.randonner-leger.org%2Fforum%2F" <?php echo $GLOBALS['Iam_forum']; ?>>dans le Forum</option>
							<option value="www.randonner-leger.org%2Fwiki%2F" <?php echo $GLOBALS['Iam_wiki']; ?>>dans le Wiki</option>
							<option value="interne">dans le Wiki (moteur interne)</option>
							<option value="www.randonner-leger.org">sur tout le site</option>
							</select>
							<button class="submit-btn" type="submit"></button>

						</form>
					</div><!-- #search -->
				</div><!-- #menu-search -->
			</div><!-- .inner -->
		</div><!-- #header -->

		<div class="outer">
