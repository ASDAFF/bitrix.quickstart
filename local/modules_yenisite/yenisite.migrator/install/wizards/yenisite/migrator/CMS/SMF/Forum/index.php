<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."members`";	
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
	$query = "SELECT * FROM ".$arResult["prefix"]."members LIMIT ".$left.", ".$right;
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$pass = "SMF-AbC".rand(300, 1999);

		$arFields = Array(
		  "NAME"             => $arItem["realName"],
		  "EMAIL"             => $arItem["emailAddress"],
		  "LOGIN"             => $arItem["memberName"],
		  "LID"               => SITE_ID,
		  "ACTIVE"            => "Y",	  
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass,
		  "XML_ID"		=> $arItem["ID_MEMBER"]
		);
		if($arItem["ID_GROUP"] == 1) 
			$arFields["GROUP_ID"] = array(1,2);
	
		$rsUser = CUser::GetByLogin($arItem["memberName"])->GetNext();
		if($rsUser)
			$users[$arItem["ID_MEMBER"]] = $rsUser["ID"];
		else
		{
			$ID = $user->Add($arFields);
			$u = CUser::GetByID($ID);
			$ar = $USER->SendPassword($USER->GetLogin(), $USER->GetParam("EMAIL"));
			$users[$arItem["ID_MEMBER"]] = $ID;
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
