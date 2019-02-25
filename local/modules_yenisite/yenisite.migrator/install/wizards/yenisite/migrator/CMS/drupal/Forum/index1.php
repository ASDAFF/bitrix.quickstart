<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */


$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}node_revisions, {$arResult["prefix"]}node_type, {$arResult["prefix"]}node, term_node WHERE {$arResult["prefix"]}node.type={$arResult["prefix"]}node_type.type AND {$arResult["prefix"]}node_type.module='forum' AND {$arResult["prefix"]}node.nid={$arResult["prefix"]}node_revisions.nid AND {$arResult["prefix"]}term_node.nid={$arResult["prefix"]}node_revisions.nid";	
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
	
	
	
	$query = "SELECT node_revisions.timestamp, {$arResult["prefix"]}users.name as USER_NAME, {$arResult["prefix"]}term_node.tid, {$arResult["prefix"]}node.nid, {$arResult["prefix"]}node_revisions.title, {$arResult["prefix"]}node_revisions.body FROM {$arResult["prefix"]}users, {$arResult["prefix"]}node_revisions, {$arResult["prefix"]}node_type, {$arResult["prefix"]}node, term_node WHERE {$arResult["prefix"]}node.type={$arResult["prefix"]}node_type.type AND {$arResult["prefix"]}node_type.module='forum' AND {$arResult["prefix"]}node.nid={$arResult["prefix"]}node_revisions.nid AND {$arResult["prefix"]}users.uid={$arResult["prefix"]}node_revisions.uid AND {$arResult["prefix"]}term_node.nid={$arResult["prefix"]}node_revisions.nid  LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	
	//print_r($query); die();

	while($arItem = mysql_fetch_assoc($result))
	{
	
			
			
			$usr = $USER->GetByLogin($arItem["USER_NAME"])->GetNext();
			$f = CForumNew::Getlist(array(), array("XML_ID" => $arItem["tid"]))->GetNext();
				
			$arFields = Array(
				"TITLE" => $arItem["title"],
				"STATE" => "Y",
				"USER_START_NAME" => $usr["ID"],
				"START_DATE" => date("Y-m-d h:i:s", $arItem["timestamp"]),
				"FORUM_ID" => $f["ID"],
				"LAST_POSTER_ID" => $usr["ID"],
				"LAST_POSTER_NAME" => $usr["NAME"],
				"LAST_POST_DATE" => date("Y-m-d h:i:s"),
				"LAST_MESSAGE_ID" => 1,
				"ICON_ID" => $arItem["nid"]
			);

			$TOPIC = CForumTopic::Add($arFields);
			
			$arFields = Array(
				  "POST_MESSAGE" => $arItem["body"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  "AUTHOR_NAME" => $usr["NAME"],
				  "AUTHOR_ID" => $usr["NAME"]?$usr["NAME"]:1,
				  "FORUM_ID" => $f["ID"],
				  "TOPIC_ID" => $TOPIC,				  
				  "NEW_TOPIC" => "N",				  
				);

				$ID = CForumMessage::Add($arFields);
			

	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
