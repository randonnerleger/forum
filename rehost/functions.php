<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function get_new_size( $old_w, $old_h, $max = 800 ) {

	if( $old_w > $old_h ) {
		$new_w = $max;
		$new_h = $old_h * ( $max/$old_w );
	}

	if( $old_w < $old_h ) {
		$new_w = $old_w * ( $max/$old_h );
		$new_h = $max;
	}

	if( $old_w == $old_h ) {
		$new_w = $max;
		$new_h = $max;
	}

	return array(
		(int)$new_w,
		(int)$new_h
	);

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
function file_get_contents_curl( $url ) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/96.0');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}
function get_rehost_attr( $url, $rehost = false ) {

	$parsed_url		 	= array_map( 'rawurlencode', parse_url( urldecode($url) ) );

	require ABSPATH . folder_forum . '/rehost/banned_domains.php';

	$parsed_url['path']	= str_replace( '%2F', '/', $parsed_url['path'] );
	$img_source			= filter_var( $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'], FILTER_SANITIZE_URL );
	$img_to_rehost		= $img_source;
	$img_extension		= pathinfo( $parsed_url['path'], PATHINFO_EXTENSION );
	$img_sha			= sha1( $img_source );
	$rehost_folder		= substr( $img_sha, 0, 2 );
	$rehost_hash		= $rehost_folder . '/' . substr($img_sha, 2);
	$rehost_path		= 'i/' . $rehost_hash . ( null != $img_extension ? '.' . $img_extension : '' );
	$rehost_path_tmp	= ABSPATH . folder_forum . '/rehost/' . str_replace( 'i/', 'tmp/', $rehost_path );
	$location			= false;

	$img_attr = array(
		'broken' => false,
		'source' => $img_source,
		'src' => path_to_forum . 'rehost/?img=' . $img_source,
		'extension' => $img_extension,
		'hash' => $rehost_hash,
		'folder' => $rehost_folder,
		'path' => $rehost_path,
		'width' => false,
		'height' => false,
		'downloading' => 'get',
		'redirect' => false,
		'tmp' => null
	);

	// Banned domains and http(s) only (SSRF)
	if ( in_array( $parsed_url['host'], $banned_from_rehost ) || ( $parsed_url['scheme'] != 'http' && $parsed_url['scheme'] != 'https' ) ) {
		$img_attr['broken'] = true;
		return $img_attr;
	}

	// Rehosted file exists, we return it
	if( file_exists( ABSPATH . folder_forum . '/rehost/' . $rehost_path) ) {

		$img_size = getimagesize( ABSPATH . folder_forum . '/rehost/' . $rehost_path );
		$img_attr = array_merge( $img_attr, array(
			'src'		=> path_to_forum . 'rehost/' . $rehost_path,
			'width'		=> $img_size[0],
			'height'	=> $img_size[1],
		));

	// Rehosted file does not exists, let's do it
	} else {

		// Prevent 504 timeout
		$ping = pingDomain( $parsed_url['host'] );
		if ( $ping == -1 ) {
			$img_attr['broken'] = true;
			return $img_attr;
		}

		$context = stream_context_create(
			array(
				'http'=> array(
					'timeout' => 1.0,
					'ignore_errors' => true
				)
			)
		);
		$headers = @get_headers( $img_source, 1, $context );

		// Wy get an header
		if ( $headers && null != $headers && strpos( $headers[0], "404" ) === false ) {

			$headers = array_change_key_case( $headers,  CASE_LOWER );

			// Redirect management
			if ( isset( $headers['location'] ) ) {

				$img_attr['redirect'] = substr( $headers[0], 9, 3 );

				if ( is_array( $headers['location'] ) ) {
					$location = end( $headers['location'] );
				} else {
					$location = $headers['location'];
				}

				foreach( $headers as $key => $value ) {
					if( ! is_int( $key ) ) {
						unset( $headers[$key] );
					}
				}

				$location = array_map( 'rawurlencode', parse_url( urldecode($location) ) );
				if ( pingDomain( $location['host'] ) == -1 || strpos( end( $headers ), "404" ) !== false ) {
					$img_attr['broken'] = true;
					return $img_attr;
				}

				$location['path']	= str_replace( '%2F', '/', $location['path'] );
				$img_to_rehost = filter_var( $location['scheme'] . '://' . $location['host'] . $location['path'], FILTER_SANITIZE_URL );

			}

		// Wy dont't get any header, lets try with curl
		} else {

			$img_attr['downloading'] = 'curl';

		}

		// Make image tmp storage folder
		if ( ! file_exists( ABSPATH . folder_forum . '/rehost/tmp' ) )
			mkdir(  ABSPATH . folder_forum . '/rehost/tmp' );

		// Make image tmp storage folder
		if ( ! file_exists( ABSPATH . folder_forum . '/rehost/tmp/' . $rehost_folder ) )
			mkdir(  ABSPATH . folder_forum . '/rehost/tmp/' . $rehost_folder );


		ini_set('user_agent', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/96.0');

		if ( $img_attr['downloading'] == 'get' )
			$data = @file_get_contents( $img_to_rehost );

		else
			$data = file_get_contents_curl( $img_to_rehost );

		// We try to download the image as tmp file
		file_put_contents( $rehost_path_tmp, $data );

		// We check the file is an image
		$img_size = @getimagesize( $rehost_path_tmp );
		if( is_array( $img_size ) ) {

			$img_attr = array_merge( $img_attr, array(
				'width'  => $img_size[0],
				'height' => $img_size[1],
				'tmp'	 => file_get_contents( $rehost_path_tmp ),
			));

		} else {

			$img_size = false;

		}

		// We remove the tmp file
		@unlink( $rehost_path_tmp );

	}

	// Rehosted file is not an image
	if ( ! is_array( $img_size ) ) {
		$img_attr['broken'] = true;
		return $img_attr;
	}

	// if( $img_attr['width'] > 800 || $img_attr['height'] > 800) {				// Rehosted file is large than 800px
	// 	$new_size = get_new_size( $img_attr['width'], $img_attr['height'], 800 );
	// 	$img_attr['width'] = $new_size[0];
	// 	$img_attr['height'] = $new_size[1];
	// }

	// Let's resize the image.
	// Max length is 800px or image length - 1px;
	$new_size = get_new_size(
		$img_attr['width'],
		$img_attr['height'],
		min( 800, ( max( (int)$img_attr['width'], (int)$img_attr['height'] ) - (int)1 ) )
	);
	$img_attr['width'] = $new_size[0];
	$img_attr['height'] = $new_size[1];

	return $img_attr;

}
