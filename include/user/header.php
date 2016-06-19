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

<?php

# Redirection Search lorsque moteur de recherche utilisé sans javascript activé
# Opitux

function str_to_noaccent($str)
{
	$url = $str;
	$url = preg_replace('#Ç#', 'C', $url);
	$url = preg_replace('#ç#', 'c', $url);
	$url = preg_replace('#è|é|ê|ë#', 'e', $url);
	$url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
	$url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
	$url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
	$url = preg_replace('#ì|í|î|ï#', 'i', $url);
	$url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
	$url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
	$url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
	$url = preg_replace('#ù|ú|û|ü#', 'u', $url);
	$url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
	$url = preg_replace('#ý|ÿ#', 'y', $url);
	$url = preg_replace('#Ý#', 'Y', $url);
	return ($url);
}

foreach ($_REQUEST as $key => $val) 
{
#	restrictif
	$val = str_to_noaccent ($val);
	$val = preg_replace("/[^_A-Za-z0-9-\.&=]/i",' ', $val);

#	moins restrictif
#	$val = trim(stripslashes(htmlentities($val)));
	$val = trim($val);
	$_REQUEST[$key] = $val;
}

if(isset($_REQUEST['domainroot']) ) {
	if ( $_REQUEST['domainroot'] == "interne") {
#		header('Location: /wiki/doku.php?do=search&id='.str_replace("&quot;", "\"", $_REQUEST['q']).'');
		header('Location: /wiki/doku.php?do=search&id='.$_REQUEST['q'].'');
	} else {
		header('Location: https://duckduckgo.com/html/?q=site%3Awww.randonner-leger.org%2F'.$_GET['domainroot'].'+'.$_GET['q'].'');
	}
}
?>

						<script type="text/javascript">
						function CheckSearchRL(){
							var domainroot=document.SearchRL.domainroot[document.SearchRL.domainroot.selectedIndex].value;
							var txtRecherche_mod = document.getElementById('q').value.replace(/ /g, '+');
							var form_valid = (txtRecherche_mod == 'fermeture+rl');
								if(!form_valid){
									if (domainroot=="interne") {
										document.location.href="http://www.randonner-leger.org/wiki/doku.php?do=search&id="+txtRecherche_mod;
										return false;
									} else {
										document.location.href="https://duckduckgo.com/?kae=d&kl=fr-fr&kad=fr_FR&k1=-1&kaj=m&kam=osm&ks=n&kw=w&kj=9EABB9&k7=F7F7F7&kt=a&k8=566579&kx=2365B0&k9=334153&kaa=566579&kai=1&ko=1&q=site%3Awww.randonner-leger.org%2F"+domainroot+"+"+txtRecherche_mod;
										return false;
									}
								}
								alert('Aïe !! \nVotre requête vous entraîne dans les tréfonds du forum ;)');
								document.location.href="http://www.randonner-leger.org/forum/uploads/2_bd_annivrl_7ans.gif";
								return false;
						}
						</script>

						<form onsubmit="return CheckSearchRL()" method="get" name="SearchRL" action="" class="search-form">
							<input class="search-field" name="q" id="q" value="" title="Saisissez les mots-cles à rechercher" type="search" placeholder="Rechercher" />
							<button class="reset-btn" type="reset"></button>

							<select class="search-select" name="domainroot">
							<option value="forum" <?php echo $GLOBALS['Iam_forum']; ?>>dans le Forum</option>
							<option value="wiki" <?php echo $GLOBALS['Iam_wiki']; ?>>dans le Wiki</option>
							<option value="interne">dans le Wiki (moteur interne)</option>
							<option value="">sur tout le site</option>
							</select>
							<button class="submit-btn" type="submit"></button>

						</form>
					</div><!-- #search -->
				</div><!-- #menu-search -->
			</div><!-- .inner -->
		</div><!-- #header -->

		<div class="outer">
