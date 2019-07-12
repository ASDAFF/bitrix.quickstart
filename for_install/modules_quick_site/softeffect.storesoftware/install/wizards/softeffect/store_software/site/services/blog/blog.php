<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("blog") || !CModule::IncludeModule("idea"))
	return;

$SocNetGroupID = false;
$IdeaBlogGroupName = GetMessage("DEMO_BLOG_GROUP_NAME");
$IdeaBlogUrl = "blog_".WIZARD_SITE_ID;

$db_blog_group = CBlogGroup::GetList(array("ID" => "ASC"), array("SITE_ID" => WIZARD_SITE_ID, "NAME" => $IdeaBlogGroupName));
if ($res_blog_group = $db_blog_group->Fetch())
{
	 $SocNetGroupID = $res_blog_group["ID"];
}

if (!$SocNetGroupID)
	$SocNetGroupID = CBlogGroup::Add(
            array(
                "SITE_ID" => WIZARD_SITE_ID, 
                "NAME" => $IdeaBlogGroupName
            )
        );
//Blog
$blogID = CBlog::Add(
	Array(
		"NAME" =>  GetMessage("DEMO_BLOG_NAME")." [".WIZARD_SITE_ID."]",
		"DESCRIPTION" => "",
		"GROUP_ID" => $SocNetGroupID,
		"ENABLE_IMG_VERIF" => 'Y',
		"EMAIL_NOTIFY" => 'Y',
		"ENABLE_RSS" => "Y",
		"ALLOW_HTML" => "Y",
		"URL" => $IdeaBlogUrl,
		"ACTIVE" => "Y",
		"OWNER_ID" => $USER->GetID(),
		"=DATE_CREATE" => $DB->GetNowFunction(),
		"=DATE_UPDATE" => $DB->GetNowFunction(),
		//"SOCNET_GROUP_ID" => 0,
		"PERMS_POST" => Array("1" => BLOG_PERMS_READ, "2" => BLOG_PERMS_WRITE), 
		"PERMS_COMMENT" => array("1" => BLOG_PERMS_WRITE , "2" => BLOG_PERMS_WRITE),
		"PATH" => WIZARD_SITE_DIR.'blog/'.$IdeaBlogUrl.'/',
	)
);

//UF
$arUFIdByName = array();
$arStatusList = CIdeaManagment::getInstance()->GetStatusList();
foreach($arStatusList as $UF)
    $arUFIdByName[$UF["XML_ID"]] = $UF["ID"];

//Categories
$categoryID = array();
$categoryID[0][] = CBlogCategory::Add(Array("BLOG_ID" => $blogID, "NAME" => GetMessage("BLOG_DEMO_CATEGORY_1")));

//Post messages
$arBlogPostFields = array();
$arBlogPostFields[] = Array(
	"TITLE" => GetMessage("BLOG_DEMO_MESSAGE_TITLE_1"),
	"DETAIL_TEXT" => GetMessage("BLOG_DEMO_MESSAGE_BODY_1"),
	"DETAIL_TEXT_TYPE" => "text",
	"BLOG_ID" => $blogID,
	"AUTHOR_ID" => 1,
	"=DATE_CREATE" => $DB->GetNowFunction(),
	"=DATE_PUBLISH" => $DB->GetNowFunction(),
	"PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
	"ENABLE_TRACKBACK" => 'N',
	"ENABLE_COMMENTS" => 'Y',
	"CATEGORY_ID" =>  implode(",", $categoryID[0]),
	"UF_CATEGORY_CODE" => ToUpper(GetMessage("UF_CATEGORY_CODE_1")),
	"UF_STATUS" => $arUFIdByName["NEW"],
	"PERMS_POST" => Array(1 => BLOG_PERMS_READ, 2 => BLOG_PERMS_READ),
	"PERMS_COMMENT" => Array(1 => BLOG_PERMS_WRITE, 2 => BLOG_PERMS_WRITE),
	"PATH" => WIZARD_SITE_DIR.'blog/'.$IdeaBlogUrl.'/#post_code#/',
	"CODE" => "bitrix12_thebest",
);

$arBlogPostFields[] = Array(
        "TITLE" => GetMessage("BLOG_DEMO_MESSAGE_TITLE_2"),
        "DETAIL_TEXT" => GetMessage("BLOG_DEMO_MESSAGE_BODY_2"),
        "DETAIL_TEXT_TYPE" => "text",
        "BLOG_ID" => $blogID,
        "AUTHOR_ID" => 1,
        "=DATE_CREATE" => $DB->GetNowFunction(),
        "=DATE_PUBLISH" => $DB->GetNowFunction(),
        "PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
        "ENABLE_TRACKBACK" => 'N',
        "ENABLE_COMMENTS" => 'Y',
        "CATEGORY_ID" =>  implode(",", $categoryID[1]),
        "UF_CATEGORY_CODE" => ToUpper(GetMessage("UF_CATEGORY_CODE_2")),
        "UF_STATUS" => $arUFIdByName["PROCESSING"],
        "PERMS_POST" => Array(1 => BLOG_PERMS_READ, 2 => BLOG_PERMS_READ),
        "PERMS_COMMENT" => Array(1 => BLOG_PERMS_WRITE, 2 => BLOG_PERMS_WRITE),
        "PATH" => WIZARD_SITE_DIR.'blog/'.$IdeaBlogUrl.'/#post_code#/',
        "CODE" => "abobe_photoshop",
);

$arBlogPostId = array();
foreach($arBlogPostFields as $BlogPostFields)
    $arBlogPostId[] = CBlogPost::Add($BlogPostFields);

foreach($arBlogPostId as $key=>$BlogPostId)
{
    if(!is_array($categoryID[$key])) continue;
    
    foreach($categoryID[$key] as $v)
		CBlogPostCategory::Add(Array("BLOG_ID" => $blogID, "POST_ID" => $BlogPostId, "CATEGORY_ID" => $v));
}
//Post Comments
$arBlogCommentFields = array();
$arBlogCommentFields[] = Array(
        "TITLE" => '',
        "POST_TEXT" => GetMessage("BLOG_DEMO_COMMENT_BODY_1"),
        "BLOG_ID" => $blogID,
        "POST_ID" => $arBlogPostId[0],
        "PARENT_ID" => 0,
        "AUTHOR_ID" => 1,
        "DATE_CREATE" => ConvertTimeStamp(false, "FULL"), 
        "AUTHOR_IP" => "192.168.0.108",
        "PATH" => WIZARD_SITE_DIR."blog/#post_id#/?commentId=#comment_id###comment_id#"
);

$arBlogCommentFields[] = Array(
        "TITLE" => '',
        "POST_TEXT" => GetMessage("BLOG_DEMO_COMMENT_BODY_2"),
        "BLOG_ID" => $blogID,
        "POST_ID" => $arBlogPostId[1],
        "PARENT_ID" => 0,
        "AUTHOR_ID" => 1,
        "DATE_CREATE" => ConvertTimeStamp(false, "FULL"), 
        "AUTHOR_IP" => "192.168.0.108",
        "PATH" => WIZARD_SITE_DIR."blog/#post_id#/?commentId=#comment_id###comment_id#"
);

$arCommentId = array();
foreach($arBlogCommentFields as $BlogCommentFields)
    $arCommentId[] = CBlogComment::Add($BlogCommentFields);

//CIdeaManagment::getInstance()->IdeaComment($arCommentId[0])->Bind();
//CIdeaManagment::getInstance()->IdeaComment($arCommentId[2])->Bind();
?>