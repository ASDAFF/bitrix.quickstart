<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("blog");

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

	$query = "SELECT {$arResult["prefix"]}node_revisions.title as TITLE, {$arResult["prefix"]}node_revisions.body as TEXT, {$arResult["prefix"]}users.name as USER_NAME, {$arResult["prefix"]}node.nid as XML_ID FROM {$arResult["prefix"]}node_revisions,{$arResult["prefix"]}users,{$arResult["prefix"]}node WHERE {$arResult["prefix"]}node.type='blog' AND{$arResult["prefix"]} node.uid=users.uid AND {$arResult["prefix"]}node.nid=node_revisions.nid LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		
		global $USER;
		$usr = $USER->GetByLogin($arItem["USER_NAME"])->GetNext();
		
		$arBlog = CBlog::GetByOwnerID($usr["ID"]);
		if(!is_array($arBlog))
		{
		
			if(!$gb = CBlogGroup::GetList(array(), array("NAME" => "Drupal blogs"))->GetNext())
			{

				$arFields = array(
					"SITE_ID" => $wizard->GetVar("siteID"),
					"NAME" => "Drupal blogs"
				);

				$GID = CBlogGroup::Add($arFields);
			}
			else $GID = $gb["ID"];						
			
			
		
			$arFields = array(
					"NAME" => GetMessage('BLOG_NAME').$arItem['USER_NAME'],			
					"GROUP_ID" => $GID,									
					"URL" => "{$arItem['USER_NAME']}-blog",
					"ACTIVE" => "Y",
					"OWNER_ID" => $usr["ID"]
			);

			$BID = CBlog::Add($arFields);
			
		}
		else
			$BID = $arBlog["ID"];

		
		
		if(!$arPost = CBlogPost::GetList(array(), array("CATEGORY_ID" => $arItem["XML_ID"]))->GetNext())
		{
		
			$arFields = array(
				"TITLE" => $arItem['TITLE'],
				"DETAIL_TEXT" => $arItem['TEXT'],
				"BLOG_ID" => $BID,
				"CATEGORY_ID" => $arItem["XML_ID"],
				"AUTHOR_ID" => $usr['ID'],
				"PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
				
			);

			$TID = CBlogPost::Add($arFields);
		}
		else
			$TID = $arPost["ID"];
		//print_r($TID);
		//die();

	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
