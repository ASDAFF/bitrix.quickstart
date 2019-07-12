<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); $this->setFrameMode(true); ?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);
/*
//проверим наличие инфоблока товаров
$searchArray = $arFormAction = array();
if(GetIBlock($arParams["CATALOG_IBLOCK_ID"]))
{
    $searchArray["products"] = GetMessage("CT_PRODUCT_SELECT_VALUE");
    $arFormAction["products"] = SITE_DIR."catalog/";
}
//проверим наличие инфоблока образов
if(GetIBlock($arParams["FASHION_IBLOCK_ID"]))
{
    $searchArray["fashion"] = GetMessage("CT_IMAGERY_SELECT_VALUE");
    $arFormAction["fashion"] = SITE_DIR."imageries/";
}

if( isset($_REQUEST['SEARCH_WHERE']) )
{
	$arResult["FORM_ACTION"] = $arFormAction[ $_REQUEST['SEARCH_WHERE'] ];
}
*/

//if(empty($arResult["FORM_ACTION"][0])) $arResult['FORM_ACTION'] = $arParams['CATALOG_IBLOCK_PATH'];

$arResult['FORM_ACTION'] = $arParams['CATALOG_IBLOCK_PATH'];

if($arParams["SHOW_INPUT"] !== "N"):?>
	<div id="<?echo $CONTAINER_ID?>" class="form-demo">
		<form id="searchForm" class="bs-docs-example form-inline" action="<?=$arResult["FORM_ACTION"];?>">
			
				<input id="<?echo $INPUT_ID?>" type="text" name="q" size="40" maxlength="50" autocomplete="off" value="<? if (isset($_REQUEST["q"])) echo htmlspecialcharsbx($_REQUEST["q"]);?>" placeholder="<?=GetMessage("CT_BST_SEARCH_BUTTON");?>"   class="searchb" />
                <?php/*
                    if(count($searchArray)>1){
                    ?>
                    <select id="searchWhere" class="searchspan" name="SEARCH_WHERE" onchange="javascript:$('#searchForm').attr( 'action', $('#searchWhere option:selected').attr('data-action') );">
                        <?
                            foreach ($searchArray as $key => $value) {
                                if (isset($_GET['SEARCH_WHERE']) && $_GET['SEARCH_WHERE'] == $key) $selected = 'selected = "selected"';
                                else $selected = '';
                            ?><option <?=$selected?> value="<?=$key?>" data-action="<?=$arFormAction[$key];?>"><?=$value?></option><?
                            }
                        ?>
                    </select>
                    <?php
                    }else{
?>
<select name="SEARCH_WHERE" id="searchWhere" style="display:none;">
	<option data-action="/catalog/" value="catalog"></option>
</select>
<?
					}*/
                ?>
                <button type="submit" class="submit-btn">&nbsp;</button>
		</form>
    </div>
    
    <div class="clear"></div>
	<div class="sample">
 <?
	$APPLICATION->IncludeComponent(
			"novagroup:search.requests",
			"",
			Array(
					"CATALOG_IBLOCK_TYPE" => "catalog",
					"CATALOG_IBLOCK_ID" => array($arParams["CATALOG_IBLOCK_ID"], $arParams["FASHION_IBLOCK_ID"]),
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "3600",
					"ROOT_SEARCH" => SITE_DIR."catalog/"
			)
	);
	?>
	</div>
<?endif?>
<script type="text/javascript">
var jsControl = new JCTitleSearch({
	//'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
	'AJAX_PAGE' : '<?=SITE_DIR?>',
	'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
	'INPUT_ID': '<?echo $INPUT_ID?>',
	'MIN_QUERY_LEN': 2
});

	$("#<?=$INPUT_ID?>").attr("value", "<?if (isset($_REQUEST["q"])) echo htmlspecialcharsbx($_REQUEST["q"])?>");
</script>
<??>