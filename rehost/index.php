<?php


if ( ! isset ( $_GET['img'] ) )
	return;

define('PUN_ROOT', '../');
include PUN_ROOT.'include/common.php';
include PUN_ROOT.'../configRL.php';

ini_set('display_errors', 'off');

// echo basename( $_SERVER['PHP_SELF'] ) . '<br>';
// $result = $db->query( 'SELECT SUM(num_posts) FROM '.$db->prefix.'forums');
// list( $stats['total_posts'] ) = $db->fetch_row( $result );
// echo (int)$stats['total_posts'];

function resize_image( $src , $dest , $toWidth , $toHeight , $options = array() ) {

	if ( ! file_exists( $src ) ) {
		die( "$src file does not exist" );
	}

	// OPEN THE IMAGE INTO A RESOURCE
	$img = imagecreatefromjpeg( $src );	// try jpg

	if ( ! $img )
		$img = imagecreatefromgif ( $src );	// try gif

	if ( ! $img )
		$img = imagecreatefrompng( $src );	// try png

	if ( ! $img )
		die( "Could Not create image resource $src" );

	// ORIGINAL DIMENTIONS
	list( $width , $height ) = getimagesize( $src );

	// ORIGINAL SCALE
	$xscale=$width/$toWidth;
	$yscale=$height/$toHeight;

	// NEW DIMENSIONS WITH SAME SCALE
	if ( $yscale && $xscale ) {
		$new_width = round( $width * (1/$yscale ) );
		$new_height = round( $height * (1/$yscale ) );
	} else {
		$new_width = round( $width * (1/$xscale ) );
		$new_height = round( $height * (1/$xscale ) );
	}

	// NEW IMAGE RESOURCE
	if ( !( $imageResized = imagecreatetruecolor( $new_width, $new_height ) ) )
		die( "Could not create new image resource of width : $new_width , height : $new_height" );

	// RESIZE IMAGE
	if ( ! imagecopyresampled( $imageResized, $img , 0 , 0 , 0 , 0 , $new_width , $new_height , $width , $height))
		die( "Resampling failed" );

	// STORE IMAGE INTO DESTINATION
	if ( ! imagejpeg( $imageResized , $dest ) )
		die( "Could not save new file" );

	// Free the memory
	imagedestroy( $img );
	imagedestroy( $imageResized );

	return true;

}

function pingDomain($domain){

	$starttime = microtime(true);
	// supress error messages with @
	$file      = @fsockopen($domain, 80, $errno, $errstr, 10);
	$stoptime  = microtime(true);
	$status    = 0;

	if (!$file){

		$status = -1;  // Site is down

	} else {

		fclose($file);
		$status = ($stoptime - $starttime) * 1000;
		$status = floor($status);

	}

	return $status;

}

$source = array_map( 'rawurlencode', parse_url( $_GET['img'] ) );
$ping = pingDomain( $source['host'] );
if ( $ping > 0 && $ping < 300 ) {	// Site id up

	$img_attr = get_rehost_attr( $_GET['img'], true );

	// Make generic storage folder
	if ( ! file_exists( 'i' ) )
		mkdir( 'i' );

	// If the file does not exist...
	if ( ! file_exists( $img_attr['path'] ) ) {

		// Only connected user are alloxad to rehost
		if ( $pun_user['group_id'] != 3 ) {

			if ( isset( $img_attr['broken'] ) && $img_attr['broken'] ) {			// If file we want to rehost is not an image (missing)
				$image = file_get_contents( htmlentities( '404-missing.png' ) );
			} else {
				$image = file_get_contents( htmlentities( $img_attr['source'] ) );	// Get file content
			}

			// Make image storage folder
			if ( ! file_exists( 'i/' . $img_attr['folder'] ) )
				mkdir( 'i/' . $img_attr['folder'] );

			// Write rehost data in a file
			file_put_contents( $img_attr['path'], $image );

			if ( extension_loaded( 'imagick' ) ) {									// Resize with Imagick

				$resized_image	= new Imagick( $img_attr['path'] );
				$resized_image->resizeImage( (int)$img_attr['width'], (int)$img_attr['height'], imagick::FILTER_LANCZOS, 1, true );
				$resized_image->writeImage( $img_attr['path'] );

			} else {																// Or resize with GD

				resize_image( $img_attr['path'] , $img_attr['path'] , (int)$img_attr['width'] , (int)$img_attr['height']);

			}

			// Add image attr in .txt files
			file_put_contents(
				'i/' . ( $img_attr['broken'] ? 'broken' : 'rehost' ) . '.txt',
				time() . ' ' . $img_attr['source'] . ' ' . $img_attr['hash'] . ' ' . $img_attr['extension'] . ' ' . $img_attr['width'] . ' ' . $img_attr['height'] . ' ' . filesize( $img_attr['path'] ) . PHP_EOL,
				FILE_APPEND
			);

		}

	}

}

if ( ! is_writable( 'i' ) )
	$img_attr['path'] = 'unwritable.png'; // Generic storage folder is unwritable

// open the file in a binary mode
$fp = fopen( $img_attr['path'], 'rb' );

// send headers
header( "Content-Type: image/png" );
header( "Content-Length: " . filesize( $img_attr['path'] ) );

// dump the image and exit
fpassthru( $fp );
exit;
