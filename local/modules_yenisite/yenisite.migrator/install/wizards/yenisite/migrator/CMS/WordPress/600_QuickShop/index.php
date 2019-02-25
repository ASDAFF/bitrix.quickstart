<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$arFields = Array(
	'ID'=>'wp',
	'SECTIONS'=>'Y',
	'SORT'=>100,
	'LANG'=>Array(
		'en'=>Array(
			'NAME'=>'WordPress',
			)
		)
	);
	
$obBlocktype = new CIBlockType;
$res = $obBlocktype->Add($arFields);

//SELECT wp_terms.name, wp_terms.slug, wp_terms.term_id, wp_term_taxonomy.parent FROM wp_term_taxonomy, wp_terms WHERE wp_term_taxonomy.taxonomy='wpsc_product_category' AND wp_term_taxonomy.term_id=wp_terms.term_id ORDER BY term_id ASC
/* количество записей */



$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}options WHERE {$arResult["prefix"]}options.option_name='quickshop_products'";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);


/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");


$arFields = array(
	"SITE_ID" => $site_id,
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "wp",
	"NAME" => "QuickShop",
	"CODE" => "wp_quickshop",		
	"SORT" => "100",
);
		
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "wp_quickshop"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);					


if(!CCatalog::GetList(array(), array('IBLOCK_ID' => $id))->GetNext())
{
			CCatalog::Add(array( "IBLOCK_ID" => $id,  "YANDEX_EXPORT" => "N",  "SUBSCRIPTION" => "N" , "OFFERS_IBLOCK_ID" => $id_pr) );			
}		



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

	$query = "SELECT * FROM {$arResult["prefix"]}options WHERE {$arResult["prefix"]}options.option_name='quickshop_products' LIMIT ".$left.", 10";	
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
		
		
		$items = explode("\n", $arItem['option_value']);
		
		foreach($items as $item)
		{
			$e = explode('|', $item);
			$arFields = array(
				"IBLOCK_ID" => $id,
				"NAME" => "{$e[0]}({$e[3]})",				
			);
			
			$el = new CIBlockElement;
			$eid = $el->Add($arFields);
			CPrice::SetBasePrice($eid, $e[1]);
			
		}
		
		//print_r( $items );
		
		//die();
		  
	}
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
