<?
global $DB;
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."topics`";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);
// echo $count["CNT"].' | '.$left.'<br/>';
/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */
if($left > $count["CNT"])
{	
    echo "{$left} из {$count['CNT']}";
    // echo $left.' ---- '.$count["CNT"] ;
    //die();
	$left = 0;
	$right = 10;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{
	$query = "SELECT * FROM ".$arResult["prefix"]."topics  LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
	
		if(!$ft = CForumTopic::GetList(array(), array('XML_ID' => $arItem['tid']))->GetNext())
		{
			$f = CForumNew::Getlist(array(), array("XML_ID" => $arItem["forum_id"]))->GetNext();
			// echo '<pre>'; print_r($arItem); // echo '</pre>';
            $user_starter =  CUser::GetList(($by="personal_country"), ($order="desc"), array("XML_ID" => $arItem["starter_id"]))->GetNext();
            $user_lastpost = CUser::GetList(($by="personal_country"), ($order="desc"), array("XML_ID" => $arItem["last_poster_id"]))->GetNext();
			$arFields = Array(
				"TITLE" => $arItem["title"],
				"DESCRIPTION" => $arItem["description"],
				"STATE" => $arItem['state'] == 'closed' ? 'N' : 'Y',
				"USER_START_ID" => $user_starter['ID'],
				"USER_START_NAME" => $arItem["starter_name"],
				"START_DATE" => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['start_date']),
                // "POSTS" => $arItem['posts'],
                // "VIEWS" => $arItem['views'],
				"FORUM_ID" => $f["ID"],
				"LAST_POSTER_ID" => $user_lastpost["ID"],
				"LAST_POSTER_NAME" => $user_lastpost["NAME"],
				"LAST_POST_DATE" => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['last_post']),

				"LAST_MESSAGE_ID" => 1,
				"XML_ID" => $arItem['tid']
			);
			
            CForumTopic::Add($arFields);
		}
        //print_r($ft); die('1');
		
    /*
		$arFields = Array(
		  "POST_MESSAGE" => $arItem["body"],
		  "USE_SMILES" => "Y",
		  "APPROVED" => "Y",
		  "AUTHOR_NAME" => $arItem["posterName"],
		  "AUTHOR_ID" => $u["ID"]?$u["ID"]:1,
		  "FORUM_ID" => $f["ID"],
		  "TOPIC_ID" => $TOPIC,
		  "AUTHOR_IP" => ($arItem["posterIP"]) ? $arItem["posterIP"] : "<no address>",
		  "NEW_TOPIC" => "N"
		);

		$ID = CForumMessage::Add($arFields);
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
