<?
IncludeModuleLangFile(__FILE__);
$aTabs = array(
  array("DIV" => "edit1", "TAB" => GetMessage("TITLE"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("TITLE")),
  //array("DIV" => "edit2", "TAB" => GetMessage("rub_tab_generation"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("rub_tab_generation_title")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);


?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($mid)?>&lang=<?=LANGUAGE_ID?>">
<?=bitrix_sessid_post()?>
<?
// отобразим заголовки закладок
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
				<tr>
					<td class="adm-detail-content-cell-l">
						<?=GetMessage("COLOR_THEME");?>	</td>
					<td class="adm-detail-content-cell-r">
						<select name="SELECT_THEME" id="select_theme" onchange="setColorsBySelect()">
						</select>
					</td>
				</tr>
				<tr>
					<td width="50%" class="adm-detail-content-cell-l"><?=GetMessage("FIRST_COLOR");?></td>
					<td width="50%" class="adm-detail-content-cell-r">#<input type="text" size="6" maxlength="255" value="<?=COption::GetOptionString("twinpx.bejetstore", "FIRST_COLOR")?>" name="FIRST_COLOR" id="first_color"></td>
				</tr>
				
				<tr>
					<td width="50%" class="adm-detail-content-cell-l"><?=GetMessage("SECOND_COLOR");?></td>
					<td width="50%" class="adm-detail-content-cell-r">#<input type="text" size="6" maxlength="255" value="<?=COption::GetOptionString("twinpx.bejetstore", "SECOND_COLOR")?>" name="SECOND_COLOR" id="second_color"></td>
				</tr>				
					
				<?if($USER->isAdmin()):?>
					<?//echo SITE_TEMPLATE_PATH?>
					
					<?include_once 'lessc.inc.php';?>
					<?//print_R($_REQUEST)?>
					<?
						if($_REQUEST['FIRST_COLOR'])
						{
							COption::SetOptionString("twinpx.bejetstore", "FIRST_COLOR", $_REQUEST['FIRST_COLOR']);
						}
						if($_REQUEST['SECOND_COLOR'])
						{
							COption::SetOptionString("twinpx.bejetstore", "SECOND_COLOR", $_REQUEST['SECOND_COLOR']);
						}
						if($_REQUEST['SELECT_THEME'])
						{
							COption::SetOptionString("twinpx.bejetstore", "SELECT_THEME", $_REQUEST['SELECT_THEME']);
						}
					?>
				
					<?
					if($_REQUEST['FIRST_COLOR']): 
						$less = new lessc;
						//colors.less.template

						$colorsLessFileText=file_get_contents(__DIR__.'/colors.less.template.txt');
						
						
						if($_REQUEST['FIRST_COLOR'])$colorsLessFileText=str_replace('#8ea32b', '#'.$_REQUEST['FIRST_COLOR'], $colorsLessFileText);
						if($_REQUEST['SECOND_COLOR'])$colorsLessFileText=str_replace('#6f840b', '#'.$_REQUEST['SECOND_COLOR'], $colorsLessFileText);
						
						//echo $colorsLessFileText;
						
						$f=fopen(__DIR__.'/colors.less', 'w');
						fwrite($f, $colorsLessFileText);
						fclose($f);
						
						try
						{
							$output = $less->compileFile(__DIR__.'/colors.less', $_SERVER["DOCUMENT_ROOT"].'/colors.css');
						}		
						catch(exception $e)
						{
							echo 'Fatal error: '.$e->getMessage();
						}
						
						?>
					<?endif?>
				
				<?endif?>

<?
// завершение формы - вывод кнопок сохранения изменений
$tabControl->Buttons(
  array(
    "disabled" => 'N',
    "back_url" => $APPLICATION->GetCurPage(),
  )
);
?>




<?
$tabControl->End();
?>
</form>


<script type="text/javascript">
if (!window.tabControl || !BX.is_subclass_of(window.tabControl, BX.adminTabControl))
	window.tabControl = new BX.adminTabControl("tabControl", "tabControl_764bff41d686bebaac38e969710b706a", [{'DIV': 'edit1' }, {'DIV': 'edit7' }, {'DIV': 'edit5' }, {'DIV': 'edit3' }, {'DIV': 'edit4' }]);
else if(!!window.tabControl)
	window.tabControl.PreInit(true);

</script>
<script>
colors=[
{
	name: 'deepBlue',
	runame: '<?=GetMessage("DEEPBLUE");?>',
	file: 'deep_blue',
	brandPrimary: '2d4270',
	brandSecondary: '052daf'
},
{
	name: 'deepNight',
	runame: '<?=GetMessage("DEEPNIGHT");?>',
	file: 'deep_night',
	brandPrimary: '2f3154',
	brandSecondary: '5e5f81'
},
{
	name: 'emerald',
	runame: '<?=GetMessage("EMERALD");?>',
	file: 'emerald',
	brandPrimary: '41aa70',
	brandSecondary: '423938'
},
{
	name: 'grayscale',
	runame: '<?=GetMessage("GRAYSCALE");?>',
	file: 'grayscale',
	brandPrimary: 'ababab',
	brandSecondary: '5b5b5b'
},
{
	name: 'green',
	runame: '<?=GetMessage("GREEN");?>',
	file: 'green',
	brandPrimary: '489526',
	brandSecondary: 'ec5f09'
},
{
	name: 'greenLime',
	runame: '<?=GetMessage("GREENLIME");?>',
	file: 'green_lime',
	brandPrimary: '8ea32b',
	brandSecondary: '6f840b'
},
{
	name: 'grey',
	runame: '<?=GetMessage("GREY");?>',
	file: 'grey',
	brandPrimary: '894b2d',
	brandSecondary: '000'
},
{
	name: 'orangeBlack',
	runame: '<?=GetMessage("ORANGEBLACK");?>',
	file: 'orange_black',
	brandPrimary: 'e56225',
	brandSecondary: '000'
},
{
	name: 'orangeGreen',
	runame: '<?=GetMessage("ORANGEGREEN");?>',
	file: 'orange_green',
	brandPrimary: 'ff7e30',
	brandSecondary: '589a53'
},
{
	name: 'salmon',
	runame: '<?=GetMessage("SALMON");?>',
	file: 'salmon',
	brandPrimary: 'ffa48f',
	brandSecondary: 'ab365a'
},
{
	name: 'scarlet',
	runame: '<?=GetMessage("SCARLET");?>',
	file: 'scarlet',
	brandPrimary: '3c333a',
	brandSecondary: 'fc354c'
},
{
	name: 'sea',
	runame: '<?=GetMessage("SEA");?>',
	file: 'sea',
	brandPrimary: '337d9f',
	brandSecondary: '196e83'
},
{
	name: 'seaWave',
	runame: '<?=GetMessage("SEAWAVE");?>',
	file: 'sea_wave',
	brandPrimary: '00a6b5',
	brandSecondary: '265c99'
},
{
	name: 'violetMagenta',
	runame: '<?=GetMessage("VIOLETMAGENTA");?>',
	file: 'violet_magenta',
	brandPrimary: '7500c0',
	brandSecondary: 'be00ca'
},
{
	name: 'red',
	runame: '<?=GetMessage("RED");?>',
	file: 'red',
	brandPrimary: 'a52525',
	brandSecondary: 'd11d32'
},
{
	name: 'raspberry',
	runame: '<?=GetMessage("RASPBERRY");?>',
	file: 'raspberry',
	brandPrimary: 'a7347d',
	brandSecondary: 'a8398d'
},
{
	name: 'pinkBlue',
	runame: '<?=GetMessage("PINKBLUE");?>',
	file: 'pink_blue',
	brandPrimary: 'eda1cf',
	brandSecondary: 'a3add7'
},
{
	name: 'purpleWhite',
	runame: '<?=GetMessage("PURPLEWHITE");?>',
	file: 'purple_white',
	brandPrimary: 'a3add7',
	brandSecondary: '5c6eb8'
},
{
	name: 'blackBalck',
	runame: '<?=GetMessage("BLACKBALCK");?>',
	file: 'black_black',
	brandPrimary: '000000',
	brandSecondary: '000000'
}
];
			

	
for (var i = 0; i < colors.length; i++) 
{
	 var option = document.createElement('option'); colors[i];
	 <?if(COption::GetOptionString("twinpx.bejetstore", "SELECT_THEME")):?>
	 var last=<?=COption::GetOptionString("twinpx.bejetstore", "SELECT_THEME");?>;
	 <?else:?>
	 var last=0;
	 <?endif?>
	 if(last==i)
	 {
		option.selected='selected';
	 }
	 option.value=i;
	 option.innerText=colors[i].runame;
	 document.getElementById('select_theme').appendChild(option);
}
function setColorsBySelect()
{
	var i=document.getElementById('select_theme').value;
	document.getElementById('first_color').value=colors[i].brandPrimary;
	document.getElementById('second_color').value=colors[i].brandSecondary;
}
</script>











