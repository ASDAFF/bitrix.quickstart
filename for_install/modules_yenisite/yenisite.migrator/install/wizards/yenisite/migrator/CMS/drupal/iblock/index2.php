<?

/*

Для разделов

SELECT term_data.tid, term_data.name, term_data.weight, term_data.description, vocabulary_node_types.type, term_hierarchy.parent FROM term_data,vocabulary,vocabulary_node_types,term_hierarchy WHERE term_data.tid = term_hierarchy.tid and term_data.vid = vocabulary_node_types.vid and term_data.vid = vocabulary.vid and vocabulary.module='taxonomy' and vocabulary.hierarchy=1 and vocabulary.tags=0 ORDER BY term_hierarchy.parent ASC

SELECT name as NAME, type as CODE FROM node_type WHERE module='node'

*/

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");

$arFields = Array(
	'ID'=>'drupal',
	'SECTIONS'=>'Y',
	'SORT'=>100,
	'LANG'=>Array(
		'en'=>Array(
			'NAME'=>'Drupal',
			)
		)
	);
	
$obBlocktype = new CIBlockType;
$res = $obBlocktype->Add($arFields);


/* количество записей */

$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}term_data, {$arResult["prefix"]}vocabulary, {$arResult["prefix"]}vocabulary_node_types, {$arResult["prefix"]}term_hierarchy WHERE {$arResult["prefix"]}term_data.tid = {$arResult["prefix"]}term_hierarchy.tid and {$arResult["prefix"]}term_data.vid = {$arResult["prefix"]}vocabulary_node_types.vid and {$arResult["prefix"]}term_data.vid = {$arResult["prefix"]}vocabulary.vid and {$arResult["prefix"]}vocabulary.module='taxonomy' and {$arResult["prefix"]}vocabulary.hierarchy=1 and {$arResult["prefix"]}vocabulary.tags=0 ORDER BY {$arResult["prefix"]}term_hierarchy.parent ASC";
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");

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
	global $USER;

	$query = "SELECT  {$arResult["prefix"]}term_data.tid, {$arResult["prefix"]}term_data.name, {$arResult["prefix"]}term_data.weight, {$arResult["prefix"]}term_data.description, {$arResult["prefix"]}vocabulary.name as DIC , {$arResult["prefix"]}vocabulary_node_types.type, {$arResult["prefix"]}term_hierarchy.parent FROM {$arResult["prefix"]}term_data, {$arResult["prefix"]}vocabulary, {$arResult["prefix"]}vocabulary_node_types, {$arResult["prefix"]}term_hierarchy WHERE {$arResult["prefix"]}term_data.tid = {$arResult["prefix"]}term_hierarchy.tid and {$arResult["prefix"]}term_data.vid = {$arResult["prefix"]}vocabulary_node_types.vid and {$arResult["prefix"]}term_data.vid = {$arResult["prefix"]}vocabulary.vid and {$arResult["prefix"]}vocabulary.module='taxonomy' and {$arResult["prefix"]}vocabulary.hierarchy=1 and {$arResult["prefix"]}vocabulary.tags=0 ORDER BY {$arResult["prefix"]}term_hierarchy.parent ASC LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$ib = CIBlock::GetList(array(), array("CODE" => $arItem["type"]))->GetNext();
		
		if($arItem["parent"] == 0)
		{
			
			
			
			$sec = new CIBlockSection;
			$res = CIBlockSection::GetList(array(), array( "IBLOCK_ID" => $ib["ID"], "NAME" => $arItem["DIC"]))->GetNext();
			if($res)
				$sid = $res["ID"];
			else
			{
				$arFields = array(
					"ACTIVE" => 'Y',					
					"IBLOCK_ID" => $ib["ID"],
					"NAME" => $arItem["DIC"],
					"SORT" => 100,					
					"DESCRIPTION" => "",
					"DESCRIPTION_TYPE" => 'html'
				);
				$sid = $sec->Add($arFields);					
			}
			
		}
		else{
		
				$res = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $ib["ID"], "XML_ID" => $arItem["parent"]))->GetNext();				
				$sid = $res["ID"];
		}
		
			$sec = new CIBlockSection;
			$arFields = array(
					"ACTIVE" => 'Y',
					"XML_ID" => $arItem["tid"],
					"IBLOCK_SECTION_ID" => $sid,
					"IBLOCK_ID" => $ib["ID"],
					"NAME" => $arItem["name"],
					"SORT" => $arItem["weight"],					
					"DESCRIPTION" => $arItem["decrtiption"],
					"DESCRIPTION_TYPE" => 'html'
			);
			
			$sec->Add($arFields);							
	}
	
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
