				<? IncludeTemplateLangFile(__FILE__); ?>
				</div><!-- #content -->
				<div id="leftside" class="noprint">
					<div id="box-filter"></div>
					<div class="sidebox" id="navcategories">
						<?
						$SHOW_CATS = TRUE;
						if (
							strpos($APPLICATION->GetCurDir(), '#SITE_DIR#help/')!==FALSE || 
							strpos($APPLICATION->GetCurDir(), '#SITE_DIR#developments/')!==FALSE  || 
							strpos($APPLICATION->GetCurDir(), '#SITE_DIR#about/')!==FALSE  ||
							strpos($APPLICATION->GetCurDir(), '#SITE_DIR#personal/')!==FALSE
							//|| CSite::InDir('#SITE_DIR#catalog/')
						) {
							$SHOW_CATS = FALSE;
						}
						?>
						<h3 class="boxheader">
							<span class="sidenavswitcher" id="switcher_navcategories">
								<? if (!$SHOW_CATS) { ?>
									<img width="9" height="9" class="sidenavswitcherimg noprint" title="<?=GetMessage('CATEGORY_SHOW')?>" alt="+" src="#SITE_DIR#images/buttons/btn_collapsed.gif">
								<? } else { ?>
									<img width="9" height="9" class="sidenavswitcherimg noprint" title="<?=GetMessage('CATEGORY_HIDE')?>" alt="-" src="#SITE_DIR#images/buttons/btn_expanded.gif">
								<? } ?>
							</span><?=GetMessage('CATEGORY_BLOCK_NAME')?>&nbsp;&nbsp;&nbsp;
						</h3>
						<div id="navcategoriescontent"<? if (!$SHOW_CATS) { ?> style="display: none;"<? } ?>>
							<?$APPLICATION->IncludeComponent("softeffect:catalog.categories", ".default", array(
	"IBLOCK_TYPE" => "sw_catalog",
	"IBLOCK" => "#sw_category#",
	"CATEGORIES_URL" => "#SITE_DIR#catalog/category/",
	),
	false
);?>
						</div>
						<div class="boxfooter"></div>
					</div><!-- #navcategories -->
					
					<? if (!$SHOW_CATS) { ?>
						<?$APPLICATION->IncludeComponent("bitrix:menu", "left_help", Array(
						"ROOT_MENU_TYPE" => "left",
						"MENU_CACHE_TYPE" => "A",
						"MENU_CACHE_TIME" => "3600",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => "",
						"MAX_LEVEL" => "1",
						"CHILD_MENU_TYPE" => "",
						"USE_EXT" => "N",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N",
						),
						false
						);?>
					<? } ?>
					<?$APPLICATION->IncludeComponent("softeffect:sale.viewed.product", ".default", array(
	"MAX_COUNT" => "10",
	),
	false
);?>
					<? if (CModule::IncludeModule('advertising')) { ?>
						<div class="sideimagelink">
							<?$APPLICATION->IncludeComponent("bitrix:advertising.banner", ".default", array(
								"TYPE" => "store_software_baner_left_1",
								"NOINDEX" => "Y",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "3600"
								),
								false
							);?>
						</div>
						<div class="sideimagelink">
							<?$APPLICATION->IncludeComponent("bitrix:advertising.banner", ".default", array(
								"TYPE" => "store_software_baner_left_2",
								"NOINDEX" => "Y",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "3600"
								),
								false
							);?>
						</div>
					<? } ?>
				</div>
			</div>
			
			<div id="rightside" class="noprint">
				<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.small", "basket_small", array(
					"PATH_TO_BASKET" => "#SITE_DIR#basket/",
					"PATH_TO_ORDER" => "#SITE_DIR#basket/#step-order"
					),
					false
				);?>
				<?$APPLICATION->IncludeComponent("softeffect:catalog.topsellers", ".default", array(
	"IBLOCK_TYPE" => "sw_catalog",
	"IBLOCK" => "#sw_liders#",
	"MAX_COUNT" => "5",
	),
	false
);?>
				<? if (CModule::IncludeModule('advertising')) { ?>
					<div class="sideimagelink">
						<?$APPLICATION->IncludeComponent("bitrix:advertising.banner", ".default", array(
							"TYPE" => "store_software_baner_right_1",
							"NOINDEX" => "Y",
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "3600"
							),
							false
						);?>
					</div>
					<div class="sideimagelink">
						<?$APPLICATION->IncludeComponent("bitrix:advertising.banner", ".default", array(
						"TYPE" => "store_software_baner_right_2",
						"NOINDEX" => "Y",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "3600"
							),
							false
						);?>
					</div>
				<? } ?>
			</div><!-- #rightside -->
			
			<div id="footer">
				<div class="content">
					<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
		"ROOT_MENU_TYPE" => "bottom",	// Тип меню для первого уровня
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"MAX_LEVEL" => "1",	// Уровень вложенности меню
		"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	),
	false
);?>
					<p class="copyright">
						&copy; <?=date('Y')?> <a href="#SITE_DIR#"><? $APPLICATION->IncludeFile('#SITE_DIR#include/copyright.php', array(), array('MODE'=>'text')); ?></a> <?=GetMessage('COPYRIGHT')?>
					</p>
				</div>
				<div id="footerbox">
					<table border="0" cellspacing="0" cellpadding="0" class="tf">
						<tr>
							<td width="355" style="padding-right: 2px;"><?$APPLICATION->IncludeFile('#SITE_DIR#include/pay.php', array(), array('MODE'=>'html')); ?></td>
							<td align="right"><a href="http://www.softeffect.ru"><?=GetMessage('WORK_SOFTEFFECT')?></a> <?=GetMessage('WORK_SOFTEFFECT_FROM')?> softeffect.ru</td>
							<td width="125" align="right" class="social">
								<?if ($shopFacebook = COption::GetOptionString("softeffect.storesoftware", "shopFacebook", "", SITE_ID)) { ?>
									<a href="<?=$shopFacebook?>" rel="nofollow"><img src="#SITE_DIR#images/facebook.gif" width="31" height="32" /></a>
								<? }
								if ($shopTwitter = COption::GetOptionString("softeffect.storesoftware", "shopTwitter", "", SITE_ID)) { ?> 
									<a href="<?=$shopTwitter?>" rel="nofollow"><img src="#SITE_DIR#images/tweet.gif" width="31" height="32" /></a>
								<? }
								if ($shopVk = COption::GetOptionString("softeffect.storesoftware", "shopVK", "", SITE_ID)) { ?>
									<a href="<?=$shopVk?>" rel="nofollow" ><img src="#SITE_DIR#images/vkont.gif" width="31" height="32" /></a>
								<? } ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>