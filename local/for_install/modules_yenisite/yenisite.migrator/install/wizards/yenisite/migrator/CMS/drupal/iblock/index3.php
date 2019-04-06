<?

/*

Для разделов

SELECT term_data.tid, term_data.name, term_data.weight, term_data.description, vocabulary_node_types.type, term_hierarchy.parent FROM term_data,vocabulary,vocabulary_node_types,term_hierarchy WHERE term_data.tid = term_hierarchy.tid and term_data.vid = vocabulary_node_types.vid and term_data.vid = vocabulary.vid and vocabulary.module='taxonomy' and vocabulary.hierarchy=1 and vocabulary.tags=0 ORDER BY term_hierarchy.parent ASC

SELECT name as NAME, type as CODE FROM node_type WHERE module='node'


SELECT node_revisions.nid AS XML_ID, node_revisions.title AS NAME, node_revisions.body AS DETAIL_TEXT, node_revisions.teaser AS PREVIEW_TEXT, node.created AS DATE_CREATE, node.type AS IBLOCK FROM node_revisions, node, vocabulary WHERE node_revisions.nid = node.nid AND node_revisions.vid = vocabulary.vid AND vocabulary.module = 'taxonomy' 

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

$query = "SELECT  COUNT(*) as CNT  FROM {$arResult["prefix"]}node_revisions, {$arResult["prefix"]}node, {$arResult["prefix"]}vocabulary WHERE {$arResult["prefix"]}node_revisions.nid = {$arResult["prefix"]}node.nid AND ( ( {$arResult["prefix"]}node_revisions.vid = {$arResult["prefix"]}vocabulary.vid AND {$arResult["prefix"]}vocabulary.module = 'taxonomy') OR {$arResult["prefix"]}node_revisions.vid = {$arResult["prefix"]}node_revisions.nid )";
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

	$query = "SELECT {$arResult["prefix"]}node_revisions.nid AS XML_ID, {$arResult["prefix"]}node_revisions.title AS NAME, {$arResult["prefix"]}node_revisions.body AS DETAIL_TEXT,{$arResult["prefix"]}node_revisions.teaser AS PREVIEW_TEXT,{$arResult["prefix"]}node.created AS DATE_CREATE,{$arResult["prefix"]}node.type AS IBLOCK FROM {$arResult["prefix"]}node_revisions, {$arResult["prefix"]}node, {$arResult["prefix"]}vocabulary WHERE {$arResult["prefix"]}node_revisions.nid = {$arResult["prefix"]}node.nid AND ( ( {$arResult["prefix"]}node_revisions.vid = {$arResult["prefix"]}vocabulary.vid AND {$arResult["prefix"]}vocabulary.module = 'taxonomy') OR {$arResult["prefix"]}node_revisions.vid = {$arResult["prefix"]}node_revisions.nid ) LIMIT ".$left.", 10";


	
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
	

	
	
		$ib = CIBlock::GetList(array(), array("CODE" => $arItem["IBLOCK"]))->GetNext();
		
		if(CIBlockElement::GetList(array(), array("XML_ID" => $arItem["XML_ID"], "IBLOCK_ID" => $ib["ID"]))->GetNext()) continue;
		
		//print_r($query); die();
		
		
		$el = new CIBlockElement;

		$arLoadProductArray = Array(			  			  
			  "IBLOCK_ID"      =>  $ib["ID"],			  
			  "NAME"           => $arItem["NAME"],
			  "ACTIVE"         => "Y", 
			  "PREVIEW_TEXT"   => $arItem["PREVIEW_TEXT"],
			  "PREVIEW_TEXT_TYPE"   => "html",
			  "DETAIL_TEXT"    => $arItem["DETAIL_TEXT"],
			  "DETAIL_TEXT_TYPE"   => "html",
			  "XML_ID"   => $arItem["XML_ID"],
		  );

		  /*
*/

//if($arItem["IBLOCK"] !='story' && $arItem["IBLOCK"] !='book')
//{
	//print_r($query);
	//die();
//}

		$PRODUCT_ID = $el->Add($arLoadProductArray);

		$query = "SELECT term_node.tid as tid, vocabulary.multiple as multiple, vocabulary.name as PROPERTY_NAME, vocabulary.vid as PROPERTY_XML_ID, term_data.name as NAME, vocabulary.hierarchy as hierarchy, vocabulary.tags as tags  FROM {$arResult["prefix"]}term_node, vocabulary, {$arResult["prefix"]}term_data WHERE {$arResult["prefix"]}term_node.nid={$arItem['XML_ID']} AND {$arResult["prefix"]}vocabulary.vid = {$arResult["prefix"]}term_data.vid AND {$arResult["prefix"]}term_data.tid = {$arResult["prefix"]}term_node.tid AND {$arResult["prefix"]}vocabulary.module='taxonomy'";
		$count = mysql_query($query, $link);
		$result2 = mysql_query($query, $link);
		
		while($arItem2 = mysql_fetch_assoc($result2))
		{
			if($arItem2['hierarchy'] && !$arItem2['tags'])
			{
			
				$sec = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $ib["ID"], "XML_ID" => $arItem2["tid"]))->GetNext();				
				$db_old_groups = CIBlockElement::GetElementGroups($PRODUCT_ID, true);
				$ar_new_groups = Array($sec["ID"]);
				while($ar_group = $db_old_groups->Fetch())
					$ar_new_groups[] = $ar_group["ID"];					
				CIBlockElement::SetElementSection($PRODUCT_ID, $ar_new_groups);
				
			}
			elseif(!$arItem2['hierarchy'] && !$arItem2['tags'])
			{
				$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$ib["ID"], "NAME" => $arItem2["PROPERTY_NAME"]))->GetNext();
				$property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$ib["ID"], "PROPERTY_ID" => $properties["ID"], "XML_ID" => $arItem2["tid"]))->GetNext();				
				if($arItem2["multiple"])				
				{
					$el = CIBlockElement::GetByID($PRODUCT_ID)->GetNextElement();
					if($el){
					$props = $el->GetProperties();
					$props[$properties["CODE"]]["VALUE_ENUM_ID"][] = $property_enums["ID"];					
					
					CIBlockElement::SetPropertyValueCode($PRODUCT_ID, $properties["CODE"], $props[$properties["CODE"]]["VALUE_ENUM_ID"]);
					}
				}
				else
				{				
					
					$properties = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$ib["ID"], "NAME" => $arItem2["PROPERTY_NAME"]))->GetNext();
				    $property_enums = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$ib["ID"], "PROPERTY_ID" => $properties["ID"], "XML_ID" => $arItem2["tid"]))->GetNext();			
					CIBlockElement::SetPropertyValueCode( $PRODUCT_ID, $properties["CODE"], array("VALUE" =>$property_enums["ID"]) );				
				}				
			}
			elseif($arItem2['tags'])
			{
					$el = CIBlockElement::GetByID($PRODUCT_ID)->GetNext();
					$tags .="{$arItem2['NAME']}," ;
					//print_r($PRODUCT_ID);	
					$el = new CIBlockElement;				
					$el->Update($PRODUCT_ID, array("TAGS" => $tags));				
			}
			
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
