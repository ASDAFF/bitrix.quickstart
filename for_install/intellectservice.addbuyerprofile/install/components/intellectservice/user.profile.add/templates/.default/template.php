<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
    use Bitrix\Main\Localization\Loc;
    CJSCore::Init(array("jquery"));
    $templateFolder = $this->GetFolder();
    Bitrix\Main\Page\Asset::getInstance()->addJs($templateFolder."/vendor.js");
?>

<?if(!empty($_SESSION["MSG_PROFILE"])):?>
    <p class="msg-header <?=$_SESSION["MSG_PROFILE_TYPE"]?>"><?=$_SESSION["MSG_PROFILE"]?></p>
	<?unset($_SESSION["MSG_PROFILE"]);?>
<?endif?>
<!--start component profile edit-->
<div class="profile-edit">
  <form class="" action="<?=$APPLICATION->GetCurPage()?>"  method="post">
  <?=bitrix_sessid_post()?>
  <div class="profile-info">
    <div class="table">
      <div class="table-row">
        <div class="table-cell">
          <?=GetMessage("INTELLECTSERVICE_ADDBUYERPROFILE_TIP_PROFILA")?></div>
        <?foreach($arResult["PERSON_TYPE"] as $key=>$ptype):?>
        <div class="table-cell">
          <div class="table-cell label-cell">
              <input type="radio" class="change-p-type" <?if($ptype["CHECKED"]):?>checked="checked" <?endif?> id="ptype-<?=$ptype["ID"]?>" name="PERSON_TYPE_ID" data-url="<?=$APPLICATION->GetCurPageParam('PERSON_TYPE_ID='.$ptype["ID"],array("PERSON_TYPE_ID"))?>" value="<?=$ptype["ID"]?>">
          </div>
          <div class="table-cell">
            <label for="ptype-<?=$ptype["ID"]?>"><?=$ptype["NAME"]?></label>
          </div>
        </div>
        <?endforeach?>
      </div>
    </div>
  </div>

    <div class="profile-input-block" id="group-id<?=$key?>">

      <div class="profile-input-item">
        <div class="profile-item-header">
            <?=Loc::getMessage('CP_DATA_PROFILE')?>
        </div>
        <div class="table">
          <div class="table-row">
            <div class="table-cell">
              <label for="profile-input-zero"><?=Loc::getMessage('CP_NAME_PROFILE')?>:</label>
            </div>
            <div class="table-cell requare-input">
              <input type="text" name="PROFILE_NAME" class="grey-input <?if(in_array(0,$_SESSION["PROFILE"]["VALIDATE"])):?>error<?endif?>" id="profile-input-0" value="<?=$_SESSION["PROFILE"]["FORM_VALUE"][0]?>">
            </div>
            <div class="table-cell tip-cell">
              <i class="ic-info-tip vtip" title="<?=Loc::getMessage('CP_CREATE_PROFILE')?>">
              </i>
            </div>
          </div>
        </div>
      </div>
      <?foreach($arResult["PROFILE_PROPS"] as $key=>$grpoup):?>
      <div class="profile-input-item">
        <div class="profile-item-header">
          <?=$key?>
        </div>
        <div class="table">
          <?foreach($grpoup as $item):?>
              <?$currentValue = $_SESSION["PROFILE"]["FORM_VALUE"][$item["ID"]];?>
          <div class="table-row">
            <div class="table-cell">
              <label for="profile-input-<?=$item["ID"]?>"><?=$item["NAME"]?>:</label>
            </div>
            <div class="table-cell <?if($item["REQUIED"]=="Y"):?>requare-input<?endif?>">

                <?if($item["TYPE"] === "LOCATION"):?>
                    <?
                    $locationValue = intval($currentValue) ? $currentValue : $property["DEFAULT_VALUE"];
                    CSaleLocation::proxySaleAjaxLocationsComponent(
                        array(
                            "AJAX_CALL" => "N",
                            'CITY_OUT_LOCATION' => 'Y',
                            'COUNTRY_INPUT_NAME' => 'PROP_'.$item["ID"],
                            'CITY_INPUT_NAME' => 'PROP_'.$item["ID"],
                            'LOCATION_VALUE' => $locationValue,
                        )
                    );
                    ?>
                 <?elseif($item["TYPE"] === "TEXT"):?>
                    <input type="text" name="PROP_<?=$item["ID"]?>_<?=$item["TYPE"]?>" class="grey-input <?if(in_array($item["ID"],$_SESSION["PROFILE"]["VALIDATE"])):?>error<?endif?>" id="profile-input-<?=$item["ID"]?>" value="<?=(isset($currentValue)) ? $currentValue : $item["DEFAULT_VALUE"];?>">

                <?elseif($item["TYPE"] === "TEXTAREA"):?>
                    <textarea
                            class="grey-input <?if(in_array($item["ID"],$_SESSION["PROFILE"]["VALIDATE"])):?>error<?endif?>"
                            id="profile-input-<?=$item["ID"]?>"
                            name="PROP_<?=$item["ID"]?>_<?=$item["TYPE"]?>"><?=(isset($currentValue)) ? $currentValue : $item["DEFAULT_VALUE"];?></textarea>

                <?elseif($item["TYPE"] == "CHECKBOX"):?>
                    <input
                            class="check-box-input <?if(in_array($item["ID"],$_SESSION["PROFILE"]["VALIDATE"])):?>error<?endif?>"
                            id="profile-input-<?=$item["ID"]?>"
                            type="checkbox"
                            name="PROP_<?=$item["ID"]?>_<?=$item["TYPE"]?>"
                            value="Y"
                        <?if ($currentValue == "Y" || !isset($currentValue) && $item["DEFAULT_VALUE"] == "Y") echo " checked";?>/>

                <?elseif($item["TYPE"] == "SELECT"):?>
                    <select
                            class="grey-input <?if(in_array($item["ID"],$_SESSION["PROFILE"]["VALIDATE"])):?>error<?endif?>"
                            name="PROP_<?=$item["ID"]?>_<?=$item["TYPE"]?>"
                            id="profile-input-<?=$item["ID"]?>">
                        <?
                        foreach ($item["VALUES"] as $value)
                        {
                            ?>
                            <option value="<?= $value["VALUE"]?>" <?if ($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"]==$item["DEFAULT_VALUE"]) echo " selected"?>>
                                <?= $value["NAME"]?>
                            </option>
                            <?
                        }
                        ?>
                    </select>

                <?elseif($item["TYPE"] == "MULTISELECT"):?>
                    <select
                            class="grey-input <?if(in_array($item["ID"],$_SESSION["PROFILE"]["VALIDATE"])):?>error<?endif?>"
                            id="profile-input-<?=$item["ID"]?>"
                            multiple name="PROP_<?=$item["ID"]?>_<?=$item["TYPE"]?>[]">
                        <?
                        $arCurVal = array();
                        $arCurVal = explode(",", $currentValue);
                        for ($i = 0, $cnt = count($arCurVal); $i < $cnt; $i++)
                            $arCurVal[$i] = trim($arCurVal[$i]);
                        $arDefVal = $item["DEFAULT_VALUE"];
                        for ($i = 0, $cnt = count($arDefVal); $i < $cnt; $i++)
                            $arDefVal[$i] = trim($arDefVal[$i]);
                        foreach($item["VALUES"] as $value)
                        {
                            ?>
                            <option value="<?= $value["VALUE"]?>"<?if (in_array($value["VALUE"], $arCurVal) || !isset($currentValue) && in_array($value["VALUE"], $arDefVal)) echo" selected"?>><?echo $value["NAME"]?></option>
                            <?
                        }
                        ?>
                    </select>

                <?elseif($item["TYPE"] == "RADIO"):?>
                    <?foreach($property["VALUES"] as $value)
                    {
                    ?>
                    <input
                            class="grey-input <?if(in_array($item["ID"],$_SESSION["PROFILE"]["VALIDATE"])):?>error<?endif?>"
                            type="radio"
                            id="profile-input-<?=$item["ID"]?>">
                            name="PROP_<?=$item["ID"]?>_<?=$item["TYPE"]?>"
                            value="<?echo $value["VALUE"]?>"
                        <?if ($value["VALUE"] == $currentValue || !isset($currentValue) && $value["VALUE"] == $property["DEFAULT_VALUE"]) echo " checked"?>>
                    <?= $value["NAME"]?><br />

                <?}?>

                <?elseif($item["TYPE"] == "FILE"):?>

                    <?$APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
                        array(
                            "INPUT_NAME"=>'PROP_'.$item["ID"]."_FILE",
                            "MULTIPLE"=>$property["MULTIPLE"],
                            "MODULE_ID"=>"main",
                            "MAX_FILE_SIZE"=>$item["SETTINGS"]["MAXSIZE"],
                            "ALLOW_UPLOAD"=>"F",
                            "ALLOW_UPLOAD_EXT"=>$item["SETTINGS"]["ACCEPT"],
                        ),
                        false
                    );?>

                <?endif?>

            </div>
            <div class="table-cell tip-cell">
                <?if(!empty($item["DESCRIPTION"])):?>
                    <i class="ic-info-tip vtip" title="<?=$item["DESCRIPTION"]?>"></i>
                <?endif?>
            </div>
          </div>
          <?endforeach?>
        </div>
      </div>
      <?endforeach?>
      <div class="row text-center">
        <button type="submit" name="save" value="Y" class="detail-link-btn"><?=Loc::getMessage('CP_ADD_PROFILE')?></button>
      </div>
    </div>
  </form>
</div>
<?unset($_SESSION["PROFILE"])?>
<!--end component profile edit-->
