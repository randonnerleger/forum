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

# Redirection Search
# Opitux
class UserInput {
	protected $post, $get, $cookie;
	/**
	* __construct
	*
	* Create a new instance of UserInput
	*/
	public function __construct() {
		$this->post = $_POST;
		$this->get = $_GET;
		$this->cookie = $_COOKIE;
	}
	/**
	* get
	* Get a value from $_GET and sanitize it
	*
	* @param string $key	Key to get from array
	* @param string $type   What type is the variable (string, email, int, float, encoded, url, email)
	* @param array  $option Options for filter_var
	* @return mixed will return false on failure
	*/
	public function get($key, $type = 'string', $options = array()) {
		if (!isset($this->get[$key])) {
			return false;
		}
		return filter_var($this->get[$key], $this->get_filter($type), $options);
	}
	/**
	* post
	* Get a value from $_POST and sanitize it
	*
	* @param string $key	Key to get from array
	* @param string $type   What type is the variable (string, email, int, float, encoded, url, email)
	* @param array  $option Options for filter_var
	* @return mixed will return false on failure
	*/
	public function post($key, $type='string', $options = array()) {
		if (isset($this->post[$key])) {
			return false;
		}
		return filter_var($this->post[$key], $this->get_filter($type), $options);
	}
	/**
	* cookie
	* Get a value from $_COOKIE and sanitize it
	*
	* @param string $key	Key to get from array
	* @param string $type   What type is the variable (string, email, int, float, encoded, url, email)
	* @param array  $option Options for filter_var
	* @return mixed will return false on failure
	*/
	public function cookie($key, $type='string', $options = array()) {
		if (isset($this->cookie[$key])) {
			return false;
		}
		return filter_var($this->cookie[$key], $this->get_filter($type), $options);
	}
	private function get_filter($type) {
		switch (strtolower($type)) {
			case 'string':
				$filter = FILTER_SANITIZE_STRING;
				break;
			case 'int':
				$filter = FILTER_SANITIZE_NUMBER_INT;
				break;
			case 'float' || 'decimal':
				$filter = FILTER_SANITIZE_NUMBER_FLOAT;
				break;
			case 'encoded':
				$filter = FILTER_SANITIZE_ENCODED;
				break;
			case 'url':
				$filter = FILTER_SANITIZE_URL;
				break;
			case 'email':
				$filter = FILTER_SANITIZE_EMAIL;
				break;
			default:
				$filter = FILTER_SANITIZE_STRING;
		}
		return $filter;
	}
}

$RLinput = new UserInput();
$RLsearch = $RLinput->get('q', 'string');
$RLpath = $RLinput->get('domainroot', 'string');
$RLwiki = $RLinput->get('do', 'string');

echo $RLpath;

if( isset($RLsearch) && null != $RLsearch ) {
	switch ($RLpath) {
		case 'interne':

			header('Location: /wiki/doku.php?do=search&id='.$RLsearch.'');
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
									document.location.href="https://www.randonner-leger.org/forum/uploads/2_bd_annivrl_7ans.gif";
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
