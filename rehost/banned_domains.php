<?php
$banned_from_rehost = array(

	'images.imagehotel.net',
	'img.imagesia.com',
	'img.directindustry.fr',
	'www.racquetsportsindustry.com',

	// Prevent SSRF
	'www.randonner-leger.org',
	'dev.randonner-leger.org',

	'localhost',
	'127.0.0.1',
	'spoofed.burpcollaborator.net',
	'127.0.0.1.nip.io',
	'127.0.1',
	'127.1',

	'::1',
	'::ffff:127.0.0.1',
	'::127.0.0.1',

);
