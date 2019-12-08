<?if(!$USER->IsAdmin()) return;

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('development.lib_func');
CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

if((isset($_REQUEST['save']) || isset($_REQUEST['apply'])) && check_bitrix_sessid())
{
	$no_photo_path = COption::GetOptionString('development.lib_func', 'no_photo_path');
	COption::SetOptionString('development.lib_func', 'fakeprice_active', ($_REQUEST['fakeprice_active']=="Y" ? "Y" : "N") );
	UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', 'development.lib_func', 'RSLibFuncOffersExtension', 'HandlerOnAfterIBlockElementAddOrUpdateFakePrice');
	UnRegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'development.lib_func', 'RSLibFuncOffersExtension', 'HandlerOnAfterIBlockElementAddOrUpdateFakePrice');
	if($_REQUEST['fakeprice_active']=="Y")
	{
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementAdd', 'development.lib_func', 'RSLibFuncOffersExtension', 'HandlerOnAfterIBlockElementAddOrUpdateFakePrice',10000);
		RegisterModuleDependences('iblock', 'OnAfterIBlockElementUpdate', 'development.lib_func', 'RSLibFuncOffersExtension', 'HandlerOnAfterIBlockElementAddOrUpdateFakePrice',10000);
	}
	COption::SetOptionString('development.lib_func', 'propcode_cml2link', $_REQUEST['propcode_cml2link']);
	COption::SetOptionString('development.lib_func', 'propcode_fakeprice', $_REQUEST['propcode_fakeprice']);
	COption::SetOptionInt('development.lib_func', 'price_for_fake', IntVal($_REQUEST['price_for_fake']));
	COption::SetOptionString('development.lib_func', 'no_photo_path', $_REQUEST['no_photo_path']);
	if($no_photo_path!=$_REQUEST['no_photo_path'])
	{
		if($_REQUEST['no_photo_path']!="")
		{
			$arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$_REQUEST['no_photo_path']);
			$fid = CFile::SaveFile($arFile, "development_lib_func_nophoto");
			COption::SetOptionInt('development.lib_func', 'no_photo_fileid', $fid);
		} else {
			COption::SetOptionInt('development.lib_func', 'no_photo_fileid', 0);
		}
	}
}

$arPrice = array();
$rsPrice = CCatalogGroup::GetList($v1='sort',$v2='asc');
while($arr = $rsPrice->Fetch())
	$arPrice[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME_LANG'];

$tabControl = new CAdminTabControl('rslib_func_laptop_settings', array(
	array('DIV' => 'rslib_func_laptop_main', 'TAB' => GetMessage('RSDF.SETTINGS'), 'ICON' => 'settings', 'TITLE' => GetMessage('RSDF.SETTINGS')),
));

$tabControl->Begin();

?><form name="development_lib_func_option" method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
	echo bitrix_sessid_post();

	$tabControl->BeginNextTab();
	?><tr><?
		$fakeprice_active = COption::GetOptionString('development.lib_func', 'fakeprice_active');
		?><td colspan="2" valign="top" width="47%"><?=GetMessage('RSDF.FAKEPRICE_ACTIVE')?></td><?
		?><td><input type="checkbox" name="fakeprice_active" value="Y" <?if($fakeprice_active=="Y"):?> checked="checked"<?endif;?> /></td><?
	?></tr><?
	?><tr><?
		$propcode_cml2link = COption::GetOptionString('development.lib_func', 'propcode_cml2link');
		?><td colspan="2" valign="top" width="47%"><?=GetMessage('RSDF.PROPCODE_CML2LINK')?></td><?
		?><td><input type="text" name="propcode_cml2link" value="<?=$propcode_cml2link?>" /></td><?
	?></tr><?
	?><tr><?
		$propcode_fakeprice = COption::GetOptionString('development.lib_func', 'propcode_fakeprice');
		?><td colspan="2" valign="top" width="47%"><?=GetMessage('RSDF.PROPCODE_FAKE_PRICE')?></td><?
		?><td><input type="text" name="propcode_fakeprice" value="<?=$propcode_fakeprice?>" /></td><?
	?></tr><?
	?><tr><?
		$price_for_fake = COption::GetOptionInt('development.lib_func', 'price_for_fake', '0');
		?><td colspan="2" valign="top" width="47%"><?=GetMessage('RSDF.PROPCODE_PRICE_ID')?></td><?
		?><td><?
			?><select name="price_for_fake"><?
				?><option value="0">-</option><?
				foreach($arPrice as $priceID => $priceName)
				{
					?><option value="<?=$priceID?>"<?if($price_for_fake==$priceID):?> selected<?endif;?>><?=$priceName?></option><?
				}
			?></select><?
		?></td><?
	?></tr><?
	?><tr><?
		$no_photo_path = COption::GetOptionString('development.lib_func', 'no_photo_path');
		$no_photo_fileid = COption::GetOptionInt('development.lib_func', 'no_photo_fileid',0);
		?><td colspan="2" valign="top" width="47%"><?=GetMessage('RSDF.NO_PHOTO_PATH')?></td><?
		?><td><input type="text" name="no_photo_path" value="<?=$no_photo_path?>" /><?
		?><input type="hidden" name="no_photo_fileid" value="<?=$no_photo_fileid?>" /><?
		?><input type="button" value="<?=GetMessage("RSDF.BTN_FILEDIALOG")?>" OnClick="BtnFileDialogOpenNoPhoto()"><?
			CAdminFileDialog::ShowScript(
				Array(
					"event" => "BtnFileDialogOpenNoPhoto",
					"arResultDest" => array("FORM_NAME" => "development_lib_func_option", "FORM_ELEMENT_NAME" => "no_photo_path"),
					"arPath" => array("SITE" => SITE_ID, "PATH" => ""),
					"select" => 'F',// F - file only, D - folder only
					"operation" => 'O',// O - open, S - save
					"showUploadTab" => true,
					"showAddToMenuTab" => false,
					"fileFilter" => 'image',
					"allowAllFiles" => true,
					"SaveConfig" => true,
				)
			);
			?></td><?
	?></tr><?
	
$tabControl->Buttons(array());
$tabControl->End();
?></form>