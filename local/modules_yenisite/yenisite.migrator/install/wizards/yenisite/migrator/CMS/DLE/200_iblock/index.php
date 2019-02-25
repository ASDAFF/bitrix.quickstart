<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$arFields = Array(
	'ID'=>'dle',
	'SECTIONS'=>'Y',
	'SORT'=>100,
	'LANG'=>Array(
		'en'=>Array(
			'NAME'=>'DLE',
			)
		)
	);
	
$obBlocktype = new CIBlockType;
$res = $obBlocktype->Add($arFields);

//SELECT wp_terms.name, wp_terms.slug, wp_terms.term_id, wp_term_taxonomy.parent FROM wp_term_taxonomy, wp_terms WHERE wp_term_taxonomy.taxonomy='wpsc_product_category' AND wp_term_taxonomy.term_id=wp_terms.term_id ORDER BY term_id ASC
/* количество записей */

$query = "SELECT COUNT(*) AS CNT FROM {$arResult['prefix']}category";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);



/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");


$arFields = array(
	"SITE_ID" => $site_id,
	"ACTIVE" => "Y",
	"IBLOCK_TYPE_ID" => "dle",
	"NAME" => "dle-categories",
	"CODE" => "dle_categories",		
	"SORT" => "100",
);
		
$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "dle_categories"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);		


if(CModule::IncludeModule("forum"))
{
	if(!$fg = CForumGroup::GetListEx(array(), array("SORT"=>"123123"))->GetNext())
	{
		$arFields = array("SORT" => 123123);
		$arSysLangs = array("ru", "en", "de");
		for ($i = 0; $i<count($arSysLangs); $i++)
		{
		  $arFields["LANG"][] = array(
			"LID" => $arSysLangs[$i],
			"NAME" => "DLE_COMMENTS",	 		
			);
		}

		$ID = CForumGroup::Add($arFields);
		
	}
	else $ID = $fg['ID'];
	
	
	
	if(!$f = CForumNew::GetListEx(array(), array(" FORUM_GROUP_ID"=>$ID))->GetNext())
	{
	
			$arFields = Array(							   
			   "ACTIVE" => "Y",
			   "NAME" => "dle-comments",			  
			   "FORUM_GROUP_ID" => $ID,
			   "GROUP_ID" => array(1 => "Y"), 
			   "SORT" => "123123",
			   "SITES" => array(
				   $wizard->GetVar("siteID") => "/url/")
			);
			$ID = CForumNew::Add($arFields);	
		
	}
	
	
	

		
		
}


	



	
if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'DESCRIPTION'))->GetNext())
{
		
	$arFields = Array(
		"NAME" => GetMessage('DESCRIPTION'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "DESCRIPTION",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}
		
if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'KEYWORDS'))->GetNext())
{

	$arFields = Array(
		"NAME" => GetMessage('KEYWORDS'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "KEYWORDS",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
	global $APPLICATION;
	print_r($arFields);
}

if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'PHOTO'))->GetNext())
{

	$arFields = Array(
		"NAME" => GetMessage('PHOTO'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "PHOTO",
		"PROPERTY_TYPE" => "F",
		"MULTIPLE" => "Y",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
	global $APPLICATION;

}

if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'FILE'))->GetNext())
{

	$arFields = Array(
		"NAME" => GetMessage('FILE'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "FILE",
		"PROPERTY_TYPE" => "F",
		"MULTIPLE" => "Y",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
	global $APPLICATION;

}


if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "CODE" => 'TITLE'))->GetNext())
{

	$arFields = Array(
		"NAME" => GetMessage('TITLE'),
		"ACTIVE" => "Y",
		"SORT" => "100",
		"CODE" => "TITLE",
		"PROPERTY_TYPE" => "S",
		"IBLOCK_ID" => $id
	);
			
	$ibp = new CIBlockProperty;
	$PropID = $ibp->Add($arFields);
}


		
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
		

	$path = $arResult["site"].'/engine/data/xfields.txt';
	$path = str_replace('www.','',$path);
	if(!substr_count($path, 'http://')) $path = 'http://'.$path;
	$fh = fopen($path, "r");
	$file = fread($fh, 2048);
	$arr = explode("\n", $file);

	foreach($arr as $prop)
	{
	
		


			$arFields = Array();
					
			

			
				$prop = explode('|', $prop);
				switch($prop[3])
				{
					case 'select':
					$arFields = Array(
							"NAME" => $prop[0],
							"ACTIVE" => "Y",
							"SORT" => "100",					
							"PROPERTY_TYPE" => "L",
							"IBLOCK_ID" => $id
						);
						$enum = explode('__NEWL__', $prop[4]);
					foreach($enum as $en)
						$arFields['VALUES'][] = array('VALUE' => $en, 'SORT' => 100, 'DEF' => 'N');
					if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "NAME" => $prop[0]))->GetNext())
					{
						$ibp = new CIBlockProperty;
						$PropID = $ibp->Add($arFields);
					}							
						break;
					case 'textarea':
						$arFields = Array(
							"NAME" => $prop[0],
							"ACTIVE" => "Y",
							"SORT" => "100",					
							"PROPERTY_TYPE" => "S",
							"USER_TYPE" => 'HTML',
							"IBLOCK_ID" => $id
						);
						if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "NAME" => $prop[0]))->GetNext())
						{
							$ibp = new CIBlockProperty;
							$PropID = $ibp->Add($arFields);
						}						
						break;				
					default:
						$arFields = Array(
							"NAME" => $prop[0],
							"ACTIVE" => "Y",
							"SORT" => "100",					
							"PROPERTY_TYPE" => "S",						
							"IBLOCK_ID" => $id
						);
						if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $id, "NAME" => $prop[0]))->GetNext())
						{
							$ibp = new CIBlockProperty;
							$PropID = $ibp->Add($arFields);
						}	
						break;
				}
	
		
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

	$query = "SELECT * FROM {$arResult['prefix']}category LIMIT {$left}, 10";
	//echo $query;
	
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['parentid']))->GetNext();
		if(!$res) $parent = 0; else $parent = $res['ID'];
		
		$bs = new CIBlockSection;
		$arFields = Array(
		  "ACTIVE" => "Y",
		  "IBLOCK_SECTION_ID" => $parent,
		  "IBLOCK_ID" => $id,
		  "NAME" => $arItem['name'],
		  "SORT" => 500,		  
		  "CODE" => $arItem['alt_name'],
		  'XML_ID' => $arItem['id']
		  );

		if($parent==0) unset($arFields["IBLOCK_SECTION_ID"]);
		  
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['id']))->GetNext();
		
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
