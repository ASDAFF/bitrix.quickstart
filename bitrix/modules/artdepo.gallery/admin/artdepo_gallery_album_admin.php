<?
##############################################
# ArtDepo.Gallery module                     #
# Copyright (c) 2013 AdrDepo                 #
# http://artdepo.com.ua                      #
# mailto:depo@artdepo.cm.ua                  #
##############################################

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/fileman/prolog.php");
require_once(dirname(__FILE__)."/../include.php");
require_once(dirname(__FILE__)."/../prolog.php");

IncludeModuleLangFile(__FILE__);

// Получим списко всех языков
$languages = CArtDepoGalleryUtils::GetSiteLangs();

$gImage = new CArtDepoGalleryImage();

$parent_id = intval($_REQUEST["find_parent_id"]);

$sTableID = "tbl_artdepo_gallery_album";
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
	"find_id",
	"find_parent_id",
);

$lAdmin->InitFilter($FilterArr);

//We have to handle current section in a special way
//This is all parameters needed for proper navigation
$sThisSectionUrl = '&lang='.urlencode(LANG).'&find_parent_id='.intval($parent_id);

$arFilter = Array();
if(CheckFilter())
{
	$arFilter = Array(
		"NAME"			 => $find_name,
		"ID" 			 => $find_id,
		"PARENT_ID"      => $find_parent_id,
	);
}
if($parent_id>0)
    $arFilter["PARENT_ID"] = $parent_id;
    
if(intVal($parent_id)<0 || strlen($parent_id)<=0)
	unset($arFilter["PARENT_ID"]);

$arParentSection = CArtDepoGallerySection:: GetByID($parent_id);
if(!$arParentSection)
    $lAdmin->AddGroupError(GetMessage("ADG_ERROR_WRONG_PARENT_SECTION"));
    
if($lAdmin->EditAction())
{
	foreach($FIELDS as $ID=>$arFields)
	{
		$ID = IntVal($ID);
		if($ID <= 0)
			continue;
		$arUpdate['SORT'] = $arFields['SORT'];
		if(!CArtDepoGalleryImage::UpdateSort($ID, $arUpdate))
		{
			$e = $APPLICATION->GetException();
			$lAdmin->AddUpdateError(($e? $e->GetString():GetMessage("ADG_PH_ERR_EDIT")), $ID);
		}
	}
}

if(($arID = $lAdmin->GroupAction()))
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = $gImage->GetList(array($by=>$order), $arFilter);
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
			case "delete":
		        if(!CArtDepoGalleryImage::Delete($ID, 'current', $parent_id))
		            $lAdmin->AddGroupError(GetMessage("ADG_PH_ERR_DEL"), $ID);
			break;
		}
	}
}

$rsData = $gImage->GetList(array($by=>$order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ADG_PH_LIST_NAV")));

$aHeaders = array(
	array("id"=>"NAME", "content"=>GetMessage("ARTDEPO_GALLERY_PHOTO_NAME"), "sort"=>"name", "default"=>true),
	array("id"=>"SORT", "content"=>GetMessage("ARTDEPO_GALLERY_PHOTO_SORT"), "sort"=>"sort", "default"=>true),
	array("id"=>"DATE_UPDATE", "content"=>GetMessage("ARTDEPO_GALLERY_PHOTO_DATE_UPDATE"), "sort"=>"date_update", "default"=>true),
	array("id"=>"DATE_CREATE", "content"=>GetMessage("ARTDEPO_GALLERY_PHOTO_DATE_CREATE"), "sort"=>"date_create", "default"=>false),
	array("id"=>"ID", "content"=>"ID", "sort"=>"id", "default"=>true),
);

$lAdmin->AddHeaders($aHeaders);
$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();

while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes);

    // pack names according to languages order
    $strNames = CArtDepoGalleryUtils::PackNamesInStringOrderedByLang($arRes, $languages);
    $row_name = $f_NAME . (($strNames) ? " ({$strNames})" : "");
    
    $name_html = "<table><tr>
    <td>
        " . CFile::Show2Images($f_THUMB_PATH, $f_PATH) . "
    </td>
    <td valign=\"top\" style=\"padding-left: 10px;\">
        <b>{$f_NAME}</b>";

    foreach($languages as $lan) {
        $k = "NAME_".strtoupper($lan["LANGUAGE_ID"]);
        if(!empty($arRes[$k]))
            $name_html .= "<br>" . $lan["NAME"] . ": " . $arRes[$k];
    }
        
    $name_html .= "
    </td>
    </tr></table>";
    
    $row->AddViewField("NAME", $name_html);
    
    $row->AddInputField("SORT", array("size"=>10));
    $row->AddViewField("SORT", $f_SORT);
	
	$row->AddViewField("DATE_UPDATE", empty($f_DATE_UPDATE) ? "" : $f_DATE_UPDATE);
	
	if (in_array("DATE_CREATE", $arSelectedFields) !== false && $f_OWNER_ID) {
	    $row->AddViewField("DATE_CREATE", empty($f_DATE_CREATE) ? "" : $f_DATE_CREATE);
	}
	
	$arActions = Array(
		array(
			"ICON"=>"rename",
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_PHOTO_LIST_EDIT"),
			"ACTION"=>"ArtDepoGallery.edit(\"{$f_ID}\", \"item\");",
		),
		array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_PHOTO_LIST_DEL"),
			"ACTION"=>"if(confirm('".GetMessage("ARTDEPO_GALLERY_PHOTO_LIST_DEL_CONF")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete", $sThisSectionUrl)
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

$lAdmin->AddGroupActionTable(Array(
	"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
));

$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

// Breadcrumbs & title
$title = "";
$chain = $lAdmin->CreateChain();
if($parent_id > 0)
{
	if($ar_nav = CArtDepoGallerySection::GetByID($parent_id))
	{
	    if($ar_nav["PARENT_ID"]) if($ar_nav_parent = CArtDepoGallerySection::GetByID($ar_nav["PARENT_ID"]))
	    {
            $sSectionUrl = BX_ROOT."/admin/artdepo_gallery_section_admin.php?lang=".urlencode(LANG)."&find_parent_id=".$ar_nav["PARENT_ID"];
		    $chain->AddItem(array(
			    "TEXT" => htmlspecialcharsex($ar_nav_parent["NAME"]),
			    "LINK" => htmlspecialcharsbx($sSectionUrl),
		    ));
		    $title .= $ar_nav_parent["NAME"];
	    }
		$sSectionUrl = BX_ROOT."/admin/artdepo_gallery_album_admin.php?lang=".urlencode(LANG)."&find_parent_id=".$parent_id;
		$chain->AddItem(array(
			"TEXT" => htmlspecialcharsex($ar_nav["NAME"]),
			"LINK" => htmlspecialcharsbx($sSectionUrl),
		));
		$title .= " - " . $ar_nav["NAME"];
	}
}
$lAdmin->ShowChain($chain);

$APPLICATION->SetTitle($title);

require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");
?>


<div id="fine-uploader">
    <!-- js render -->
</div><!-- #fine-uploader -->


<?
$lAdmin->DisplayList();
?>

<script src="http://yandex.st/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript" src="<?=BX_ROOT?>/js/artdepo.gallery/jquery.fineuploader-3.7.1.min.js"></script>
<link href="<?=BX_ROOT?>/js/artdepo.gallery/style/fineuploader.css" rel="stylesheet">

<script type="text/javascript">
    var artdepo_gallery_section = {
        'languages': [],
        'parent_collection': "<?=$parent_id?>",
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

<?CJSCore::Init(array('artdepo_gallery_image_upload_handler'));?>

<?require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
