<?
// ������� ������������� CUsers � �������� ��������
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
            "NAME" => 'IPBoard',	 
            "XML_ID"   => 'IPBoard'
        );
    }
    $ID = CForumGroup::Add($arFields);
}

$user = new CUser;
$users = array();
/* ���������� ������� */
$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."members`";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

/* ���� ����� ������� ������ ���������� ��������� - �������� ������� ��������� ��� */
//$count["CNT"] = -1;
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
    global $DB;
    
    $query = "SELECT * FROM ".$arResult["prefix"]."members LIMIT ".$left.", ".$right;
    $result = mysql_query($query, $link);
    while($arItem = mysql_fetch_assoc($result))
    {
        $pass = "ipb-AbC".rand(300, 1999);

        $arFields = Array(
          "NAME"              => $arItem["members_display_name"],
          "EMAIL"             => $arItem["email"],
          "LOGIN"             => $arItem["name"],
          "LID"               => SITE_ID,
          "ACTIVE"            => "Y",	  
          "PASSWORD"          => $pass,
          "CONFIRM_PASSWORD"  => $pass,
          "DATE_REGISTER"     => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['joined']),
          "LAST_LOGIN"        => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['last_visit']),
          "LAST_ACTIVITY_DATE"  => $arItem['last_activity'],
          "XML_ID"		      => $arItem["member_id"],
          "PERSONAL_BIRTHDAY" => date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), mktime(0,0,0,$arItem['bday_month'],$arItem['bday_day'],$arItem['bday_year']))
        );
        //if($arItem["ID_GROUP"] == 1) 
            //$arFields["GROUP_ID"] = array(1,2);
        
        $rsUser = CUser::GetByLogin($arItem["user_login"])->GetNext();
        if($rsUser)
            $users[$arItem["member_id"]] = $rsUser["ID"];
        else
        {
            $ID = $user->Add($arFields);
            $u = CUser::GetByID($ID);
            //$ar = $USER->SendPassword($USER->GetLogin(), $USER->GetParam("EMAIL"));
            $users[$arItem["member_id"]] = $ID;
            
            $arFFields = Array(
                'USER_ID'        => $ID,
                'DESCRIPTION'    => $arItem['members_display_name'],
                'ALLOW_POST'     => 'Y',
                'LAST_VISIT'     => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['last_visit']),
                'DATE_REG'       => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['joined']),
                'SHOW_NAME'      => 'N',
                'HIDE_FROM_ONLINE' => 'N',
                'POINTS'         => $arItem['thanks_point']
            );
            CForumUser::Add($arFFields);
        }
    } 
    /* ����������� ����� � ������ ������� */
    $left += 10;
    $right += 10;
}
/* ������������� ����� � ������ ������� */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);
?>
