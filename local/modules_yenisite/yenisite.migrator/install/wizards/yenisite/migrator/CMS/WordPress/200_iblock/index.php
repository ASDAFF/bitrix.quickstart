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
$query = "SELECT COUNT(*) as CNT  FROM {$arResult["prefix"]}term_taxonomy, {$arResult["prefix"]}terms WHERE {$arResult["prefix"]}term_taxonomy.taxonomy='category' AND {$arResult["prefix"]}term_taxonomy.term_id={$arResult["prefix"]}terms.term_id ORDER BY {$arResult["prefix"]}terms.term_id ASC";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);


/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");


$arFields = array(
	"SITE_ID" => $site_id,
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "wp",
	"NAME" => "Rubrics",
	"CODE" => "wp_rubrics",		
	"SORT" => "100",
);
		
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "wp_rubrics"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);		


if(CModule::IncludeModule("forum"))
{
	if(!$fg = CForumGroup::GetListEx(array(), array("SORT"=>"1234567"))->GetNext())
	{
		$arFields = array("SORT" => 1234567);
		$arSysLangs = array("ru", "en", "de");
		for ($i = 0; $i<count($arSysLangs); $i++)
		{
		  $arFields["LANG"][] = array(
			"LID" => $arSysLangs[$i],
			"NAME" => "WP_COMMENTS",	 		
			);
		}

		$ID = CForumGroup::Add($arFields);
		
	}
	else $ID = $fg['ID'];
	
	
	
	if(!$f = CForumNew::GetListEx(array(), array(" FORUM_GROUP_ID"=>$ID))->GetNext())
	{
	
			$arFields = Array(				
			   "XML_ID" => $arItem["tid"],
			   "ACTIVE" => "Y",
			   "NAME" => "comments",			  
			   "FORUM_GROUP_ID" => $ID,
			   "GROUP_ID" => array(1 => "Y"), 
			   "SORT" => "1234567",
			   "SITES" => array(
				   $wizard->GetVar("siteID") => "/url/")
			);
			$ID = CForumNew::Add($arFields);	
		
	}
	
	
	

		
		
}


	

/*
$arFields = array(
	"SITE_ID" => $site_id,
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "wp",
	"NAME" => "Variation",
	"CODE" => "wp_proposal",		
	"SORT" => "100",
);
		
		
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "wp_proposal"))->GetNext();
if($res)
	$id_pr = $res["ID"];
else
	$id_pr = $iblock->Add($arFields);					


if(!CCatalog::GetList(array(), array('IBLOCK_ID' => $id))->GetNext())
{
			CCatalog::Add(array( "IBLOCK_ID" => $id,  "YANDEX_EXPORT" => "N",  "SUBSCRIPTION" => "N" , "OFFERS_IBLOCK_ID" => $id_pr) );			
}		

if(!CCatalog::GetList(array(), array('IBLOCK_ID' => $id_pr))->GetNext())
{
			CCatalog::Add(array( "IBLOCK_ID" => $id_pr,  "YANDEX_EXPORT" => "N",  "SUBSCRIPTION" => "N" ) );			
}	

if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'ARTICUL'))->GetNext())
{
		
	$arFields = Array(
		"NAME" => GetMessage('ARTICUL'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "ARTICUL",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id
		);
	
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}


if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id_pr, "CODE" => 'CML2_LINK'))->GetNext())
{
		
	$arFields = Array(
		"NAME" => 'proposal',
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "CML2_LINK",
		"PROPERTY_TYPE" => "E",
		"LINK_IBLOCK_ID" => $id,
		"IBLOCK_ID" => $id_pr
		);
	
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}

		
if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'LENGTH'))->GetNext())
{
	$arFields = Array(
		"NAME" => GetMessage('LENGTH'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "LENGTH",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}
		
if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'WEIGHT'))->GetNext())
{
		
	$arFields = Array(
		"NAME" => GetMessage('WEIGHT'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "WEIGHT",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}
		
if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'WIDTH'))->GetNext())
{

	$arFields = Array(
		"NAME" => GetMessage('WIDTH'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "WIDTH",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}

*/
		
if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'FORUM_TOPIC_ID'))->GetNext())
{

	$arFields = Array(
		"NAME" => "FORUM_TOPIC_ID",
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "FORUM_TOPIC_ID",
		"PROPERTY_TYPE" => "N",
		"IBLOCK_ID" => $id
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

	$query = "SELECT {$arResult["prefix"]}terms.name, {$arResult["prefix"]}terms.slug, {$arResult["prefix"]}terms.term_id, {$arResult["prefix"]}term_taxonomy.parent FROM {$arResult["prefix"]}term_taxonomy, {$arResult["prefix"]}terms WHERE {$arResult["prefix"]}term_taxonomy.taxonomy='category' AND {$arResult["prefix"]}term_taxonomy.term_id={$arResult["prefix"]}terms.term_id ORDER BY {$arResult["prefix"]}terms.term_id ASC LIMIT ".$left.", 10";
	//echo $query;
	
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
	
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['parent']))->GetNext();
		if(!$res) $parent = 0; else $parent = $res['ID'];
		
		$bs = new CIBlockSection;
		$arFields = Array(
		  "ACTIVE" => "Y",
		  "IBLOCK_SECTION_ID" => $parent,
		  "IBLOCK_ID" => $id,
		  "NAME" => $arItem['name'],
		  "SORT" => 500,		  
		  "CODE" => $arItem['slug'],
		  'XML_ID' => $arItem['term_id']
		  );

		if($parent==0) unset($arFields["IBLOCK_SECTION_ID"]);
		  
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['term_id']))->GetNext();
		
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
