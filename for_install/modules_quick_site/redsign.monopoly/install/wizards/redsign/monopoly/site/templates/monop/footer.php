<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
IncludeTemplateLangFile(__FILE__);

// monopoly options
$blackMode = RSMonopoly::getSettings('blackMode', 'N' );
$headType = RSMonopoly::getSettings('headType', 'type1');
$filterType = RSMonopoly::getSettings('filterType', 'ftype0');
$sidebarPos = RSMonopoly::getSettings('sidebarPos', 'pos1');
global $IS_CATALOG, $IS_CATALOG_SECTION;

// is main page
$IS_MAIN = false;
if( $APPLICATION->GetCurPage(true)==SITE_DIR.'index.php' )
	$IS_MAIN = true;

// hide sidebar
$HIDE_SIDEBAR = false;
if($APPLICATION->GetProperty('hidesidebar')=='Y' || $IS_MAIN)
	$HIDE_SIDEBAR = true;
if($headType=='type3' || ($IS_CATALOG && $IS_CATALOG_SECTION && $filterType=='ftype1')) { $HIDE_SIDEBAR = false; }
if(defined('ERROR_404') && ERROR_404=='Y') { $HIDE_SIDEBAR = true; }

if($HIDE_SIDEBAR):?>
<script>$('.maincontent').removeClass('col-md-9 col-md-push-3').addClass('col-md-12');</script>
<?else:?>
</div>
<div id="sidebar" class="col col-md-3<?=($sidebarPos=='pos1' ? ' col-md-pull-9' : '')?>">

<div class="hidden-xs hidden-sm">
	<?$APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/menu.php",array("HEAD_TYPE"=>$headType),array("MODE"=>"html"));?>
</div>

<?$APPLICATION->ShowViewContent('smartfilter');?>

<div class="hidden-xs hidden-sm">
	<?$APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/widgets.php",array(),array("MODE"=>"html"));?>
	<?$APPLICATION->IncludeFile(SITE_DIR."include_areas/sidebar/text.php",array(),array("MODE"=>"html"));?>
</div>

<?endif;?>

				</div><!-- /col -->
			</div><!-- /row -->

		</div><!-- /container -->

		<footer>
			<div class="container">
				<div class="row">
					<div class="col col-md-3">
						<div class="footer_logo_wrap logo">
							<a href="<?=SITE_DIR?>">
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?>
							</a>
						</div>
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_name.php"), false);?>
					</div>
					<div class="col col-md-3">
						<div class="box contacts"><div class="in roboto"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/phones.php"), false);?></div></div>
					</div>
					<div class="col col-md-3">
						<div class="other_buttons_wrap">
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer/other_buttons.php"), false);?>
                        </div>
					</div>
					<div class="col col-md-3">
						<?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/subscribe.php'); ?>
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer/social_buttons.php"), false);?>
					</div>
				</div>
			</div>
		</footer>
		<div class="footer_copyright">
			<div class="container">
				<div class="row">
					<div class="col col-lg-6 col-md-8"><span class="all_rights"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/copyright.php"), false);?></span></div>
					<div class="col col-lg-3 col-md-4 alright"><span id="bx-composite-banner"></span></div>
					<? // #REDSIGN_COPYRIGHT# ?>
					<div class="col col-lg-3 col-md-12 alright"><span class="alfa_title"><?=GetMessage('RS.MONOPOLY.COPYRIGHT')?></span></div>
				</div>
			</div>
		</div>

	</div><!-- wrapper -->

<?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/widget_settings.php'); ?>
<?$APPLICATION->IncludeFile(SITE_DIR."include_areas/body_end.php",array(),array("MODE"=>"html"));?>
<div id="fixedcomparelist"><?php include($_SERVER['DOCUMENT_ROOT'].SITE_DIR.'include/footer/compares.php'); ?></div>

</body>
</html>