<?//страница настроек для административной части
use Bitrix\Main\Localization\Loc;
use	Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Config\Configuration as Conf;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

$arConfig = Conf::getInstance()->get('exception_handling');

$aTabs = array
(
    array
    (
        "DIV" 	  => "edit1",
        "TAB" 	  => "Robots.txt",
        "TITLE"   => "Robots.txt",
        "OPTIONS" => array
        (
            Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_COMMON"),
            array
            (
                "site_index",
                Loc::getMessage("AAM_DUTIL_OPTIONS_ROBOTS"),
                false,
                array("checkbox")
            )
        )
    ),

    array
    (
        "DIV" 	  => "edit2",
        "TAB" 	  => Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_NAME"),
        "TITLE"   => Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_NAME"),
        "OPTIONS" => array
        (
            Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_COMMON"),
            array
            (
                "debug",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_DEBUG_ON"),
                false,
                array("checkbox")
            ),
            array
            (
                "handled_errors_types",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_HANDLED_ERRORS_TYPES"),
                4437,
                array("text")
            ),
            array
            (
                "exception_errors_types",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_EXCEPTION_ERRORS_TYPES"),
                4437,
                array("text")
            ),
            array
            (
                "ignore_silence",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_IGNORE_SILENCE"),
                false,
                array("checkbox")
            ),
            array
            (
                "assertion_throws_exception",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_ASSERTION_THROWS_EXCEPTION"),
                true,
                array("checkbox")
            ),
            array
            (
                "assertion_error_type",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_ASSERTION_ERROR_TYPE"),
                256,
                array("text")
            ),

            Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_LOG"),
            array
            (
                "log",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_LOG_ON"),
                false,
                array("checkbox")
            ),
            array
            (
                "log_file",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_LOG_FILE"),
                'bitrix/modules/error.log',
                array("text")
            ),
            array
            (
                "log_file_size",
                Loc::getMessage("AAM_DUTIL_OPTIONS_TAB_LOG_FILE_SIZE"),
                1000000,
                array("text")
            )
        )
    )
);


if($request->isPost() && check_bitrix_sessid())
{
    foreach($aTabs as $aTab)
    {
        foreach($aTab["OPTIONS"] as $arOption)
        {
            if(!is_array($arOption))
                continue;

            if($arOption["note"])
                continue;

            if($request["apply"])
            {
                $optionValue = $request->getPost($arOption[0]);

                if($optionValue == "")
                    $optionValue = "N";

                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            }
            elseif($request["default"])
            {
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    \AAM\DUtil\CMain::ProcessingOfResults();

    LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}


$tabControl = new CAdminTabControl("tabControl", $aTabs);

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

	<input type="submit" name="apply" value="<? echo(Loc::GetMessage("AAM_DUTIL_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
	<input type="submit" name="default" value="<? echo(Loc::GetMessage("AAM_DUTIL_OPTIONS_INPUT_DEFAULT")); ?>" />

	<?
	echo(bitrix_sessid_post());
	?>

</form>

<?
$tabControl->End();