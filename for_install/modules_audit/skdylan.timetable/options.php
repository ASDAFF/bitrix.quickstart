    <?php
use Bitrix\Main\Localization\Loc;
use	Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);


$aTabs = array(
    array(
        "DIV" 	  => "edit",
        "TAB" 	  => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_NAME"),
        "TITLE"   => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_NAME"),
        "OPTIONS" => array(
            Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_COMMON"),
            array(
                "switch_on",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SWITCH_ON"),
                "Y",
                array("checkbox")
            ),
            Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_APPEARANCE"),
            array(
                "width",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_WIDTH"),
                "50",
                array("text", 5)
            ),
            array(
                "height",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_HEIGHT"),
                "50",
                array("text", 5)
            ),
            array(
                "radius",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_RADIUS"),
                "50",
                array("text", 5)
            ),
            array(
                "color",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_COLOR"),
                "#bf3030",
                array("text", 5)
            ),
            Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_POSITION_ON_PAGE"),
            array(
                "side",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SIDE"),
                "left",
                array("selectbox", array(
                    "left"  => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SIDE_LEFT"),
                    "right" => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SIDE_RIGHT")
                ))
            ),
            array(
                "indent_bottom",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_INDENT_BOTTOM"),
                "10",
                array("text", 5)
            ),
            array(
                "indent_side",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_INDENT_SIDE"),
                "10",
                array("text", 5)
            ),
            Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_ACTION"),
            array(
                "speed",
                Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SPEED"),
                "normal",
                array("selectbox", array(
                    "slow"   => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SPEED_SLOW"),
                    "normal" => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SPEED_NORMAL"),
                    "fast"   => Loc::getMessage("FALBAR_TOTOP_OPTIONS_TAB_SPEED_FAST")
                ))
            )
        )
    )
);

$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);

if($request->isPost() && check_bitrix_sessid()){

    foreach($aTabs as $aTab){

        foreach($aTab["OPTIONS"] as $arOption){

            if(!is_array($arOption)){

                continue;
            }

            if($arOption["note"]){

                continue;
            }

            if($request["apply"]){

                $optionValue = $request->getPost($arOption[0]);

                if($arOption[0] == "switch_on"){

                    if($optionValue == ""){

                        $optionValue = "N";
                    }
                }

                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            }elseif($request["default"]){

                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}


$tabControl->Begin();

?>
<form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">

    <?
    foreach($aTabs as $aTab){

        if($aTab["OPTIONS"]){

            $tabControl->BeginNextTab();

            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="<? echo(Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
    <input type="submit" name="default" value="<? echo(Loc::GetMessage("FALBAR_TOTOP_OPTIONS_INPUT_DEFAULT")); ?>" />

    <?
    echo(bitrix_sessid_post());
    ?>

</form>

<?php

$tabControl->End();

?>