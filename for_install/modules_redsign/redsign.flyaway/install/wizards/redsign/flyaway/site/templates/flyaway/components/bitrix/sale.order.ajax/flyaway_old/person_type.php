<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
?>

<div class="row personal-makeorder__person-type">
    <?php if (is_array($arResult["ORDER_PROP"]["USER_PROFILES"]) && !empty($arResult["ORDER_PROP"]["USER_PROFILES"])): ?>
        <?php if ($arParams["ALLOW_NEW_PROFILE"] == "Y"): ?>
        <div class="col col-xs-12 loss-menu-right" style="margin-bottom: 15px">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">
                    <i class="fa visible-xs-inline fileicon"></i>
                    <?php
                    $currentProfile = undefined;
                    foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {
                        
                        if ($arUserProfiles["CHECKED"]=="Y") {
                            $currentProfile = $arUserProfiles['NAME'];
                        }
                    }
                    ?>
                    <?php if($currentProfile): ?>
                        <?=$currentProfile?>
                    <?php else: ?>
                        <?=Loc::getMessage('SOA_TEMPL_PROP_CHOOSE')?>
                    <?php endif; ?>
                    <i class="fa fa-angle-down"></i>
                </button>
                <ul class="dropdown-menu views-box drop-panel" role="menu" aria-labelledby="dLabel">
                    <?php foreach ($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles): ?> 
                        <li class="views-item">
                            <a href="javascript:void(0)" onclick="SetContact(<?=$arUserProfiles["ID"]?>)">
                                <?=$arUserProfiles["NAME"]?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <select name="PROFILE_ID" class="hidden" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
                        <option value="0"><?=Loc::getMessage("SOA_TEMPL_PROP_NEW_PROFILE")?></option>
                        <?php foreach ($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles): ?>
                            <option value="<?= $arUserProfiles["ID"] ?>"<?php if ($arUserProfiles["CHECKED"]=="Y") echo " selected";?>><?=$arUserProfiles["NAME"]?></option>
                        <?php endforeach; ?>
                    </select>
                </ul>
            </div>
        </div>
        <?php endif;?>
    <?php endif; ?>
    <div class="col col-xs-12">
        <?php if(!empty($arResult["PERSON_TYPE"]) && count($arResult["PERSON_TYPE"]) > 0): ?>
        <div class = "form-group">
            <label><?=Loc::getMessage('SOA_TEMPL_PERSON_TYPE')?></label>
            <div>
                <?php foreach($arResult['PERSON_TYPE'] as $v): ?>
                <label for="PERSON_TYPE_<?=$v["ID"]?>">
					<input type = "radio" id="PERSON_TYPE_<?=$v["ID"]?>" name="PERSON_TYPE" value="<?=$v["ID"]?>" <?=$v['CHECKED'] == 'Y'?'checked':''?> onClick="submitForm()" style="display: none;">
					<span class = "btn btn-default btn-button<?=$v['CHECKED'] == 'Y'?' active':''?>">
						<?=$v["NAME"]?>
					</span>
				</label>
                <?php endforeach; ?>
                <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult["USER_VALS"]["PERSON_TYPE_ID"]?>">
            </div>
        </div>
        <?php else: ?>
            <?php if((int) $arResult["USER_VALS"]["PERSON_TYPE_ID"] > 0): ?>
            <span style="display:none;">
			<input type="text" name="PERSON_TYPE" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
			<input type="text" name="PERSON_TYPE_OLD" value="<?=IntVal($arResult["USER_VALS"]["PERSON_TYPE_ID"])?>">
			</span>
            <?php else: ?>
                <?php foreach($arResult["PERSON_TYPE"] as $v): ?>
                    <input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v["ID"]?>">
                    <input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v["ID"]?>">
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
        <pre style="display: none"><?php var_dump($arResult["PERSON_TYPE"]); ?></pre>
    </div>
</div>