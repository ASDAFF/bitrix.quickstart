<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");
?>

<div class="row">
    <div class="col-xs-12">
        <?php
        $userProps = array_merge(
			(array) $arResult["ORDER_PROP"]["USER_PROPS_N"],
			(array) $arResult["ORDER_PROP"]["USER_PROPS_Y"]
		);
            
        PrintPropsForm($userProps, $arParams["TEMPLATE_LOCATION"]);
        ?>
    </div>
</div>

