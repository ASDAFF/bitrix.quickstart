<?
// Перенос пользователей CUsers и создание профилей
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

$user = new CUser;
$users = array();

/* количество записей */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."pfields_content`";	
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
    global $DB;
    
    $query = "SELECT * FROM ".$arResult["prefix"]."pfields_content LIMIT ".$left.", ".$right;
    $result = mysql_query($query, $link);
    while($arItem = mysql_fetch_assoc($result))
    {
        echo 'left='.$left;
        $u = CUser::GetList(($by="personal_country"), ($order="desc"), array("XML_ID" => $arItem["member_id"]))->GetNext(); 
        
        $USER_ID = $u['ID']; 
        
        $u['NAME'] = $arItem['field_1'];
        $u['LAST_NAME'] = $arItem['field_2'];
        $u['SECOND_NAME'] = $arItem['field_3'];
        $u['PERSONAL_COUNTRY'] = $arItem['field_27'];
        $u['PERSONAL_WWW'] = $arItem['field_31'];
        $u['PERSONAL_ICQ'] = $arItem['field_32'];
        $u['PERSONAL_GENDER'] = $arItem['field_33'] == 'u' ? 'F' : 'M';
        $u['PERSONAL_CITY'] = $arItem['field_34'];
        $u['WORK_COMPANY'] = $arItem['field_4'];
        $u['WORK_FAX'] = $arItem['field_6']; // год выпуска
        $u['WORK_DEPARTMENT'] = $arItem['field_7'];
         
        echo '<pre>'; print_r($u); echo '</pre>';   
        
        $user->Update($USER_ID, $u);
        $profile = CForumUser::GetByUSER_ID($USER_ID);
        echo '<pre>'; print_r($profile); echo '</pre>';
        if($profile)
        {
            $profile['INTERESTS'] = $arItem['field_35'];
            CForumUser::Update($profile['ID'], $profile);
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
