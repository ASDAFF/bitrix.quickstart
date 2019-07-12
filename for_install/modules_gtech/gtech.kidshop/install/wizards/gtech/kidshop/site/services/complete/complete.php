<?defined("B_PROLOG_INCLUDED")&&B_PROLOG_INCLUDED or die();

// copy tmp replaced files from tmp to site and delete tmp files
CopyDirFiles(
	WIZARD_SITE_PATH."tmp-".WIZARD_SITE_ID,
	WIZARD_SITE_PATH,
	$rewrite = true,
	$recursive = true,
	$delete_after_copy = true
);

if(CModule::IncludeModule("forum")){
  $arForumReviews = CForumNew::GetList(array(),array("TEXT"=>"Отзывы к товарам"))->Fetch();
  if(!$arForumReviews["ID"]){
  $arFields = Array(
    "NAME" => GetMessage("FORUM_REVIEWS_NAME"),
    "GROUP_ID" => array(1 => "Y", 2 => "I"),
    "ACTIVE" => "Y",
    "ALLOW_HTML" => "N",
    "ALLOW_NL2BR" => "N",
    "ALLOW_ANCHOR" => "N",
    "ALLOW_BIU" => "N",
    "ALLOW_IMG" => "N",
    "ALLOW_LIST" => "N",
    "ALLOW_QUOTE" => "N",
    "ALLOW_CODE" => "N",
    "ALLOW_FONT" => "N",
    "ALLOW_UPLOAD" => "N",
    "MODERATION" => "N",
    "ASK_GUEST_EMAIL" => "Y",
    "SITES" => array(
      "s1" => "/forum/#FORUM_ID#/#TOPIC_ID#/")
    );
  $res = CForumNew::Add($arFields);
  }
}

?>