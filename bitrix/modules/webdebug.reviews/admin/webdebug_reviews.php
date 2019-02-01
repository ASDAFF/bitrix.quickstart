<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

if (!CModule::IncludeModule("webdebug.reviews")) {
	die("Module not installed!");
}

$ModuleRights = $APPLICATION->GetGroupRight("webdebug.reviews");
if($ModuleRights=="D") {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$sTableID = "WebdebugReviews";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

// Filter
function CheckFilter() {
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $f;
	return count($lAdmin->arFilterErrors)==0;
}
$FilterArr = Array(
	"find_id",
	"find_iblock_id",
	"find_element_id",
	"find_moderated",
	"find_site_id",
	"find_user_id",
	"find_name",
	"find_email",
	"find_email_public",
	"find_www",
	"find_text_plus",
	"find_text_minus",
	"find_text_comments",
	"find_vote_0",
	"find_vote_1",
	"find_vote_2",
	"find_vote_3",
	"find_vote_4",
	"find_vote_5",
	"find_vote_6",
	"find_vote_7",
	"find_vote_8",
	"find_vote_9",
);
$lAdmin->InitFilter($FilterArr);
if (CheckFilter()) {
	$arFilter = Array(
		"ID" => $find_id,
		"IBLOCK_ID" => $find_iblock_id,
		"ELEMENT_ID" => $find_element_id,
		"MODERATED" => $find_moderated,
		"SITE_ID" => $find_site_id,
		"USER_ID" => $find_user_id,
		"%NAME" => $find_name,
		"%EMAIL" => $find_email,
		"EMAIL_PUBLIC" => $find_email_public,
		"%WWW" => $find_www,
		"%TEXT_PLUS" => $find_text_plus,
		"%TEXT_MINUS" => $find_text_minus,
		"%TEXT_COMMENTS" => $find_text_comments,
		"VOTE_0" => $find_vote_0,
		"VOTE_1" => $find_vote_1,
		"VOTE_2" => $find_vote_2,
		"VOTE_3" => $find_vote_3,
		"VOTE_4" => $find_vote_4,
		"VOTE_5" => $find_vote_5,
		"VOTE_6" => $find_vote_6,
		"VOTE_7" => $find_vote_7,
		"VOTE_8" => $find_vote_8,
		"VOTE_9" => $find_vote_9,
	);
}

$IBlockTypes = array();
if (CModule::IncludeModule("iblock")) {
	$resIBlock = CIBlock::GetList(array(),array());
	while ($arIBlock = $resIBlock->GetNext(false,false)) {
		$IBlockTypes[$arIBlock["ID"]] = $arIBlock["IBLOCK_TYPE_ID"];
	}
}

// Processing with actions
if($lAdmin->EditAction()) {
	foreach($FIELDS as $ID=>$arFields) {
		if(!$lAdmin->IsUpdated($ID)) continue;
		$DB->StartTransaction();
		$ID = IntVal($ID);
		if(($rsData = CWebdebugReviews::GetByID($ID)) && ($arData = $rsData->Fetch())) {
			foreach($arFields as $key=>$value) $arData[$key]=$value;
			if(!CWebdebugReviews::Update($ID, $arData)) {
				$lAdmin->AddGroupError(GetMessage("rub_save_error"), $ID);
				$DB->Rollback();
			}
		} else {
			$lAdmin->AddGroupError(GetMessage("rub_save_error")." ".GetMessage("rub_no_rubric"), $ID);
			$DB->Rollback();
		}
		$DB->Commit();
	}
}
if(($arID = $lAdmin->GroupAction())) {
  if($_REQUEST['action_target']=='selected') {
    $rsData = CWebdebugReviews::GetList(array($by=>$order), $arFilter);
    while($arRes = $rsData->Fetch()) $arID[] = $arRes['ID'];
  }
  foreach($arID as $ID) {
    if(strlen($ID)<=0) continue;
    $ID = IntVal($ID);
    switch($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!CWebdebugReviews::Delete($ID)) {
					$DB->Rollback();
					$lAdmin->AddGroupError(GetMessage("rub_del_err"), $ID);
				}
				$DB->Commit();
				break;
			case "set_y":
				CWebdebugReviews::Update($ID, array("MODERATED"=>"Y"));
				break;
			case "set_n":
				CWebdebugReviews::Update($ID, array("MODERATED"=>"N"));
				break;
    }
  }
}

// Get items list
$rsData = CWebdebugReviews::GetList(array($by => $order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("rub_nav")));

// Add headers
$lAdmin->AddHeaders(array(
  array(
	  "id" => "ID",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_ID"),
    "sort" => "id",
    "align" => "right",
    "default" => true,
  ),
  array(
	  "id" => "IBLOCK_ID",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_IBLOCK_ID"),
    "sort" => "iblock_id",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "ELEMENT_ID",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_ELEMENT_ID"),
    "sort" => "element_id",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"MODERATED",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_MODERATED"),
    "sort" => "moderated",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"SITE_ID",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_SITE_ID"),
    "sort" => "site_id",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"USER_ID",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_USER_ID"),
    "sort" => "user_id",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"NAME",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_NAME"),
    "sort" => "name",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"EMAIL",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_EMAIL"),
    "sort" => "email",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"EMAIL_PUBLIC",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_EMAIL_PUBLIC"),
    "sort" => "email_public",
		"align" => "left",
    "default" => false,
  ),
  array(
	  "id" =>"WWW",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_WWW"),
    "sort" => "www",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"DATETIME",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_DATETIME"),
    "sort" => "www",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"TEXT_PLUS",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_TEXT_PLUS"),
    "sort" => "text_plus",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"TEXT_MINUS",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_TEXT_MINUS"),
    "sort" => "text_minus",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" =>"TEXT_COMMENTS",
    "content" => GetMessage("WEBDEBUG_REVIEWS_HEADER_TEXT_COMMENTS"),
    "sort" => "text_comments",
		"align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "VOTE_0",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_0"),
    "sort" => "vote_0",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "VOTE_1",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_1"),
    "sort" => "vote_1",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "VOTE_2",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_2"),
    "sort" => "vote_2",
    "align" => "left",
    "default" => true,
  ),
  array(
	  "id" => "VOTE_3",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_3"),
    "sort" => "vote_3",
    "align" => "left",
    "default" => false,
  ),
  array(
	  "id" => "VOTE_4",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_4"),
    "sort" => "vote_4",
    "align" => "left",
    "default" => false,
  ),
  array(
	  "id" => "VOTE_5",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_5"),
    "sort" => "vote_5",
    "align" => "left",
    "default" => false,
  ),
  array(
	  "id" => "VOTE_6",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_6"),
    "sort" => "vote_6",
    "align" => "left",
    "default" => false,
  ),
  array(
	  "id" => "VOTE_7",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_7"),
    "sort" => "vote_7",
    "align" => "left",
    "default" => false,
  ),
  array(
	  "id" => "VOTE_8",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_8"),
    "sort" => "vote_8",
    "align" => "left",
    "default" => false,
  ),
  array(
	  "id" => "VOTE_9",
    "content" => COption::GetOptionString("webdebug.reviews", "vote_name_9"),
    "sort" => "vote_9",
    "align" => "left",
    "default" => false,
  ),
));

$arSites = array();
$resSite = CSite::GetList($siteBy="sort",$siteOrder="asc");
while ($arSite = $resSite->GetNext()) {
	$arSites[$arSite["ID"]] = $arSite;
}

$arIBlocks = array();
if (CModule::IncludeModule("iblock")) {
	$resIBlock = CIBlock::GetList(array("ID"=>"ASC"));
	while ($arIBlock = $resIBlock->GetNext(false,false)) {
		$arIBlocks[$arIBlock["ID"]] = $arIBlock["NAME"];
	}
}

// Build items list
while ($arRes = $rsData->NavNext(true, "f_")) {
  $row = &$lAdmin->AddRow($f_ID, $arRes); 
	// ID
	$row->AddViewField("ID", $f_ID);
	// IBLOCK_ID
	$row->AddViewField("IBLOCK_ID", "[".$f_IBLOCK_ID."] ".$arIBlocks[$f_IBLOCK_ID]);
	// ELEMENT_ID
	$ElementHTML = $f_ELEMENT_ID;
	if (CModule::IncludeModule("iblock")) {
		$resElement = CIBlockElement::GetList(false, array("IBLOCK_ID"=>$f_IBLOCK_ID,"ID"=>$f_ELEMENT_ID), false, false, array("IBLOCK_ID","NAME"));
		if ($arElement = $resElement->GetNext(false,false)) {
			$ItemURL_Admin = "/bitrix/admin/iblock_element_edit.php?WF=Y&ID=".$f_ELEMENT_ID."&lang=".LANGUAGE_ID."&IBLOCK_ID=".$f_IBLOCK_ID."&type=".$IBlockTypes[$f_IBLOCK_ID];
			$ItemURL_Public = CWebdebugReviews::GetItemURL($f_ELEMENT_ID);
			$ElementHTML = "[<a href='{$ItemURL_Admin}'>".$f_ELEMENT_ID."</a>]";
			if ($ItemURL_Public) {
				$ElementHTML .= "<a href='{$ItemURL_Public}' target='_blank'>".$arElement["NAME"]."</a>";
			} else {
				$ElementHTML .= ' '.$arElement["NAME"];
			}
		}
	}
	$row->AddViewField("ELEMENT_ID", $ElementHTML);
  // MODERATED
  $row->AddViewField("MODERATED", "<div class='webdebug-reviews-moderated-".($f_MODERATED=="Y"?"Y":"N")."'>".($f_MODERATED=="Y"?GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED_Y"):GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED_N"))."</div>");
	$ModeratedHTML = "";
	$ModeratedHTML .= "<div><label><input type='radio' name='FIELDS[".$f_ID."][MODERATED]' value='Y'".($f_MODERATED=="Y"?" checked='checked'":"")." />".GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED_Y")."</label></div>";
	$ModeratedHTML .= "<div><label><input type='radio' name='FIELDS[".$f_ID."][MODERATED]' value='N'".($f_MODERATED!="Y"?" checked='checked'":"")." />".GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED_N")."</label></div>";
	$row->AddEditField("MODERATED", $ModeratedHTML);
	// SITE_ID
	$row->AddViewField("SITE_ID", "[".$f_SITE_ID."] ".$arSites[$f_SITE_ID]["NAME"]);
	// USER_ID
	$row->AddViewField("USER_ID", $f_USER_ID ? ("[".$f_USER_ID."] ".CUser::GetFirstName($f_USER_ID)) : "-");
	// NAME
  $row->AddInputField("NAME",array("SIZE" => "30"));
  $row->AddViewField("NAME", $f_NAME);
  // EMAIL
  $row->AddInputField("EMAIL",array("SIZE" => "30"));
  $row->AddViewField("EMAIL", "<a href='mailto:{$f_EMAIL}'>{$f_EMAIL}</a>");
  // WWW
	$WWW = $f_WWW;
	if ($WWW && substr($WWW,0,5)!="http:" && substr($WWW,0,6)!="https:") {
		$WWW = "http://".$WWW;
	}
  $row->AddInputField("WWW",array("SIZE" => "30"));
  $row->AddViewField("WWW", "<a href='{$WWW}'>{$WWW}</a>");
  // EMAIL_PUBLIC
  $row->AddCheckField("EMAIL_PUBLIC");
	// DATETIME
	$row->AddViewField("DATETIME", CDatabase::FormatDate($f_DATETIME, "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")));
	$row->AddCalendarField("DATETIME", array("size"=>"15"));
  // TEXT_PLUS
  $row->AddViewField("TEXT_PLUS", $f_TEXT_PLUS);
	$sHTML = "<textarea cols='30' rows='4'name='FIELDS[".$f_ID."][TEXT_PLUS]'>".$f_TEXT_PLUS."</textarea>";
  $row->AddEditField("TEXT_PLUS", $sHTML);
  // TEXT_MINUS
  $row->AddViewField("TEXT_MINUS", $f_TEXT_MINUS);
	$sHTML = "<textarea cols='30' rows='4'name='FIELDS[".$f_ID."][TEXT_MINUS]'>".$f_TEXT_MINUS."</textarea>";
  $row->AddEditField("TEXT_MINUS", $sHTML);
  // TEXT_COMMENTS
  $row->AddViewField("TEXT_COMMENTS", $f_TEXT_COMMENTS);
	$sHTML = "<textarea cols='30' rows='4'name='FIELDS[".$f_ID."][TEXT_COMMENTS]'>".$f_TEXT_COMMENTS."</textarea>";
  $row->AddEditField("TEXT_COMMENTS", $sHTML);
	// VOTES
	for ($i=0; $i<10; $i++) {
		$VoteID = "f_VOTE_".$i;
		$VoteValue = $$VoteID;
		$row->AddViewField("VOTE_".$i, "<span class='webdebug-reviews-item-stars'><span class='webdebug-reviews-item-star-{$VoteValue}'></span></span>");
		$VoteHTML = "<div class='webdebug-reviews-rating' id='webdebug-reviews-rating-{$f_ID}-{$i}'>";
		$VoteHTML .= "<input type='hidden' value='".$VoteValue."' />";
		for ($j=1; $j<=5; $j++) {
			$Checked = "";
			if ($VoteValue==$j) $Checked = " checked='checked'";
			$VoteHTML .= '<label><input type="radio" name="FIELDS['.$f_ID.'][VOTE_'.$i.']" value="'.$j.'"'.$Checked.' />'.$j.'</label><br/>';
		}
		$VoteHTML .= "</div>";
		$row->AddEditField("VOTE_".$i, $VoteHTML);
	}
	// Build context menu
  $arActions = Array();
	if ($f_MODERATED=="Y") {
		$arActions[] = array(
			"ICON" => "edit",
			"DEFAULT"=>false,
			"TEXT" => GetMessage("WEBDEBUG_REVIEWS_CONTEXT_SET_N"),
			"ACTION" => $lAdmin->ActionDoGroup($f_ID, "set_n"),
		);
	} else {
		$arActions[] = array(
			"ICON" => "edit",
			"DEFAULT"=>false,
			"TEXT" => GetMessage("WEBDEBUG_REVIEWS_CONTEXT_SET_Y"),
			"ACTION" => $lAdmin->ActionDoGroup($f_ID, "set_y"),
		);
	}
	$arActions[] = array(
		"SEPARATOR" => true,
	);
	$arActions[] = array(
		"ICON" => "delete",
		"DEFAULT"=>false,
		"TEXT" => GetMessage("WEBDEBUG_REVIEWS_CONTEXT_DELETE"),
		"ACTION" => "if(confirm('".GetMessage('WEBDEBUG_REVIEWS_CONTEXT_DELETE_CONFIRM')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
	);
  $row->AddActions($arActions);
}

$lAdmin->AddAdminContextMenu(array());

// List Footer
$lAdmin->AddFooter(
  array(
    array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
    array("counter"=>true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
  )
);
$lAdmin->AddGroupActionTable(Array(
  "delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
  "set_y" => GetMessage("WEBDEBUG_REVIEWS_ADMINLIST_ACTION_SET_Y"),
  "set_n" => GetMessage("WEBDEBUG_REVIEWS_ADMINLIST_ACTION_SET_N"),
));

global $APPLICATION;
$Action = $APPLICATION->GetPopupLink(
	array(
		"URL" => "/bitrix/admin/wd_reviews2_v1_import.php?public_add=Y&bxpublic=Y&interface={$InterfaceID}&target={$arParams['TARGET']}&lang={$Lang}",
		"PARAMS" => array(
			"width" => 700,
			'height' => 350,
			'resizable' => true,
		),
	)
);
$aContext = array(
  array(
    "TEXT" => GetMessage("WEBDEBUG_REVIEWS_BTN_IMPORT_TITLE"),
    "LINK" => "javascript:".$Action,
    "ICON" => "btn_new",
  ),
);
$lAdmin->AddAdminContextMenu($aContext);

// Start output
$lAdmin->CheckListMode();
$APPLICATION->SetTitle(GetMessage("WEBDEBUG_REVIEWS_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

// Output filter
$oFilter = new CAdminFilter(
  $sTableID."_filter",
  array(
		GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_SITE_ID"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_IBLOCK_ID"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_ELEMENT_ID"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_USER_ID"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_NAME"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_EMAIL"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_EMAIL_PUBLIC"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_WWW"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_TEXT_PLUS"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_TEXT_MINUS"),
		GetMessage("WEBDEBUG_REVIEWS_FILTER_TEXT_COMMENTS"),
		COption::GetOptionString("webdebug.reviews", "vote_name_0", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_1", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_2", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_3", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_4", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_5", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_6", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_7", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_8", ""),
		COption::GetOptionString("webdebug.reviews", "vote_name_9", ""),
  )
);
?>

<?
function Webdebug_Reviews_SelectBoxFromArrayGrouped($strBoxName, $db_array, $strSelectedVal = "", $strDetText = "", $field1="class='typeselect'", $go=false, $form="form1") {
	if($go) {
		$strReturnBox = "<script type=\"text/javascript\">\n".
			"function ".$strBoxName."LinkUp()\n".
			"{var number = document.".$form.".".$strBoxName.".selectedIndex;\n".
			"if(document.".$form.".".$strBoxName.".options[number].value!=\"0\"){ \n".
			"document.".$form.".".$strBoxName."_SELECTED.value=\"yes\";\n".
			"document.".$form.".submit();\n".
			"}}\n".
			"</script>\n";
		$strReturnBox .= '<input type="hidden" name="'.$strBoxName.'_SELECTED" id="'.$strBoxName.'_SELECTED" value="">';
		$strReturnBox .= '<select '.$field1.' name="'.$strBoxName.'" id="'.$strBoxName.'" size="1" onchange="'.$strBoxName.'LinkUp()" class="typeselect">';
	} else {
		$strReturnBox = '<select '.$field1.' name="'.$strBoxName.'" id="'.$strBoxName.'" size="1">';
	}

	if($strDetText <> '') $strReturnBox .= '<option value="">'.$strDetText.'</option>';

	foreach ($db_array as $group_name => $group_data) {
		if (is_array($group_data)) {
			$strReturnBox .= '<optgroup label="'.$group_name.'">';
			foreach ($group_data as $item_value => $item_name) {
				$strReturnBox .= '<option';
				if(strcasecmp($item_value, $strSelectedVal) == 0) $strReturnBox .= ' selected="selected"';
				$strReturnBox .= ' value="'.htmlspecialchars($item_value).'">'.htmlspecialchars($item_name).'</option>';
			}
			$strReturnBox .= '</optgroup>';
		}
	}
	return $strReturnBox.'</select>';
}
?>

<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
	<?$oFilter->Begin();?>
	<tr>
		<td><b><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_ID")?>:</b></td>
		<td><input type="text" size="25" name="find_id" value="<?=htmlspecialchars($find_id)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED")?>:</td>
		<td>
			<?
			$arr = array(
				"reference" => array(
					GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED_Y"),
					GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED_N"),
				),
				"reference_id" => array("Y","N")
			);
			echo SelectBoxFromArray("find_moderated", $arr, $find_moderated, GetMessage("WEBDEBUG_REVIEWS_FILTER_MODERATED_ANY"), "");
			?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_SITE_ID")?>:</td>
		<td>
			<?
			$arr = array(
				"reference" => array(),
				"reference_id" => array(),
			);
			foreach ($arSites as $arSite) {
				$arr["reference"][] = "[".$arSite["ID"]."] ".$arSite["NAME"];
				$arr["reference_id"][] = $arSite["ID"];
			}
			echo SelectBoxFromArray("find_site_id", $arr, $find_site_id, GetMessage("WEBDEBUG_REVIEWS_FILTER_SITE_ID_ANY"), "");
			?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_IBLOCK_ID")?>:</td>
		<td>
			<?
			// Get IBlock Types
			$arIBlockTypes = array();
			$ResIBlockType = CIBlockType::GetList(array("SORT"=>"ASC"), array());
			while ($arIBlockType = $ResIBlockType->GetNext(false,false)) {
				if ($arIBType = CIBlockType::GetByIDLang($arIBlockType["ID"], LANG)) {
					$arIBlockType["NAME"] = $arIBType["NAME"];
				}
				$arIBlockTypes[] = $arIBlockType;
			}
			$arIBlocks = array();
			$ResIBlock = CIBlock::GetList(array(), array(), false);
			while($arIBlock = $ResIBlock->GetNext(false,false)) {
				$arIBlocks[] = $arIBlock;
			}
			$arSelectBoxIBlocks = array();
			unset($arIBlockType, $arIBlock);
			foreach ($arIBlockTypes as $arIBlockType) {
				$arSelectBoxIBlocks[$arIBlockType["NAME"]] = array();
				foreach ($arIBlocks as $arIBlock) {
					if ($arIBlock["IBLOCK_TYPE_ID"]==$arIBlockType["ID"]) {
						$arSelectBoxIBlocks[$arIBlockType["NAME"]][$arIBlock["ID"]] = "[".$arIBlock["ID"]."] ".$arIBlock["NAME"];
					}
				}
			}
			echo Webdebug_Reviews_SelectBoxFromArrayGrouped("find_iblock_id", $arSelectBoxIBlocks, $find_iblock_id, GetMessage("WEBDEBUG_REVIEWS_FILTER_IBLOCK_ID_ANY"), "");
			?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_ELEMENT_ID")?>:</td>
		<td><input type="text" size="40" maxlength="10" name="find_element_id" value="<?=htmlspecialchars($find_element_id)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_NAME")?>:</td>
		<td><input type="text" size="40" maxlength="255" name="find_name" value="<?=htmlspecialchars($find_name)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_USER_ID")?>:</td>
		<td><input type="text" size="40" maxlength="10" name="find_user_id" value="<?=htmlspecialchars($find_user_id)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_EMAIL")?>:</td>
		<td><input type="text" size="40" maxlength="255" name="find_email" value="<?=htmlspecialchars($find_email)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_WWW")?>:</td>
		<td><input type="text" size="40" maxlength="255" name="find_www" value="<?=htmlspecialchars($find_www)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_EMAIL_PUBLIC")?>:</td>
		<td>
			<?
			$arr = array(
				"reference" => array(
					GetMessage("WEBDEBUG_REVIEWS_FILTER_EMAIL_PUBLIC_Y"),
					GetMessage("WEBDEBUG_REVIEWS_FILTER_EMAIL_PUBLIC_N"),
				),
				"reference_id" => array("Y","N")
			);
			echo SelectBoxFromArray("find_email_public", $arr, $find_email_public, GetMessage("WEBDEBUG_REVIEWS_FILTER_EMAIL_PUBLIC_ANY"), "");
			?>
		</td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_TEXT_PLUS")?>:</td>
		<td><input type="text" size="40" maxlength="255" name="find_text_plus" value="<?=htmlspecialchars($find_text_plus)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_TEXT_MINUS")?>:</td>
		<td><input type="text" size="40" maxlength="255" name="find_text_minus" value="<?=htmlspecialchars($find_text_minus)?>"/></td>
	</tr>
	<tr>
		<td><?=GetMessage("WEBDEBUG_REVIEWS_FILTER_TEXT_COMMENTS")?>:</td>
		<td><input type="text" size="40" maxlength="255" name="find_text_comments" value="<?=htmlspecialchars($find_text_comments)?>"/></td>
	</tr>

	<?for($i=0; $i<10; $i++):?>
		<?$VoteID = "find_vote_".$i; $VoteID = $$VoteID;?>
		<tr>
			<td><?=COption::GetOptionString("webdebug.reviews", "vote_name_".$i, "")?>:</td>
			<td>
			<?
			$arr = array(
				"reference" => array(
					GetMessage("WEBDEBUG_REVIEWS_FILTER_VOTE_VALUE_1"),
					GetMessage("WEBDEBUG_REVIEWS_FILTER_VOTE_VALUE_2"),
					GetMessage("WEBDEBUG_REVIEWS_FILTER_VOTE_VALUE_3"),
					GetMessage("WEBDEBUG_REVIEWS_FILTER_VOTE_VALUE_4"),
					GetMessage("WEBDEBUG_REVIEWS_FILTER_VOTE_VALUE_5"),
				),
				"reference_id" => array("1","2","3","4","5")
			);
			echo SelectBoxFromArray("find_vote_".$i, $arr, $VoteID, GetMessage("WEBDEBUG_REVIEWS_FILTER_VOTE_ANY"), "");
			?>
			</td>
		</tr>
	<?endfor?>

	<?$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form" => "find_form"));?>
	<?$oFilter->End();?>
</form>

<?// Output ?>
<?$lAdmin->DisplayList();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>