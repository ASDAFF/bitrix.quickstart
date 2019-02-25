<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */


$query = "SELECT COUNT(*) as CNT FROM node_revisions,users,node WHERE {$arResult["prefix"]}node.type='blog' AND {$arResult["prefix"]}node.uid={$arResult["prefix"]}users.uid AND node.nid={$arResult["prefix"]}node_revisions.nid";	
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
	global $USER;

	
	$query = "SELECT {$arResult["prefix"]}term_data.tid, {$arResult["prefix"]}term_data.name, {$arResult["prefix"]}term_hierarchy.parent FROM {$arResult["prefix"]}term_hierarchy, {$arResult["prefix"]}term_data, {$arResult["prefix"]}vocabulary_node_types, {$arResult["prefix"]}vocabulary WHERE {$arResult["prefix"]}term_data.vid={$arResult["prefix"]}vocabulary_node_types.vid AND {$arResult["prefix"]}term_data.vid={$arResult["prefix"]}vocabulary.vid AND {$arResult["prefix"]}vocabulary.module='forum' AND {$arResult["prefix"]}term_hierarchy.tid={$arResult["prefix"]}term_data.tid LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		
		if($arItem['parent'] == '0')
		{
			$arFields = array("SORT" => 500);
			$arSysLangs = array("ru", "en", "de");
			for ($i = 0; $i<count($arSysLangs); $i++)
			{
			  $arFields["LANG"][] = array(
				"LID" => $arSysLangs[$i],
				"NAME" => $arItem["name"],	 
						"XML_ID"   => $arItem["tid"]
				);
			}

			$ID = CForumGroup::Add($arFields);
		}
		else
		{
			$res = CForumGroup::GetList(array(), array("XML_ID" => $arItem["parent"]))->GetNext();
			$arFields = Array(
			   "XML_ID" => $arItem["tid"],
			   "ACTIVE" => "Y",
			   "NAME" => $arItem["name"],			  
			   "FORUM_GROUP_ID" => $res["ID"],
			   "GROUP_ID" => array(1 => "Y"), 
			   "SITES" => array(
				   $wizard->GetVar("siteID") => "/url/")
			);
			$ID = CForumNew::Add($arFields);	
		
		
		}
		//print_r($arItem);
		

	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
