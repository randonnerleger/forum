<?php
// Retrieving smilies set enabled
require PUN_ROOT.'plugins/ezbbc/config.php';
$smilies = array(
	':)' => 'smile.png',
	'=)' => 'smile.png',
	':|' => 'neutral.png',
	'=|' => 'neutral.png',
	':(' => 'sad.png',
	'=(' => 'sad.png',
	':D' => 'big_smile.png',
	'=D' => 'big_smile.png',
	':o' => 'yikes.png',
	':O' => 'yikes.png',
	';)' => 'wink.png',
	':/' => 'hmm.png',
	':P' => 'tongue.png',
	':p' => 'tongue.png',
	':lol:' => 'lol.png',
	':mad:' => 'mad.png',
	':rolleyes:' => 'roll.png',
	':cool:' => 'cool.png',

	// MODIF
	// Ajout de smileys
	'8.(' => 'cry.gif',
	':cry:' => 'cry.gif',
	':-O' => 'eek.gif',
	':eek:' => 'eek.gif',
	':-[' => 'ops.png',
	':ops:' => 'ops.png',
	']:D' => 'devil.gif',
	':devil:' => 'devil.gif',
	':rl:' => 'rl.png',
	':unicorn:' => 'unicorn.png',
	':pouce:' => 'pouce.png',
	':calin:' => 'calin.png',
	// END

	);

if ($ezbbc_config['smilies_set'] == 'ezbbc_smilies'):
$ezbbc_smilies = array(
	//New smilies
	'O:)' => 'angel.png',
	'o:)' => 'angel.png',
	':angel:' => 'angel.png',
	'8.(' => 'cry.png',
	':cry:' => 'cry.png',
	']:D' => 'devil.png',
	':devil:' => 'devil.png',
	'8)' => 'glasses.png',
	':glasses:' => 'glasses.png',
	'{)' => 'kiss.png',
	':kiss:' => 'kiss.png',
	'8o' => 'monkey.png',
	':monkey:' => 'monkey.png',
	':8' => 'ops.png',
	':ops:' => 'ops.png');

$smilies = array_merge($smilies, $ezbbc_smilies);
endif;
?>
