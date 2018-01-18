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
$query = "SELECT COUNT(*) as CNT  FROM {$arResult["prefix"]}node_type WHERE module='node' OR module='uc_product'";	
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

	$query = "SELECT name as NAME, type as CODE FROM {$arResult["prefix"]}node_type WHERE module='node' OR module='uc_product' LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
	
		$arFields = array(
			"SITE_ID" => $site_id,
			"ACTIVE" => "Y",
			"IBLOCK_TYPE_ID" => "drupal",
			"NAME" => $arItem["NAME"],
			"CODE" => $arItem["CODE"],		
			"SORT" => "100",
		);
		
		$iblock = new CIBlock;
		$res = CIBlock::GetList(array(), array("CODE" => $arItem["CODE"]))->GetNext();
		if($res)
			$id = $res["ID"];
		else
		{
			$id = $iblock->Add($arFields);					
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
