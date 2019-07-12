<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/wizard.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/wizard_sol/utils.php");
global $DB;
$updater=$DB;
if(!CModule::IncludeModule("iblock"))
	return;
	
$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
fwrite($f, 'end start'."\r\n");
fclose($f);
function SetBUserOption($settings, $IBLOCK_CODE)
{
	global $DB;
	$updater=$DB;
	//свойства беджетстора
	$devPropIds=unserialize('a:69:{i:116;s:22:"CONDITIONS_OF_PURCHASE";i:115;s:10:"MORE_PHOTO";i:114;s:8:"ELEMENTS";i:113;s:11:"IS_APPROVED";i:112;s:2:"TO";i:111;s:4:"FROM";i:110;s:4:"DOCS";i:109;s:7:"IS_DOCS";i:108;s:8:"DATE_PAY";i:107;s:9:"NUMBER_PP";i:106;s:8:"IS_PAYED";i:105;s:11:"IS_APPROVED";i:104;s:8:"CURRENCY";i:103;s:3:"SUM";i:102;s:4:"FILE";i:101;s:4:"DATE";i:100;s:6:"NUMBER";i:99;s:4:"NAME";i:98;s:4:"DATE";i:97;s:11:"IS_REPORTED";i:96;s:11:"IS_APPROVED";i:95;s:3:"SUM";i:94;s:8:"APPROVED";i:93;s:18:"DOCUMENTS_RECEIVED";i:92;s:9:"DOCUMENTS";i:91;s:2:"TO";i:90;s:4:"FROM";i:89;s:5:"WHERE";i:88;s:4:"FILE";i:87;s:4:"DATE";i:86;s:3:"NUM";i:57;s:5:"GROUP";i:56;s:8:"LINK_ALT";i:55;s:11:"LINK_TARGET";i:54;s:11:"BANNER_TYPE";i:53;s:4:"LINK";i:52;s:6:"rating";i:51;s:8:"vote_sum";i:50;s:10:"vote_count";i:49;s:15:"CHARACTERISTICS";i:48;s:4:"link";i:47;s:5:"BRAND";i:45;s:14:"SALE_RECOMMEND";i:44;s:4:"link";i:43;s:17:"BLOG_COMMENTS_CNT";i:42;s:12:"BLOG_POST_ID";i:32;s:10:"MORE_PHOTO";i:31;s:9:"COLOR_REF";i:30;s:13:"SIZES_CLOTHES";i:29;s:11:"SIZES_SHOES";i:28;s:9:"ARTNUMBER";i:27;s:9:"CML2_LINK";i:17;s:9:"BRAND_REF";i:16;s:13:"MAXIMUM_PRICE";i:15;s:13:"MINIMUM_PRICE";i:14;s:17:"FORUM_MESSAGE_CNT";i:13;s:14:"FORUM_TOPIC_ID";i:12;s:9:"RECOMMEND";i:11;s:10:"MORE_PHOTO";i:10;s:5:"COLOR";i:9;s:8:"MATERIAL";i:8;s:12:"MANUFACTURER";i:7;s:9:"ARTNUMBER";i:6;s:12:"SPECIALOFFER";i:5;s:10:"SALELEADER";i:4;s:10:"NEWPRODUCT";i:3;s:16:"META_DESCRIPTION";i:2;s:8:"KEYWORDS";i:1;s:5:"TITLE";}');
	
	$properties = CIBlockProperty::GetList(Array("ID"=>"DESC", "name"=>"asc"), Array("ACTIVE"=>"Y", 'IBLOCK_CODE'=>$IBLOCK_CODE));
	while ($prop_fields = $properties->GetNext())
	{
		$arCatalogProps[ $prop_fields["CODE"] ] = $prop_fields["ID"];
	}
	
	$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
	fwrite($f,'27 '.json_encode($arCatalogProps)."\r\n");
	fclose($f);
	//print_r($arCatalogProps);
	
	$convertIds=array();
	foreach($devPropIds as $k=>$c)
	{
		if($arCatalogProps[$c])
		{
			$convertIds[$k]=$arCatalogProps[$c];
		}
	}
	
	$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
	fwrite($f,'41 '.json_encode($convertIds)."\r\n");
	fclose($f);
	//$f1tovarSettings=explode(",", $tovarSettings);
	////print_r($f1tovarSettings);
	//foreach($f1tovarSettings as $el)
	//{
	//	if(strpos($el, '-PROPERTY_')!==false)
	//	{
	//		$elMass=explode("-PROPERTY_", $el);
	//		$elMass2=explode("--#--", $elMass[1]);
	//		$props_only[$elMass2[0]]=$el;
	//	}
	//}
	//print_r($props_only);
	
	//foreach($props_only as $k=>$v)
	//{
	//	$oldStr=$v;
	//	$newStr=str_replace('PROPERTY_'.$k.'-','PROPERTY_'.$convertIds[$k].'-', $v);
	//	//echo $oldStr.' => '.$newStr."\n";
	//	$tovarSettings=str_replace($oldStr , $newStr , $tovarSettings);		
	//}	
	///////////////////////////////////////////////
	///////////////////////////////////////////////
	///////////////////////////////////////////////
	$f1looksSettings=explode(",", $settings);
	//print_r($f1looksSettings);
	foreach($f1looksSettings as $el)
	{
		if(strpos($el, '-PROPERTY_')!==false)
		{
			$elMass=explode("-PROPERTY_", $el);
			$elMass2=explode("--#--", $elMass[1]);
			$propslooks_only[$elMass2[0]]=$el;
		}
	}
	//print_r($propslooks_only);
	
	foreach($propslooks_only as $k=>$v)
	{
		$oldStr=$v;
		$newStr=str_replace('PROPERTY_'.$k.'-','PROPERTY_'.$convertIds[$k].'-', $v);
		//echo $oldStr.' => '.$newStr."\n";
		$settings=str_replace($oldStr , $newStr , $settings);		
	}
	
	$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
	fwrite($f,'88 '.json_encode($settings)."\r\n");
	fclose($f);
	//print_r($settings);
	
	
	$SettingsSerialize=serialize(array('tabs' => $settings ));
	
	$rsLooks = CIBlock::GetList(array(),array("CODE" => $IBLOCK_CODE));
	
	if($arLooks= $rsLooks->Fetch())
	{
		$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
		fwrite($f,'100 '.json_encode($arLooks)."\r\n");
		fclose($f);
	
		$strFormValue = $SettingsSerialize;
		
		$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
		fwrite($f,'106 '.json_encode($SettingsSerialize)."\r\n");
		fclose($f);
		
		
		if($strFormValue && $arLooks["ID"])
		{
			$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
			fwrite($f,'113 '.json_encode($arLooks)."\r\n");
			fclose($f);
			
			$strSql = 'SELECT `VALUE` FROM `b_user_option` WHERE `NAME` = "form_element_'.$arLooks["ID"].'"';
			$results = $updater->Query($strSql);
			if ($row = $results -> Fetch()) {
				$value = unserialize($row["VALUE"]);
				if($value["tabs"] != $strFormValue){
					$strSql = "UPDATE `b_user_option` SET `VALUE` = '".$strFormValue."' WHERE `NAME` = 'form_element_".$arLooks["ID"]."'";
					$results = $updater->Query($strSql);
				}
			}else{
				$strSql = "INSERT INTO `b_user_option` (`USER_ID`,`CATEGORY`,`NAME`,`VALUE`,`COMMON`) VALUES (1,'form','form_element_".$arLooks["ID"]."','".$strFormValue."','N')";
				$results = $updater->Query($strSql);
			}
		}
	}	
}
	
	
	
	$devPropIds=unserialize('a:69:{i:116;s:22:"CONDITIONS_OF_PURCHASE";i:115;s:10:"MORE_PHOTO";i:114;s:8:"ELEMENTS";i:113;s:11:"IS_APPROVED";i:112;s:2:"TO";i:111;s:4:"FROM";i:110;s:4:"DOCS";i:109;s:7:"IS_DOCS";i:108;s:8:"DATE_PAY";i:107;s:9:"NUMBER_PP";i:106;s:8:"IS_PAYED";i:105;s:11:"IS_APPROVED";i:104;s:8:"CURRENCY";i:103;s:3:"SUM";i:102;s:4:"FILE";i:101;s:4:"DATE";i:100;s:6:"NUMBER";i:99;s:4:"NAME";i:98;s:4:"DATE";i:97;s:11:"IS_REPORTED";i:96;s:11:"IS_APPROVED";i:95;s:3:"SUM";i:94;s:8:"APPROVED";i:93;s:18:"DOCUMENTS_RECEIVED";i:92;s:9:"DOCUMENTS";i:91;s:2:"TO";i:90;s:4:"FROM";i:89;s:5:"WHERE";i:88;s:4:"FILE";i:87;s:4:"DATE";i:86;s:3:"NUM";i:57;s:5:"GROUP";i:56;s:8:"LINK_ALT";i:55;s:11:"LINK_TARGET";i:54;s:11:"BANNER_TYPE";i:53;s:4:"LINK";i:52;s:6:"rating";i:51;s:8:"vote_sum";i:50;s:10:"vote_count";i:49;s:15:"CHARACTERISTICS";i:48;s:4:"link";i:47;s:5:"BRAND";i:45;s:14:"SALE_RECOMMEND";i:44;s:4:"link";i:43;s:17:"BLOG_COMMENTS_CNT";i:42;s:12:"BLOG_POST_ID";i:32;s:10:"MORE_PHOTO";i:31;s:9:"COLOR_REF";i:30;s:13:"SIZES_CLOTHES";i:29;s:11:"SIZES_SHOES";i:28;s:9:"ARTNUMBER";i:27;s:9:"CML2_LINK";i:17;s:9:"BRAND_REF";i:16;s:13:"MAXIMUM_PRICE";i:15;s:13:"MINIMUM_PRICE";i:14;s:17:"FORUM_MESSAGE_CNT";i:13;s:14:"FORUM_TOPIC_ID";i:12;s:9:"RECOMMEND";i:11;s:10:"MORE_PHOTO";i:10;s:5:"COLOR";i:9;s:8:"MATERIAL";i:8;s:12:"MANUFACTURER";i:7;s:9:"ARTNUMBER";i:6;s:12:"SPECIALOFFER";i:5;s:10:"SALELEADER";i:4;s:10:"NEWPRODUCT";i:3;s:16:"META_DESCRIPTION";i:2;s:8:"KEYWORDS";i:1;s:5:"TITLE";}');
	//$looksSettings='edit1--#--Элемент--,--ACTIVE--#--Активность--,--ACTIVE_FROM--#--Начало активности--,--ACTIVE_TO--#--Окончание активности--,--NAME--#--*Название--,--CODE--#--Символьный код--,--PREVIEW_PICTURE--#--Картинка для анонса--,--DETAIL_PICTURE--#--Детальная картинка--,--PROPERTY_115--#--Фотографии--,--DETAIL_TEXT--#--Детальное описание--,--PROPERTY_116--#--Условия покупки--;--cedit1--#--Состав лука--,--PROPERTY_114--#--Товары лука--;--edit14--#--SEO--,--IPROPERTY_TEMPLATES_ELEMENT_META_TITLE--#--Шаблон META TITLE--,--IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS--#--Шаблон META KEYWORDS--,--IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION--#--Шаблон META DESCRIPTION--,--IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE--#--Заголовок элемента--,--IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE--#----Настройки для картинок анонса элементов--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT--#--Шаблон ALT--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE--#--Шаблон TITLE--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME--#--Шаблон имени файла--,--IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE--#----Настройки для детальных картинок элементов--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT--#--Шаблон ALT--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE--#--Шаблон TITLE--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME--#--Шаблон имени файла--,--SEO_ADDITIONAL--#----Дополнительно--,--TAGS--#--Теги--;--edit2--#--Разделы--,--SORT--#--Сортировка--,--SECTIONS--#--Разделы--;--';
	//$tovarSettings='edit1--#--Товар--,--edit1_csection1--#----Активноcть--,--ACTIVE--#--Активность--,--ACTIVE_FROM--#--Начало активности--,--ACTIVE_TO--#--Окончание активности--,--edit1_csection2--#----Название--,--NAME--#--*Название--,--CODE--#--*Символьный код--,--edit1_csection3--#----Свойства товара--,--PROPERTY_7--#--Артикул--,--PROPERTY_8--#--Страна производитель--,--PROPERTY_47--#--Бренд--,--PROPERTY_9--#--Материалы--,--PROPERTY_10--#--Цвет--,--PROPERTY_49--#--Описание характеристик--,--edit1_csection6--#----Галерея--,--PROPERTY_11--#--Галерея--,--edit1_csection4--#----Анонс товара--,--PREVIEW_PICTURE--#--Картинка для анонса--,--PREVIEW_TEXT--#--Описание для анонса--,--edit1_csection5--#----Подробное описание товара--,--DETAIL_PICTURE--#--Детальная картинка--,--DETAIL_TEXT--#--Детальное описание--;--cedit1--#--Отметки--,--PROPERTY_4--#--Новинка--,--PROPERTY_5--#--Хит продаж--,--PROPERTY_45--#--Рекомендуем--,--PROPERTY_17--#--Бонусы--;--cedit4--#--Рекомендации--,--PROPERTY_12--#--С этим товаром рекомендуем--,--LINKED_PROP--#--Связанные элементы--;--cedit2--#--Акции--,--PROPERTY_6--#--Акция--;--edit2--#--Порядок размещения--,--SORT--#--Сортировка--,--SECTIONS--#--*Разделы--;--edit14--#--SEO--,--IPROPERTY_TEMPLATES_ELEMENT_META_TITLE--#--Шаблон META TITLE--,--IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS--#--Шаблон META KEYWORDS--,--IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION--#--Шаблон META DESCRIPTION--,--IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE--#--Заголовок товара--,--IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE--#----Настройки для картинок анонса элементов--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT--#--Шаблон ALT--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE--#--Шаблон TITLE--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME--#--Шаблон имени файла--,--IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE--#----Настройки для детальных картинок элементов--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT--#--Шаблон ALT--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE--#--Шаблон TITLE--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME--#--Шаблон имени файла--,--SEO_ADDITIONAL--#----Дополнительно--,--TAGS--#--Теги--;--cedit3--#--Служебные--,--PROPERTY_42--#--ID поста блога для комментариев--,--PROPERTY_43--#--Количество комментариев--,--PROPERTY_14--#--Количество комментариев к элементу--,--PROPERTY_50--#--Количество проголосовавших--,--PROPERTY_52--#--Рейтинг--;--edit10--#--Торговый каталог--,--CATALOG--#--*Торговый каталог--;--';
	
	$looksSettings=GetMessage("WZD_LOOKS_OPTIONS");
	$tovarSettings=GetMessage("WZD_CATALOG_OPTIONS");
	$bannerSettings=GetMessage("WZD_BANNER_OPTIONS");
	$journalSettings=GetMessage("WZD_JOURNAL_OPTIONS");
	$campaignSettings=GetMessage("WZD_CAMPAIGN_OPTIONS");
	$newsSettings=GetMessage("WZD_NEWS_OPTIONS");

	SetBUserOption($looksSettings, 'LOOKS');
	SetBUserOption($tovarSettings, 'clothes');
	SetBUserOption($bannerSettings, 'banner');
	SetBUserOption($journalSettings, 'journal');
	SetBUserOption($campaignSettings, 'campaign');
	SetBUserOption($newsSettings, 'news');
	
if(CModule::IncludeModule("iblock"))
{
	$sortTypes=array();
	$sortTypes['10']  ='catalog';
	$sortTypes['20']  ='offers';
	$sortTypes['30']  ='looks';
	$sortTypes['40']  ='campaign';
	$sortTypes['50']  ='brands';
	$sortTypes['60']  ='bj_articles';
	$sortTypes['70']  ='news';
	$sortTypes['80']  ='bj_vacancies';
	$sortTypes['100'] ='banners';
	$sortTypes['110'] ='references';
	$sortTypes['120'] ='menu';
	$sortTypes['300'] ='services';
	$sortTypes['500'] ='bitrix_processes';
	$obBlocktype = new CIBlockType;
	global $DB;
	foreach($sortTypes as $k=>$v)
	{
		$arFields['SORT']=$k;
		$arFields['SECTIONS']='Y';
		
		$DB->StartTransaction();
		$res = $obBlocktype->Update($v, $arFields);
		if(!$res)
		{
		   $DB->Rollback();
		   echo 'Error: '.$obBlocktype->LAST_ERROR.'<br>';
		}
		else
		{
			$DB->Commit();
		}
	}
	
	
	$arNewName = Array(
    'LANG'=>Array(
        'ru'=>Array(
            'NAME'=>GetMessage("JOURNAL_NAME"),
            'SECTION_NAME'=>GetMessage("JOURNAL_SECTION_NAME"),
            'ELEMENT_NAME'=>GetMessage("JOURNAL_ELEMENT_NAME")
            )
        )
    );
	$obBlocktype->Update('bj_articles', $arNewName);
	
	$rsLooks = CIBlock::GetList(array(),array("CODE" => 'LOOKS'));
	if($arLooks= $rsLooks->Fetch())
	{
		if($arLooks["ID"])
		{
			$fields = CIBlock::getFields($arLooks["ID"]);
			$fields["CODE"]["IS_REQUIRED"] = "Y";
			$fields["CODE"]["DEFAULT_VALUE"]["UNIQUE"] = "Y";//Если код задан, то проверять на уникальность
			$fields["CODE"]["DEFAULT_VALUE"]["TRANSLITERATION"] = "Y";//Транслитерировать из названия при добавлении элемента
			CIBlock::setFields($arLooks["ID"], $fields);
		}
	}
	
}
	
if(file_exists($_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/index.php"))
{
	$intexText=file_get_contents(WIZARD_SITE_PATH."index.php");
	$intexText=str_replace('"SEF_MODE" => "Y"', '"SEF_MODE" => "N"', $intexText);
	$f=fopen(WIZARD_SITE_PATH."index.php", 'w');
	fwrite($f, $intexText);
	fclose($f);
	
}	
if(file_exists(WIZARD_SITE_PATH."index.php"))
{
	$intexText=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/index.php");
	$intexText=str_replace('"SEF_MODE" => "Y"', '"SEF_MODE" => "N"', $intexText);
	$f=fopen(WIZARD_SITE_PATH."index.php", 'w');
	fwrite($f, $intexText);
	fclose($f);
	
}
	
			////создание файла с макросами
			if(CModule::IncludeModule("iblock"))
			{
				$res = CIBlock::GetList(Array(), Array('SITE_ID'=>$site_id), true);
				$init_text='';
				while($ar_res = $res->Fetch())
				{
					//echo $ar_res['NAME'].': '.$ar_res['CODE'].': '.$ar_res['ID']."\r\n";
					$def='BEJET_SELLER_'.strtoupper(str_replace('bj_', '', $ar_res['CODE']));
					//echo $def.' '.$ar_res['ID']."\r\n";
					$iblockMacrosIds[$def]=$ar_res['ID'];
					if($def=='BEJET_SELLER_CLOTHES_OFFERS')
					{
						$init_text.='define("BEJET_SELLER_OFFERS_CLOTHES", "'.$ar_res['ID'].'");'."\n";
					}
					$init_text.='define("'.$def.'", "'.$ar_res['ID'].'");'."\n";
				}
				//echo $init_text;
			}

			if($init_text)
			{
				$init_file_path=$_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/".$site_id."/init.php";
				//echo $init_file_path;
				if(file_exists($init_file_path))
				{
					$connt=file_get_contents($init_file_path);
					//if(strpos($connt, 'BEJET_SELLER_CLOTHES')==false)
					//{
						$f=fopen($init_file_path, 'w');
						fwrite($f, trim($connt)."<?\r\n".$init_text."\r\n?>");
						fclose($f);
					//}
				}
				else
				{
					if(!file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/".$site_id))//
					{
						mkdir($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/".$site_id, 0755);
					}
					$f=fopen($init_file_path, 'w');
					fwrite($f, "<?\r\n".$init_text."\r\n?>");
					fclose($f);
				}
			}
			
				$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
				$logStr=__FILE__." ".date("d.m.y H:i:s")."\n";
				$logStr.="php_interface ".$site_id." - ".$init_text."\n\n";
				fwrite($f, $logStr);
				fclose($f);
			////создание файла с макросами
			
			////Здесь работа с публичными файлами сайта
			$needReplaseFiles[]='bitrix/components/bejetstore/catalog/templates/.default/tabs.php';
			$needReplaseFiles[]='brand/detail.php';
			$needReplaseFiles[]='brand/index.php';
			$needReplaseFiles[]='campaign/detail.php';
			$needReplaseFiles[]='campaign/index.php';
			$needReplaseFiles[]='catalog/index.php';
			$needReplaseFiles[]='index.php';
			$needReplaseFiles[]='_index.php';
			$needReplaseFiles[]='journal/index.php';
			$needReplaseFiles[]='lookbook/detail.php';
			$needReplaseFiles[]='lookbook/index.php';
			$needReplaseFiles[]='lookbook/section.php';
			foreach($needReplaseFiles as $needReplaseFile)
			{
			
				$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
				$logStr=__FILE__." ".date("d.m.y H:i:s")."\n";
				$logStr.="ReplaceMacros ".$_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile." ".$iblockMacrosIds['BEJET_SELLER_CLOTHES']."\n\n";
				fwrite($f, $logStr);
				fclose($f);
				
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile))
				{	
					$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
					$logStr=__FILE__." ".date("d.m.y H:i:s")."\n";
					$logStr.="FILE OK ".$_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile.": ".$iblockMacrosIds['BEJET_SELLER_CLOTHES'].' - '.$iblockMacrosIds['BEJET_SELLER_CLOTHES_OFFERS']."\n\n";
					fwrite($f, $logStr);
					fclose($f);
				}
				else
				{
					$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
					$logStr=__FILE__." ".date("d.m.y H:i:s")."\n";
					$logStr.="NOT FILE ".$_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile.": ".$iblockMacrosIds['BEJET_SELLER_CLOTHES'].' - '.$iblockMacrosIds['BEJET_SELLER_CLOTHES_OFFERS']."\n\n";
					fwrite($f, $logStr);
					fclose($f);
				}
				
				
				if(file_exists($_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile))
				{
				
					$f=fopen($_SERVER["DOCUMENT_ROOT"].'/setup-log.txt', 'a+');
					$logStr=__FILE__." ".date("d.m.y H:i:s")."\n";
					$logStr.="Replace macros: ".$_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile.": ".$iblockMacrosIds['BEJET_SELLER_CLOTHES'].' - '.$iblockMacrosIds['BEJET_SELLER_CLOTHES_OFFERS']."\n\n";
					fwrite($f, $logStr);
					fclose($f);
					
					
					$fileCon=file_get_contents($_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile);
					$fileCon=str_replace('#BEJET_SELLER_CLOTHES#', $iblockMacrosIds['BEJET_SELLER_CLOTHES'], $fileCon);
					$fileCon=str_replace('#BEJET_SELLER_OFFERS_CLOTHES#',$iblockMacrosIds['BEJET_SELLER_CLOTHES_OFFERS'], $fileCon);
					
					$f=fopen($_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile, 'w');
					fwrite($f, $fileCon);
					fclose($f);
					
					//CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile, array("BEJET_SELLER_CLOTHES" => $iblockMacrosIds['BEJET_SELLER_CLOTHES']));
					//CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$arSiteDir[ $site_id ]."/".$needReplaseFile, array( "BEJET_SELLER_OFFERS_CLOTHES" => $iblockMacrosIds['BEJET_SELLER_CLOTHES_OFFERS']));
				}
			}
	
//$f=fopen($_SERVER["DOCUMENT_ROOT"]."/setup-log.txt", 'a+');
//fwrite($f, 'add.php'."\r\n");
//fclose($f);
?>