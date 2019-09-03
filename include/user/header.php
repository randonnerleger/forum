<?php
// Je check si connecté
$connected = ($pun_user['group_id']==3 || $conf['group_id']==3 ) ? '' : 'connected' ;
?>
<div id="top-page"><a onclick="window.top.window.scrollTo(0,0);return false" href="#wrapper-rl"></a></div>
<div id="wrapper-rl" class="<?php echo $connected; ?>">
	<input type="checkbox" id="menu-left-checkbox" class="menu-left-checkbox" role="button">
	<input type="checkbox" id="menu-search-checkbox" class="menu-search-checkbox">
	<input type="checkbox" id="menu-forum-checkbox" class="menu-forum-checkbox">

	<div id="wrapper-inner" class="<?php echo $hackOperaMini ?>">
		<div id="header">
			<label for="menu-left-checkbox" class="slide-left-toggle" onClick="CloseOtherMenu('forum','search', '', 0);"></label>
			<label for="menu-forum-checkbox" class="menu-forum-toggle" onClick="CloseOtherMenu('left','search', '', 0);"></label>
			<label for="menu-search-checkbox" class="menu-search-toggle" onClick="CloseOtherMenu('left','forum', '', 1);"></label>
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

# Redirection Search
# Opitux
$RLinput = new UserInput();
$RLsearch = str_replace('&#34;', '"', $RLinput->get('q', 'string'));
$RLpath = $RLinput->get('domainroot', 'string');
$RLwiki = $RLinput->get('do', 'string');

if( isset($RLsearch) && null != $RLsearch ) {
	switch ($RLpath) {
		case 'interne':

			header('Location: '.folder_rl.'/wiki/doku.php?do=search&id='.$RLsearch.'');
			break;

		default:

			if ($RLwiki != 'search' ) :
				header('Location: https://www.qwant.com/?q=site%3Awww.randonner-leger.org%2F'.$RLpath.'+'.$RLsearch.'');
			endif;

		break;
	}
}
?>

						<script type="text/javascript">
						function CheckSearchRL(){
							var RLsearch = document.getElementById('q').value.replace(/ /g, '+');
							var RLvintage = (RLsearch == 'fermeture+rl');
								if(RLvintage){
									alert('Aïe !! \nVotre requête vous entraîne dans les tréfonds du forum ;)');
									document.location.href="<?php echo $site_url . folder_rl ; ?>/forum/uploads/2_bd_annivrl_7ans.gif";
									return false;
								}
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
