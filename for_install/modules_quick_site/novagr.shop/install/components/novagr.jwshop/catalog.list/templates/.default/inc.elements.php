<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><!--start_html-->
<?
/**
* шаблон для вывода списка элементов каталога
*/
?>
<div class="col3-list stuff-box" id="elements">
	<div class="product-count-bottom">
		<span><?=GetMessage("FOUND_LABEL")?> <?=$arResult['NavRecordCount'];?> <?=Novagroup_Classes_General_Main::pluralForm($arResult['NavRecordCount'], GetMessage("MODEL_ONE"), GetMessage("MODEL_MANY"), GetMessage("MODEL_OF"))?></span>
<?
	if ( $arResult['NavRecordCount'] > 16)
	{
?>
<a class="npagesize <? if($arParams['nPageSize'] == 16){echo'active';}else{echo'incative';}?>" value="16"><?=GetMessage("FORMAT_LABEL")?> 16</a><a class="npagesize <? if($arParams['nPageSize'] == 160){echo'active';}else{echo'incative';}?>" value="160"><?=GetMessage("FORMAT_LABEL")?> 160</a>
<?
	}
?>
	</div>
	
	<div class="clear"></div>
	
	<div class="list">
		<div class="line">
			<div class="item_number">	
<?
	if ( isset($arResult['ELEMENT']) )
	{
		$row = 1;
		foreach($arResult['ELEMENT'] as $val)
		{
            $arButtons = CIBlock::GetPanelButtons(
                $val["IBLOCK_ID"],
                $val["ID"],
                0,
                array("SECTION_BUTTONS"=>false, "SESSID"=>false)
            );
            $val["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];

            $this->AddEditAction($val['ID'], $val["EDIT_LINK"], CIBlock::GetArrayByID($val["IBLOCK_ID"], "ELEMENT_EDIT"));
		
			if ($row == 1) 
			{
				?>
				<div class="item-block">
				<?
			}
			$idElem = $this->GetEditAreaId($val['ID']);
			?>
					<div class="item" id="<?=$idElem?>" data-catalog-id="<?=$val['ID']?>" data-iblock-id="<?=$arParams["CATALOG_IBLOCK_ID"]?>">
            <div class="over">
                <?php
                    $SECTION = GetIBlockSection($val['IBLOCK_SECTION_ID']);
                ?>
                <?$APPLICATION->IncludeComponent(
                        "novagr.jwshop:catalog.element.preview",
                        "",
                        Array(
                            "SORT_FIELD" => "ID",
                            "SORT_BY" => "DESC",
                            "CATALOG_IBLOCK_TYPE" => $arParams['CATALOG_IBLOCK_TYPE'],
                            "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
                            "CATALOG_OFFERS_IBLOCK_ID" => $arParams['OFFERS_IBLOCK_ID'],
                            "ARTICLES_IBLOCK_ID" => $arParams['ARTICLES_IBLOCK_ID'],
                            "FASHION_IBLOCK_ID" => $arParams['FASHION_IBLOCK_ID'],
                            "SAMPLES_IBLOCK_CODE" => $arParams['SAMPLES_IBLOCK_CODE'],
                            "BRANDNAME_IBLOCK_CODE" => $arParams['BRANDNAME_IBLOCK_CODE'],
                            "COLORS_IBLOCK_CODE" => $arParams['COLORS_IBLOCK_CODE'],
                            "MATERIALS_IBLOCK_CODE" => $arParams['MATERIALS_IBLOCK_CODE'],
                            "STD_SIZES_IBLOCK_CODE" => $arParams['STD_SIZES_IBLOCK_CODE'],
                            "INET_MAGAZ_ADMIN_USER_GROUP_ID" => $arParams['INET_MAGAZ_ADMIN_USER_GROUP_ID'],
                            "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                            "CACHE_TIME" => $arParams['CACHE_TIME'],
                            "SET_TITLE" => "N",
                            "DISABLE_QUICK_VIEW" => $arParams['DISABLE_QUICK_VIEW'],
                            "COMPONENT_CURRENT_PAGE" => $arParams['ROOT_PATH'].$SECTION['CODE']."/".$val['CODE']."/",
                        ),
                        false,
                        Array(
                            'ACTIVE_COMPONENT' => 'Y',
                            "HIDE_ICONS"=>"Y"
                        )
                    );?>
            </div>
            <?php if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
            <div class="preview-info-boxover" style="display: none;">
                <div class="middle">
                        <?$APPLICATION->IncludeComponent(
                                "novagr.jwshop:catalog.element.preview",
                                "",
                                Array(
                                    "SORT_FIELD" => "ID",
                                    "SORT_BY" => "DESC",
                                    "CATALOG_IBLOCK_TYPE" => $arParams['CATALOG_IBLOCK_TYPE'],
                                    "CATALOG_IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'],
                                    "CATALOG_OFFERS_IBLOCK_ID" => $arParams['OFFERS_IBLOCK_ID'],
                                    "ARTICLES_IBLOCK_ID" => $arParams['ARTICLES_IBLOCK_ID'],
                                    "FASHION_IBLOCK_ID" => $arParams['FASHION_IBLOCK_ID'],
                                    "SAMPLES_IBLOCK_CODE" => $arParams['SAMPLES_IBLOCK_CODE'],
                                    "BRANDNAME_IBLOCK_CODE" => $arParams['BRANDNAME_IBLOCK_CODE'],
                                    "COLORS_IBLOCK_CODE" => $arParams['COLORS_IBLOCK_CODE'],
                                    "MATERIALS_IBLOCK_CODE" => $arParams['MATERIALS_IBLOCK_CODE'],
                                    "STD_SIZES_IBLOCK_CODE" => $arParams['STD_SIZES_IBLOCK_CODE'],
                                    "INET_MAGAZ_ADMIN_USER_GROUP_ID" => $arParams['INET_MAGAZ_ADMIN_USER_GROUP_ID'],
                                    "CACHE_TYPE" => $arParams['CACHE_TYPE'],
                                    "CACHE_TIME" => $arParams['CACHE_TIME'],
                                    "SET_TITLE" => "N",
                                    "DISABLE_QUICK_VIEW" => $arParams['DISABLE_QUICK_VIEW'],
                                    "COMPONENT_CURRENT_PAGE" => $arParams['ROOT_PATH'].$SECTION['CODE']."/".$val['CODE']."/",
                                ),
                                false,
                                Array(
                                    'ACTIVE_COMPONENT' => 'Y',
                                    "HIDE_ICONS"=>"Y"
                                )
                            );?>
                </div>
            </div>
            <?php endif;?>


                     </div>	
<?
			if ($row == 4)
			{
?>				
				</div>
<?
				$row = 1;
			} else $row++;
		}
		if ($row <= 4){
?>
				</div>
<?	
		}
	}
?>
				<div class="clear"></div>	
			
			</div>
		</div>

        <div class="product-count-bottom bot-p">
            <span><?=GetMessage("FOUND_LABEL")?> <?=$arResult['NavRecordCount'];?> <?=Novagroup_Classes_General_Main::pluralForm($arResult['NavRecordCount'], GetMessage("MODEL_ONE"), GetMessage("MODEL_MANY"), GetMessage("MODEL_OF"))?></span>
            <?
                if( $arResult['NavRecordCount'] > 16)
                {
                ?>
                <a class="npagesize <? if($arParams['nPageSize'] == 16){echo'active';}else{echo'incative';}?>" value="16"><?=GetMessage("FORMAT_LABEL")?> 16</a><a class="npagesize <? if($arParams['nPageSize'] == 160){echo'active';}else{echo'incative';}?>" value="160"><?=GetMessage("FORMAT_LABEL")?> 160</a>
                <?
                }
            ?>
        </div>
        <div class="pagination pagination-right bot-p">
            <?=$arResult['NAV_STRING'];?>
        </div>
        <div class="clear"></div>
        <?php
            if(trim($arResult["META_DATA"]["DESCRIPTION"])<>""):
            ?>
            <div class="info-block">
                <?php
                    if( trim( $UF_TITLE_H1 = $arResult['META_DATA']['UF_TITLE_H1'])<>"")
                    {
                        print "<h1>{$UF_TITLE_H1}</h1>";
                    }
                    ?>
                <?php
                    print $arResult["META_DATA"]["DESCRIPTION"];
                ?>   
            </div>
            <?php
                endif;
        ?>

	</div>
<?php if ($arParams['DISABLE_QUICK_VIEW'] !== 'Y'): ?>
<script type="text/javascript">

        /*catalog.element.preview - всплывающий быстрый просмотр*/ 
        $('.list .line .item').hover(
            function() {$(this).find('.preview-info-boxover').stop(true).fadeIn("600");},
            function() {$(this).find('.preview-info-boxover').stop(true).fadeOut("600");}
        );
        $('.quickViewLink').click(function(){
            return loadPreviewElementModalWindow($(this).attr('href'));
        });
        $('span.link-popover-card').click(function(){
            return loadPreviewElementModalWindow($(this).find('a').attr('href'));
        });
        /*конец всплывающий быстрый просмотр*/
</script>
<?php endif;?>
<script type="text/javascript">
    <?php
        if(isset($_REQUEST['arOffer'][0]['PROPERTY_COLOR']))
        {
            print '$(".button-color-button-'.(int)$_REQUEST['arOffer'][0]['PROPERTY_COLOR'].'").click();';
        }
    ?>
</script>

</div>
<!--end_html-->