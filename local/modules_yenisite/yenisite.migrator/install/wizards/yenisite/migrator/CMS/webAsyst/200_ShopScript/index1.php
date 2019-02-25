<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

$iblock = new CIBlock;
$res = CIBlock::GetList(array(), array("CODE" => "shopscript_catalog"))->GetNext();
if($res)
	$id = $res["ID"];
else
	$id = $iblock->Add($arFields);	
	


/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM {$arResult['prefix']}SC_categories";
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
	
	
	$query = "SELECT * FROM {$arResult['prefix']}SC_categories ORDER BY parent ASC LIMIT {$left}, 10";

	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{		
		
        if($arItem["name_ru"] == "ROOT") continue;
        
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['parent']))->GetNext();
		if(!$res) $parent = 0; else $parent = $res['ID'];
		
		$bs = new CIBlockSection;
		$arFields = Array(
		  "ACTIVE" => "Y",
		  "IBLOCK_SECTION_ID" => $parent,
		  "IBLOCK_ID" => $id,
		  "NAME" => $arItem['name_ru'],
		  "SORT" => 500,		  
		  "CODE" => $arItem['slug'],
		  'XML_ID' => $arItem['categoryID'],
		  );

		if($parent==0) unset($arFields["IBLOCK_SECTION_ID"]);
		  
		$res = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $id, 'XML_ID' => $arItem['categoryID']))->GetNext();
		
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
