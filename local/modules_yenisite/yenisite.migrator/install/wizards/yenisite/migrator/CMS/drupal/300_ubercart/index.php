<?

/*

Для разделов

SELECT term_data.tid, term_data.name, term_data.weight, term_data.description, vocabulary_node_types.type, term_hierarchy.parent FROM term_data,vocabulary,vocabulary_node_types,term_hierarchy WHERE term_data.tid = term_hierarchy.tid and term_data.vid = vocabulary_node_types.vid and term_data.vid = vocabulary.vid and vocabulary.module='taxonomy' and vocabulary.hierarchy=1 and vocabulary.tags=0 ORDER BY term_hierarchy.parent ASC

SELECT name as NAME, type as CODE FROM node_type WHERE module='node'

*/

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

/* количество записей */
$query = "SELECT COUNT(*) as CNT  FROM {$arResult["prefix"]}uc_products";	
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

	$query = "SELECT * FROM {$arResult["prefix"]}uc_products LIMIT ".$left.", 10";	
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{

		$el = CIBlockElement::GetList(array(), array("XML_ID" => $arItem["nid"]))->GetNext();
		if(!CCatalog::GetList(array(), array('IBLOCK_ID' => $el['IBLOCK_ID']))->GetNext())
		{
			CCatalog::Add(array( "IBLOCK_ID" => $el['IBLOCK_ID'],  "YANDEX_EXPORT" => "N",  "SUBSCRIPTION" => "N" ) );			
		}
		
		if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el['IBLOCK_ID'], "CODE" => 'ARTICUL'))->GetNext())
		{
		
			$arFields = Array(
				"NAME" => GetMessage('ARTICUL'),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "ARTICUL",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $el['IBLOCK_ID']
				);
			
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFields);
		}
		
		if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el['IBLOCK_ID'], "CODE" => 'LENGTH'))->GetNext())
		{
		
			$arFields = Array(
				"NAME" => GetMessage('LENGTH'),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "LENGTH",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $el['IBLOCK_ID']
				);
			
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFields);
		}
		
		if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el['IBLOCK_ID'], "CODE" => 'WEIGHT'))->GetNext())
		{
		
			$arFields = Array(
				"NAME" => GetMessage('WEIGHT'),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "WEIGHT",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $el['IBLOCK_ID']
				);
			
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFields);
		}
		
		if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el['IBLOCK_ID'], "CODE" => 'WIDTH'))->GetNext())
		{
		
			$arFields = Array(
				"NAME" => GetMessage('WIDTH'),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "WIDTH",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $el['IBLOCK_ID']
				);
			
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFields);
		}
		
		if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el['IBLOCK_ID'], "CODE" => 'HEIGHT'))->GetNext())
		{
		
			$arFields = Array(
				"NAME" => GetMessage('HEIGHT'),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "HEIGHT",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $el['IBLOCK_ID']
				);
			
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFields);
		}
		
		if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el['IBLOCK_ID'], "CODE" => 'WEIGHT_UNITS'))->GetNext())
		{
		
			$arFields = Array(
				"NAME" => GetMessage('WEIGHT_UNITS'),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "WEIGHT_UNITS",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $el['IBLOCK_ID']
				);
			
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFields);
		}
		
		if(!CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $el['IBLOCK_ID'], "CODE" => 'LENGTH_UNITS'))->GetNext())
		{
		
			$arFields = Array(
				"NAME" => GetMessage('LENGTH_UNITS'),
				"ACTIVE" => "Y",
				"SORT" => "100",
				"CODE" => "LENGTH_UNITS",
				"PROPERTY_TYPE" => "S",
				"IBLOCK_ID" => $el['IBLOCK_ID']
				);
			
			$ibp = new CIBlockProperty;
			$PropID = $ibp->Add($arFields);
		}
		
		CIBlockElement::SetPropertyValueCode($el["ID"], "ARTICUL", $arItem['model']);		
		CIBlockElement::SetPropertyValueCode($el["ID"], "HEIGHT", $arItem['height']);		
		CIBlockElement::SetPropertyValueCode($el["ID"], "WIDTH", $arItem['width']);		
		CIBlockElement::SetPropertyValueCode($el["ID"], "LENGTH", $arItem['length']);	
		CIBlockElement::SetPropertyValueCode($el["ID"], "WEIGHT_UNITS", $arItem['weight_units']);		
		CIBlockElement::SetPropertyValueCode($el["ID"], "LENGTH_UNITS", $arItem['length_units']);		
		CPrice::SetBasePrice($el["ID"],  floatval($arItem["sell_price"]),  "RUB");
	}
	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
