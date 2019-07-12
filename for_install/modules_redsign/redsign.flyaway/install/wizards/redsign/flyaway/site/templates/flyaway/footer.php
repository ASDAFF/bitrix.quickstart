<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $HIDE_SIDEBAR;
if(!$HIDE_SIDEBAR) {
    $APPLICATION->AddViewContent('sidebar_wrap', 'sidebar-wrap');
}
?>

            </div><!-- /content -->
        </div><!-- /maincontent -->

    </div><!-- /row -->

</div><!--/container-->

		<footer class="footer footer-decor">
			<div class="footer-logo separator">
				<div class="container">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/company_logo.php"), false);?>
				</div>
			</div>
			<div class="footer-nav separator">
				<div class="container">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer/other_buttons.php"), false);?>
					<?$APPLICATION->ShowViewContent("location");?>
				</div>
			</div>
			<div class="footer-social">
				<div class="container">
					<div class="footer-block footer-block_subscribe">
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.include",
							"flyaway",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => SITE_DIR."include/footer/subscribe.php",
								"COMPONENT_TEMPLATE" => "flyaway",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</div>
					<div class="clearfix visible-sm visible-xs"></div>
					<div class="footer-block footer-block_social">
						<div class="footer-block__social">
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer/social_buttons.php"), false);?>
						</div>
					</div>
					<div class="clearfix visible-sm visible-xs"></div>
					<div class="footer-block">
						<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer/pay_buttons.php"), false);?>
					</div>
				</div>
			</div>
			<div class="footer-contacts">
				<div class="container">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/header/phones.php"), false);?>
				</div>
			</div>
			<div class="footer-copyright footer-decor">
				<div class="container">
					<div class="footer-copy__block element"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer/law.php"), false);?></div>
					<div class="footer-copy__block"><span id="bx-composite-banner"></span></div>
					<?php // #REDSIGN_COPYRIGHT# ?>
					<div class="footer-copy__block element"><?=Loc::getMessage('RS.FLYAWAY.COPYRIGHT')?></div>
				</div>
			</div>
		</footer>
    <?$APPLICATION->IncludeFile(SITE_DIR."include_areas/footer/compare.php", Array(), Array("MODE"=>"html"));?>
    <?$APPLICATION->IncludeFile(SITE_DIR."include_areas/footer/widget.php", Array(), Array("MODE"=>"html"));?>
	</div><!--/wrapper-->

	<div id="fixedcomparelist">
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer/compares.php"), false);?>
	</div>

        <div class="mobile-menu-wrap js-mobile-menu">
            <div class="mobile-menu">
                <div class="mobile-menu__personal">
                    <div class="mobile-menu__profile">
                        <?php $APPLICATION->ShowViewContent('system_auth_for_mobile'); ?>
                    </div>
                    <div class="mobile-menu__personal-icons">
                         <?php $APPLICATION->ShowViewContent('inheadcompare_mobile'); ?>
                        <?php $APPLICATION->ShowViewContent('inheadfavorite_mobile'); ?>
                    </div>
                </div>
                <?$APPLICATION->IncludeFile(SITE_DIR."include_areas/footer/mobile_menu.php", Array(), Array("MODE"=>"html"));?>
            </div>
        </div>
        <div style="display:none;">AlfaSystems FlyAway DSA821HWAD21</div>
	<?$APPLICATION->IncludeFile(SITE_DIR."include/template/body_end.php",array(),array("MODE"=>"html"))?>
</body>
</html>
