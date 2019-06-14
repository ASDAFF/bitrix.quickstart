<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$frame = $this->createFrame()->begin();?>
<?if($arResult["GEO_CITY"]):

    //Путь до файлов обработчиков
    $arJSParams = array(
        "AJAX_URL" => array(
            "SELECT" => $templateFolder . "/ajax_select_city.php",
            "GET" => $templateFolder . "/ajax_geobase_get.php",
            "SAVE" => $templateFolder . "/ajax_geobase_save.php",
        ),
		"CLASS" => array(
			"WRAP_QUESTION_REASAPEKT" => "wrapQuestionReaspekt"
		)
    );

    if ($arResult["SET_LOCAL_DB"] == "local_db") :?>
        <div class="wrapGeoIpReaspekt">
            <?=GetMessage("REASPEKT_GEOIP_TITLE_YOU_CITY");?>: <span data-reaspektmodalbox-href="<?=$templateFolder?>/ajax_popup_city.php" class="cityLinkPopupReaspekt linkReaspekt"><?=$arResult["GEO_CITY"]["CITY"]?>, <?=$arResult["GEO_CITY"]["REGION"]?></span>
            <?if (
				$arParams["CHANGE_CITY_MANUAL"] == "Y"
				&& $arResult["CHANGE_CITY"] == "N"
			) :?>
			<div class="<?=$arJSParams["CLASS"]["WRAP_QUESTION_REASAPEKT"]?>">
                <div class="questionYourCityReaspekt"><?=GetMessage("REASPEKT_GEOIP_TITLE_YOU_CITY");?>:</div>
                <div class="questionCityReaspekt"><strong><?=$arResult["GEO_CITY"]["CITY"]?></strong> (<?=$arResult["GEO_CITY"]["REGION"]?>)</div>
                <div class="questionButtonReaspekt reaspekt_clearfix">
                    <div class="questionNoReaspekt cityLinkPopupReaspekt" data-reaspektmodalbox-href="<?=$templateFolder?>/ajax_popup_city.php"><?=GetMessage("REASPEKT_GEOIP_BUTTON_N");?></div>
                    <div class="questionYesReaspekt" onClick="objJCReaspektGeobase.onClickReaspektSaveCity('N');"><?=GetMessage("REASPEKT_GEOIP_BUTTON_Y");?></div>
                </div>
            </div>
			<?endif;?>
        </div>
        
        <script type="text/javascript">
        $('.cityLinkPopupReaspekt').ReaspektModalBox();
        var objJCReaspektGeobase = new JCReaspektGeobase(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
        </script>
    <?else:?>
		<div class="wrapGeoIpReaspekt">
			<strong><?=$arResult["GEO_CITY"]["CITY"]?></strong>, <?=$arResult["GEO_CITY"]["REGION"]?>
		</div>
    <?endif;
endif;?>
<?$frame->beginStub();?>
<?$frame->end();?>
