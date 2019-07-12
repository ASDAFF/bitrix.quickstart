<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
$APPLICATION->setTitle(str_replace("#ID#", $arResult["ID"], Loc::getMessage("SPPD_PROFILE_NO")));
?>

<div class="row profile-detail">
    <div class="col col-md-12">
        <form class="form form-horizontal" method="post" action="<?=POST_FORM_ACTION_URI?>">
            <?=bitrix_sessid_post();?>
            <input type="hidden" name="ID" value="<?=$arResult["ID"]?>">
            
            <div class="form-group">
                <label for="TITLE" class="col-sm-3 control-label text-nowrap">
                    <?=Loc::getMessage('SALE_PERS_TYPE')?>:
                </label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?=$arResult["PERSON_TYPE"]["NAME"]?></p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="TITLE" class="col-sm-3 control-label text-nowrap">
                    <?=Loc::getMessage('SALE_PNAME')?>:
                </label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" name="NAME" value="<?=$arResult["NAME"]?>" size="40">
                </div>
            </div>
            
            <?php 
            foreach($arResult["ORDER_PROPS"] as $val): 
                if(empty($val["PROPS"])) {
                    continue;
                }
            ?>
            
            <h3><?=$val["NAME"]?></h3>
            
                <?php 
                foreach($val["PROPS"] as $vval): 
                    $currentValue = $arResult["ORDER_PROPS_VALUES"]["ORDER_PROP_".$vval["ID"]];
                    $name = "ORDER_PROP_".$vval["ID"];
                ?>
                <div class="form-group">
                    <label for="TITLE" class="col-sm-3 control-label text-nowrap">
                        <?=$vval["NAME"]?>
                        <?php if ($vval["REQUIED"]=="Y"): ?>
                            <span class="required">*</span>
                        <?php endif; ?>
                    </label>
                    <div class="col col-sm-9">
                        
                        <?php if ($vval["TYPE"]=="CHECKBOX"):?>
                        <div class="gui-box">
                            
                        </div>
                        <?php elseif ($vval["TYPE"]=="TEXT"): ?>
                        <input class="form-control" 
                               type="text"
                               size="<?=(intval($vval["SIZE1"]) > 0) ? $vval["SIZE1"] : 30; ?>"
                               maxlength="250"
                               value="<?=(isset($currentValue)) ? $currentValue : $vval["DEFAULT_VALUE"]; ?>"
                               name="<?=$name?>"
                        >
                        
                        <?php elseif ($vval["TYPE"]=="SELECT"): ?>
                        <select class="form-control"
                                name="<?= $name ?>"
                                size="<?=(intval($vval["SIZE1"]) > 0) ? $vval["SIZE1"] : 1; ?>"
                        >
                            <?php foreach ($vval["VALUES"] as $vvval):?>
                                <option
                                    value="<?php $vvval["VALUE"] ?>"
                                    <?php if ($vvval["VALUE"] == $currentValue || !isset($currentValue) && $vvval["VALUE"] == $vval["DEFAULT_VALUE"]) echo " selected" ?>><?php echo $vvval["NAME"] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <?php elseif($vval["TYPE"]=="MULTISELECT"): ?>
                        <select class="form-control" 
                                multiple name="<?=$name?>[]"
                                size="<?=(intval($vval["SIZE1"]) > 0) ? $vval["SIZE1"] : 5; ?>"
                        >
                            <?php
                            $arCurVal = array();
                            $arCurVal = explode(",", $currentValue);
                            for ($i = 0, $cnt = count($arCurVal); $i < $cnt; $i++)
                                $arCurVal[$i] = trim($arCurVal[$i]);
                            $arDefVal = explode(",", $vval["DEFAULT_VALUE"]);
                            for ($i = 0, $cnt = count($arDefVal); $i < $cnt; $i++)
                                $arDefVal[$i] = trim($arDefVal[$i]);
                            ?>

                            <?php foreach ($vval["VALUES"] as $vvval): ?>
                                <option value="<?php echo $vvval["VALUE"] ?>"<?php if (in_array($vvval["VALUE"], $arCurVal) || !isset($currentValue) && in_array($vvval["VALUE"], $arDefVal)) echo " selected" ?>><?php echo $vvval["NAME"] ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <?php elseif ($vval["TYPE"]=="TEXTAREA"): ?>
                        <textarea class="form-control"
                                  rows="<?=(intval($vval["SIZE2"]) > 0) ? $vval["SIZE2"] : 4; ?>"
                                  cols="<?=(intval($vval["SIZE1"]) > 0) ? $vval["SIZE1"] : 40; ?>"
                                  name="<?=$name?>"><?=(isset($currentValue)) ? $currentValue : $vval["DEFAULT_VALUE"];?></textarea>
                        
                        
                        <?php elseif ($vval["TYPE"]=="LOCATION"): ?>
                            <?php if ($arParams['USE_AJAX_LOCATIONS'] == 'Y'): ?>
                                <?php
                                $locationValue = intval($currentValue) ? $currentValue : $vval["DEFAULT_VALUE"];
                                CSaleLocation::proxySaleAjaxLocationsComponent(
                                    array(
                                        "AJAX_CALL" => "N",
                                        'CITY_OUT_LOCATION' => 'Y',
                                        'COUNTRY_INPUT_NAME' => $name . '_COUNTRY',
                                        'CITY_INPUT_NAME' => $name,
                                        'LOCATION_VALUE' => $locationValue,
                                    ),
                                    array(),
                                    $locationTemplate,
                                    true,
                                    'location-block-wrapper'
								)
                                ?>
                            <?php else: ?>
                            <select class="form-control" name="<?=$name?>" size="<?=(intval($vval["SIZE1"]) > 0) ? $vval["SIZE1"] : 1; ?>">
                                <option value="<?php echo $vvval["ID"] ?>"<?php if (IntVal($vvval["ID"]) == IntVal($currentValue) || !isset($currentValue) && IntVal($vvval["ID"]) == IntVal($vval["DEFAULT_VALUE"])) echo " selected" ?>><?php echo $vvval["COUNTRY_NAME"] . " - " . $vvval["CITY_NAME"] ?></option>
                            </select>
                            <?php endif; ?>
                        
                        <?php endif; ?>
                        
                    </div>
                </div>
                <?php endforeach; ?>
            
            <?php endforeach; ?>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    <input class="btn btn-default btn2" type="submit" name="save" value="<?=Loc::getMessage("SALE_SAVE")?>">
                    <input class="btn btn-default btn2" type="submit" name="apply" value="<?=Loc::getMessage("SALE_APPLY")?>">
                    <input class="btn btn-default btn-button" type="submit" name="reset" value="<?=Loc::getMessage("SALE_RESET")?>">
                </div>
            </div>
        </form>
    </div>
    <div class="col col-md-12">
        <?=Loc::getMessage('REQUIED_FIELDS_NOTE')?>
    </div>
</div>
