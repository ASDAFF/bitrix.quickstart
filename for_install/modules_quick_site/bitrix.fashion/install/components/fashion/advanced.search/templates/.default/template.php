<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?if(count($arResult["ITEMS"])>0||count($arResult['SEARCH_RESULT']["CONTENT"])>0){?>
<?if(count($arResult["ITEMS"])>0){?>
<div class="catalog">
    <?if(isset($_REQUEST['area'])||$_REQUEST['area']=='catalog'){?>
    <h2><a href="<?echo $arResult['PURE_URL'];?>">Все результаты</a> поиска по сайту</h2>
    <?}else{?>
    <h2><a href="<?echo $arResult['CATALOG_URL'];?>">Все результаты</a> поиска по каталогу</h2>
    <?}?>
    <div class="overflow">
        <table class="catalog">
    <?foreach($arResult["ITEMS"] as $arLine):?>
            <?
            $arImgs = $arData = array();

            foreach($arLine as $arElement):?>
                <?$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

                $pin = $sale = $hit = false;

                if($arElement['IBLOCK_CODE']=='tyres'){
                    $season_name = $arElement['PROPERTIES']['model_season']['VALUE_ENUM'];
                    if($arElement['PROPERTIES']['model_season']['VALUE_XML_ID']=='leto'){
                        $season_class = 'summer';
                    }elseif($arElement['PROPERTIES']['model_season']['VALUE_XML_ID']=='zima'){
                        $season_class = 'winter';
                        $pin = $arElement['PROPERTIES']['model_pin']['VALUE_XML_ID']=='yes'?true:false;
                    }else{
                        $season_class = 'allseason';
                    }
                }

                $arPrice = $arElement['PRICE'];
                $price = '';
//                foreach($arElement["PRICES"] as $code=>$arPrice){
//                    if($arPrice["CAN_ACCESS"]){
                        if($arPrice["DISCOUNT_PRICE"] < $arPrice["PRICE"]['PRICE']){
                            $price = '<p><span class="strike">'.$arPrice["PRICE"]['PRICE'].' <span class="rubl">P</span></span> ('.($arPrice["DISCOUNT_PRICE"] - $arPrice["PRICE"]['PRICE']).' <span class="rubl">P</span>)</p>
                                      <p class="price">'.$arPrice["DISCOUNT_PRICE"].' <span class="rubl">P</span></p>';
                            $sale = true;
                        } else {
                            $price = '<p class="price">'.$arPrice["PRICE"]['PRICE'].' <span class="rubl">P</span></p>';
                        }
//                    }
//                }

                $quantity = '';
                if($arElement['CATALOG_QUANTITY'] < 1){
                    $quantity = '<p><span class="absent">'.dvsListABSENT.'</span></p>';//***
                } else {
                    $quantity = '<p>'.$arElement['CATALOG_QUANTITY'] . dvsListQUANTITY.'</p>';//***
                }

                $icons = '';
                if ($sale || $hit) {
                    $icons = '<ul class="icons">'.
                        ($sale ? '<li><span class="red">'.dvsSALE.'</span></li>' : '')//***
                        .
                        ($hit ? '<li><span class="green">'.dvsHIT.'</span></li>' : '')//***
                    .'</ul>';
                }

                $arImgs[] = '<td><div><a href="'.$arElement['DETAIL_PAGE_URL'].'"><img src="'.$arElement['PREVIEW_PICTURE']['SRC'].'" width="'.$arElement['PREVIEW_PICTURE']['WIDTH'].'" height="'.$arElement['PREVIEW_PICTURE']['HEIGHT'].'" alt="'.$arElement['NAME'].'" id="i'.$arElement['ID'].'" /></a>'.$icons.'</div></td>';

                $arData[] = '<td id="'.$this->GetEditAreaId($arElement['ID']).'">
                <h4 id="name'.$arElement['ID'].'"><a href="'.$arElement['DETAIL_PAGE_URL'].'">'.$arElement['NAME'].'</a></h4>'.

                (($arElement['IBLOCK_CODE']=='tyres')?('<p><span class="'.$season_class.'">'.$season_name.($pin?', ':'').'</span>'.($pin?'<span class="spike">шип</span>':'').'</p>
                <p>'.$arElement['PROPERTIES']['model_type']['VALUE'].'</p>'):'')

                .$quantity.'
                '.$price.'
                <form action="'.POST_FORM_ACTION_URI.'" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="'.$arParams["ACTION_VARIABLE"].'" value="BUY">
                    <input type="hidden" name="'.$arParams["PRODUCT_ID_VARIABLE"].'" value="'.$arElement["ID"].'">
                    <input type="hidden" name="'.$arParams["ACTION_VARIABLE"].'BUY" value="Y">
                    <div class="tocart buy-i" itemID="'.$arElement['ID'].'" offerStatus="'.($arElement['CATALOG_QUANTITY']==0?'not-available':'available').'">
                            <input type="hidden" id="price'.$arElement['ID'].'" value="'.$arElement['PRICE']['PRICE']['PRICE'].'" />
                            <input type="text" id="count'.$arElement['ID'].'" name="'.$arParams["PRODUCT_QUANTITY_VARIABLE"].'" value="'.($arElement['CATALOG_QUANTITY']<4?$arElement['CATALOG_QUANTITY']:4).'" size="5" class="text2">
                            <span class="pcs">шт.</span>
                            <button type="button" class="button2 buy"><span>В корзину</span></button>
                            <div class="clear"></div>
                    </div>
                </form>';
        ?>
            <?endforeach;?>
            <tr class="img"><?echo implode('', $arImgs);?></tr>
            <tr class="txt"><?echo implode('', $arData);?></tr>
    <?endforeach;?>
        </table>
    </div>
</div>
<?echo $arResult['NAV_CHAIN'];?>
<?}?>

<?if(count($arResult['SEARCH_RESULT']["CONTENT"])>0){?>
<!-- Search Results -->
<style>
    div.results p b{
        color: white;
        background: #333;
        font-weight:normal;
    }
</style>
<div class="block full crop-bottom">
        <div class="results">
                <?if(isset($_REQUEST['area'])||$_REQUEST['area']=='catalog'){?>
                <h3><a href="<?echo $arResult['PURE_URL'];?>">Все результаты</a> поиска по сайту</h3>
                <?}else{?>
                <h3><a href="<?echo $arResult['CONTENT_URL'];?>">Все результаты</a> поиска по контенту</h3>
                <?}?>
                <ol>
                <?foreach($arResult['SEARCH_RESULT']["CONTENT"] as $content){?>
                        <li>
                                <p><a href="<?echo $content['URL_WO_PARAMS']?>"><?echo $content['TITLE'];?></a></p>
                                <p><?echo $content['BODY_FORMATED'];?></p>
                        </li>
                <?}?>
                </ol>
        </div>
</div>
<!-- // Search Results -->
<?}?>
<?}else{
    echo 'Ничего не найдено';
}?>