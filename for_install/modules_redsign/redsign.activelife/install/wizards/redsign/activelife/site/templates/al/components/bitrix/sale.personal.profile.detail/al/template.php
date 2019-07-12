<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

?>
<div class="sale-profile-detail-link-list">
	<a href="<?=$arParams["PATH_TO_LIST"]?>"><?=GetMessage("SPPD_RECORDS_LIST")?></a>
</div>

<?
if(strlen($arResult["ID"])>0)
{
	ShowError($arResult["ERROR_MESSAGE"]);
	?>
	<form method="post" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
        <div class="panel">
            <div class="panel__head">
                <h4 class="panel__name"><?= Loc::getMessage('SPPD_PROFILE_NO', array("#ID#" => $arResult["ID"]))?></h4>
            </div>
            <div class="panel__body">
            
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="ID" value="<?=$arResult["ID"]?>">

            <div class="form-group row">
                <label class="sale-personal-profile-detail-form-label col-md-3 text-md-right"><?=Loc::getMessage('SALE_PERS_TYPE')?></label>
                <div class="col-md-12">
                    <b><?=$arResult["PERSON_TYPE"]["NAME"]?></b>
                </div>
            </div>		
            <div class="form-group row">
                <label class="sale-personal-profile-detail-form-label col-md-3 text-md-right" for="sale-personal-profile-detail-name">
                    <?=Loc::getMessage('SALE_PNAME')?>:<span class="req">*</span>
                </label>
                <div class="col-md-12">
                    <input class="form-control" type="text" name="NAME" maxlength="50" id="sale-personal-profile-detail-name" value="<?=$arResult["NAME"]?>" />
                </div>
            </div>
            <?
            foreach($arResult["ORDER_PROPS"] as $block)
            {
                if (!empty($block["PROPS"]))
                {
                    ?>
                    <h4>
                        <b><?= $block["NAME"]?></b>
                    </h4>
                    <?
                    foreach($block["PROPS"] as $key => $property)
                    {
                        $name = "ORDER_PROP_".$property["ID"];
                        $currentValue = $arResult["ORDER_PROPS_VALUES"][$name];
                        $alignTop = ($property["TYPE"] === "LOCATION" && $arParams['USE_AJAX_LOCATIONS'] === 'Y') ? "vertical-align-top" : "";
                        ?>
                        <div class="form-group row sale-personal-profile-detail-property-<?=strtolower($property["TYPE"])?>">
                            <label class="sale-personal-profile-detail-form-label col-md-3 text-md-right <?=$alignTop?>" for="sppd-property-<?=$key?>">
                                <?= $property["NAME"]?>:
                                <?
                                if ($property["REQUIED"] == "Y")
                                {
                                    ?>
                                    <span class="req">*</span>
                                    <?
                                }
                                ?>
                            </label>
                            <div class="col-md-12">
                                <?
                                if ($property["TYPE"] == "CHECKBOX")
                                {
                                    ?>
                                    <input
                                        class="sale-personal-profile-detail-form-checkbox"
                                        id="sppd-property-<?=$key?>"
                                        type="checkbox"
                                        name="<?=$name?>"
                                        value="Y"
                                        <?if ($currentValue == "Y" || !isset($currentValue) && $property["DEFAULT_VALUE"] == "Y") echo " checked";?>/>
                                    <?
                                }
                                elseif ($property["TYPE"] == "TEXT")
                                {
                                    ?>
                                    <input
                                        class="form-control"
                                        type="text" name="<?=$name?>"
                                        maxlength="50"
                                        id="sppd-property-<?=$key?>"
                                        value="<?=$currentValue?>"/>
                                    <?
                                }
                                elseif ($property["TYPE"] == "SELECT")
                                {
                                    ?>
                                    <select
                                        class="form-control"
                                        name="<?=$name?>"
                                        id="sppd-property-<?=$key?>"
                                        size="<?echo (intval($property["SIZE1"])>0)?$property["SIZE1"]:1; ?>">
                                            <?
                                            foreach ($property["VALUES"] as $value)
                                            {
                                                ?>
                                                <option value="<?= $value["VALUE"]?>" <?if ($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"]==$property["DEFAULT_VALUE"]) echo " selected"?>>
                                                    <?= $value["NAME"]?>
                                                </option>
                                                <?
                                            }
                                            ?>
                                    </select>
                                    <?
                                }
                                elseif ($property["TYPE"] == "MULTISELECT")
                                {
                                    ?>
                                    <select
                                        class="form-control"
                                        id="sppd-property-<?=$key?>"
                                        multiple name="<?=$name?>[]"
                                        size="<?echo (intval($property["SIZE1"])>0)?$property["SIZE1"]:5; ?>">
                                            <?
                                            $arCurVal = array();
                                            $arCurVal = explode(",", $currentValue);
                                            for ($i = 0, $cnt = count($arCurVal); $i < $cnt; $i++)
                                                $arCurVal[$i] = trim($arCurVal[$i]);
                                            $arDefVal = explode(",", $property["DEFAULT_VALUE"]);
                                            for ($i = 0, $cnt = count($arDefVal); $i < $cnt; $i++)
                                                $arDefVal[$i] = trim($arDefVal[$i]);
                                            foreach($property["VALUES"] as $value)
                                            {
                                                ?>
                                                <option value="<?= $value["VALUE"]?>"<?if (in_array($value["VALUE"], $arCurVal) || !isset($currentValue) && in_array($value["VALUE"], $arDefVal)) echo" selected"?>><?echo $value["NAME"]?></option>
                                                <?
                                            }
                                            ?>
                                    </select>
                                    <?
                                }
                                elseif ($property["TYPE"] == "TEXTAREA")
                                {
                                    ?>
                                    <textarea
                                        class="form-control"
                                        id="sppd-property-<?=$key?>"
                                        rows="<?echo ((int)($property["SIZE2"])>0)?$property["SIZE2"]:4; ?>"
                                        cols="<?echo ((int)($property["SIZE1"])>0)?$property["SIZE1"]:40; ?>"
                                        name="<?=$name?>"><?= (isset($currentValue)) ? $currentValue : $property["DEFAULT_VALUE"];?>
                                    </textarea>
                                    <?
                                }
                                elseif ($property["TYPE"] == "LOCATION")
                                {
                                    $locationTemplate = ($arParams['USE_AJAX_LOCATIONS'] !== 'Y') ? "popup" : "";

                                    $locationValue = intval($currentValue) ? $currentValue : $property["DEFAULT_VALUE"];
                                    CSaleLocation::proxySaleAjaxLocationsComponent(
                                        array(
                                            "AJAX_CALL" => "N",
                                            'CITY_OUT_LOCATION' => 'Y',
                                            'COUNTRY_INPUT_NAME' => $name.'_COUNTRY',
                                            'CITY_INPUT_NAME' => $name,
                                            'LOCATION_VALUE' => $locationValue,
                                        ),
                                        array(
                                        ),
                                        $locationTemplate,
                                        true,
                                        'location-block-wrapper'
                                    );

                                }
                                elseif ($property["TYPE"] == "RADIO")
                                {
                                    foreach($property["VALUES"] as $value)
                                    {
                                        ?>
                                        <input
                                            class="form-control"
                                            type="radio"
                                            id="sppd-property-<?=$key?>"
                                            name="<?=$name?>"
                                            value="<?echo $value["VALUE"]?>"
                                            <?if ($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"]) echo " checked"?>>
                                        <?= $value["NAME"]?><br />
                                        <?
                                    }
                                }
                                elseif ($property["TYPE"] == "FILE")
                                {
                                    $multiple = ($property["MULTIPLE"] === "Y") ? "multiple" : '';
                                    ?>
                                    <label>
                                        <span class="btn-themes btn-default btn-md btn">
                                            <?=Loc::getMessage('SPPD_SELECT')?>
                                        </span>
                                        <span class="sale-personal-profile-detail-load-file-info">
                                            <?=Loc::getMessage('SPPD_FILE_NOT_SELECTED')?>
                                        </span>
                                        <?=CFile::InputFile($name."[]", 20, null, false, 0, "IMAGE", "class='btn sale-personal-profile-detail-input-file' ".$multiple)?>
                                    </label>
                                    <span class="sale-personal-profile-detail-load-file-cancel sale-personal-profile-hide">
                                        <svg class="icon-close icon-svg"><use xlink:href="#svg-close"></use></svg>
                                    </span>
                                    <?
                                    if (count($currentValue) > 0)
                                    {
                                        ?>
                                        <input type="hidden" name="<?=$name?>_del" class="profile-property-input-delete-file">
                                        <?
                                        $profileFiles = unserialize(htmlspecialchars_decode($currentValue));
                                        foreach ($profileFiles as $file)
                                        {
                                            ?>
                                            <div class="sale-personal-profile-detail-form-file">
                                                <input type="checkbox" value="<?=$file?>" class="profile-property-check-file" id="profile-property-check-file-<?=$file?>">
                                                <label for="profile-property-check-file-<?=$file?>"><?=Loc::getMessage('SPPD_DELETE_FILE')?></label>
                                                <?
                                                $fileInfo = CFile::GetByID($file);
                                                $fileInfoArray = $fileInfo->Fetch();
                                                if (CFile::IsImage($fileInfoArray['FILE_NAME']))
                                                {
                                                    ?>
                                                    <p>
                                                        <?=CFile::ShowImage($file, 150, 150, "border=0", "", true)?>
                                                    </p>
                                                    <?
                                                }
                                                else
                                                {
                                                    ?>
                                                    <a download="<?=$fileInfoArray["ORIGINAL_NAME"]?>" href="<?=CFile::GetFileSRC($fileInfoArray)?>">
                                                        <?=Loc::getMessage('SPPD_DOWNLOAD_FILE', array("#FILE_NAME#" => $fileInfoArray["ORIGINAL_NAME"]))?>
                                                    </a>
                                                    <?
                                                }
                                                ?>
                                            </div>
                                            <?
                                        }
                                    }
                                }

                                if (strlen($property["DESCRIPTION"]) > 0)
                                {
                                    ?>
                                    <br /><small><?= $property["DESCRIPTION"] ?></small>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                        <?
                    }
                }
            }
            ?>
            </div>
        </div>
        
        <div>
            <input type="submit" class="btn btn1" name="save" value="<?echo GetMessage("SALE_SAVE") ?>">
            &nbsp;
            <input type="submit" class="btn btn1"  name="apply" value="<?=GetMessage("SALE_APPLY")?>">
            &nbsp;
            <input type="submit" class="btn btn1"  name="reset" value="<?echo GetMessage("SALE_RESET")?>">
        </div>
	</form>
	<script>
		BX.message({
			SPPD_FILE_COUNT: '<?=Loc::getMessage('SPPD_FILE_COUNT')?>',
			SPPD_FILE_NOT_SELECTED: '<?=Loc::getMessage('SPPD_FILE_NOT_SELECTED')?>'
		});
		BX.Sale.PersonalProfileComponent.PersonalProfileDetail.init();
	</script>
	<?
}
else
{
	ShowError($arResult["ERROR_MESSAGE"]);
}
?>

