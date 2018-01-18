<?
/* 1.0.10  */
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}post";

$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);



/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

if($left > $count["CNT"])
{	
	
	$left = 0;
	$right = 10;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{
	$query = "SELECT * FROM {$arResult['prefix']}post LIMIT {$left}, 10";
	$result = mysql_query($query, $link);
	
	
	while($arItem = mysql_fetch_assoc($result))
	{
	
	
	
	$ib = CIBlock::GetList(array(), array("CODE"=>"dle_categories"))->GetNext();

	$usr = CUser::GetByLogin($arItem['autor'])->GetNext();
	
	$secs = explode(',' , $arItem['category']);
	
	$categories = array();
	
	foreach($secs as $s)
	{
		$sec = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'XML_ID' => $s))->GetNext();
		$categories[] = $sec['ID'];
	}
	
	$el = new CIBlockElement;		
	$arLoadProductArray = Array(		  			
			"IBLOCK_ID"      => $ib['ID'],		  
			"CODE" => $arItem['alt_name'],
			"NAME"           => $arItem['title'],
			"ACTIVE"         => "Y",            // активен		  
			"DETAIL_TEXT"    => $arItem['full_story'],		
			"DETAIL_TEXT_TYPE" => 'html',
			"PREVIEW_TEXT"    => $arItem['short_story'],		
			"PREVIEW_TEXT_TYPE" => 'html',
			"XML_ID" => $arItem['id'],
			"DATE_CREATE" => $arItem['date'],
			"CREATED_BY" => $usr['ID'],
			//"IBLOCK_SECTION_ID" => $sec['ID']
	);
	
	
	
	$query = "SELECT * FROM {$arResult['prefix']}images WHERE news_id={$arItem['id']}";
	$re = mysql_query($query, $link);
	$photo = array();
	while($arPhoto = mysql_fetch_assoc($re))
	{
				    $photos = explode('|||', $arPhoto['images']);
					
					foreach($photos as $ph)
					{
						$path = $arResult["site"].'/uploads/posts/'.$ph;
						$path = str_replace('www.','',$path);
						if(!substr_count($path, 'http://')) $path = 'http://'.$path;						
						$photo[] = array("VALUE" =>CFile::MakeFileArray($path));
					}
	}
	
	
	$arLoadProductArray['DETAIL_PICTURE'] = $photo[0]['VALUE'];
	$arLoadProductArray['PREVIEW_PICTURE'] = $photo[0]['VALUE'];
	
	
	$e = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'XML_ID' => $arItem['id']))->GetNext();
	
	if(!$e)
	{		
		$PRODUCT_ID = $el->Add($arLoadProductArray);
	}
	else $PRODUCT_ID = $e['ID'];
	
	CIBlockElement::SetElementSection($PRODUCT_ID, $categories);
	
	$query = "SELECT * FROM {$arResult['prefix']}files WHERE news_id={$arItem['id']}";
	$re = mysql_query($query, $link);
	$file = array();
	while($arFile = mysql_fetch_assoc($re))
	{
				   
						$path = $arResult["site"].'/uploads/files/'.$arFile['onserver'];
						$path = str_replace('www.','',$path);
						if(!substr_count($path, 'http://')) $path = 'http://'.$path;						
						$file[] = array("VALUE" =>CFile::MakeFileArray($path));
					
	}
	

	
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'TITLE', $arItem['metatitle']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'DESCRIPTION', $arItem['descr']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'KEYWORDS', $arItem['keywords']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'PHOTO', $photo);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'FILE', $file);
	

	$arr = explode('||', $arItem['xfields']);
	foreach($arr as $prop)
	{
		$prop = explode('|', $prop);
		$p = CIBlockProperty::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'NAME' => $prop[0]))->GetNext();
		switch($p['PROPERTY_TYPE'])
		{
			case 'L':
					$e = CIBlockPropertyEnum::GetList(array(), array('PROPERTY_ID' => $p['ID'], 'VALUE' => $prop[1]))->GetNext();
					CIBlockElement::SetPropertyValues($PRODUCT_ID, $ib['ID'], $e['ID'], $p['ID']);					
				break;
			default:
				if($p['USER_TYPE']=='HTML')
					CIBlockElement::SetPropertyValues($PRODUCT_ID, $ib['ID'], array('VALUE' => array('TEXT' => $prop[1], 'TYPE' => 'html')), $p['ID']);					
				else
					CIBlockElement::SetPropertyValues($PRODUCT_ID, $ib['ID'], $prop[1], $p['ID']);					
				break;
		}
		

	}

	
	
	
}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
