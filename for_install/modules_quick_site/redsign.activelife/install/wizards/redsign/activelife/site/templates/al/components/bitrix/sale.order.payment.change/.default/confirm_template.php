<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Localization\Loc;

if (!empty($arResult["errorMessage"]))
{
	if (!is_array($arResult["errorMessage"]))
	{
		ShowError($arResult["errorMessage"]);
	}
	else
	{
		foreach ($arResult["errorMessage"] as $errorMessage)
		{
			ShowError($errorMessage);
		}
	}
}
else
{
	if (empty($arResult['PAYMENT_LINK']) && !$arResult['IS_CASH'] && strlen($arResult['TEMPLATE']))
	{
		echo $arResult['TEMPLATE'];
	}
	else
	{
		?>
		<div class='row'>
			<div class='col-xs-12'>
				<p><?=Loc::getMessage("SOPC_ORDER_SUC", array("#ORDER_ID#"=>$arResult['ORDER_ID'],"#ORDER_DATE#"=>$arResult['ORDER_DATE']))?></p>
				<p><?=Loc::getMessage("SOPC_PAYMENT_SUC", array("#PAYMENT_ID#"=>$arResult['PAYMENT_ID']))?></p>
				<p><?=Loc::getMessage("SOPC_PAYMENT_SYSTEM_NAME", array("#PAY_SYSTEM_NAME#"=>$arResult['PAY_SYSTEM_NAME']))?></p>
				<?
				if (!$arResult['IS_CASH'] && strlen($arResult['PAYMENT_LINK']))
				{
					?>
					<p><?=Loc::getMessage("SOPC_PAY_LINK", array("#LINK#"=>$arResult['PAYMENT_LINK']))?></p>
					<?
				}
				?>
			</div>
		</div>
		<?
		if (!$arResult['IS_CASH'])
		{
			?>
			<script type="text/javascript">
				window.open("<?=$arResult['PAYMENT_LINK']?>");
			</script>
			<?
		}
	}
}
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
?>