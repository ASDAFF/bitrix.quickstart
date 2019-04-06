<?

if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("socialnetwork");
$user = new CUser;
$users = array();
/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM {$arResult['prefix']}bp_friends WHERE {$arResult['prefix']}bp_friends.is_confirmed=1";
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

	$query = "SELECT {$arResult['prefix']}bp_friends.initiator_user_id , {$arResult['prefix']}bp_friends.friend_user_id  FROM {$arResult['prefix']}bp_friends WHERE {$arResult['prefix']}bp_friends.is_confirmed=1  LIMIT ".$left.", 10";	
	$result = mysql_query($query, $link);
	
	while($arItem = mysql_fetch_assoc($result))
	{
	
		$query = "SELECT *   FROM {$arResult['prefix']}users WHERE {$arResult['prefix']}users.ID={$arItem['initiator_user_id']}";	
		$r= mysql_query($query, $link); $u1 = mysql_fetch_assoc($r);
		$usr1 = CUser::GetByLogin($u1['user_login'])->GetNext();
		
		$query = "SELECT *   FROM {$arResult['prefix']}users WHERE {$arResult['prefix']}users.ID={$arItem['friend_user_id']}";	
		$r= mysql_query($query, $link); $u2 = mysql_fetch_assoc($r);
		$usr2 = CUser::GetByLogin($u2['user_login'])->GetNext();
	
		//print_r($usr2);
		
		CSocNetUserRelations::Add(array("FIRST_USER_ID" => $usr1["ID"], "SECOND_USER_ID" => $usr2["ID"], "RELATION" => SONET_RELATIONS_FRIEND));
		
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
