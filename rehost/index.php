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

$img_attr = get_rehost_attr($_GET['img'], true );

// Make generic storage folder
if ( ! file_exists( 'i' ) )
	mkdir( 'i' );

// If rehosted file does not exist...
if ( ! file_exists( $img_attr['path'] ) ) {

	// Only connected user are alloxed to rehost
	// if ( $pun_user['group_id'] != 3 ) {

		// If file we want to rehost is not an image (missing)
		if ( isset( $img_attr['tmp'] ) && $img_attr['tmp'] ) {
			$image = $img_attr['tmp'];

		// If file we want to rehost is an image
		} else {
			$img_attr = array_merge( $img_attr, array(
				'broken'	=> true,
				'width'		=> 200,
				'height'	=> 200
			));
			$image = file_get_contents( '404-missing.png' );
		}

		// Make image storage folder
		if ( ! file_exists( 'i/' . $img_attr['folder'] ) )
			mkdir( 'i/' . $img_attr['folder'] );

		// Write rehost data in a file
		file_put_contents( $img_attr['path'], $image );

		// Resize with Imagick
		if ( extension_loaded( 'imagick' ) ) {

			$resized_image = new Imagick( $img_attr['path'] );
			$resized_image->resizeImage( (int)$img_attr['width'], (int)$img_attr['height'], imagick::FILTER_LANCZOS, 1, true );
			$resized_image->writeImage( $img_attr['path'] );

		// Or resize with GD
		} else {

			resize_image( $img_attr['path'] , $img_attr['path'] , (int)$img_attr['width'] , (int)$img_attr['height']);

		}

		// Add image attr in .txt files
		file_put_contents(
			'i/' . ( $img_attr['broken'] ? 'broken' : 'rehost' ) . '.txt',
			  time() . ' '
			. $img_attr['source'] . ' '
			. $img_attr['hash'] . ' '
			. $img_attr['extension'] . ' '
			. $img_attr['width'] . ' '
			. $img_attr['height'] . ' '
			. filesize( $img_attr['path'] ) . ' '
			. $img_attr['downloading'] . ' '
			. $img_attr['redirect'] . PHP_EOL,
			FILE_APPEND
		);

	// }

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
