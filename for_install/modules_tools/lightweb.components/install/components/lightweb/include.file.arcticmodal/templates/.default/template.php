<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


/** @var array $arParams */
/** @var array $arResult */
?>
	<div style="display:none;">
        <div class="lw_include_file_arcticmodal-window" id="<?=$arParams['WINDOW_ID']?>">
            <button class="cross arcticmodal-close" title="<?=GetMessage("LW_IF_WINDOW_CLOSE");?>"></button>
            <? $APPLICATION->IncludeFile($arParams['FILE_CONNECTION'],Array(),Array("MODE"=>$arParams['CONTENT_TYPE'])); ?>
        </div>
	</div>
 