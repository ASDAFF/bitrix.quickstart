<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$user = new CUser;

$users = array();

/* количество записей */

//SELECT wp_posts.ID, wp_posts.post_parent, wp_posts.post_title, wp_postmeta.meta_value FROM wp_postmeta, wp_posts WHERE wp_posts.post_parent>0 AND wp_posts.post_type='wpsc-product' AND wp_posts.ID=wp_postmeta.post_id AND wp_postmeta.meta_key='_wpsc_price'

$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}postmeta, {$arResult['prefix']}posts WHERE {$arResult['prefix']}posts.post_parent>0 AND {$arResult['prefix']}posts.post_type='wpsc-product' AND {$arResult['prefix']}posts.ID={$arResult['prefix']}postmeta.post_id AND {$arResult['prefix']}postmeta.meta_key='_wpsc_price'";

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
	$query = "SELECT {$arResult['prefix']}posts.ID, {$arResult['prefix']}posts.post_parent, {$arResult['prefix']}posts.post_title, {$arResult['prefix']}postmeta.meta_value FROM {$arResult['prefix']}postmeta, {$arResult['prefix']}posts WHERE {$arResult['prefix']}posts.post_parent>0 AND {$arResult['prefix']}posts.post_type='wpsc-product' AND {$arResult['prefix']}posts.ID={$arResult['prefix']}postmeta.post_id AND {$arResult['prefix']}postmeta.meta_key='_wpsc_price' LIMIT ".$left.",  10";
	$result = mysql_query($query, $link);
	//print_r($query); die();
	while($arItem = mysql_fetch_assoc($result))
	{
	
	//print_r($arItem);

	
	
	
	
		$ib = CIBlock::GetList(array(), array("CODE"=>"wp_proposal"))->GetNext();
		$ibc = CIBlock::GetList(array(), array("CODE"=>"wp_catalog"))->GetNext();

		
	$e = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $ibc['ID'], 'XML_ID' => $arItem['post_parent']))->GetNext();			
	$el = new CIBlockElement;		
	
	
	
	
	$arLoadProductArray = Array(		  
			//"IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
			"IBLOCK_ID"      => $ib['ID'],		  
			"NAME"           => $arItem['post_title'],
			"ACTIVE"         => "Y",            // активен		  
			"DETAIL_TEXT"    => $arItem['post_content'],		

			"XML_ID" => $arItem['ID'],
			"PROPERTY_VALUES" => array("CML2_LINK" => $e['ID'])
	);
	
	
	
	$e = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $ib['ID'], 'XML_ID' => $arItem['ID']))->GetNext();		
	if(!$e)
		$PRODUCT_ID = $el->Add($arLoadProductArray);
	else
		 $PRODUCT_ID = $e['ID'];


		 
	CPrice::SetBasePrice($PRODUCT_ID, $arItem['meta_value'], 'RUB');
	//$cp = CCatalogProduct::GetLis
	CCatalogProduct::Add(array('ID' => $PRODUCT_ID, 'QUANTITY' => 0));
	}

	//die();
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
