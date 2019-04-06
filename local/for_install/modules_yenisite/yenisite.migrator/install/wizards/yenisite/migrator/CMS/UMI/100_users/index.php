<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

$user = new CUser;

$users = array();

$query = "SELECT id FROM `".$arResult["prefix"]."object_types` WHERE name='i18n::object-type-users-user'";
$res = mysql_query($query, $link);
$res = mysql_fetch_assoc($res);
$id = $res['id'];

$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."objects` WHERE type_id={$id}";
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
	
	// Read all users
	$query = "SELECT id FROM `".$arResult["prefix"]."objects` WHERE type_id={$id} LIMIT ".$left.", ".$right;
	//$query = "SELECT id FROM `".$arResult["prefix"]."objects` WHERE type_id={$id}";
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$pass = "UMI-AbC".rand(300, 1999);
		
		// Read all user properties
		$query = "SELECT * FROM `".$arResult["prefix"]."object_content` WHERE obj_id={$arItem['id']}";
		$res = mysql_query($query, $link);
		
		while($arI = mysql_fetch_assoc($res))
		{
			switch($arI['field_id'])
			{
				case 45:
					$arItem['user_login'] = $arI['varchar_val'];
					break;
					
				case 12:
					$arItem['user_email'] = $arI['varchar_val'];
					break;
					
				case 9:
					$arItem['first_name'] = $arI['varchar_val'];
					break;
					
				case 11:
					$arItem['last_name'] = $arI['varchar_val'];
					break;
			}
		}
			
		$arFields = Array(
		  "NAME"             	=> $arItem["first_name"],
		  "LAST_NAME"			=> $arItem["last_name"],
		  "EMAIL"             	=> $arItem["user_email"],
		  "LOGIN"             	=> $arItem["user_login"],
		  //"LID"               => SITE_ID,
		  "ACTIVE"            	=> "Y",	  
		  "PASSWORD"          	=> $pass,
		  "CONFIRM_PASSWORD"  	=> $pass,
		  "XML_ID"				=> $arI['obj_id'],
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
