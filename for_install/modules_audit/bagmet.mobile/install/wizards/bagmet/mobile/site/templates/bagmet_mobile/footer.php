<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
IncludeTemplateLangFile(__FILE__);
?>
			</div><!-- //workarea-->
			<div>
				<?$APPLICATION->IncludeComponent(
					"bitrix:main.include",
					"",
					Array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => SITE_DIR."include/viewed.php",
						"AREA_FILE_RECURSIVE" => "N",
						"EDIT_MODE" => "html",
					),
					false,
					Array('HIDE_ICONS' => 'Y')
				);?>
			</div>
			<div class="splitter"></div>
		</div><!--//page-->
		<!-- SKU window will be-->
		<div class="footer_splitter"></div>
	</div><!--//wrapper-->

	<div class="footer">
		<div class="footer_content">
			<?$APPLICATION->IncludeComponent("bitrix:menu", "footer", array(
					"ROOT_MENU_TYPE" => "bottom",
					"MAX_LEVEL" => "1",
					"MENU_CACHE_TYPE" => "A",
					"MENU_CACHE_TIME" => "36000000",
					"MENU_CACHE_USE_GROUPS" => "Y",
					"MENU_CACHE_GET_VARS" => array(
					),
				),
				false
			);?>
			<div class="footer_left">
				<div class="footer_contacts">
					<a class="footer_phone" href="<?=SITE_DIR?>about/contacts/"><span itemprop="telephone"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?></span></a>
				</div>
				<?
				$shopFacebook = COption::GetOptionString("bagmet_mobile", "shopFacebook", "", SITE_ID);
				$shopTwitter = COption::GetOptionString("bagmet_mobile", "shopTwitter", "", SITE_ID);
				$shopGooglePlus = COption::GetOptionString("bagmet_mobile", "shopGooglePlus", "", SITE_ID);
				$shopVk = COption::GetOptionString("bagmet_mobile", "shopVk", "", SITE_ID);
				if ($shopFacebook || $shopTwitter || $shopGooglePlus || $shopVk):
				?>
				<div class="social_btns">
					<?if ($shopFacebook):?>
						<a href="<?=$shopFacebook?>" class="fb_btn"></a>
					<?endif?>
					<?if ($shopTwitter):?>
						<a href="<?=$shopTwitter?>" class="twitter_btn"></a>
					<?endif?>
					<?if ($shopGooglePlus):?>
						<a href="<?=$shopGooglePlus?>" class="google_plus_btn"></a>
					<?endif?>
					<?if ($shopVk):?>
						<a href="<?=$shopVk?>" class="vk_btn"></a>
					<?endif?>
					<div class="splitter"></div>
				</div>
				<?endif?>
				<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/footer_info.php"), false);?>
				<div class="footer_address">
					<p><b><?=GetMessage("SHOES_FOOTER_PHONE")?></b> <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/telephone.php"), false);?><br>
					<b>Email:</b> <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/email.php"), false);?></p>
				</div>
			</div>
			<div class="footer_right">
				<div class="developed_in">
					<a href="http://bagmet-studio.ru/" class="bm_logo"></a>
					<p><?=GetMessage("FOOTER_DEVELOPER")?></p>
				</div>
			</div>
			<div class="splitter"></div>
		</div>
		<div class="footer_bottom_line">
			<div class="copyright"><?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/copyright.php"), false);?></div>
		</div>
	</div>
	<a href="javascript:void(0)" id="toTop" title="<?=GetMessage("FOOTER_UPPER")?>"></a>


<div class="layer_bg">
<!--SKU-->
	<div class="buy_layer" id="buy_layer_1" style="display: block;">
		<a href='javascript:void(0)' class="close_btn" onclick="$('#buy_layer_1').css('display', 'none');$('.layer_bg').css('display', 'none');"></a>
		<div>
			<h3 id="popup_offer_title"></h3>
		</div>
		<div id="popup_offer_img" class="buy_layer_img"></div>
		<div class="tovar_layer_buy_block">
			<div class="tovar_buy">
				<div id="popup_offer_props"></div>
					<div class="tovar_buy_content_btns" >
					<span id="popup_offer_buy_button" style="display:none">
						<a href='#' class='tovar_buy_button tovar_buy_content_btns_position'><?=GetMessage("FOOTER_SKU_BUY")?></a></br>
					</span>
					<!--<a href='#' class='tovar_one_click_btn'> упить в 1 клик</a>-->
					<span id="popup_offer_subscribe_button" style="display:none">
						<p><?=GetMessage("FOOTER_SKU_SUBSCRIBE_DESCR")?></p>
						<a href='#' class='tovar_mail_button tovar_buy_content_btns_position'><?=GetMessage("FOOTER_SKU_SUBSCRIBE")?></a>
					</span>
				</div>
			</div>
		</div>
		<div class="splitter"></div>
		<a href='javascript:void(0)' class='continue_btn' onclick="$('#buy_layer_3').css('display', 'none');$('.layer_bg').css('display', 'none');"><?=GetMessage("SHOES_FOOTER_GOTO_BACK")?></a>
	</div>
</div><!--layer_bg - end-->
<?
function GritterPanelPosition()
{
	if ($GLOBALS["APPLICATION"]->PanelShowed !== true)
		return "";
	$userOptions = CUserOptions::GetOption("admin_panel", "settings");
	return "<style type=\"text/css\">#gritter-notice-wrapper{top:".($userOptions["collapsed"] == "on" ? "75" : "184")."px}</style>
		<script>
			window.AdminPanel = 'Y';
			if($(window).scrollTop()!='0'){
				$('#gritter-notice-wrapper').css('top', '35px');
			}
			$(window).scroll(function(){
				if($(window).scrollTop()=='0'){
					$('#gritter-notice-wrapper').css('top', '".($userOptions["collapsed"] == "on" ? "75" : "184")."px');
				}else{
					$('#gritter-notice-wrapper').css('top', '35px');
				}
			});
		</script>";
}
$APPLICATION->AddBufferContent("GritterPanelPosition");
?>
<?
$arMessages = array(
	"ITEM_ADDED_TO_CART" => GetMessage("FOOTER_ITEM_ADDED_TO_CART"),
	"ORDER_ITEM" => GetMessage("FOOTER_ORDER_ITEM"),
	"SUBSCRIBED" => GetMessage('FOOTER_SUBSCRIBED'),
	"CATALOG_SUBSCRIBE_INACT" => GetMessage('FOOTER_CATALOG_SUBSCRIBE_INACT'),
	"CATALOG_SUBSCRIBE_INACT2" => '<a href="'.SITE_DIR.'login/">'.GetMessage('FOOTER_CATALOG_SUBSCRIBE_INACT2')."</a>",
	"COMPARE_PATH" => GetMessage("FOOTER_COMPARE_PATH"),
	"COMPARE_DESCR" => GetMessage("FOOTER_COMPARE_DESCR"),
	"COMPARE_ADD" => GetMessage("FOOTER_SKU_COMPARE_ADD"),
);
?>
<script>
	var mess = <?=CUtil::PhpToJsObject($arMessages)?>;
	BX.message(mess);
</script>

<?
$arFilter = Array("TYPE"=>"catalog", "SITE_ID"=>SITE_ID);
$obCache = new CPHPCache;
if($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog/active"))
{
	$arIBlocks = $obCache->GetVars();
}
elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache())
{

	$arIBlocks = array();
	$dbRes = CIBlock::GetList(Array(), $arFilter);
	$dbRes = new CIBlockResult($dbRes);

	if(defined("BX_COMP_MANAGED_CACHE"))
	{
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache("/iblock/catalog/active");

		while($arIBlock = $dbRes->GetNext())
		{
			$CACHE_MANAGER->RegisterTag("iblock_id_".$arIBlock["ID"]);

			if($arIBlock["ACTIVE"] == "Y")
				$arIBlocks[$arIBlock["ID"]] = $arIBlock;
		}

		$CACHE_MANAGER->RegisterTag("iblock_id_new");
		$CACHE_MANAGER->EndTagCache();
	}
	else
	{
		while($arIBlock = $dbRes->GetNext())
		{
			if($arIBlock["ACTIVE"] == "Y")
				$arIBlocks[$arIBlock["ID"]] = $arIBlock;
		}
	}

	$obCache->EndDataCache($arIBlocks);
}
else
{
	$arIBlocks = array();
}

//if(count($arIBlocks) == 1)
//{

	foreach($arIBlocks as $iblock_id => $arIBlock)
	{
		if ($APPLICATION->GetProperty("CATALOG_COMPARE_LIST", false) == false && IsModuleInstalled('iblock'))
		{
			$APPLICATION->IncludeComponent(
				"bitrix:catalog.compare.list",
				"mobile",
				Array(
					"IBLOCK_ID" => $iblock_id,
					"COMPARE_URL" => SITE_DIR."catalog/compare/"
				),
				false,
				Array("HIDE_ICONS" => "Y")
			);
		}
	}
//}
?>
</body>
</html>