<?require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); CModule::IncludeModule('iblock');
    __IncludeLang(__DIR__."/lang/en/reviews.php");
    __IncludeLang(__DIR__."/lang/ru/reviews.php");
    global $ElementID;
    if($_POST['addreview']=='Y'){
        if(strlen($_POST['PROPS']['USER'])<3 || strlen($_POST['PROPS']['EMAIL'])<3 || strlen($_POST['COMMENT'])<3)$err=true;
    }
    global $USER;
    $rsUser = CUser::GetByID($USER->GetID());
    $arUser = $rsUser->Fetch();
    $PROP['USER'] = $arUser['NAME'].' '.$arUser['LAST_NAME'];
    $PROP['EMAIL'] = $arUser['EMAIL'];
    if(array_key_exists('addreview', $_POST) && !$err){
        if($_POST['PROPS']['ELEMENT'] && $_POST['PROPS']['USER'] && $_POST['PROPS']['EMAIL'] && $_POST['COMMENT']){
            $CODE='REVIEWS_BLOG';
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
                    "NAME" => GetMessage('REVIEWS_BLOG_IBLOCK_NAME'),
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
            
            }
        }
    }
    if(!function_exists('digital')){
        function digital($n, $form1, $form2, $form3)
        {
            $n = abs($n) % 100;
            $n1 = $n % 10;
            if ($n > 10 && $n < 20) return $form3;
            if ($n1 > 1 && $n1 < 5) return $form2;
            if ($n1 == 1) return $form1;
            return $form3;
        }
    }
    $arSelect=Array("ID", "PROPERTY_USER",  "PREVIEW_TEXT", "DATE_ACTIVE_FROM", "PROPERTY_ELEMENT");
    $res = CIBlockElement::GetList(Array("DATE_ACTIVE_FROM"=>"DESC"), Array("IBLOCK_CODE"=>'REVIEWS_BLOG', "PROPERTY_ELEMENT"=>($ElementID)?$ElementID:$_POST['PROPS']['ELEMENT']), false, false, $arSelect);
    while($arFields = $res->GetNext())$arComments[]=$arFields;?>
    <?if($arComments):?>
<section id="Comments" class="row comments">
    <div class="section-header col-xs-12">
        <hr>
        <h2 class="strong-header">
            <?=count($arComments)?> <?=digital(count($arComments), GetMessage('REVIEWS_B_COMMENT'), GetMessage('REVIEWS_B_COMMENTA'), GetMessage('REVIEWS_B_COMMENTOV'))?>
        </h2>
    </div>    
    <div class="col-xs-12">
        <ul class="comment-list list-unstyled">
            <?foreach($arComments as $arItem):?>
                <li>
                    <article class="comment">
                        <header>
                            <h4 class="author"><?=$arItem['PROPERTY_USER_VALUE']?></h4>
                            <span class="date"><?$format = CSite::GetDateFormat("FULL"); echo $DB->FormatDate($arItem['DATE_ACTIVE_FROM'], $format);?></span>
                        </header>

                        <div class="comment-content">
                            <p><?=$arItem['PREVIEW_TEXT']?></p>
                        </div>
                    </article>
                </li>
                <?endforeach?>
        </ul>
    </div>
</section>
 <?endif?>
<section class="row">
    <div class="section-header col-xs-12">
        <hr>
        <h2 class="strong-header">
            <?=GetMessage('REVIEWS_ADD_REVIEW')?>
        </h2>
    </div>
    <div class="col-xs-12">
        <?       
            if($_POST['addreview']=='Y'){
                if(strlen($_POST['PROPS']['USER'])<3){echo '<p style="color:red">'.GetMessage('REVIEWS_ERROR_NAME').'</p>'; $err=true;}
                if(strlen($_POST['PROPS']['EMAIL'])<3){echo '<p style="color:red">'.GetMessage('REVIEWS_ERROR_EMAIL').'</p>'; $err=true;}
                if(strlen($_POST['COMMENT'])<3){echo '<p style="color:red">'.GetMessage('REVIEWS_ERROR_COMMENT').'</p>'; $err=true;}
            }

        ?>
        <form action="<?=$APPLICATION->GetCurPage()?>#Comments" method="POST" class="review-blog">
            <input type="hidden" name="addreview" value="Y" />
            <input type="hidden" name="PROPS[ELEMENT]" value="<?=($ElementID)?$ElementID:$_POST['PROPS']['ELEMENT'];?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="inputName1"><?=GetMessage('REVIEWS_NAME')?></label>
                        <input type="text" class="form-control" name="PROPS[USER]" value="<?=(strlen($PROP['USER'])>2)?$PROP['USER']:0?>" id="inputName1">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="inputEmail1">Email</label>
                        <input type="email" name="PROPS[EMAIL]" value="<?=(strlen($PROP['EMAIL'])>2)?$PROP['EMAIL']:''?>" class="form-control" id="inputEmail1">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="inputComment"><?=GetMessage('REVIEWS_ADD_REVIEW')?></label>
                <textarea name="COMMENT" class="form-control" id="inputComment" rows="10"></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?=GetMessage('REVIEWS_ADD_SHORT')?></button>
        </form>
    </div>
</section>
