<?




if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$user = new CUser;

$users = array();

/* количество записей */

//SELECT {$arResult['prefix']}postmeta.meta_value, {$arResult['prefix']}postmeta.post_id, {$arResult['prefix']}posts.post_title, {$arResult['prefix']}posts.post_content FROM {$arResult['prefix']}postmeta, {$arResult['prefix']}posts WHERE {$arResult['prefix']}postmeta.meta_key='_eshop_product' AND {$arResult['prefix']}postmeta.post_id = {$arResult['prefix']}posts.ID
$query = "SELECT COUNT(*) as CNT FROM {$arResult['prefix']}postmeta, {$arResult['prefix']}posts WHERE {$arResult['prefix']}postmeta.meta_key='_eshop_product' AND {$arResult['prefix']}postmeta.post_id = {$arResult['prefix']}posts.ID";

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
	$query = "SELECT {$arResult['prefix']}postmeta.meta_value, {$arResult['prefix']}postmeta.post_id, {$arResult['prefix']}posts.post_title, {$arResult['prefix']}posts.post_content FROM {$arResult['prefix']}postmeta, {$arResult['prefix']}posts WHERE {$arResult['prefix']}postmeta.meta_key='_eshop_product' AND {$arResult['prefix']}postmeta.post_id = {$arResult['prefix']}posts.ID LIMIT ".$left.",  10";
	$result = mysql_query($query, $link);


	while($arItem = mysql_fetch_assoc($result))
	{
	
	$ib = CIBlock::GetList(array(), array("CODE"=>"wp_eshop"))->GetNext();
				
	$el = new CIBlockElement;		
	$arLoadProductArray = Array(		  			
			"IBLOCK_ID"      => $ib['ID'],		  
			"NAME"           => $arItem['post_title'],
			"ACTIVE"         => "Y",            // активен		  
			"DETAIL_TEXT"    => $arItem['post_content'],		
			"DETAIL_TEXT_TYPE" => 'html',
			"XML_ID" => $arItem['post_id']
	);
	
	

	

	$query = "SELECT {$arResult['prefix']}term_taxonomy.term_id FROM {$arResult['prefix']}term_relationships, {$arResult['prefix']}term_taxonomy WHERE {$arResult['prefix']}term_relationships.object_id={$arItem['post_id']} AND {$arResult['prefix']}term_taxonomy.term_taxonomy_id={$arResult['prefix']}term_relationships.term_taxonomy_id AND {$arResult['prefix']}term_taxonomy.taxonomy='category' ";

	$result1 = mysql_query($query, $link);	
	
	$arSect = array();
	while($arI = mysql_fetch_assoc($result1))
	{
		$sec = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'XML_ID' => $arI['term_id']))->GetNext();		
		$arSect[] = $sec['ID'];		
	}
	
	
	
	
	$e = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'XML_ID' => $arItem['post_id']))->GetNext();	
	if(!$e)
	{		
		$PRODUCT_ID = $el->Add($arLoadProductArray);
	}
	else $PRODUCT_ID = $e['ID'];
	
	CCatalogProduct::Add(array('ID' => $PRODUCT_ID, 'QUANTITY' => 0));
	
	
	$res = CIBlock::GetList(array(), array("CODE" => "wp_eshop_proposal"))->GetNext();	
	$id_pr = $res["ID"];
	
	
	
	$el->SetElementSection($PRODUCT_ID, $arSect);				
	$params = $arItem['meta_value'];
	if(SITE_CHARSET == 'windows-1251')
		$params = iconv('cp1251','utf-8', $arItem['meta_value']);
	$params = unserialize($params);	
	if(SITE_CHARSET == 'windows-1251')
	{
		$params['description'] =  iconv('utf-8','cp1251', $params['description']);
		$params['sku'] =  iconv('utf-8','cp1251', $params['sku']);
		foreach($params['products'] as &$par)
		{
			$par['option'] = iconv('utf-8','cp1251', $par['option']);		
		}
	}
	
	foreach($params['products'] as &$par)
		{
			if(!$par['option']) continue;			
			$el = new CIBlockElement;		
			$arLoadProductArray = Array(		  			
				"IBLOCK_ID"      => $id_pr,		  
				"NAME"           => $arItem['post_title']."({$par['option']})",
				"ACTIVE"         => "Y",            // активен		  
				"DETAIL_TEXT"    => $arItem['post_content'],		
				"DETAIL_TEXT_TYPE" => 'html',
				"XML_ID" => $arItem['post_id']
			);
			
			$PRODUCT_ID1 = $el->Add($arLoadProductArray);
			CCatalogProduct::Add(array('ID' => $PRODUCT_ID1, 'QUANTITY' => 0));			
			CPrice::SetBasePrice($PRODUCT_ID1, $par['price'], 'RUB');
			CIBlockElement::SetPropertyValueCode($PRODUCT_ID1, "CML2_LINK", $PRODUCT_ID);
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
