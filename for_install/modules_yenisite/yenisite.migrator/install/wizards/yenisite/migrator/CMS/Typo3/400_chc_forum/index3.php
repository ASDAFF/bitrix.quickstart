<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* ���������� ������� */

$query = "SELECT COUNT(*) as CNT FROM {$arResult["prefix"]}tx_chcforum_post";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

/* ���� ����� ������� ������ ���������� ��������� - �������� ������� ��������� ��� */

if($left > $count["CNT"])
{	
	
	$left = 0;
	$right = 10;

	/* ��� ��� ������� ��������������� ��������� ��� � ������ ��������� � ���������� �����(���� �� ����������) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{
	global $USER;
	
	$query = "SELECT  * FROM {$arResult["prefix"]}tx_chcforum_post LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);
	
	

	while($arItem = mysql_fetch_assoc($result))
	{		
			$usr = $USER->GetByLogin($arItem["post_author_name"])->GetNext();
			//$t = CForumTopic::GetList(array(), array('ICON_ID' => $arItem['thread_id']))->GetNext();
			$query = "SELECT * FROM b_forum_topic WHERE ICON_ID='{$arItem['thread_id']}'";
			global $DB;
			$t = $DB->query($query)->GetNext();		
			
			$arFields = Array(
				  "POST_MESSAGE" => $arItem["post_text"],
				  "USE_SMILES" => "Y",
				  "APPROVED" => "Y",
				  //"POST_DATE" => date("Y-m-d h:i:s", $arItem["timestamp"]),
				  "AUTHOR_NAME" => $usr["NAME"]?$usr["NAME"]:$arItem["post_author_name"],
				  "AUTHOR_ID" => $usr["ID"]?$usr["ID"]:1,
				  "FORUM_ID" => $t["FORUM_ID"],
				  "TOPIC_ID" => $t["ID"],				  
				  "NEW_TOPIC" => "N"
				);
				$ID = CForumMessage::Add($arFields);
	}

	/* ����������� ����� � ������ ������� */
	$left += 10;
	$right += 10;

}

/* ������������� ����� � ������ ������� */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
