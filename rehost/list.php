<?php


require_once '../../configRL.php';
define('PUN_ROOT', '../');
require_once PUN_ROOT.'include/common.php';

$pun_user_guest = ($pun_user['is_guest'] ? true : false );
define('pun_user_guest', $pun_user_guest);

$GLOBALS['punname'] 	= $pun_user['username'];
$GLOBALS['punid'] 		= $pun_user['id'];
$GLOBALS['punusergroup'] 	= $pun_user['g_id'];

if( ! isset( $_COOKIE['FotooModo'] ) )
	header("Location: " . path_to_forum);

setlocale (LC_TIME, 'fr_FR.utf8');

function getPictureSize($size) {
	if ($size > (1024 * 1024))
		$size = round($size / 1024 / 1024, 2) . ' MB';
	elseif ($size > 1024)
		$size = round($size / 1024, 2) . ' KB';
	else
		$size = $size . ' B';

		return $size;
}

if (file_exists('../uploads/user_header.php'))
	require_once '../uploads/user_header.php';

$rehosted	= "i/rehost.txt";
$broken		= "i/broken.txt";
$rehosted_file		= file( $rehosted );
$broken_file		= file( $broken );

$limit	= 20;
$rehosted_count	= count( $rehosted_file );
$broken_count	= count( $broken_file );
echo '<div class="rehost-count">'. (int)$rehosted_count . ' rehost / ' . (int)$broken_count . ' en 404</div>';
$page	= isset($_GET['page']) ? (int)$_GET['page'] : (int)1;
$nbpage	= ceil($rehosted_count/$limit);

// $min  = ($page-1)*$limit;
// $max  = $page*$limit-1;

$count = isset( $_GET['404'] ) ? $broken_count : $rehosted_count ;
$file = isset( $_GET['404'] ) ? $broken_file : $rehosted_file ;

$max  = ($rehosted_count-1)-(($page-1)*$limit);
$min  = $max-($limit-1);
$min  = $min > 0 ? $min : 0;

echo '<div id="punindex" class="pun">

	<div class="punwrap">

		<div id="brdheader" class="block">

		<div class="box">

			<div id="brdtitle" class="inbox">
				<h1><a href="../uploads">Gérer mes images</a></h1>
				<h2 class="admin-mode">(admin mode)</h2>
			</div>

			<div id="brdmenu" class="inbox">
				<ul>
					<li><a href="../uploads">Envoyer une image</a></li>
					<li><a href="../uploads?list&mesphotos">Mes images</a></li>
					<li><a href="../uploads?albums&mesalbums">Mes albums</a></li>
					<li class="hidden-from-ez-toolbar"><a href="../uploads?list">Parcourir toutes les images</a></li>
					<li class="hidden-from-ez-toolbar"><a href="../uploads?albums">Parcourir tous les albums</a></li>
					<li class="hidden-from-ez-toolbar"><a href="../uploads?stats">Stats</a></li>
					<li class="hidden-from-ez-toolbar"><a href="../rehost/list.php?rehost">Rehost</a></li>
					<li class="hidden-from-ez-toolbar"><a href="../rehost/list.php?rehost&404">404</a></li>
				</ul>
			</div>

			<div id="brdwelcome" class="inbox">
				<ul class="conl">
					<li><span>Connecté(e) sous l\'identité&#160; <strong>' . htmlspecialchars($GLOBALS['punname'], ENT_QUOTES, 'utf-8', false) .'</strong></span></li>
				</ul>
				<div class="clearer"></div>
			</div>

		</div>

		</div>

		<div id="page">

		<article class="browse">
			<h2>Rehost</h2>';

		if( $min < $count ) {

			// for ( $i=$min; $i<=$max; $i++ ) {
			for ( $i=$max; $i>=$min; $i-- ) {
				$img = explode(' ', $file[$i]);

				$search = str_replace( 'https://www.dailymotion.com/thumbnail/video/', 'https://www.dailymotion.com/video/', $img[1] );
				$search = preg_replace( '/https:\/\/i.vimeocdn\.com\/video\/([0-9]+[0-9])+_([0-9]+[0-9])/', '[video][url]https://vimeo.com/', $search);
				$search = str_replace( 'https://img.youtube.com/vi/', 'https://youtube.com/watch?v=', $search );

				$source = array_map( 'rawurlencode', parse_url( $img[1] ) );

				echo '
				<figure>
					<div class="img">
						<a href="i/' . $img[2] . (null!=$img[3] ? '.' . $img[3] : '') . '" target="_blank"><img src="i/' . $img[2] . (null!=$img[3] ? '.' . $img[3] : '') . '"/></a>
					</div>
					<p class="meta profile">
						<strong class="author">' . filter_var( str_replace( 'www.', '', $source['host'] ), FILTER_SANITIZE_URL ) . '</strong><br />
						<i class="size">' . getPictureSize($img[6]) . '<br />
						<time datetime="'.date(DATE_W3C, $img[0]).'">'.strftime('%a %e %b %Y', $img[0]).'</time></i>
					</p>
					<p class="meta search"><a href="../search.php?action=search&keywords=&quot;' . $search . '&quot;&search_in=1&sort_by=0&sort_dir=DESC&show_as=posts&search=Valider" target="_blank">Rechercher</a> - <a href="' . filter_var( $img[1], FILTER_SANITIZE_URL ) . '" target="_blank"><small>source</small</a></p>
				</figure>';
			}

		}

		echo '<nav class="pagination"><ul>';
		for ( $i=1; $i<=$nbpage; $i++ ) {
			$selected = $i == $page ? 'selected' : '';
			echo '<li class="' . $selected . '"><a href="' . path_to_forum . 'rehost/list.php?page=' . (int)$i . '">' . (int)$i . '</a> </li>';
		}
		echo '</ul></nav>';

		echo '</article>

		</div><!-- #page -->

	</div><!-- .punwrap -->

</div><!-- #punindex -->';

if (file_exists('../uploads/user_footer.php'))
	require_once '../uploads/user_footer.php';
