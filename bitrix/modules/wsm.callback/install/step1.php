<?IncludeModuleLangFile(__FILE__);?>

<?
$arType = array();
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{
	if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
	{
		$arType[$arIBType["ID"]] = $arIBType["NAME"];
	}   
}

$arSiteFilter = Array(
	"ACTIVE" => "Y",
	);

$dbSites = CSite::GetList(($b = ""), ($o = ""), $arSiteFilter);
while ($site = $dbSites->Fetch())
{
	$site["ID"] = htmlspecialcharsbx($site["ID"]);
	$site["NAME"] = htmlspecialcharsbx($site["NAME"]);
	$arSites[] = $site;
}
?>

<form action="<?=$APPLICATION->GetCurPage()?>" name="form1" id="wsm_install">
	<?=bitrix_sessid_post()?>
	
	<input type="hidden" name="id" value="wsm.callback"/>
	<input type="hidden" name="install" value="Y"/>
	<input type="hidden" name="step" value="2"/>
	<input type="hidden" name="lang" value="<?echo LANG?>"/>

	<b class="heading"><?echo GetMessage("WSM_CALLBACK_INSTALL_MAIN")?></b>
	<br/>
	<p><input type="checkbox" name="install_iblock" id="wsm_instaliblock" value="Y" checked><label for="wsm_instaliblock"><?echo GetMessage("WSM_CALLBACK_INSTALL_IBLOCK")?></label></p>

	<p class="wsm_instaliblock">
		<?echo GetMessage("WSM_CALLBACK_IBLOCK_TYPE")?> <small>(<?echo GetMessage("WSM_CALLBACK_IBLOCK_TYPE_DESC")?>)</small>: 
		<select name="iblock_type" id="wsm_iblock_type">
		<?foreach($arType as $id => $type):?>
			<option value="<?=$id?>" <?if($id == 'services'):?>selected<?endif;?>><?=$type?></option>
		<?endforeach?>
		</select>
	</p>
	<p class="wsm_instaliblock">
		<input type="checkbox" name="install_demo" id="wsm_instaldemo" value="Y" checked><label for="wsm_instaldemo"><?echo GetMessage("WSM_CALLBACK_INSTALL_DEMO")?></label>
		<small>(<?echo GetMessage("WSM_CALLBACK_IBLOCK_DEMO_DESC")?>)</small>
	</p>
	
	<div class="wsm_instaliblock wsm_instaldemo">
	<?if(count($arSites)>0):?> 
	<br/>
	<b class="heading"><?echo GetMessage("WSM_CALLBACK_INSTALL_SITE")?></b>
	<br/>
	<p><input type="checkbox" name="install_allsite" id="wsm_install_allsite" value="Y" checked /><label for="wsm_install_allsite"><?echo GetMessage("WSM_CALLBACK_INSTALL_ALLSITE")?></label></p>
	<p class="wsm_install_allsite" style="display:none;">
		<?echo GetMessage("WSM_CALLBACK_INSTALL_TOSITE")?>: 
		<select name="install_site" id="install_site">
		<?foreach($arSites as $id => $site):?>
			<option value="<?=$site['ID']?>"><?=$site['NAME']?></option>
		<?endforeach?>
		</select>
		<br/>
	</p>
	<?endif;?>
	</div>
	<input type="submit" name="" value="<?echo GetMessage("MOD_INSTALL")?>"/>
</form>


<script>
BX.bind(BX('wsm_instaliblock'), 'click', function(){
	
	var items = BX.findChildren(BX('wsm_install'), {className: 'wsm_instaliblock'}, true);
	var i;
	for (i=0; i<items.length; i++){
		if(this.checked)
			BX.show(items[i]);
		else
			BX.hide(items[i]);		
		}
	});

BX.bind(BX('wsm_instaldemo'), 'click', function(){
	var	items = BX.findChildren(BX('wsm_install'), {className: 'wsm_instaldemo'}, true),
	i;
		
	for (i=0; i < items.length; i++){
		if(this.checked)
			BX.show(items[i]);
		else
			BX.hide(items[i]);		
		}
	
	});	
	
BX.bind(BX('wsm_install_allsite'), 'click', function(){
	var parent = BX.findParent(BX('wsm_install_allsite'), {className: 'wsm_instaliblock'}),
		items = BX.findChildren(parent, {className: 'wsm_install_allsite'}, true),
		i;

	for (i=0; i < items.length; i++){
		if(this.checked)
			BX.hide(items[i]);
		else
			BX.show(items[i]);		
		}	
	
	});	

</script>