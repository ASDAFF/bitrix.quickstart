<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}tt_products";

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
	$query = "SELECT * FROM {$arResult['prefix']}tt_products LIMIT {$left}, 10";
	$result = mysql_query($query, $link);
	
	
	while($arItem = mysql_fetch_assoc($result))
	{
	
	$ib = CIBlock::GetList(array(), array("CODE"=>"typo3_tt_products"))->GetNext();

	$usr = CUser::GetByLogin($arItem['autor'])->GetNext();
	
	$el = new CIBlockElement;		
	$arLoadProductArray = Array(		  			
			"IBLOCK_ID"      => $ib['ID'],		
			
			"NAME"           => $arItem['title'],
			"ACTIVE"         => "Y",            // активен		  
			"DETAIL_TEXT"    => $arItem['note'],		
			"DETAIL_TEXT_TYPE" => 'html',
			"PREVIEW_TEXT"    => $arItem['note2'],		
			"PREVIEW_TEXT_TYPE" => 'html',
			"XML_ID" => $arItem['uid'],
			"DATE_CREATE" => $arItem['crdate'],
	);

	$path = $arResult["site"].'/uploads/pics/'.$arItem['image'];
	$path = str_replace('www.','',$path);
	if(!substr_count($path, 'http://')) $path = 'http://'.$path;						
	$photo = array("VALUE" =>CFile::MakeFileArray($path));
	

	$arLoadProductArray['DETAIL_PICTURE'] = $photo['VALUE'];
	$arLoadProductArray['PREVIEW_PICTURE'] = $photo['VALUE'];
	
	
	
	if(!$e)
	{		
		$PRODUCT_ID = $el->Add($arLoadProductArray);
	}
	else $PRODUCT_ID = $e['ID'];
	
	$sec = array();
	$r = CIBlockSection::GetList(array(), array('IBLOCK_ID' =>$ib['ID'], 'XML_ID' => $arItem['category']))->GetNext();
	$sec[] = $r['ID'];
	
	
	if(is_array($sec))
		CIBlockElement::SetElementSection($PRODUCT_ID, $sec);
	
	
	
	if($arItem['directcost'])
	{
		$arFields = Array(
			"PRODUCT_ID" => $PRODUCT_ID,
			"CATALOG_GROUP_ID" => 1,
			"PRICE" => $arItem['directcost'],
			"CURRENCY" => "RUB"
		);

		$res = CPrice::GetList( array(), array( "PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => 1 ));
		
		if ($arr = $res->Fetch()){ CPrice::Update($arr["ID"], $arFields);}
		else	{ CPrice::Add($arFields); }
		
	}
	
	if($arItem['price'])
	{
		$arFields = Array(
			"PRODUCT_ID" => $PRODUCT_ID,
			"CATALOG_GROUP_ID" => 2,
			"PRICE" => $arItem['price'],
			"CURRENCY" => "RUB"
		);

		$res = CPrice::GetList( array(), array( "PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => 2 ));
		
		if ($arr = $res->Fetch()){ CPrice::Update($arr["ID"], $arFields);}
		else	{ CPrice::Add($arFields); }
		
	}
	
	if($arItem['price2'])
	{
		$arFields = Array(
			"PRODUCT_ID" => $PRODUCT_ID,
			"CATALOG_GROUP_ID" => 3,
			"PRICE" => $arItem['price2'],
			"CURRENCY" => "RUB"
		);
		$res = CPrice::GetList( array(), array( "PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => 3 ));		
		if ($arr = $res->Fetch()){ CPrice::Update($arr["ID"], $arFields);}
		else	{ CPrice::Add($arFields); }
		
	}
	
	
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'WEIGHT', $arItem['weight']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'COLOR', $arItem['color']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'COLOR2', $arItem['color2']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'COLOR3', $arItem['color3']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'SIZE', $arItem['size']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'SIZE2', $arItem['size2']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'SIZE3', $arItem['size3']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'DESCRIPTION', $arItem['description']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'MATERIAL', $arItem['material']);
	CIBlockElement::SetPropertyValueCode($PRODUCT_ID, 'QUALITY', $arItem['quality']);
	
	
	/*
	$query = "SELECT * FROM {$arResult['prefix']}files WHERE news_id={$arItem['id']}";
	$re = mysql_query($query, $link);
	$file = array();
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
*/
	
	
	
}



	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
