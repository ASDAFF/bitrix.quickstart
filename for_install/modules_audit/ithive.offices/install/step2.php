<?if(!check_bitrix_sessid()) return;?>
<?
global $errors;
$install_public = (($install_public == "Y") ? "Y" : "N");
$install_demo_data = "Y"; // устанавливаем демо данные по любому, так как без них разобраться в работе модуля очень сложно
require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");


// обрабатываем переменные по публичным файлам
if ($install_public == "Y")
{
	$public_dir = Trim($public_dir);
	$public_rewrite = (($public_rewrite == "Y") ? "Y" : "N");
	$bReWritePublicFiles = (($public_rewrite == "Y") ? True : False);
}
else
{
	$public_dir = "";
	$public_rewrite = "N";
	$bReWritePublicFiles = False;
}

// копируем публичные файлы
if ($install_public == "Y" && !empty($public_dir))
{
	$dbSites = CSite::GetList(($b = ""), ($o = ""), Array("ACTIVE" => "Y", "ID" => CMainPage::GetSiteByHost()));
	while ($site = $dbSites->Fetch())
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ithive.offices/install/public/".$site['LANGUAGE_ID'], $site['ABS_DOC_ROOT'].$site["DIR"].$public_dir, $bReWritePublicFiles, true);
	}
}

// обрабатываем переменные по импорту инфоблока
if ($install_demo_data == "Y")
{
	$demo_data_iblock_type = Trim($demo_data_iblock_type);
	$demo_data_rewrite = (($demo_data_rewrite == "N") ? False : True);
}
else
{
	$demo_data_iblock_type = "";
	$demo_data_rewrite = False;
}

// если нет инфоблоков - создаем новый
if($install_demo_data == "Y" && !empty($install_demo_data_new_iblock_type))
{
	$arFields = Array(
		'ID'=>$install_demo_data_new_iblock_type,
		'SECTIONS'=>'N',
		'IN_RSS'=>'N',
		'SORT'=>500,
		'LANG'=>Array(
			'ru'=>Array(
				'NAME'=>'Компания',
				'SECTION_NAME'=>'Разделы',
				'ELEMENT_NAME'=>'Элементы'
				)
			)
		);
	$obBlocktype = new CIBlockType;

	$DB->StartTransaction();

	$res = $obBlocktype->Add($arFields);

	if(!$res)
	{
		$DB->Rollback();
		echo 'Ошибка: '.$obBlocktype->LAST_ERROR.'<br>';
	}
	else
	{
		$DB->Commit();	
		$demo_data_iblock_type = $install_demo_data_new_iblock_type;
	}
}

// импортируем демо данные
if($install_demo_data == "Y" && !empty($demo_data_iblock_type))
{
	if(!CModule::IncludeModule("iblock"))
		return;
	$iblockXMLFile = "/bitrix/modules/ithive.offices/install/xml/offices.xml"; 
	$iblockCode = "offices_".LANGUAGE_ID; 
	$iblockType = $demo_data_iblock_type; 
	$iblockID = false; 
	
	// если выбрано затереть ранее импортированные данные и мы их находим - затираем
	$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode));
	if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
	{
		$iblockID = $arIBlock["ID"]; 
		$iblockFound = true;
		if ($demo_data_rewrite)
		{
			CIBlock::Delete($arIBlock["ID"]); 
			echo CAdminMessage::ShowNote("Предыдущие демо данные обновлены");
			$iblockID = false; 
		}
	}
} 

if (!$iblockID || $demo_data_rewrite)
{
	$iblock_import_success = ImportXMLFile($iblockXMLFile, $iblockType, CMainPage::GetSiteByHost(), "N", "N");

	if (!$iblock_import_success)
	{
		echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>"Демо данные не импортированы!", "DETAILS"=>"install_public: ".$install_public."<br />install_demo_data: ".$install_demo_data."<br />demo_data_iblock_type: ".$demo_data_iblock_type."<br />iblockXMLFile: ".$iblockXMLFile."<br />iblockType: ".$iblockType, "HTML"=>true));
	}
	else 
	{
		$rsIBlock = CIBlock::GetList(array(), array("CODE" => $iblockCode, "TYPE" => $iblockType));
		if ($rsIBlock && $arIBlock = $rsIBlock->Fetch())
		{
			$iblockID = $arIBlock["ID"]; 
			$dbSites = CSite::GetList(($b = ""), ($o = ""), Array("ACTIVE" => "Y", "ID" => CMainPage::GetSiteByHost()));
			while ($site = $dbSites->Fetch())
			{
				$filename = $site['ABS_DOC_ROOT'].$site["DIR"].$public_dir."/index.php";
				$MAP_KEY = trim(strip_tags($yandex_map_api_key));
				if (is_writable($filename)) 
				{
					$fp = fopen($filename, "rb");
					if(!$fp) return;
					$contents = fread($fp, filesize($filename));
					//echo "Исходный файл: ".$filename."<br />Контент: <pre>".htmlspecialchars($contents)."</pre>";
					if($install_public)
					{
						$pattern[] = "/\"IBLOCK_TYPE\" => \"(\w+)\"/i";
						$replacement[] = "\"IBLOCK_TYPE\" => \"".$iblockType."\"";
						$pattern[] = "/\"IBLOCK_ID\" => \"(\d+)\"/i";
						$replacement[] = "\"IBLOCK_ID\" => \"".$iblockID."\"";
					}
					if($MAP_KEY)
					{
						$pattern[] = "/\"KEY\" => \"(.{0,88})\"/i";
						$replacement[] = "\"KEY\" => \"".$MAP_KEY."\"";
					}
					if($demo_data_rewrite || $MAP_KEY)
					{
						$contents = preg_replace($pattern, $replacement, $contents);
					}
					$d_d_r=($demo_data_rewrite)?"Y":"N";
					//echo "MAP_KEY: ".$MAP_KEY."<br />Измененный контент: <pre>".htmlspecialchars($contents)."</pre>";
					fclose($fp);
					if($demo_data_rewrite || $MAP_KEY)
					{
						$fp = fopen($filename, "wb");
						fwrite($fp, $contents);
						fclose($fp);
					}
				}
			}
		}
	}
}

if(strlen($errors)<=0):
	echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
else:
	for($i=0; $i<count($errors); $i++)
		$alErrors .= $errors[$i]."<br>";
	echo CAdminMessage::ShowMessage(Array("TYPE"=>"ERROR", "MESSAGE" =>GetMessage("MOD_INST_ERR"), "DETAILS"=>$alErrors, "HTML"=>true));
endif;
if ($ex = $APPLICATION->GetException())
{
	echo CAdminMessage::ShowMessage(Array("TYPE" => "ERROR", "MESSAGE" => GetMessage("MOD_INST_ERR"), "HTML" => true, "DETAILS" => $ex->GetString()));
}

if (strlen($public_dir)>0) :
?>
<p><?=GetMessage("MOD_DEMO_DIR")?></p>
<table border="0" cellspacing="0" cellpadding="3">
	<tr>
		<td align="center"><p><b><?=GetMessage("MOD_DEMO_SITE")?></b></p></td>
		<td align="center"><p><b><?=GetMessage("MOD_DEMO_LINK")?></b></p></td>
	</tr>
	<?
	$dbSites = CSite::GetList(($b = ""), ($o = ""), Array("ACTIVE" => "Y", "ID" => CMainPage::GetSiteByHost()));
	while ($site = $dbSites->Fetch())
	{
		?>
		<tr>
			<td width="0%"><p>[<?=$site["ID"]?>] <?=$site["NAME"]?></p></td>
			<td width="0%"><p><a href="<?if(strlen($site["SERVER_NAME"])>0) echo "http://".$site["SERVER_NAME"];?><?=$site["DIR"].$public_dir?>/" target="_blank"><?=$site["DIR"].$public_dir?>/</a></p></td>
		</tr>
		<?
	}
	?>
</table>
<?
endif;
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">	
<form>