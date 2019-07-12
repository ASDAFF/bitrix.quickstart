<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
##############################################################
// замен€ем ссылки в описании на softeffect.ru дл€ DEMO данных
// удалить если DEMO данные более не используютс€
preg_match_all('|src=\"([^\"]*)\"|', $arResult["DETAIL_TEXT"], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value)) {
		$arResult["DETAIL_TEXT"] = str_replace($value, "http://softeffect.ru".$value, $arResult["DETAIL_TEXT"]);
	}
}

preg_match_all('|src=\"([^\"]*)\"|', $arResult["PREVIEW_TEXT"], $matches);
foreach ($matches[1] as $value) {
	if (!file_exists($_SERVER['DOCUMENT_ROOT'].$value)) {
		$arResult["PREVIEW_TEXT"] = str_replace($value, "http://softeffect.ru".$value, $arResult["PREVIEW_TEXT"]);
	}
}
##############################################################
?>

<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
	<span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
<?endif;?>
<br /><br />

<?$APPLICATION->AddChainItem($arResult["NAME"]);?>

<? if ($arResult['ACTIVE_TO']) {  ?>
	<center><h2><?=GetMessage('SE_ACTION_END');?></h2></center>
	<br />
	<div id="countdown"></div>
	<p id="note">
		<span class="day"><?=GetMessage('SE_DAYS');?></span>
		<span class="hours"><?=GetMessage('SE_HOURS');?></span>
		<span class="minutes"><?=GetMessage('SE_MIN');?></span>
		<span><?=GetMessage('SE_SEC');?></span>
		<br clear="both" />
	</p>
	<div class="activ-to"> <?=GetMessage('SE_ACTION_TO');?> <?=$arResult['ACTIVE_TO']?></div>
<? } ?>


</ul>
<center><h2><?=GetMessage('SE_DESCR_IF');?></h2></center></br>
<ul class="tabs">
	<li><a href="#descr"><?=GetMessage('SE_DESCR');?></a></li>
	<? if (strlen($resCompare['DETAIL_TEXT']))  { ?><li><a href="#can" class="double-string"><?=GetMessage('SE_COMPARE');?></a></li><? } ?>
	<li><a href="#reviews" ><?=GetMessage('SE_REVIEWS');?></a></li>
</ul>
<div class="panes">
	<div>
		<div class="news-detail">
			<div id="content-text">
				<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
					<img class="detail_picture" border="0" src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>" height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>" alt="<?=$arResult["NAME"]?>"  title="<?=$arResult["NAME"]?>" />
				<?endif?>
				<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
					<h3><?=$arResult["NAME"]?></h3>
				<?endif;?>
				<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
					<?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?>
				<?endif;?>
				<?if($arResult["NAV_RESULT"]):?>
					<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
					<?echo $arResult["NAV_TEXT"];?>
					<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
			 	<?elseif(strlen($arResult["DETAIL_TEXT"])>0):?>
					<?echo $arResult["DETAIL_TEXT"];?>
				<?else:?>
					<?echo $arResult["PREVIEW_TEXT"];?>
				<?endif?>
				<div style="clear:both"></div>
				<br />
			</div>


			<?/*foreach($arResult["FIELDS"] as $code=>$value):?>
					<?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
					<br />
			<?endforeach;*/?>
			<?/*foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
		
				<?=$arProperty["NAME"]?>:&nbsp;
				<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
					<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
				<?else:?>
					<?=$arProperty["DISPLAY_VALUE"];?>
				<?endif?>
				<br />
			<?endforeach;*/?>
			<? if (array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y") { ?>
				<div class="news-detail-share">
					<noindex>
					<?
					$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
							"HANDLERS" => $arParams["SHARE_HANDLERS"],
							"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
							"PAGE_TITLE" => $arResult["~NAME"],
							"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
							"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
							"HIDE" => $arParams["SHARE_HIDE"],
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);
					?>
					</noindex>
				
				</div>
			<? } ?>
			</p>
		</div>
	</div>
<div>
	<?$APPLICATION->IncludeComponent("bitrix:news", "reviews", array(
	"IBLOCK_TYPE" => "sw_content",
	"IBLOCK_ID" => "#sw_reviews_comp#",
	"NEWS_COUNT" => "20",
	"USE_SEARCH" => "N",
	"USE_RSS" => "N",
	"USE_RATING" => "N",
	"USE_CATEGORIES" => "N",
	"USE_REVIEW" => "N",
	"USE_FILTER" => "N",
	"SORT_BY1" => "ACTIVE_FROM",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"CHECK_DATES" => "Y",
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "#SITE_DIR#about/reviews/",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "N",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"SET_TITLE" => "Y",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"USE_PERMISSIONS" => "N",
	"PREVIEW_TRUNCATE_LEN" => "",
	"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
	"LIST_FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"LIST_PROPERTY_CODE" => array(
		0 => "",
		1 => "STATUS",
		2 => "",
	),
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"DISPLAY_NAME" => "Y",
	"META_KEYWORDS" => "-",
	"META_DESCRIPTION" => "-",
	"BROWSER_TITLE" => "-",
	"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
	"DETAIL_FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"DETAIL_PROPERTY_CODE" => array(
		0 => "ABOUT_COMPANY",
		1 => "STATUS",
		2 => "SERT",
		3 => "SECTION",
		4 => "",
	),
	"DETAIL_DISPLAY_TOP_PAGER" => "N",
	"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
	"DETAIL_PAGER_TITLE" => "—траница",
	"DETAIL_PAGER_TEMPLATE" => "",
	"DETAIL_PAGER_SHOW_ALL" => "Y",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "Ќовости",
	"PAGER_SHOW_ALWAYS" => "Y",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "Y",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"USE_SHARE" => "Y",
	"SHARE_HIDE" => "N",
	"SHARE_TEMPLATE" => "",
	"SHARE_HANDLERS" => array(
		0 => "lj",
		1 => "delicious",
		2 => "twitter",
		3 => "facebook",
	),
	"SHARE_SHORTEN_URL_LOGIN" => "",
	"SHARE_SHORTEN_URL_KEY" => "",
	"AJAX_OPTION_ADDITIONAL" => "",
	"SEF_URL_TEMPLATES" => array(
		"news" => "",
		"section" => "",
		"detail" => "#ELEMENT_ID#/",
	)
	),
	false
);?></div>
</div>


<? $APPLICATION->IncludeFile('#SITE_DIR#news/footer_detail.php', array(), array('MODE'=>'html', 'SHOW_BORDER'=>true)); ?>
<input type="hidden" id="ACTIVE_TO" value="<?=$arResult['ACTIVE_TO']?>" />