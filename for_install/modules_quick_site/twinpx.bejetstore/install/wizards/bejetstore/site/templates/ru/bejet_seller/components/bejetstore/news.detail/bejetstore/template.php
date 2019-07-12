<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="bj-block-group">
	<div class="row">
		<div class="col-sm-8 col-xs-12">
			<p><?echo $arResult["DETAIL_TEXT"];?></p>
		</div>
		<?if(!empty($arResult["PREV_NEWS"])):?>
		<hr class="clearfix visible-xs-block">
		
		<?$ElementID = $APPLICATION->IncludeComponent(
			"bejetstore:news.detail",
			"read_also",
			Array(
				"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
				"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
				"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
				"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"FIELD_CODE" => array(
					"0" => "ID",
					"1" => "NAME",
					"2" => "PREVIEW_TEXT",
					"3" => "PREVIEW_PICTURE",
					"4" => "CODE"
				),
				"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
				"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
				"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
				"META_KEYWORDS" => $arParams["META_KEYWORDS"],
				"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
				"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
				"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
				"SET_TITLE" => "N",
				"SET_STATUS_404" => $arParams["SET_STATUS_404"],
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"ACTIVE_DATE_FORMAT" => "j F Y",
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
				"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
				"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
				"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
				"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
				"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
				"CHECK_DATES" => $arParams["CHECK_DATES"],
				"ELEMENT_ID" => $arResult["PREV_NEWS"]["ID"],
				"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
				"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
				"USE_SHARE" 			=> $arParams["USE_SHARE"],
				"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
				"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
				"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
				"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
				"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
				"ADD_ELEMENT_CHAIN" => ''
			),
			$component
		);?>
		<?endif;?>
	</div>
	<p><a href="<?=SITE_DIR?>news/"><?=GetMessage("BACK_TO_NEWS")?></a></p>
</div>
<?if(!$arResult["SUBSCRIBED"]):?>
<hr class="i-line i-size-L">
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<div class="bj-news-subscribe__s">

			<div class="alert alert-warning center-block text-center hide" role="alert">
				<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
				<h3><?=GetMessage("SMTH_WRONG")?></h3>
			</div>

			<div class="alert alert-success center-block text-center hide" role="alert">
				<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
				<h3><?=GetMessage("NOW_U_SUBSCRIBED")?></h3>
				<a href class="bj-news-unsubscribe-link"><?=GetMessage("UNSUBSCRIBE")?></a>
			</div>

			<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="hide bj-news-unsubscribe-form">
				<div class="form-group">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="unsubscribe" value="Y">
				</div>
			</form>

			<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="bj-news-subscribe-form">
				<h3><b><?=GetMessage("SUBSCRIBE_ON_NEWS")?></b></h3>
				<div class="form-group">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="email" value="">
					<input type="email" name="ml" value="" class="form-control" required>
				</div>
				<button type="submit" class="btn btn-default btn-100"><?=GetMessage("SUBSCRIBE")?></button>
			</form>
		</div>
	</div>
</div>
<?else:?>
<hr class="i-line i-size-L">
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<div class="bj-news-subscribe__s">
			<div class="alert alert-warning center-block text-center hide" role="alert">
				<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
				<h3><?=GetMessage("SMTH_WRONG")?></h3>
			</div>

			<div class="alert alert-success center-block text-center" role="alert">
				<div class="i-relative"><div class="bj-envelope-icon bj-alert-top-icon"></div></div>
				<h3><?=GetMessage("NOW_U_SUBSCRIBED")?></h3>
				<a href class="bj-news-unsubscribe-link"><?=GetMessage("UNSUBSCRIBE")?></a>
			</div>

			<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="bj-news-unsubscribe-form">
				<div class="form-group">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="unsubscribe" value="Y">
				</div>
			</form>

			<form action="<?=$this->GetFolder()?>/subscribe.php" method="post" class="hide bj-news-subscribe-form">
				<h3><b><?=GetMessage("SUBSCRIBE_ON_NEWS")?></b></h3>
				<div class="form-group">
					<?=bitrix_sessid_post()?>
					<input type="hidden" name="email" value="">
					<input type="email" name="ml" value="" class="form-control" required>
				</div>
				<button type="submit" class="btn btn-default btn-100"><?=GetMessage("SUBSCRIBE")?></button>
			</form>
		</div>
	</div>
</div>
<?endif;?>