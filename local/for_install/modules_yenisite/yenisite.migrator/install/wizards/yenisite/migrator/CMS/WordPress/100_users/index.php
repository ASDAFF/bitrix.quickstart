<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');
CModule::IncludeModule("forum");

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




		$pass = "WP-AbC".rand(300, 1999);
		
		$query = "SELECT *  FROM ".$arResult["prefix"]."usermeta WHERE {$arResult['prefix']}usermeta.user_id={$arItem['ID']} AND meta_key='first_name' ";
		$res = mysql_query($query, $link);
		$arI = mysql_fetch_assoc($res);
		$arItem['first_name'] = $arI['meta_value'];
		
		$query = "SELECT *  FROM ".$arResult["prefix"]."usermeta WHERE {$arResult['prefix']}usermeta.user_id={$arItem['ID']} AND meta_key='last_name' ";
		$res = mysql_query($query, $link);
		$arI = mysql_fetch_assoc($res);
		$arItem['last_name'] = $arI['meta_value'];
		
		
		$arFields = Array(
		  "NAME"             => $arItem["first_name"],
		  "LAST_NAME"             => $arItem["last_name"],
		  "EMAIL"             => $arItem["user_email"],
		  "LOGIN"             => $arItem["user_login"],
		  //"LID"               => SITE_ID,
		  "ACTIVE"            => "Y",	  
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass,
		  "XML_ID"		=> $arItem["ID"]
		);




		$ID = $user->Add($arFields);


		

		$u = CUser::GetByID($ID);
		//$ar = $USER->SendPassword($USER->GetLogin(), $USER->GetParam("EMAIL"));

	}



	/* Увеличиваем левую и правую границу */
	$left += 10;
	$right += 10;

}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>
