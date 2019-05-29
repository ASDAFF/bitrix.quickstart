<?
$iModuleID = "mibix.yamexport";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/include.php"); // инициализация модуля
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$iModuleID."/prolog.php"); // пролог модуля

IncludeModuleLangFile(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight($iModuleID);
if($POST_RIGHT=="D")
{
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

// сформируем список закладок
$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("MIBIX_YAM_TAB_RULES"), "ICON" => "main_user_edit", "TITLE" => GetMessage("MIBIX_YAM_TAB_RULES_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

$ID = intval($ID); // идентификатор редактируемой записи
$strError = ""; // сообщение об ошибке
$bVarsFromForm = false; // флаг "Данные получены с формы", обозначающий, что выводимые данные получены с формы, а не из БД.

// ОБРАБОТКА ИЗМЕНЕНИЙ ФОРМЫ
if($REQUEST_METHOD == "POST" && ($save != "" || $apply != "") && $POST_RIGHT >= "W" && check_bitrix_sessid())
{
    $rules = new CMibixModelRules();

    // обработка переменных каталога яндекса
    $strMarketCategories = "";
    $arCategoryValues = array();
    for($i=0;$i<6;$i++) {
        $f_market_category_id = ${"f_market_category_id_".$i};
        if(!empty($f_market_category_id)) {
            $arCategoryValues[] = $f_market_category_id;
        }
    }
    if(count($arCategoryValues)>0) {
        $strMarketCategories = implode(",", $arCategoryValues);
    }

    // обработка переменной множественного выбора картинки
    $strPictures = "";
    if(count($f_picture)>0)
    {
        $strPictures = implode(",", $f_picture);
    }

    // обработка переменных param
    $strParams = "";
    if(isset($f_param_name[0]) && isset($f_param_value[0]) && strlen($f_param_name[0])>0 && strlen($f_param_value[0])>0)
    {
        // количество параметров в массиве
        $cntPv = count($f_param_name);

        // Проходимся по всем параметрам в массиве и преобразуем их к виду "name,unit,value|name,unit,value|name,unit,value.." для записи в базу
        $arStrParams = array();
        for($i=0;$i<$cntPv;$i++)
        {
            $strParName = (isset($f_param_name[$i]))?$f_param_name[$i]:"";
            $strParUnit = (isset($f_param_unit[$i]))?$f_param_unit[$i]:"";
            $strParValue = (isset($f_param_value[$i]))?$f_param_value[$i]:"";
            $arStrParams[] = $strParName.",".$strParUnit.",".$strParValue;
        }
        if(count($arStrParams)>0) $strParams = implode("|", $arStrParams);
    }

    // Обработка данных форм с собственными значениями
    $arParamsSelf = array("bid","cbid","typeprefix","model","local_delivery_cost","sales_notes","manufacturer_warranty","seller_warranty","country_of_origin","age","barcode","expiry","hall_plan");
    foreach($arParamsSelf as $paramSelf)
    {
        $tmp_f = "f_".$paramSelf;
        $tmp_f = $$tmp_f;

        $tmp_self = "self_".$paramSelf;
        $tmp_self = $$tmp_self;

        // Параметру присваиваем значения с префиксом "self@" для различия установки "собственных" значений или свойства инфоблока
        if($tmp_f=="self" && isset($tmp_self))
        {
            ${"f_".$paramSelf} = "self@".$tmp_self;
        }
    }

    // обработка данных формы
    $arFields = Array(
        "datasource_id"		=> $f_datasource_id,
        "active"		    => ($f_active <> "Y"? "N":"Y"),
        "type"		        => $f_type,
        "name_rule"		    => $f_name_rule,
        "category_id"		=> intval($f_category_id),
        "market_category_id"=> $strMarketCategories,
        "available"         => $f_available,
        "bid"	            => $f_bid,
        "cbid"	            => $f_cbid,
        "url"		        => $f_url,
        "price"             => $f_price,
        "price_optimal"     => ($f_price_optimal <> "Y"? "N":"Y"),
        "price_currency"    => $f_price_currency,
        "oldprice"          => $f_oldprice,
        "oldprice_optimal"  => ($f_oldprice_optimal <> "Y"? "N":"Y"),
        "picture"           => $strPictures,
        "typeprefix"        => $f_typeprefix,
        "model"             => $f_model,
        "store"             => $f_store,
        "pickup"            => $f_pickup,
        "delivery"          => $f_delivery,
        "name"              => $f_name,
        "description"       => $f_description,
        "description_frm"   => ($f_description_frm <> "Y"? "N":"Y"),
        "vendor"            => $f_vendor,
        "vendorcode"        => $f_vendorcode,
        "local_delivery_cost"   => $f_local_delivery_cost,
        "sales_notes"       => $f_sales_notes,
        "manufacturer_warranty" => $f_manufacturer_warranty,
        "seller_warranty"   => $f_seller_warranty,
        "country_of_origin" => $f_country_of_origin,
        "adult"             => $f_adult,
        "downloadable"      => $f_downloadable,
        "rec"               => $f_rec,
        "age"               => $f_age,
        "ageunit"           => $f_ageunit,
        "barcode"           => $f_barcode,
        "expiry"            => $f_expiry,
        "weight"            => $f_weight,
        "dimensions"        => $f_dimensions,
        "param"             => $strParams,
        "cpa"               => $f_cpa,
        "author"            => $f_author,
        "publisher"         => $f_publisher,
        "series"            => $f_series,
        "year"              => $f_year,
        "isbn"              => $f_isbn,
        "volume"            => $f_volume,
        "part"              => $f_part,
        "language"          => $f_language,
        "binding"           => $f_binding,
        "page_extent"       => $f_page_extent,
        "table_of_contents" => $f_table_of_contents,
        "performed_by"      => $f_performed_by,
        "performance_type"  => $f_performance_type,
        "format"            => $f_format,
        "storage"           => $f_storage,
        "recording_length"  => $f_recording_length,
        "artist"            => $f_artist,
        "title"             => $f_title,
        "media"             => $f_media,
        "starring"          => $f_starring,
        "director"          => $f_director,
        "originalname"      => $f_originalname,
        "country"           => $f_country,
        "worldregion"       => $f_worldregion,
        "region"            => $f_region,
        "days"              => $f_days,
        "datatour"          => $f_datatour,
        "hotel_stars"       => $f_hotel_stars,
        "room"              => $f_room,
        "meal"              => $f_meal,
        "included"          => $f_included,
        "transport"         => $f_transport,
        "place"             => $f_place,
        "hall_plan"         => $f_hall_plan,
        "date"              => $f_date,
        "is_premiere"       => $f_is_premiere,
        "is_kids"           => $f_is_kids,
        "adt_dress_group_id"=> ($f_adt_dress_group_id <> "Y"? "N":"Y"),
    );

    // сохранение данных (обновление или добавление)
    if($ID > 0)
    {
        $res = $rules->Update($ID, $arFields);
    }
    else
    {
        $ID = $rules->Add($arFields);
        $res = ($ID>0);
    }

    if($res)
    {
        // если сохранение прошло удачно - перенаправим на новую страницу
        // (в целях защиты от повторной отправки формы нажатием кнопки "Обновить" в браузере)
        if($apply!="")
            LocalRedirect("/bitrix/admin/mibix.yamexport_rules_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&".$tabControl->ActiveTabParam());
        else
            LocalRedirect("/bitrix/admin/mibix.yamexport_rules_admin.php?lang=".LANG);
    }
    else
    {
        // если в процессе сохранения возникли ошибки - получаем текст ошибки и меняем вышеопределённые переменные
        if($e = $APPLICATION->GetException())
        {
            $message = new CAdminMessage(GetMessage("MIBIX_YAM_RULES_SAVE_ERROR"), $e);
        }
        $bVarsFromForm = true;
    }
}

// Удаление глобальных переменных с префиксом str_
ClearVars();

// Выберем данные из базы и сохранем в переменные с префиксом str_
if($ID > 0)
{
    $rules = CMibixModelRules::GetByID($ID);
    if(!$rules->ExtractFields("str_"))
    {
        $ID=0;
    }
}

// если данные переданы из формы, инициализируем их
if($bVarsFromForm)
{
    $DB->InitTableVarsForEdit("b_mibix_yam_rules", "", "str_");
}

// определяем ID инфоблока о источнику данных
if($str_datasource_id>0 && empty($str_iblock_id))
{
    $str_iblock_id = CMibixModelRules::GetIBlockByDatasourceID($str_datasource_id);
}

// Для поля "картинки" преобразуем строку в массив (т.к. там могут быть множественные выделенные значения)
$str_picture = explode(",", $str_picture);

// определяем значения для параметров
if(strlen($str_param)>0)
{
    // из строки формируем массив параметров
    $arParams = explode("|", $str_param);
    if(count($arParams)>0)
    {
        $str_param_name = array();
        $str_param_unit = array();
        $str_param_value = array();
        foreach($arParams as $str_param)
        {
            // формируем отдельный массива для элементов каждого параметра
            $arParamElements = explode(",", $str_param);
            if(count($arParamElements)==3 && isset($arParamElements[0]) && isset($arParamElements[1]) && isset($arParamElements[2]))
            {
                $str_param_name[] = $arParamElements[0];
                $str_param_unit[] = $arParamElements[1];
                $str_param_value[] = $arParamElements[2];
            }
        }
    }
}

// Устанавливаем заголовок в зависимости от ее типа (обновление/добавление)
$APPLICATION->SetTitle(($ID > 0 ? GetMessage("MIBIX_YAM_RULES_EDIT_TITLE").$ID : GetMessage("MIBIX_YAM_RULES_ADD_TITLE")));

// второй общий пролог
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Проверка статуса модуля
switch(CModule::IncludeModuleEx($iModuleID))
{
    case MODULE_NOT_FOUND:
        echo '<div style="padding-bottom:10px;color:red;">'.GetMessage("MIBIX_YAM_MODULE_NOT_FOUND").'</div>';
        return;
    case MODULE_DEMO:
        echo '<div style="padding-bottom:10px;color:red;">'.GetMessage("MIBIX_YAM_MODULE_DEMO").'</div>';
        break;
    case MODULE_DEMO_EXPIRED:
        echo '<div style="padding-bottom:10px;color:red;">'.GetMessage("MIBIX_YAM_MODULE_DEMO_EXPIRED").'</div>';
}

// Административное меню, которое будет отображаться над таблицей со списком (Вернуться к списку)
$aMenu = array(
    array(
        "TEXT"=>GetMessage("MIBIX_YAM_RULES_LIST_TEXT"),
        "TITLE"=>GetMessage("MIBIX_YAM_RULES_LIST"),
        "LINK"=>"mibix.yamexport_rules_admin.php?lang=".LANG,
        "ICON"=>"btn_list",
    )
);

// В режиме редактирования добавляем дополнительные пункты меню (Добавить/Удалить)
if($ID>0)
{
    $aMenu[] = array("SEPARATOR"=>"Y");
    $aMenu[] = array(
        "TEXT"=>GetMessage("MIBIX_YAM_RULES_ADD_TEXT"),
        "TITLE"=>GetMessage("MIBIX_YAM_RULES_MNU_ADD"),
        "LINK"=>"mibix.yamexport_rules_edit.php?lang=".LANG,
        "ICON"=>"btn_new",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("MIBIX_YAM_RULES_DEL_TEXT"),
        "TITLE"=>GetMessage("MIBIX_YAM_RULES_MNU_DEL"),
        "LINK"=>"javascript:if(confirm('".GetMessage("MIBIX_YAM_RULES_MNU_DEL_CONF")."'))window.location='mibix.yamexport_rules_admin.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
        "ICON"=>"btn_delete",
    );
}

// создадим экземпляр класса административного меню
$context = new CAdminContextMenu($aMenu);

// выведем меню
$context->Show();

// если есть сообщения об ошибках или об успешном сохранении - выведем их
if($_REQUEST["mess"] == "ok" && $ID>0)
    CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("MIBIX_YAM_RULES_SAVED"), "TYPE"=>"OK"));
if($message)
    echo $message->Show();
?>
    <form method="POST" action="<?=$APPLICATION->GetCurPage();?>"  enctype="multipart/form-data" name="rulesform">
        <?
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        ?>
        <?if($ID > 0):?>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DATE_ADD");?>:</td>
                <td width="60%" class="adm-detail-content-cell-r"><?=$str_date_insert;?></td>
            </tr>
            <?if($str_date_update <> ""):?>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DATE_UPD");?>:</td>
                    <td width="60%" class="adm-detail-content-cell-r"><?=$str_date_update;?></td>
                </tr>
            <?endif?>
        <?endif?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ACTIVE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_active" value="Y"<?if($str_active=="Y" || empty($str_active)) echo " checked";?>>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_NAME")?></span>:
            </td>
            <td width="60%">
                <input type="text" size="50" maxlength="255" value="<?=$str_name_rule;?>" name="f_name_rule" />
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_DATASOURCE");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getSelectBoxDataSource($str_datasource_id);?>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?=GetMessage("MIBIX_YAM_RULES_DATASOURCE_NOTE");?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_TYPE");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getSelectBoxYandexType($str_type);?>
            </td>
        </tr>
        <!--tr>
            <td width="40%" class="adm-detail-content-cell-l">
                <span class="required"><?=GetMessage("MIBIX_YAM_RULES_CATID");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getSelectBoxCategoryClass($str_category_id);?>
            </td>
        </tr-->
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l">
                <?=GetMessage("MIBIX_YAM_RULES_MARKET_CATID");?>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r" id="market_category_td">
                <?echo CMibixModelRules::getSelectBoxYMCategories($str_market_category_id);?>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?=GetMessage("MIBIX_YAM_RULES_MARKET_CATID_NOTE");?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_AVAILABLE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("available", $str_available, $str_iblock_id);?>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?=GetMessage("MIBIX_YAM_RULES_AVAILABLE_NOTE");?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_BID");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("bid", $str_bid, $str_iblock_id);?>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?=GetMessage("MIBIX_YAM_RULES_BID_NOTE");?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_CBID");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("cbid", $str_cbid, $str_iblock_id);?>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?=GetMessage("MIBIX_YAM_RULES_CBID_NOTE");?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_URL");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_url" value="Y"<?if($str_url=="Y" || empty($str_url)) echo " checked";?>>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PRICE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getSelectBoxPriceType($str_price, $str_iblock_id);?>
            </td>
        </tr>
        <?if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")):?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PRICE_OPTIMAL");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_price_optimal" value="Y"<?if($str_price_optimal=="Y" || empty($str_price_optimal)) echo " checked";?>>
            </td>
        </tr>
        <?endif;?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PRICE_CURRENCY")?>:</td>
            <td width="60%">
                <input type="text" size="10" maxlength="15" value="<?=$str_price_currency;?>" name="f_price_currency" />
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PICTURE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsMultiSelectBoxPicture("picture", $str_picture, $str_iblock_id);?>
            </td>
        </tr>
        <tr id="t_typeprefix" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_TYPEPREFIX");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("typeprefix", $str_typeprefix, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_TYPEPREFIX_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_model" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_MODEL");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("model", $str_model, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_MODEL_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_store" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_STORE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("store", $str_store, $str_iblock_id);?>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?=GetMessage("MIBIX_YAM_RULES_STORE_NOTE");?>
                    </div>
                </div>
            </td>
        </tr>
        <tr id="t_pickup" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PICKUP");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("pickup", $str_pickup, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PICKUP_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_delivery" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DELIVERY");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("delivery", $str_delivery, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DELIVERY_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_name" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_NAME");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("name", $str_name, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_NAME_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_description" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DESCRIPTION");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("description", $str_description, $str_iblock_id, false, false);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DESCRIPTION_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_description_frm" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DESCRIPTION_FRM");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_description_frm" value="Y"<?if($str_description_frm=="Y") echo " checked";?>><br />
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DESCRIPTION_FRM_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_vendor" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_VENDOR");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("vendor", $str_vendor, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_VENDOR_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_vendorсode" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_VENDORCODE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("vendorcode", $str_vendorcode, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_VENDORCODE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_local_delivery_cost" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_LOCALDELIVERYCOST");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("local_delivery_cost", $str_local_delivery_cost, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_LOCALDELIVERYCOST_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_sales_notes" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_SALESNOTES");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("sales_notes", $str_sales_notes, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_SALESNOTES_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_manufacturer_warranty" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_MANUFACTURERWARRANTY");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("manufacturer_warranty", $str_manufacturer_warranty, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_MANUFACTURERWARRANTY_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_seller_warranty" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_SELLERWARRANTY");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("seller_warranty", $str_seller_warranty, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_SELLERWARRANTY_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_country_of_origin" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_COUNTRYOFORIGIN");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("country_of_origin", $str_country_of_origin, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_COUNTRYOFORIGIN_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_adult" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ADULT");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("adult", $str_adult, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ADULT_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_downloadable" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DOWNLOADABLE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("downloadable", $str_downloadable, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DOWNLOADABLE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_rec" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_REC");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("rec", $str_rec, $str_iblock_id, "E");?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_REC_NOTE");?>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_AGE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("age", $str_age, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_AGE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_AGEUNIT");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("ageunit", $str_ageunit, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_AGEUNIT_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_barcode" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_BARCODE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("barcode", $str_barcode, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_BARCODE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_expiry" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_EXPIRY");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("expiry", $str_expiry, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_EXPIRY_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_weight" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_WEIGHT");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("weight", $str_weight, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_WEIGHT_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_dimensions" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DIMENSIONS");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("dimensions", $str_dimensions, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DIMENSIONS_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_param" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PARAM");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParams($str_iblock_id, $str_param_name, $str_param_unit, $str_param_value);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PARAM_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_cpa" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_CPA");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("cpa", $str_cpa, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_CPA_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_author" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_AUTHOR");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("author", $str_author, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_AUTHOR_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_publisher" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PUBLISHER");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("publisher", $str_publisher, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PUBLISHER_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_series" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_SERIES");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("series", $str_series, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_SERIES");?>
                </div>
            </td>
        </tr>
        <tr id="t_year" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_YEAR");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("year", $str_year, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_YEAR_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_isbn" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ISBN");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("isbn", $str_isbn, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ISBN_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_volume" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_VOLUUME");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("volume", $str_volume, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_VOLUUME_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_part" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PART");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("part", $str_part, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PART_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_language" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_LANGUAGE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("language", $str_language, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_LANGUAGE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_binding" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_BINDING");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("binding", $str_binding, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_BINDING_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_page_extent" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PAGEEXTENT");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("page_extent", $str_page_extent, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PAGEEXTENT_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_table_of_contents" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_TABLEOFCONTENTS");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("table_of_contents", $str_table_of_contents, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_TABLEOFCONTENTS_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_performed_by" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PERFORMEDBY");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("performed_by", $str_performed_by, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PERFORMEDBY_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_performance_type" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PERFORMANCETYPE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("performance_type", $str_performance_type, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PERFORMANCETYPE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_format" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_FORMAT");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("format", $str_format, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_FORMAT_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_storage" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_STORAGE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("storage", $str_storage, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_STORAGE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_recording_length" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_RECORDINGLENGTH");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("recording_length", $str_recording_length, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_RECORDINGLENGTH_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_artist" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ARTIST");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("artist", $str_artist, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ARTIST");?>
                </div>
            </td>
        </tr>
        <tr id="t_title" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_TITLE");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("title", $str_title, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_TITLE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_media" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_MEDIA");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("media", $str_media, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_MEDIA_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_starring" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_STARRING");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("starring", $str_starring, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_STARRING_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_director" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DIRECTOR");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("director", $str_director, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DIRECTOR_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_originalname" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ORIGINALNAME");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("originalname", $str_originalname, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ORIGINALNAME_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_country" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_COUNTRY");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("country", $str_country, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_COUNTRY_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_worldregion" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_WORLDREGION");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("worldregion", $str_worldregion, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_WORLDREGION_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_region" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_REGION");?>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("region", $str_region, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_REGION_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_days" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_DAYS");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("days", $str_days, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DAYS_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_datatour" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DATATOUR");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("datatour", $str_datatour, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DATATOUR_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_hotel_stars" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_HOTELSTARS");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("hotel_stars", $str_hotel_stars, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_HOTELSTARS_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_room" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ROOM");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("room", $str_room, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ROOM_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_meal" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_MEAL");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("meal", $str_meal, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_MEAL_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_included" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><span class="required">*</span>
                <span class="adm-required-field"><?=GetMessage("MIBIX_YAM_RULES_INCLUDED");?></span>:
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("included", $str_included, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_INCLUDED_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_transport" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_TRANSPORT");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("transport", $str_transport, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_TRANSPORT_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_place" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_PLACE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("place", $str_place, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_PLACE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_hall_plan" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_HALLPLAN");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("hall_plan", $str_hall_plan, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_HALLPLAN_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_date" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_DATE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("date", $str_date, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_DATE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_is_premiere" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ISPREMIERE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("is_premiere", $str_is_premiere, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ISPREMIERE_NOTE");?>
                </div>
            </td>
        </tr>
        <tr id="t_is_kids" style="display:none;">
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ISKIDS");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getControlParamsSelectBox("is_kids", $str_is_kids, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ISKIDS_NOTE");?>
                </div>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?=GetMessage("MIBIX_YAM_RULES_ADDIT_DRESS_TITLE")?></td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ADDIT_DRESS_GROUPID");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_adt_dress_group_id" value="Y"<?if($str_adt_dress_group_id=="Y" || empty($str_adt_dress_group_id)) echo " checked";?>><br />
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ADDIT_DRESS_GROUPID_NOTE");?>
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-valign-top adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_ADDIT_OLDPRICE");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <?echo CMibixModelRules::getSelectBoxOldPriceType($str_oldprice, $str_iblock_id);?>
                <div class="adm-info-message">
                    <?=GetMessage("MIBIX_YAM_RULES_ADDIT_OLDPRICE_NOTE");?>
                </div>
            </td>
        </tr>
        <?if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")):?>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l"><?=GetMessage("MIBIX_YAM_RULES_OLDPRICE_OPTIMAL");?>:</td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="checkbox" name="f_oldprice_optimal" value="Y"<?if($str_oldprice_optimal=="Y" || empty($str_oldprice_optimal)) echo " checked";?>>
            </td>
        </tr>
        <?endif;?>
        <?
        $tabControl->Buttons(
            array(
                "disabled"=>($POST_RIGHT<"W"),
                "back_url"=>"mibix.yamexport_rules_admin.php?lang=".LANG
            )
        );
        ?>
        <?=bitrix_sessid_post();?>
        <input type="hidden" name="lang" value="<?=LANG?>">
        <?if($ID>0):?>
            <input type="hidden" name="ID" value="<?=$ID?>">
        <?endif;?>
        <?
        $tabControl->End();
        ?>
    </form>
    <script src="/bitrix/js/mibix.yamexport/script.js"></script>

<?$tabControl->ShowWarnings("rulesform", $message);?>
<?=BeginNote();?>
    <span class="required">*</span> <?=GetMessage("REQUIRED_FIELDS");?>
<?=EndNote();?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>