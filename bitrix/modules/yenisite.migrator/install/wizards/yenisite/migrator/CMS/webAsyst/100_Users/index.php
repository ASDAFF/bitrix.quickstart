<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

$user = new CUser;

$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."WBS_USER`";	
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

	$query = "SELECT * FROM ".$arResult["prefix"]."WBS_USER LIMIT ".$left.", 10";
	$result = mysql_query($query, $link);

	while($arItem = mysql_fetch_assoc($result))
	{
		$pass = "SHOP-SCRIPT-AbC".rand(300, 1999);


		//if($arItem["uid"] == "Super Administrator") 
			//$arFields["GROUP_ID"] = array(1,2);
			
        $query1 = "SELECT * FROM ".$arResult["prefix"]."CONTACT WHERE ".$arResult["prefix"]."C_ID=".$arItem["C_ID"];			
       	$result1 = mysql_query($query1, $link);
       	$arItem1 = mysql_fetch_assoc($result1);
       	
       	$arFields = Array(
		  "NAME"             => $arItem1["C_FIRSTNAME"],
 		  "LAST_NAME"             => $arItem1["C_LASTNAME"],
  		  "SECOND_NAME"             => $arItem1["C_MIDDLENAME"],
		  "EMAIL"             => $arItem1["C_EMAILADDRESS"],
		  "LOGIN"             => $arItem["U_ID"],
		  "LID"               => SITE_ID,
		  "ACTIVE"            => "Y",	  
		  "PASSWORD"          => $pass,
		  "CONFIRM_PASSWORD"  => $pass,
		  "XML_ID"		=> $arItem["C_ID"],
		  "PERSONAL_STREET" => $arItem1["C_HOMESTREET"],
 		  "PERSONAL_PHONE" => $arItem1["C_HOMEPHONE"],
  		  "PERSONAL_MOBILE" => $arItem1["C_MODILEPHONE"],
 		  "PERSONAL_CITY" => $arItem1["C_HOMECITY"],
  		  "PERSONAL_STATE" => $arItem1["C_HOMESTATE"],
  		  "PERSONAL_ZIP" => $arItem1["C_HOMEPOSTALCODE"],
 		  "PERSONAL_COUNTRY" => $arItem1["C_HOMECOUNTRY"],
   		  "PERSONAL_FAX" => $arItem1["C_PERSONALFAX"], 		  
 		  
		  "WORK_STREET" => $arItem1["C_WORKSTREET"],
 		  "WORK_PHONE" => $arItem1["C_WORKPHONE"],
 		  "WORK_CITY" => $arItem1["C_WORKCITY"],
  		  "WORK_STATE" => $arItem1["C_WORKSTATE"],
  		  "WORK_ZIP" => $arItem1["C_WORKPOSTALCODE"],
 		  "WORK_COUNTRY" => $arItem1["C_WORKCOUNTRY"],
 		  "WORK_COMPANY" => $arItem1["C_WORKCOMPANY"],
   		  "WORK_FAX" => $arItem1["C_WORKFAX"]

 		  
		  		  
		);

        //echo "<pre>"; print_r($arItem1); echo "</pre>";
        //die();

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
