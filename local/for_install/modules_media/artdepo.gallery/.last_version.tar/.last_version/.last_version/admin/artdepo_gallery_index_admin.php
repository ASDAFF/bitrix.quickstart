<?
##############################################
# ArtDepo.Gallery module                     #
# Copyright (c) 2013 AdrDepo                 #
# http://artdepo.com.ua                      #
# mailto:depo@artdepo.cm.ua                  #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

global $USER, $APPLICATION, $DOCUMENT_ROOT;

require_once(dirname(__FILE__)."/../include.php");
require_once(dirname(__FILE__)."/../prolog.php");

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($DOCUMENT_ROOT.BX_ROOT."/modules/main/interface/admin_lib.php");

$gSection = new CArtDepoGallerySection();

// Get all languages in system
$languages = CArtDepoGalleryUtils::GetSiteLangs();

$sTableID = "tbl_artdepo_gallery_collections";
$oSort = new CAdminSorting($sTableID, "id", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
	global $FilterArr, $lAdmin;
	foreach ($FilterArr as $f) global $$f;
	return count($lAdmin->arFilterErrors)==0;
}

$FilterArr = Array(
	"find_name",
	"find_active",
	"find_owner_id",
	"find_id",
);

$lAdmin->InitFilter($FilterArr);

$arFilter = Array("ACTIVE"=> Array("Y", "N"));
if(CheckFilter())
{
	$arFilter = Array(
		"NAME"			 => $find_name,
		"ACTIVE"		 => $find_active,
		"OWNER_ID"       => $find_owner_id,
		"ID" 			 => $find_id,
	);
}

if(($arID = $lAdmin->GroupAction()))
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = $gSection->GetList(array($by=>$order), $arFilter);
		while($arRes = $rsData->Fetch())
			$arID[] = $arRes['ID'];
	}
	
	foreach($arID as $ID)
	{
		$ID = IntVal($ID);
		if($ID <= 0)
			continue;

		switch($_REQUEST['action'])
		{
		    case "activate":
		    case "deactivate":
				$arFields = Array("ACTIVE"=>($_REQUEST['action']=="activate"?"Y":"N"));
				if(!$gSection->Update($ID, $arFields, true))
					$lAdmin->AddGroupError(GetMessage("IBEL_A_UPDERR").$gSection->LAST_ERROR, $ID);
			break;
			case "delete":
				if(!CArtDepoGallerySection::Delete($ID))
					$lAdmin->AddGroupError(GetMessage("ARTDEPO_GALLERY_LIST_ERR_DEL"), $ID);
			break;
		}
	}
}

$rsData = $gSection->GetList(array($by=>$order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ARTDEPO_GALLERY_LIST_NAV")));

// string => "Russina / English / ..."
foreach ($languages as $lang) {
    $strLangTitles[] = $lang["NAME"];
}
$strLangTitles = implode(" / ", $strLangTitles);

$aHeaders = array(
	array("id"=>"NAME", "content"=>GetMessage("ARTDEPO_GALLERY_NAME") . " (" . $strLangTitles . ")", "sort"=>"name", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("ARTDEPO_GALLERY_ACTIVE"), "sort"=>"active", "default"=>true),
	array("id"=>"DATE_UPDATE", "content"=>GetMessage("ARTDEPO_GALLERY_DATE_UPDATE"), "sort"=>"date_update", "default"=>true),
	array("id"=>"OWNER_ID", "content"=>GetMessage("ARTDEPO_GALLERY_OWNER_ID"), "sort"=>"owner_id", "default"=>false),
	array("id"=>"ID", "content"=>"ID", "sort"=>"id", "default"=>true),
);

$lAdmin->AddHeaders($aHeaders);

$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();

while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes);
    // pack names in right order
    $strNames = CArtDepoGalleryUtils::PackNamesInStringOrderedByLang($arRes, $languages);
    
    $open_url = 'artdepo_gallery_section_admin.php?find_parent_id='.$f_ID.'&lang='.LANGUAGE_ID;
    
    $row_name = $f_NAME . (($strNames) ? " ({$strNames})" : "");
    $row->AddViewField("NAME", '<a href="'.$open_url.'" class="adm-list-table-icon-link" title="'.GetMessage("IBLIST_A_LIST").'"><span class="adm-submenu-item-link-icon adm-list-table-icon iblock-section-icon"></span><span class="adm-list-table-link">'.htmlspecialchars_decode($row_name).'</span></a>');
	
	$row->AddViewField("ACTIVE", $f_ACTIVE == "Y" ? GetMessage("ARTDEPO_GALLERY_ACTIVE_YES") : GetMessage("ARTDEPO_GALLERY_ACTIVE_NO"));
	
	if (in_array("OWNER_ID", $arSelectedFields) !== false && $f_OWNER_ID) {
	    $rsUser = CUser::GetByID($f_OWNER_ID);
        $arUser = $rsUser->Fetch();
        $f_OWNER_USER_NAME = sprintf('<a href="user_edit.php?lang=%s&ID=%d">[%d]</a>', LANGUAGE_ID, $f_OWNER_ID, $f_OWNER_ID);
	    $f_OWNER_USER_NAME .= rtrim(sprintf(" (%s) %s %s", $arUser['LOGIN'], $arUser['NAME'], $arUser['LAST_NAME']));
	    $row->AddViewField("OWNER_ID", '<a href="user_edit.php?lang='.LANGUAGE_ID.'&ID='.$f_OWNER_ID.'">'.$f_OWNER_USER_NAME.'</a>');
	}
	
	$row->AddViewField("DATE_UPDATE", $f_DATE_UPDATE);

	$arActions = Array(
		array(
			"ICON"=>"",
			"DEFAULT"=>true,
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_LIST_ENTER"),
			"ACTION"=>$lAdmin->ActionRedirect($open_url)
		),
		array(
			"ICON"=>"edit",
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_LIST_EDIT"),
			"ACTION"=>"ArtDepoGallery.edit($f_ID);"//$lAdmin->ActionRedirect("rating_edit.php?ID=".$f_ID)
		),
		array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_LIST_DEL"),
			"ACTION"=>"if(confirm('".GetMessage("ARTDEPO_GALLERY_LIST_DEL_CONF")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")."return false;"
		),
	);
	$row->AddActions($arActions);
}

$lAdmin->AddFooter(
	array(
		array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
		array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
	)
);

// Action bar
if(true)
{
	$arActions = array(
		"delete" => GetMessage("MAIN_ADMIN_LIST_DELETE"),
		"activate" => GetMessage("MAIN_ADMIN_LIST_ACTIVATE"),
		"deactivate" => GetMessage("MAIN_ADMIN_LIST_DEACTIVATE"),
	);

	$lAdmin->AddGroupActionTable($arActions, array());
}

$aContext = array(
	array(
        "ICON"=>"btn_new",
		"TEXT"=>GetMessage("ARTDEPO_GALLERY_LIST_ADD"),
		"LINK"=>"javascript:ArtDepoGallery.add();",
		"TITLE"=>GetMessage("ARTDEPO_GALLERY_LIST_ADD_TITLE"),
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ARTDEPO_GALLERY_LIST_PAGE_TITLE"));
require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList(array());
?>

<script type="text/javascript">
    var artdepo_gallery_section = {
        'languages': [],
        'parent_collection': "<?=$gSection->GetRootCollectionID()?>",
        'site_id': "<?=SITE_ID?>"
    };
    
    var languages = [];
    <?foreach ($languages as $lan):?>
        languages.push({
            'lid': "<?=$lan['LANGUAGE_ID']?>",
            'name': "<?=$lan['NAME']?>"
        });
    <?endforeach;?>        
    artdepo_gallery_section.languages = languages;
</script>

<?CJSCore::Init(array('artdepo_gallery_sections'));?>

<?require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
