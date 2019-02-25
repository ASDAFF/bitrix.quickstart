<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

if(!CForumGroup::GetList(array(), array('SORT' => 777))->GetNext())
{
	echo 'YO';
	$arFields = array("SORT" => 777);
	$arSysLangs = array("ru", "en");
	for ($i = 0; $i<count($arSysLangs); $i++)
	{
		$arFields["LANG"][] = array(
			"LID" => $arSysLangs[$i],
			"NAME" => 'bbPress',	 
			"XML_ID"   => 'bbPress'
		);
	}
	$ID = CForumGroup::Add($arFields);
}


$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."users`";	
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
	$query = "SELECT * FROM ".$arResult["prefix"]."users LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$pass = "bb-AbC".rand(300, 1999);

		$arFields = Array(
		  "NAME"             => $arItem["display_name"],
		  "EMAIL"             => $arItem["user_email"],
		  "LOGIN"             => $arItem["user_login"],
		  "LID"               => SITE_ID,
		  "ACTIVE"            => "Y",	  
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass,
		  "XML_ID"		=> $arItem["ID"]
		);
		//if($arItem["ID_GROUP"] == 1) 
			//$arFields["GROUP_ID"] = array(1,2);
	
		$rsUser = CUser::GetByLogin($arItem["user_login"])->GetNext();
		if($rsUser)
			$users[$arItem["ID"]] = $rsUser["ID"];
		else
		{
			$ID = $user->Add($arFields);
			$u = CUser::GetByID($ID);
			//$ar = $USER->SendPassword($USER->GetLogin(), $USER->GetParam("EMAIL"));
			$users[$arItem["ID"]] = $ID;
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
