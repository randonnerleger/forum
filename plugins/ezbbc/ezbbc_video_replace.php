<?php
// MODIF RL OPITUX
// RGPD VIDEO CONSENT
//
// CODE ORIGINAL
// // DailyMotion videos
// $replace[] = '<iframe width="480" height="360" src="https://www.dailymotion.com/embed/video/$4"></iframe>';
// $replace[] = '<iframe width="$1" height="$2" src="https://www.dailymotion.com/embed/video/$6"></iframe>';
// // Youtube Videos
// $replace[] = '<iframe width="480" height="360" src="https://www.youtube.com/embed/$4"></iframe>';
// $replace[] = '<iframe width="$1" height="$2" src="https://www.youtube.com/embed/$6"></iframe>';
// // Vimeo Videos
// $replace[] = '<iframe width="480" height="360" src="https://player.vimeo.com/video/$4"></iframe>';
// $replace[] = '<iframe width="$1" height="$2" src="https://player.vimeo.com/video/$6"></iframe>';

// MODELE HTML
// <div class="embedvideo" id="$6" style="max-width:480px;max-height:360px;">
// 	<div id="embedhover">
// 		<a onClick="VideoConsentDisplay('$6', 'youtube', '480', '360');return false" href="https://youtube.com/watch?v=$6" target="_blank">
// 			<div><svg class="video-overlay-play-button" viewBox="0 0 200 200" alt="Play video"><polygon points="70, 55 70, 145 145, 100" fill="#fff"/></svg></div>
// 		</a>
// 	</div>
// 	<a onClick="VideoConsentDisplay('$6', 'youtube', '480', '360');return false" href="https://youtube.com/watch?v=$6" id="$6THUMB" target="_blank"><img src="https://img.youtube.com/vi/$6/0.jpg" /></a>
// </div>
// CODE MODIFIE

$playbutton='<div><svg class="video-overlay-play-button" viewBox="0 0 200 200" alt="Play video"><polygon points="70, 55 70, 145 145, 100" fill="#fff"/></svg></div>';

// DailyMotion videos
$replace[] = '
<div class="embedvideo" id="$4" style="max-width:480px;max-height:360px;"><div id="embedhover"><a onClick="VideoConsentDisplay(\'$4\', \'dailymotion\', \'480\', \'360\');return false" href="https://www.dailymotion.com/video/$4" target="_blank">' . $playbutton . '</a></div><a onClick="VideoConsentDisplay(\'$4\', \'dailymotion\', \'480\', \'360\');return false" href="https://www.dailymotion.com/video/$4" id="$4THUMB" target="_blank"><img src="https://www.dailymotion.com/thumbnail/video/$4/" /></a></div>';
$replace[] = '<div class="embedvideo" id="$6" style="max-width:$1px;max-height:$2px;"><div id="embedhover"><a onClick="VideoConsentDisplay(\'$6\', \'dailymotion\', \'$1\', \'$2\');return false" href="https://www.dailymotion.com/video/$6" target="_blank">' . $playbutton . '</a></div><a onClick="VideoConsentDisplay(\'$6\', \'dailymotion\', \'$1\', \'$2\');return false" href="https://www.dailymotion.com/video/$6" id="$6THUMB" target="_blank"><img src="https://www.dailymotion.com/thumbnail/video/$6/" /></a></div>';

// Youtube Videos
$replace[] = '
<div class="embedvideo" id="$4" style="max-width:480px;max-height:360px;"><div id="embedhover"><a onClick="VideoConsentDisplay(\'$4\', \'youtube\', \'480\', \'360\');return false" href="https://youtube.com/watch?v=$4" target="_blank">' . $playbutton . '</a></div><a onClick="VideoConsentDisplay(\'$4\', \'youtube\', \'480\', \'360\');return false" href="https://youtube.com/watch?v=$4" id="$4THUMB" target="_blank"><img src="https://img.youtube.com/vi/$4/0.jpg" /></a></div>';
$replace[] = '<div class="embedvideo" id="$6" style="max-width:$1px;max-height:$2px;"><div id="embedhover"><a onClick="VideoConsentDisplay(\'$6\', \'youtube\', \'$1\', \'$2\');return false" href="https://youtube.com/watch?v=$6" target="_blank">' . $playbutton . '</a></div><a onClick="VideoConsentDisplay(\'$6\', \'youtube\', \'$1\', \'$2\');return false" href="https://youtube.com/watch?v=$6" id="$6THUMB" target="_blank"><img src="https://img.youtube.com/vi/$6/0.jpg" /></a></div>';

// Vimeo Videos
$replace[] = '
<div class="embedvideo" id="$4" style="max-width:480px;max-height:360px;"><div id="embedhover"><a onClick="VideoConsentDisplay(\'$4\', \'vimeo\', \'480\', \'360\');return false" href="https://vimeo.com/$4" target="_blank">' . $playbutton . '</a></div><a onClick="VideoConsentDisplay(\'$4\', \'vimeo\', \'480\', \'360\');return false" href="https://vimeo.com/$4" id="$4THUMB" target="_blank"><img src="https://i.vimeocdn.com/video/$4" /></a></div>';
$replace[] = '<div class="embedvideo" id="$6" style="max-width:$1px;max-height:$2px;"><div id="embedhover"><a onClick="VideoConsentDisplay(\'$6\', \'vimeo\', \'$1\', \'$2\');return false" href="https://vimeo.com/$6" target="_blank">' . $playbutton . '</a></div><a onClick="VideoConsentDisplay(\'$6\', \'vimeo\', \'$1\', \'$2\');return false" href="https://vimeo.com/$6" id="$6THUMB" target="_blank"><img src="https://i.vimeocdn.com/video/$6" /></a></div>';
?>
