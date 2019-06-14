<?

/*

Для разделов

SELECT term_data.tid, term_data.name, term_data.weight, term_data.description, vocabulary_node_types.type, term_hierarchy.parent FROM term_data,vocabulary,vocabulary_node_types,term_hierarchy WHERE term_data.tid = term_hierarchy.tid and term_data.vid = vocabulary_node_types.vid and term_data.vid = vocabulary.vid and vocabulary.module='taxonomy' and vocabulary.hierarchy=1 and vocabulary.tags=0 ORDER BY term_hierarchy.parent ASC

SELECT name as NAME, type as CODE FROM node_type WHERE module='node'

*/

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("iblock");



/* количество записей */

$query = "SELECT COUNT(*)  as CNT FROM {$arResult["prefix"]}term_data, {$arResult["prefix"]}vocabulary, {$arResult["prefix"]}vocabulary_node_types, {$arResult["prefix"]}term_hierarchy WHERE {$arResult["prefix"]}term_data.tid = {$arResult["prefix"]}term_hierarchy.tid and term_data.vid = {$arResult["prefix"]}vocabulary_node_types.vid and {$arResult["prefix"]}term_data.vid = {$arResult["prefix"]}vocabulary.vid and {$arResult["prefix"]}vocabulary.module='taxonomy' and {$arResult["prefix"]}vocabulary.hierarchy=0 and {$arResult["prefix"]}vocabulary.tags=0 ORDER BY {$arResult["prefix"]}term_hierarchy.parent ASC";
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

	$query = "SELECT {$arResult["prefix"]}term_data.tid as XML_ID, {$arResult["prefix"]}term_data.name as VALUE,{$arResult["prefix"]}vocabulary_node_types.type as IBLOCK, {$arResult["prefix"]}vocabulary.multiple, {$arResult["prefix"]}vocabulary.required ,{$arResult["prefix"]}vocabulary.name as PROPERTY_NAME,{$arResult["prefix"]}vocabulary.vid as PROPERTY_XML_ID FROM {$arResult["prefix"]}term_data, {$arResult["prefix"]}vocabulary, {$arResult["prefix"]}vocabulary_node_types, {$arResult["prefix"]}term_hierarchy WHERE {$arResult["prefix"]}term_data.tid = {$arResult["prefix"]}term_hierarchy.tid and term_data.vid = {$arResult["prefix"]}vocabulary_node_types.vid and {$arResult["prefix"]}term_data.vid = {$arResult["prefix"]}vocabulary.vid and {$arResult["prefix"]}vocabulary.module='taxonomy' and {$arResult["prefix"]}vocabulary.hierarchy=0 and {$arResult["prefix"]}vocabulary.tags=0 ORDER BY {$arResult["prefix"]}term_hierarchy.parent ASC LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{

		$ib = CIBlock::GetList(Array(), array("CODE" => $arItem["IBLOCK"]))->GetNext();		
		$prop = CIBlockProperty::GetList(Array(), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$ib["ID"], "NAME" => $arItem["PROPERTY_NAME"], "PROPERTY_TYPE" => "L"))->GetNext();
		if($prop)
			$pid = $prop["ID"];
		else
		{			
			$arFields = Array(
				  "NAME" =>  $arItem["PROPERTY_NAME"],
				  "ACTIVE" => "Y",
				  "IS_REQUIRED" => $arItem["required"]?"Y":"N",
				  "MULTIPLE" => $arItem["multiple"]?"Y":"N",
				  "SORT" => "100",
				  "CODE" => 'DRUPAL_'.rand(0,5000),
				  "PROPERTY_TYPE" => "L",
				  "IBLOCK_ID" => $ib["ID"],
				  "XML_ID" => $arItem["PROPERTY_XML_ID"],
				  );
				 //print_r($arFields);
			$ibp = new CIBlockProperty;
			$pid = $ibp->Add($arFields);
		}
		
		$enum = CIBlockPropertyEnum::GetList(Array(), Array("IBLOCK_ID"=>$ib["ID"], "PROPERTY_ID" => $pid, "XML_ID" => $arItem["XML_ID"]))->GetNext();
		if(!$enum)
		{
			$ibpenum = new CIBlockPropertyEnum;
			$ibpenum->Add(Array('PROPERTY_ID'=>$pid, 'VALUE'=>$arItem['VALUE'], 'XML_ID' => $arItem['XML_ID']));
		}
		//print_r($arItem); echo "<br/><br/>";
		
			
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
