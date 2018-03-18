<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
include(GetLangFileName(dirname(__FILE__).'/', '/payment.php'));
CModule::IncludeModule("tcsbank.kupivkredit");
$obTCSModule = new CTCSBank;
$iOrderID = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$sButton = $obTCSModule->GetButton($iOrderID);
?>
<?if(strlen($sButton)):?>
	<div style = "margin:10px 0;">
		<?=$sButton;?>
	</div>
<?endif;?>
<?/*
	<!--- If you don't want to use default button use such code --->
	
	
	echo $obTKSModule->GetButton($iOrderID,true);
	?>
		<input type = "button" onclick = "vkredit.openWidget();" value = "Pay"/>
	<?
	
*/?>