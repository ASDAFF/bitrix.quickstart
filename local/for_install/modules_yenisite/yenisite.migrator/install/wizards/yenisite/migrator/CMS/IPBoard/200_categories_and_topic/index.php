<?
function create_forum($arItem, $parent_id, $wizard)
{
    global $APPLICATION;
    global $DB;
    // echo '<br/>create_forum!1<br/>';
    $find_forum = CForumNew::GetList(Array(), Array('XML_ID'=>$arItem['id']))->GetNext();
    // echo 'wtf?...'.$find_forum['ID'].'!<br/>';
    if($find_forum['ID'] > 0)
    {
        // echo $arItem['id'].' --- created is '.$find_forum['ID'].'<br/>';
    }
    else
    {
        $u = CUser::GetList(($by="personal_country"), ($order="desc"), array("XML_ID" => $arItem["last_poster_id"]))->GetNext(); 
        // echo 'llll<br/>';
        $arFields = array(
            'NAME' => $arItem['name'],
            "ACTIVE" => "Y",
            'DESCRIPTION' => $arItem['description'],
            // 'TOPICS' => $arItem['topics'],
            // 'POSTS' => $arItem['posts'],
            'SORT' => $arItem['position'],
            'LAST_POST_DATE' => date($DB->DateFormatToPHP(CLang::GetDateFormat("FULL")), $arItem['last_post']),
            'LAST_POSTER_ID' => $u['ID'],
            'LAST_POSTER_NAME' => $arItem['last_poster_name'],
            "GROUP_ID" => array(1 => "Y"),  // ???
            "SITES" => array(
		       $wizard->GetVar("siteID") => "/url/"),
            'XML_ID'  => $arItem['id'],
            'FORUM_GROUP_ID' => ($parent_id > 0) ? $parent_id : 0
        );
        // echo 'before add<br/>';
        $NewForumID = CForumNew::Add($arFields);
        // echo 'newforum = '.$NewForumID.'<br/>';
        if (IntVal($NewForumID)<=0)
            if($ex = $APPLICATION->GetException())
            {
                $StrError = $ex->GetString();
                // echo 'strerror = '.$StrError;
                //die('kill create forum');
                return $StrError;
            }
            else
            {   
                // echo 'unknown<br/>';
            }
        else
            return $NewForumID;
    }
    return $find_forum;
}

function create_forum_group($arItem, $parent_id)
{
    global $APPLICATION;
    // echo '<br/>create_forum_group!1<br/>';
    // проверяем наличие такой же группы
    $find_group = CForumGroup::GetList(Array(), Array('SORT'=>$arItem['id']))->GetNext();
    if(!($find_group['ID'] > 0))
    {
        // echo 'parid='.$parent_id.'<br/>';
        $arFields = array("SORT" => $arItem['id'], 'PARENT_ID'=> $parent_id > 0 ? $parent_id : 0); // используем SORT, вместо XML_ID
        $arSysLangs = array("ru", "en");

        for ($i = 0; $i < count($arSysLangs); $i++)
            $arFields["LANG"][] = array(
                'NAME' => $arItem['name'],
                'DESCRIPTION' => $arItem['description'],
                "LID" => $arSysLangs[$i]
            );

        
        $NewGroupID = CForumGroup::Add($arFields);
        // echo '002 newgrid='.$NewGroupID.'<br/>';
        if (IntVal($NewGroupID)<=0)
            if($ex = $APPLICATION->GetException())
            {
                $StrError = $ex->GetString();
                // echo 'strerror = '.$StrError;
                //die('kill create group');
                return $StrError;
            }
        else
            // echo 'newgroup:'; print_r($NewGroupID); // echo '<br/>';
            return $NewGroupID;
    }
    else
        // echo $arItem['id'].' -- created <br/>';
    
    return $find_group;
}

function create_wtf($arItem, $arResult, $link, $parent_id, $wizard)
{
    // echo '<br/>create_wtf!1<br/>';
    // поиск детей
    $query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."forums` WHERE parent_id = ".$arItem['id'];	
    $count = mysql_query($query, $link);
    $count = mysql_fetch_assoc($count);
  
    if($count['CNT'] > 0)
    {
        //группа
        return create_forum_group($arItem, $parent_id);
    }
    else
    {
        //форум
        return create_forum($arItem, $parent_id, $wizard);
    }
}

function recursive_find_parent($arItem, $arResult, $link, $wizard)
{
    // echo '<br/>recursive_find_parent!<br/>';
    $find_parent_group = CForumGroup::GetList(Array(), Array('SORT'=>$arItem['parent_id']))->GetNext();
    // если группа существует
    if($find_parent_group['ID'] > 0)
    {   
        // echo $arItem['name'].' -> '.$arItem['parent_id'].'='.$find_parent_group['ID'].'  it\'s created<br/>';
        return $find_parent_group['ID'];
    }
    
    $query = "SELECT * FROM ".$arResult["prefix"]."forums WHERE id = ".$arItem['parent_id'];
    $result = mysql_query($query, $link);
    if($arParent = mysql_fetch_assoc($result))
    {
        // поиск родителей
        if($arParent['parent_id'] > 0)
        {
            $parent_id = recursive_find_parent($arParent, $arResult, $link, $wizard);
        }        
        return create_wtf($arItem, $arResult, $link, $parent_id, $wizard);
    }
}

// Перенос категорий
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

CModule::IncludeModule("forum");

global $APPLICATION ;

$user = new CUser;
$users = array();

/* количество записей */

$query = "SELECT COUNT(*) as CNT FROM `".$arResult["prefix"]."forums`";	
$count = mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

/* Если левая граница больше количества элементов - обнуляем границы завершаем шаг */

//$count["CNT"] = -1;
// echo $count["CNT"].' | '.$left.'<br/>';
if($left > $count["CNT"])
{	
    // echo $left.' ---- '.$count["CNT"] ;
    //die();
    echo "{$left} из {$count['CNT']}";
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
    
    $query = "SELECT * FROM `".$arResult["prefix"]."forums` ORDER BY `parent_id` LIMIT ".$left.", ".$right;
    $result = mysql_query($query, $link);
    while($arItem = mysql_fetch_assoc($result))
    {
        // echo '<pre>'; print_r($arItem); // echo '</pre>';
        if($arItem['parent_id'] > 0)
        {
            $parent_id = recursive_find_parent($arItem, $arResult, $link, $wizard);
            // echo $parent_id.' | '.$count["CNT"].' | '.$left.'<br/>';
            $res = create_wtf($arItem, $arResult, $link, $parent_id, $wizard);
            // echo 'res = '.$res.'<br/>';
        }
        elseif($arItem['parent_id'] == -1)
        {
            // echo create_forum_group($arItem, 0);           
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
