<?php
/**
 * View - Sheepla shipments table (using widget)
 */
if (!defined('SHEEPLA_DIR'))
{
	define('SHEEPLA_DIR', $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sheepla.delivery/");
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if($APPLICATION->GetGroupRight("main") < "R") 
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");
require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");
CModule::IncludeModule("sheepla.delivery");
$config = (array)CSheepla::getConfig();
$culture = (int)CSheepla::GetCultureId();

$APPLICATION->SetTitle(GetMessage('SHEEPLA_DELIVERIS'));
$APPLICATION->AddHeadString('<script type="text/javascript" charset="utf-8" src="'.$config['jsUrl'].'"></script>');
$APPLICATION->AddHeadString('<script type="text/javascript">sheepla.init({apikey: \''.$config['adminApiKey'].'\',cultureId: '.$culture.'});</script>');
$APPLICATION->AddHeadString('<meta http-equiv="X-UA-Compatible" content="IE=9" />');
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="'.$config['cssUrl'].'" />');
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/js/sheepla.delivery/css/sheepla.css" />');
?>

<script type="text/javascript">
    sheepla.init({apikey: '<?=$config['adminApiKey'];?>', cultureId: <?=$culture?>});
    sheepla.call_registry.ready = function() {
        sheepla.get_shipments('#sheepla-sdk-list', 1);
    }
</script>
<div id="sheepla-sdk-list"></div>

<? require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php"); ?>