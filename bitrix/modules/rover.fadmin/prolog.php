<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

define("ADMIN_MODULE_NAME", "rover.fadmin");
define("ADMIN_MODULE_ICON", "<img src=\"/bitrix/images/iblock/iblock.gif\" width=\"48\" height=\"48\" border=\"0\" alt=\"" . Loc::getMessage("rover_fa__icon_hint") . "\" title=\"" . Loc::getMessage("rover_fa__icon_hint") . "\">");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/admin_tools.php");
