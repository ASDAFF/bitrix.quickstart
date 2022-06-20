<?
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/bitrix/sale.order.ajax/lang/'.LANGUAGE_ID.'/delivery_extra_params.php');

$deliveryId = isset($_REQUEST["deliveryId"]) ? trim($_REQUEST["deliveryId"]) : "";

\Bitrix\Main\Loader::includeModule("sale");

$formName = isset($_REQUEST["formName"]) ? trim($_REQUEST["formName"]) : "extra_params_form";

?>
<html>
<head>
<?$APPLICATION->ShowHead();?>
<title><?$APPLICATION->ShowTitle()?></title>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" bgcolor="#FFFFFF">
<form name="<?=$formName?>" id="<?=$formName?>">
<?if(isset($_SESSION["SALE_DELIVERY_EXTRA_PARAMS"][$deliveryId]) && is_array($_SESSION["SALE_DELIVERY_EXTRA_PARAMS"][$deliveryId])):?>
	<table>
		<?foreach($_SESSION["SALE_DELIVERY_EXTRA_PARAMS"][$deliveryId] as $fieldName => $fieldParams):
			if(isset($_REQUEST[$fieldName]))
			{
				$fieldParams["VALUE"] = $_REQUEST[$fieldName];
			}
			?>
			<tr>
				<td><?=$fieldParams["TITLE"].":"?></td>
				<td><?=CSaleHelper::getAdminHtml("", $fieldParams, $fieldName, 'extra_params_form')?></td>
			</tr>
		<?endforeach;?>
	</table>
<?endif;?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
</body>
</html>
<?
