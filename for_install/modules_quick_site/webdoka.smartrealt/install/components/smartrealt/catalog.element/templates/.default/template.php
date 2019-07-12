<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if ($arResult['arObject']['Number'])
{    
    ?>
    <div class="clearer object_detail_info">
        <h1 class="capt" id="h1"><?=$arResult['arObject']['SectionFullNameSign']?></h1>
        <?php
            if (count($arResult['arObject']['PhotoMedium']) > 0)
            { 
                ?>
                <div class="detail-photo" id="detail-photo">
                    <img src="<?php echo $arResult['arObject']['PhotoMedium'][0]['src']?>" alt="<?=$arTitle['TITLE']?>" title="<?=$arTitle['TITLE']?>" />
                    <a id="ph_1" class="lupa lbx" target="_blank" href="<?php echo $arResult['arObject']['PhotoBig'][0]['src']?>">
                    <span></span>
                    </a>
                    <?php if ($arResult['arObject']['PhotoCount'] > 1) { ?>
                    <a href="<?php echo $arResult['arObject']['PhotoBig'][0]['src']?>" class="all-photos"><?php echo GetMessage('SMARTREALT_MORE') ?> <?php echo $arResult['arObject']['PhotoCount']-1; ?> <?php echo GetMessage('SMARTREALT_PHOTO') ?></a>
                    <?php } ?>
                    <div style="display: none;">
                    <?php
                        if ($arResult['arObject']['PhotoCount'] > 1)
                        {
                            for ($i=1;$i<count($arResult['arObject']['PhotoBig']);$i++)
                            {
                                ?><a href="<?=$arResult['arObject']['PhotoBig'][$i]['src']?>" class="lbx"><img src="<?=$arResult['arObject']['PhotoBig'][$i]['src']?>" alt="" /></a><?
                            }    
                        }
                    ?>
                    </div> 
                </div>
                <?
            }
        ?>
        <div class="detail-info <?=count($arResult['arObject']['PhotoCount'])==0? "no-photo": ''?>">
            <div class="clearer info-param">
                <div class="obj-info-param"><?php echo GetMessage('SMARTREALT_NUMBER') ?></div>
                <div class="obj-info-value obj-address">
                    <?php echo $arResult['arObject']['Number'];?><br />
                </div>
            </div>
            <div class="clearer info-param address_label">
                <div class="obj-info-param"><?php echo GetMessage('SMARTREALT_ADDRESS') ?></div>
                <div class="obj-info-value obj-address">
                    <?php echo $arResult['arObject']['Address'];?>
                    <?php if (strlen($arResult['arObject']['CityAreaName']) > 0) { ?>
                        <br /><span class="cityArea"><?php echo GetMessage('SMARTREALT_CITY_AREA') ?> <?php echo $arResult['arObject']['CityAreaName'] ?></span>
                    <?php } ?>
                    <?php if (strlen($arResult['arObject']['MetroStationName']) > 0) { ?>
                        <br /><span class="metro"><b><?php echo GetMessage('SMARTREALT_METRO') ?></b> <?php echo $arResult['arObject']['MetroStationName'] ?></span>
                    <?php } ?>
                </div>
            </div>
            <div class="clearer info-param">
                <div class="obj-info-param"><?php echo GetMessage('SMARTREALT_PRICE') ?></div>
                <div class="obj-info-value obj-price">
                    <?php echo $arResult['arObject']['Price'] ?><br />
                    <? if ($arResult['arObject']['TypeId']!=13
                        && (!in_array($arResult['arObject']['TypeId'], array(1,2,4,5,6,19,20,21))
                        || $arResult['arObject']['TransactionType'] == 'SALE')) { ?>
                         <span><?php echo $arResult['arObject']['PricePerMetr']; ?> / 
                       <?php echo $arResult['arObject']['AreaUnitName']?></span><br />
                    <? } ?>
                </div>
            </div>
            <div class="clearer info-param">
                <div class="obj-info-param area-title">
                    <span><?php echo GetMessage('SMARTREALT_AREA') ?></span>
                </div>
                <div class="obj-info-value obj-area">
                    <span><?php echo SmartRealt_CatalogElement::GetAreaString($arResult['arObject']); ?></span>
                </div>
            </div>
            <div class="clearer info-param">
                <div class="obj-info-param area-title">
                    <span><?php echo GetMessage('SMARTREALT_AGENT') ?></span>
                </div>
                <div class="obj-info-value obj-area">
                    <span><?php echo $arResult['arObject']['AgentPhone'] ?> <?php echo $arResult['arObject']['AgentName'] ?></span>
                </div>
            </div>
            <div class="clearer info-param">
                <div class="obj-info-param">&#171; <a href="<?php echo $arResult['CATALOG_LIST_URL']?>" class="back" target="_parent"><?php echo GetMessage('SMARTREALT_GO_BACK') ?></a></div>
            </div>
        </div>
    </div>
    <?php 
    
    if (count($arResult['arProperties']) > 0)
    {
        ?>
        <h2><?php echo GetMessage('SMARTREALT_PARAMETERS') ?></h2>
        <div class="clearer obj-param-block">
            <?php
                $iColumns = 1;
                $iIndex = 0;

                $arProperties = array();

                if (SmartRealt_Options::GetShowEmptyParameters() != "Y")
                {
                    foreach ($arResult['arProperties'] as $sPropertyName => $sTitle)
                    {
                        if (!empty($arResult['arObject'][$sPropertyName]))
                        {
                            switch ($sPropertyName)
                            {
                                case 'EstateMarket':
                                    if ($arResult['arObject']['TypeId'] == 13)
                                        break;
                                case 'StreetRetail':
                                case 'Mortgage':
                                    if ($arResult['arObject'][$sPropertyName] != 'Y')
                                        break;
                                default:
                                    $arProperties[$sPropertyName] = $sTitle;
                            }
                        }
                    }
                }
                else
                {
                    $arProperties = $arResult['arProperties'];
                }

                foreach ($arProperties as $sPropertyName => $sTitle)
                {
                    $iIndex++;
                    $bLastRow = $iIndex%ceil(count($arProperties)/2) == 0 || $iIndex == count($arProperties);
                    $sLastClass = $bLastRow?'last':'';
                    
                    if ($iIndex == 1)
                    {
                        ?><div class="param-lcol"><?php
                    }
                    
                    echo '<div class="row '.$sLastClass.'"><b>'.$sTitle.':</b>';
                    
                    if (empty($arResult['arObject'][$sPropertyName]))
                    {
                        echo '-</div>';
                    }
                    else
                    {
                        switch ($sPropertyName)
                        {
                            case 'EstateMarket':
                                printf('<span>%s</span>',
                                    ($arResult['arObject']['EstateMarket'] == 'PRIMARY') 
                                        ? GetMessage('SMARTREALT_YES') 
                                        : (($arResult['arObject']['TypeId'] != 13)?GetMessage('SMARTREALT_NO'):'-')
                                );
                                break;
                            case 'Mortgage':
                            case 'StreetRetail':
                                printf('<span>%s</span>',
                                    ($arResult['arObject'][$sPropertyName] == 'Y') 
                                        ? GetMessage('SMARTREALT_YES') 
                                        : GetMessage('SMARTREALT_NO')
                                        );
                                break;
                            case 'RoomQuantity':
                                printf('<span>%s%s</span>', 
                                        $arResult['arObject']['RoomOffer']?$arResult['arObject']['RoomOffer'].'/':'', 
                                        $arResult['arObject'][$sPropertyName]
                                    );
                                break;
                            case 'Floor':
                                printf('<span>%s%s</span>', 
                                        $arResult['arObject'][$sPropertyName], 
                                        $arResult['arObject']['FloorQuantity']?'/'.$arResult['arObject']['FloorQuantity']:''
                                    );
                                break;
                            case 'BuiltYear':
                                printf('<span>%s%s</span>',                    
                                        $arResult['arObject']['BuiltQuarter']?$arResult['arObject']['BuiltQuarter'].' ':'',
                                        $arResult['arObject'][$sPropertyName]
                                    );
                                break;
                                
                            default:
                                printf('<span>%s</span>', $arResult['arObject'][$sPropertyName]);
                                break;
                        }
                        
                        echo '</div>';
                    }
                    
                    if ($bLastRow)
                    {
                        echo '</div>';
                    }
                    
                    if ($bLastRow && $iColumns == 1 && $iIndex < count($arProperties))
                    {
                        $iColumns++;
                        echo '<div class="param-rcol">';
                    }
                }
            ?>
            </div>
        <?php
        }
        if (!empty($arResult['arObject']['Description']))
        {
            ?>
            <h2><?php echo GetMessage('SMARTREALT_DESCRIPTION')?></h2>
            <div class="clearer obj-param-block">
                <p><?php echo str_replace("\n", '<br>', $arResult['arObject']['Description']); ?></p>
            </div>
            <?php
        }
    ?>
    <div class="clearer obj-param-block">
        <div class="clearBoth"></div>
        <?php /*<div class="param-lcol">
            <small><?php echo GetMessage('SMARTREALT_CREATE_DATE')?> <?=date('d.m.Y H:i', strtotime($arResult['arObject']['CreateDate']))?></small>
        </div>*/ ?>
        <small><?php echo GetMessage('SMARTREALT_UPDATE_DATE')?> <?=date('d.m.Y H:i', strtotime($arResult['arObject']['UpdateDate']))?></small>
    </div> 
    <script type='text/javascript'>
        $(function(){
            $('#detail-photo a.lbx').lightBox({
                imageLoading: '<?php echo SITE_TEMPLATE_PATH ?>/images/litebox/lightbox-ico-loading.gif',
                imageBtnClose: '<?php echo SITE_TEMPLATE_PATH ?>/images/litebox/lightbox-btn-close.gif',
                imageBtnPrev: '<?php echo SITE_TEMPLATE_PATH ?>/images/litebox/lightbox-btn-prev.gif',
                imageBtnNext: '<?php echo SITE_TEMPLATE_PATH ?>/images/litebox/lightbox-btn-next.gif',
                });
            $('a.all-photos').bind('click', function(){
                $('a#ph_1').click();
                return false;
            });
        });
    </script>
<?
}
else
{
    LocalRedirect('/404.php');
    return;
}  
?>
