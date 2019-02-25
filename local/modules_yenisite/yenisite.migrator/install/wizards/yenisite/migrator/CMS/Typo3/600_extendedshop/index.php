<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$arFields = Array(
	'ID'=>'typo3',
	'SECTIONS'=>'Y',
	'SORT'=>100,
	'LANG'=>Array(
		'en'=>Array(
			'NAME'=>'Typo3',
			)
		)
	);
	
$obBlocktype = new CIBlockType;
$res = $obBlocktype->Add($arFields);

//SELECT wp_terms.name, wp_terms.slug, wp_terms.term_id, wp_term_taxonomy.parent FROM wp_term_taxonomy, wp_terms WHERE wp_term_taxonomy.taxonomy='wpsc_product_category' AND wp_term_taxonomy.term_id=wp_terms.term_id ORDER BY term_id ASC
/* количество записей */

$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}pages WHERE {$arResult['prefix']}doktype=201 ORDER BY {$arResult['prefix']}uid DESC";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);



/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");


$arFields = array(
	"SITE_ID" => $site_id,
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "typo3",
	"NAME" => "typo3-extendedshop",
	"CODE" => "typo3_extendedshop",		
	"SORT" => "100",
);
		
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "typo3_extendedshop"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);		
	


if(!CCatalog::GetList(array(), array('IBLOCK_ID' => $id))->GetNext())
{
			CCatalog::Add(array( "IBLOCK_ID" => $id,  "YANDEX_EXPORT" => "N",  "SUBSCRIPTION" => "N" , "OFFERS_IBLOCK_ID" => $id_pr) );			
}


if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'WEIGHT'))->GetNext())
{		
	$arFields = Array(
		"NAME" => 'WEIGHT',
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "WEIGHT",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id,
		);			
	
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}	
		

if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'VOLUME'))->GetNext())
{		
	$arFields = Array(
		"NAME" => 'VOLUME',
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "VOLUME",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id,
		);			
	
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}	


if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'SIZE'))->GetNext())
{		
	$arFields = Array(
		"NAME" => 'SIZE',
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "SIZE",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id,
		);			
	
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}	




if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'COLOR'))->GetNext())
{		
	$arFields = Array(
		"NAME" => 'COLOR',
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "COLOR",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id,
		);			
	
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
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

	$query = "SELECT * FROM {$arResult['prefix']}pages WHERE {$arResult['prefix']}doktype=201 LIMIT {$left}, 10";
	//echo $query;
	//die($query);
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
		
		
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['pid']))->GetNext();
		if(!$res) $parent = 0; else $parent = $res['ID'];
		
		
		$bs = new CIBlockSection;
		$arFields = Array(
		  "ACTIVE" => "Y",
		  "IBLOCK_SECTION_ID" => $parent,
		  "IBLOCK_ID" => $id,
		  "NAME" => $arItem['title'],
		  "SORT" => 500,		  
		  //"CODE" => $arItem['alt_name'],
		  'XML_ID' => $arItem['uid'],
		  );

		if($parent==0) unset($arFields["IBLOCK_SECTION_ID"]);
		  
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['uid']))->GetNext();
		
		if(!$res)							
		  $bs->Add($arFields);
		  
	}	
		
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
