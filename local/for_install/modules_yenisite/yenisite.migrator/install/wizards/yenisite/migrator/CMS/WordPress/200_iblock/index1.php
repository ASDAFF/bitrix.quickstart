<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) AS CNT FROM  {$arResult['prefix']}posts WHERE {$arResult['prefix']}posts.post_parent=0 AND {$arResult['prefix']}posts.post_type='post' ";

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
	$query = "SELECT {$arResult['prefix']}posts.ID, {$arResult['prefix']}posts.post_title, {$arResult['prefix']}posts.post_content FROM  {$arResult['prefix']}posts WHERE {$arResult['prefix']}posts.post_parent=0 AND {$arResult['prefix']}posts.post_type='post' LIMIT ".$left.",  10";
	$result = mysql_query($query, $link);
	
	
	while($arItem = mysql_fetch_assoc($result))
	{
	
	$ib = CIBlock::GetList(array(), array("CODE"=>"wp_rubrics"))->GetNext();
				
	$el = new CIBlockElement;		
	$arLoadProductArray = Array(		  
			//"IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
			"IBLOCK_ID"      => $ib['ID'],		  
			"NAME"           => $arItem['post_title'],
			"ACTIVE"         => "Y",            // активен		  
			"DETAIL_TEXT"    => $arItem['post_content'],		
			"DETAIL_TEXT_TYPE" => 'html',
			"XML_ID" => $arItem['ID']
	);
	//
	

	

	$query = "SELECT {$arResult['prefix']}term_taxonomy.term_id FROM {$arResult['prefix']}term_relationships, {$arResult['prefix']}term_taxonomy WHERE {$arResult['prefix']}term_relationships.object_id={$arItem['ID']} AND {$arResult['prefix']}term_taxonomy.term_taxonomy_id={$arResult['prefix']}term_relationships.term_taxonomy_id AND {$arResult['prefix']}term_taxonomy.taxonomy='category' ";

	$result1 = mysql_query($query, $link);	
	
	$arSect = array();
	while($arI = mysql_fetch_assoc($result1))
	{
		$sec = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'XML_ID' => $arI['term_id']))->GetNext();		
		$arSect[] = $sec['ID'];		
	}
	
	
	
	
	$e = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'XML_ID' => $arItem['ID']))->GetNext();
	
	if(!$e)
	{		
		$PRODUCT_ID = $el->Add($arLoadProductArray);
	}
	else $PRODUCT_ID = $e['ID'];
	
	
	$el->SetElementSection($PRODUCT_ID, $arSect);		
	//CPrice::SetBasePrice($PRODUCT_ID, $arItem['meta_value']);
}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
