<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?$incMod = CModule::IncludeModuleEx("altasib.geobase");
if ($incMod == '0' || $incMod == '3')
	return false;?>
<?if($_REQUEST['bxsender'] != 'fileman_html_editor'):?>
<div style="background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E;  margin-bottom: 15px;"><div style="background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff">
		<a href="http://www.is-market.ru?param=cl" target="_blank"><img src="/bitrix/components/altasib/geobase.your.city/images/is-market.gif" style="float: left; margin-right: 15px;" border="0" /></a>
		<div style="margin: 13px 0px 0px 0px">
				<a href="http://www.is-market.ru?param=cl" target="_blank" style="color: #fff; font-size: 10px; text-decoration: none"><?=GetMessage("ALTASIB_IS")?></a>
		</div>
</div></div>
<? if ($incMod == '0') ShowError(GetMessage("ALTASIB_GEOBASE_MODULE_NOT_FOUND"));
elseif ($incMod == '3') ShowError(GetMessage("ALTASIB_GEOBASE_DEMO_EXPIRED"));
elseif ($incMod == '2') ShowNote(GetMessage("ALTASIB_GEOBASE_DEMO"));?>
<? endif;


$arComponentParameters = array(
	"PARAMETERS" => Array(
	)
);
?>