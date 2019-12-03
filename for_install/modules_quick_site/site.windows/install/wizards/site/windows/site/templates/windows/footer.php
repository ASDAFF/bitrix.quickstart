<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	IncludeTemplateLangFile(__FILE__);
?>
</div>
</div>
</div>
<footer class="motopress-wrapper footer">
	<div class="container">
	<div class="row">
		<div class="span12">
			<div class="row footer-widgets">
				<div class="span12" >
					<div id="text-6" class="visible-all-devices ">
						<div class="textwidget"><?
							$APPLICATION->IncludeFile(
								SITE_DIR."include/contacts_address_footer.php",
								Array(),
								Array("MODE"=>"html")
							);
						?></div>
					</div>
					<div id="text-7" class="visible-all-devices large-text ">
						<div class="textwidget"><?
							$APPLICATION->IncludeFile(
								SITE_DIR."include/contacts_phone_footer.php",
								Array(),
								Array("MODE"=>"html")
							);
						?></div>
						</div>
						</div>
				</div>
				<div class="row">
					<div class="span12 social-nets-wrapper" data-motopress-type="static" data-motopress-static-file="static/static-social-networks.php">
						<ul class="social">
						<li><a href="http://twitter.com" title="twitter"><img src="<?=SITE_TEMPLATE_PATH?>/images/social/twitter.png" alt="twitter"></a></li><li><a href="http://facebook.com" title="facebook"><img src="<?=SITE_TEMPLATE_PATH?>/images/social/facebook.png" alt="facebook"></a></li><li><a href="http://google.com" title="google"><img src="<?=SITE_TEMPLATE_PATH?>/images/social/google+.png" alt="google"></a></li><li><a href="#" title="forrst"><img src="<?=SITE_TEMPLATE_PATH?>/images/social/forrst.png" alt="forrst"></a></li><li><a href="#" title="dribbble"><img src="<?=SITE_TEMPLATE_PATH?>/images/social/dribbble.png" alt="dribbble"></a></li></ul> </div>
				</div>
				<div class="row copyright">
					<?
						$APPLICATION->IncludeFile(
							SITE_DIR."include/copyright.php",
							Array(),
							Array("MODE"=>"html")
						);
					?>
				</div>
				<div class="row">
					<div class="span12" data-motopress-type="static">
					</div>
			</div> </div>
		</div>
	</div>
</footer>

</div>
<div id="back-top-wrapper" class="visible-desktop">
	<p id="back-top">
		<a href="#top"><span></span></a>
	</p>
</div>
<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/superfish.js'></script>
<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.mobilemenu.js'></script>
<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.easing.1.3.js'></script>
<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.magnific-popup.min.js'></script>
<?if($APPLICATION->GetCurDir() == "/"):?>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.flexslider.js'></script>
	<!--script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jplayer.playlist.min.js'></script>
	<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.jplayer.min.js'></script-->
	<?endif;?>
		<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/camera.min.js'></script>
		<script type='text/javascript' src='<?=SITE_TEMPLATE_PATH?>/js/jquery.debouncedresize.js'></script>
	</body>
</html>


