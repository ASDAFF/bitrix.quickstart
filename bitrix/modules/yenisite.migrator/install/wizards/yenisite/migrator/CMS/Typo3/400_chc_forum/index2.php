<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */

$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}tx_chcforum_thread";	
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
	
	$query = "SELECT  * FROM {$arResult["prefix"]}tx_chcforum_thread LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	
	

	while($arItem = mysql_fetch_assoc($result))
	{		
			
			
			$f = CForumNew::GetList(array(), array('XML_ID' => $arItem['conference_id']))->GetNext();
			
			$usr = $USER->GetByLogin($arItem["author_topic"])->GetNext();
			
			$arFields = Array(
				"TITLE" => $arItem["thread_subject"],
				"STATE" => "Y",
				"USER_START_NAME" => $usr["NAME"]?$usr["NAME"]:'admin',
				"START_DATE" => $arItem["start_date"],
				"FORUM_ID" => $f["ID"],
				"LAST_POSTER_ID" =>$usr["ID"]?$usr["ID"]:'admin',
				"LAST_POSTER_NAME" => $usr["NAME"]?$usr["NAME"]:'admin',
				"LAST_POST_DATE" => date("Y-m-d h:i:s"),
				"LAST_MESSAGE_ID" => 1,
				"ICON_ID" => $arItem["uid"]
			);
			
			//print_r($arFields);
			//die();

			$TOPIC = CForumTopic::Add($arFields);
	
			
/*				
			$arFields = Array(
				  "POST_MESSAGE" => $arItem["comment"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  //"POST_DATE" => date("Y-m-d h:i:s", $arItem["timestamp"]),
				  "AUTHOR_NAME" => $usr["NAME"],
				  "AUTHOR_ID" => $usr["NAME"]?$usr["NAME"]:1,
				  "FORUM_ID" => $db_res["FORUM_ID"],
				  "TOPIC_ID" => $db_res["ID"],				  
				  "NEW_TOPIC" => "N"
				);

				$ID = CForumMessage::Add($arFields);
			*/
/*	print_r($arItem);			
	echo "<br/>";
	print_r($arFields);
	die();
	*/
	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
