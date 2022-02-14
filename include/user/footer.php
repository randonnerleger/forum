			</div><!-- .content -->
		</div><!-- .outer -->

		<div id="footer">
			<div class="inner">
			</div><!-- .inner -->
		</div><!-- #footer -->
	</div><!-- #wrapper-inner -->
</div><!-- #wrapper-rl -->

<script type='text/javascript'>
/* <![CDATA[ */
var myrl_info = {"folder_rl":"<?php echo folder_rl; ?>"};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo path_to_rl.'tpl/js/scripts.min.js?version=<?php echo current_theme ?>'?>'></script>

<?php
if ( defined( 'SWIPEBOX_ON' ) && SWIPEBOX_ON ) {
?>
<script type='text/javascript' id='swipebox-js-extra'>
/* <![CDATA[ */
var SwipeboxInit = {"lightbox":{"useCSS":true,"useSVG":true,"initialIndexOnArray":0,"hideCloseButtonOnMobile":false,"removeBarsOnMobile":false,"hideBarsDelay":3000,"videoMaxWidth":1140,"vimeoColor":"#cccccc","loopAtEnd":false,"autoplayVideos":false},"autodetect":{"autodetectImage":true,"autodetectVideo":true,"autodetectExclude":".no-swipebox"}};
/* ]]> */
</script>
<script type='text/javascript' src='<?php echo path_to_rl ?>tpl/js/jquery.swipebox.min.js?version=<?php echo current_theme ?>' id='swipebox-js'></script>
<script type='text/javascript' src='<?php echo path_to_rl ?>tpl/js/jquery.init.min.js?version=<?php echo current_theme ?>' id='swipebox-init-js'></script>
<?php
}
?>
