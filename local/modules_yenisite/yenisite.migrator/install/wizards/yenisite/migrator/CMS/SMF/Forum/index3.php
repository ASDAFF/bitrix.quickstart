<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."messages`";	
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

	$query = "SELECT * FROM ".$arResult["prefix"]."messages  LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);
	while($arItem = mysql_fetch_assoc($result))
	{
		$TOPIC = 0;

		foreach($topics as $k=>$v)
			if($k == $arItem["ID_TOPIC"])
				$TOPIC = $v;
	
		if(!$TOPIC)
		{

			$query = "SELECT * FROM ".$arResult["prefix"]."topics WHERE ID_TOPIC=".$arItem["ID_TOPIC"];
			$res = mysql_query($query, $link);
			$arI = mysql_fetch_assoc($res);
		
			$arI["ID_MEMBER_UPDATED"] = $arI["ID_MEMBER_UPDATED"]?$arI["ID_MEMBER_UPDATED"]:1;

			$u = CUser::GetList(($by="personal_country"), ($order="desc"), array("XML_ID" => $arI["ID_MEMBER_UPDATED"]))->GetNext(); 

			$f = CForumNew::Getlist(array(), array("XML_ID" => $arItem["ID_BOARD"]))->GetNext();
				
			$arFields = Array(
				"TITLE" => $arItem["subject"],
				"STATE" => "Y",
				"USER_START_NAME" => $arItem["posterName"],
				"START_DATE" => date("Y-m-d h:i:s",$arItem["posterTime"]),
				"FORUM_ID" => $f["ID"],
				"LAST_POSTER_ID" => $u["ID"],
				"LAST_POSTER_NAME" => $u["NAME"],
				"LAST_POST_DATE" => date("Y-m-d h:i:s"),
				"LAST_MESSAGE_ID" => 1,
			);


			$TOPIC = CForumTopic::Add($arFields);

			$topics[$arItem["ID_TOPIC"]] = $TOPIC;

		}
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



	}

	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
