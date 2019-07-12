<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); CModule::IncludeModule('iblock'); 
    __IncludeLang(__DIR__."/lang/en/reviews.php");
    __IncludeLang(__DIR__."/lang/ru/reviews.php");
 
    if($_POST['addreview']=='Y'){
        if(strlen($_POST['PROPS']['USER'])<3 || strlen($_POST['PROPS']['EMAIL'])<3 || strlen($_POST['PROPS']['RATE'])<=0 || strlen($_POST['COMMENT'])<3)$err=true;
    }
    global $USER;
    $rsUser = CUser::GetByID($USER->GetID());
    $arUser = $rsUser->Fetch();
    $PROP['USER'] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
    $PROP['EMAIL'] = $arUser['EMAIL'];
    if(array_key_exists('addreview', $_POST) && !$err){
        if($_POST['PROPS']['ELEMENT'] && $_POST['PROPS']['USER'] && $_POST['PROPS']['EMAIL'] && $_POST['COMMENT']){
            $CODE='REVIEWS';
            $res = CIBlock::GetList(Array(), Array("CODE"=>$CODE), true);
            if($ar_res = $res->Fetch()){$iblock=true; $ID=$ar_res['ID'];}
            else{
                //new iblocktype
                $arTypes = Array(
                    Array(
                        "ID" => "comments",
                        "SECTIONS" => "N",
                        "IN_RSS" => "N",
                        "SORT" => 50,
                        "LANG" => Array(),
                    )
                );

                $arLanguages = Array();
                $rsLanguage = CLanguage::GetList($by, $order, array());
                while($arLanguage = $rsLanguage->Fetch())
                    $arLanguages[] = $arLanguage["LID"];

                $iblockType = new CIBlockType;
                foreach($arTypes as $arType)
                {
                    $dbType = CIBlockType::GetList(Array(),Array("=ID" => $arType["ID"]));
                    if($dbType->Fetch())continue;
                        

                    foreach($arLanguages as $languageID)
                    {
                        $code = strtoupper($arType["ID"]);
                        $arType["LANG"][$languageID]["NAME"] = GetMessage($code."_TYPE_NAME");
                        $arType["LANG"][$languageID]["ELEMENT_NAME"] = GetMessage($code."_ELEMENT_NAME");
                    }

                    $iblockType->Add($arType);
                }
                //new iblock
                $rsSites = CSite::GetList($by="sort", $order="desc", Array());
                while ($arSite = $rsSites->Fetch()) $sites[]=$arSite['ID'];  
                $ib = new CIBlock;
                $valuesRate= Array(
                    Array(
                        "VALUE" => "1",
                        "DEF" => "N",
                        "SORT" => "100"
                    ), Array(
                        "VALUE" => "2",
                        "DEF" => "N",
                        "SORT" => "200"
                    ), Array(
                        "VALUE" => "3",
                        "DEF" => "N",
                        "SORT" => "300"
                    ), Array(
                        "VALUE" => "4",
                        "DEF" => "N",
                        "SORT" => "400"
                    ), Array(
                        "VALUE" => "5",
                        "DEF" => "N",
                        "SORT" => "500"
                    )
                );


                $arFields = Array(
                    "ACTIVE" => 'Y',
                    "NAME" => GetMessage('REVIEWS_IBLOCK_NAME'),
                    "CODE" => $CODE,
                    "IBLOCK_TYPE_ID" => 'comments',
                    "SITE_ID" => $sites,
                    "GROUP_ID" => Array("2"=>"R"),
                    "VERSION" => '2'
                );
                $ID = $ib->Add($arFields);
                if($ID){$iblock=true;
                    $arFieldsProp = Array(
                        Array(
                            "NAME" => GetMessage('REVIEWS_PRODUCT'),
                            "ACTIVE" => "Y",
                            "SORT" => "100",
                            "CODE" => "ELEMENT",
                            "PROPERTY_TYPE" => "E",
                            "IBLOCK_ID" => $ID,
                            "LINK_IBLOCK_ID" =>$_POST['IBLOCK_ID']
                        ), Array(
                            "NAME" => GetMessage('REVIEWS_USER'),
                            "ACTIVE" => "Y",
                            "SORT" => "100",
                            "CODE" => "USER",
                            "PROPERTY_TYPE" => "S",
                            "IBLOCK_ID" => $ID
                        ), Array(
                            "NAME" => GetMessage('REVIEWS_RATE'),
                            "ACTIVE" => "Y",
                            "SORT" => "100",
                            "CODE" => "RATE",
                            "PROPERTY_TYPE" => "S",
                            "IBLOCK_ID" => $ID,

                        ), 
                        Array(
                            "NAME" => "E-mail",
                            "ACTIVE" => "Y",
                            "SORT" => "100",
                            "CODE" => "EMAIL",
                            "PROPERTY_TYPE" => "S",
                            "IBLOCK_ID" => $ID
                        ));
                    foreach($arFieldsProp as $prop){
                        $ibp = new CIBlockProperty;
                        $PropID = $ibp->Add($prop);   
                    }
                }
            }
            if($iblock){
                $iblockEL=CIBlockElement::GetIBlockByID(intval($_POST['PROPS']['ELEMENT']));
                $PROPS=$_POST['PROPS'];
                $PROPS['RATE']=intval($PROPS['RATE']);
                $el = new CIBlockElement;
                $res = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>$iblockEL, "ID"=>$_POST['PROPS']['ELEMENT']), false, false, Array("ID", "NAME"));
                if($arFields = $res->GetNext())$element = $arFields;

                $arLoadProductArray = Array(
                    "MODIFIED_BY"    => $USER->GetID(), 
                    "IBLOCK_ID"      => $ID,
                    "PROPERTY_VALUES"=> $PROPS,
                    "DATE_ACTIVE_FROM"   =>  ConvertTimeStamp(time(), 'FULL'),
                    "NAME"           => $PROPS['USER'].' / '.$element['NAME'].' / '.date("d.m.Y H:i:s"),
                    "ACTIVE"         => "Y",            
                    "PREVIEW_TEXT"   => $_POST['COMMENT'],
                );
                if($PRODUCT_ID = $el->Add($arLoadProductArray))                  
                    echo '<p style="color:green;">'.GetMessage('REVIEWS_OK').'</p>';
                else
                    echo '<p style="color:red;">Error: '.$el->LAST_ERROR.'</p>';
                $arSelect=Array("ID", "PROPERTY_ELEMENT", "PROPERTY_RATE");
                $res = CIBlockElement::GetList(Array(), Array("IBLOCK_CODE"=>'REVIEWS', "PROPERTY_ELEMENT"=>($arResult['ID'])?$arResult['ID']:$_POST['PROPS']['ELEMENT']), false, false, $arSelect);
                $elementID=$arResult['ID']?$arResult['ID']:$_POST['PROPS']['ELEMENT'];
                while($arFields = $res->GetNext()){
                    $cntComms++;
                    $rat+=intval($arFields['PROPERTY_RATE_VALUE']);
                }
                CIBlockElement::SetPropertyValuesEx($elementID, $iblockEL, array('RATE' => $rat/$cntComms, 'COMMENTS' => $cntComms));

            }
        }
    }

    $arSelect=Array("ID", "PROPERTY_USER", "PROPERTY_RATE", "PREVIEW_TEXT", "DATE_ACTIVE_FROM", "PROPERTY_ELEMENT");
    $res = CIBlockElement::GetList(Array("DATE_ACTIVE_FROM"=>"DESC"), Array("IBLOCK_CODE"=>'REVIEWS', "PROPERTY_ELEMENT"=>($arResult['ID'])?$arResult['ID']:$_POST['PROPS']['ELEMENT']), false, false, $arSelect);
    while($arFields = $res->GetNext()){?>
    <article class="review">
        <header>
            <span class="rating" data-score="<?=$arFields['PROPERTY_RATE_VALUE']?>"></span><br>
            <h4 class="author"><?=$arFields['PROPERTY_USER_VALUE']?></h4>
            <span class="date"><?$format = CSite::GetDateFormat("FULL"); echo $DB->FormatDate($arFields['DATE_ACTIVE_FROM'], $format);?></span>
        </header>
        <p><?=$arFields['PREVIEW_TEXT']?></p>
    </article>
    <?}       
    if($_POST['addreview']=='Y'){
        if(strlen($_POST['PROPS']['USER'])<3){echo '<p style="color:red">'.GetMessage('REVIEWS_ERROR_NAME').'</p>'; $err=true;}
        if(strlen($_POST['PROPS']['EMAIL'])<3){echo '<p style="color:red">'.GetMessage('REVIEWS_ERROR_EMAIL').'</p>'; $err=true;}
        if(strlen($_POST['PROPS']['RATE'])<=0){echo '<p style="color:red">'.GetMessage('REVIEWS_ERROR_RATE').'</p>'; $err=true;}
        if(strlen($_POST['COMMENT'])<3){echo '<p style="color:red">'.GetMessage('REVIEWS_ERROR_COMMENT').'</p>'; $err=true;}
    }

?>

<form class="review-form" action="<?=$APPLICATION->GetCurPage()?>#reviews" method="POST" >
    <input type="hidden" name="addreview" value="Y" />
    <input type="hidden" name="PROPS[ELEMENT]" value="<?=($arResult['ID'])?$arResult['ID']:$_POST['PROPS']['ELEMENT'];?>">

    <label class="raty-label">
        <?=GetMessage('REVIEWS_ERROR_RATE')?><br>
        <span class="rate" data-score="<?=(strlen($PROP['USER'])>2)?$PROP['USER']:0?>"></span>
    </label>

    <div class="form-group">
    <table>
        <tr><td width="100"><label for="nickname_field" class="required"><?=GetMessage('REVIEWS_NAME')?></label></td><td>
        <input type="text" name="PROPS[USER]" id="nickname_field" class="input-text required-entry" value="<?=(strlen($PROP['USER'])>2)?$PROP['USER']:''?>" />
        </td>
        </tr>
        </table>
    </div> 
    <div class="form-group">
        <table>
        <tr><td width="100"><label for="summary_field" class="required">E-mail</label></td><td>
        <input type="text" name="PROPS[EMAIL]" id="summary_field" class="input-text required-entry" value="<?=(strlen($PROP['EMAIL'])>2)?$PROP['EMAIL']:''?>" />
   </td>
        </tr>
        </table>
    </div> <div class="form-group">
        <label for="review"><?=GetMessage('REVIEWS_ADD_REVIEW')?></label>
        <textarea class="form-control" id="review" name="COMMENT" rows="6"></textarea>
    </div>
    <button type="submit" class="btn btn-primary"><?=GetMessage('REVIEWS_ADD_SHORT')?></button>
</form>