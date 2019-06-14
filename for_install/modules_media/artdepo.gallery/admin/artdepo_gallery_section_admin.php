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

$gSection = new CArtDepoGallerySection();

// Получим списко всех языков
$languages = CArtDepoGalleryUtils::GetSiteLangs();

$parent_id = intval($_REQUEST["find_parent_id"]);

$sTableID = "tbl_artdepo_gallery_albums";
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
		"ACTIVE"		 => $find_active,
		"ID" 			 => $find_id,
		"PARENT_ID"      => $find_parent_id,
	);
}
if($parent_id>0)
    $arFilter["PARENT_ID"] = $parent_id;
    
if(IntVal($parent_id)<0 || strlen($parent_id)<=0)
	unset($arFilter["PARENT_ID"]);

$arParentSection = CArtDepoGallerySection:: GetByID($parent_id);
if(!$arParentSection)
    $lAdmin->AddGroupError(GetMessage("ADG_ERROR_WRONG_PARENT_SECTION"));
	
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
					$lAdmin->AddGroupError(GetMessage("IBEL_D_UPDERR"), $ID);
			break;
		}
	}
}

$rsData = $gSection->GetList(array($by=>$order), $arFilter);
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint(GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_NAV")));

// string => "Russina / English / ..."
foreach ($languages as $lang) {
    $strLangTitles[] = $lang["NAME"];
}
$strLangTitles = implode(" / ", $strLangTitles);

$aHeaders = array(
    array("id"=>"NAME", "content"=>GetMessage("ARTDEPO_GALLERY_ALBUM_NAME") . " (" . $strLangTitles . ")", "sort"=>"name", "default"=>true),
	array("id"=>"ACTIVE", "content"=>GetMessage("ARTDEPO_GALLERY_ALBUM_ACTIVE"), "sort"=>"active", "default"=>true),
	array("id"=>"SORT", "content"=>GetMessage("ARTDEPO_GALLERY_ALBUM_SORT"), "sort"=>"sort", "default"=>true),
	array("id"=>"DATE_UPDATE", "content"=>GetMessage("ARTDEPO_GALLERY_ALBUM_DATE_UPDATE"), "sort"=>"date_update", "default"=>true),
	array("id"=>"ID", "content"=>"ID", "sort"=>"id", "default"=>true),
);

$lAdmin->AddHeaders($aHeaders);

while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes);
	$open_url = "artdepo_gallery_album_admin.php?find_parent_id=".$f_ID."&lang=".LANGUAGE_ID;
    // pack names in right order
    $strNames = CArtDepoGalleryUtils::PackNamesInStringOrderedByLang($arRes, $languages, " /<br>");
    $row_name = $f_NAME . (($strNames) ? " <br>({$strNames})" : "");
    $row->AddViewField("NAME", '<a href="'.$open_url.'" class="adm-list-table-icon-link"><span class="adm-submenu-item-link-icon adm-list-table-icon iblock-section-icon"></span><span class="adm-list-table-link">'.$row_name.'</span></a>');
	$row->AddViewField("ACTIVE", $f_ACTIVE == "Y" ? GetMessage("ARTDEPO_GALLERY_ALBUM_ACTIVE_YES") : GetMessage("ARTDEPO_GALLERY_ALBUM_ACTIVE_NO"));
	$row->AddViewField("SORT", $f_SORT);
	$row->AddViewField("DATE_UPDATE", empty($f_DATE_UPDATE) ? "" : $f_DATE_UPDATE);

	$arActions = Array(
		array(
			"ICON"=>"",
			"DEFAULT"=>true,
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_ENTER"),
			"ACTION"=>$lAdmin->ActionRedirect($open_url)
		),
		array(
			"ICON"=>"edit",
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_EDIT"),
			"ACTION"=>"ArtDepoGallery.edit(\"{$f_ID}\");",
		),
		array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_DEL"),
			"ACTION"=>"if(confirm('".GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_DEL_CONF")."')) ".$lAdmin->ActionDoGroup($f_ID, "delete", $sThisSectionUrl)."return false;"
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

$chain = $lAdmin->CreateChain();

if($parent_id > 0)
{
	if($ar_nav = CArtDepoGallerySection::GetByID($parent_id))
	{
		$sSectionUrl = BX_ROOT."/admin/artdepo_gallery_section_admin.php?lang=".urlencode(LANG)."&find_parent_id=".$parent_id;
		$chain->AddItem(array(
			"TEXT" => htmlspecialcharsex($ar_nav["NAME"]),
			"LINK" => htmlspecialcharsbx($sSectionUrl),
			"ONCLICK" => $lAdmin->ActionAjaxReload($sSectionUrl).';return false;',
		));
	}
}

$lAdmin->ShowChain($chain);

$aContext = array(
	array(
		"TEXT"=>GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_ADD"),
		"LINK"=>"javascript:ArtDepoGallery.add();",
		"ICON"=>"btn_new",
	),
);
$lAdmin->AddAdminContextMenu($aContext);
$lAdmin->CheckListMode();

$APPLICATION->SetTitle(GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_PAGE_TITLE"));
require_once ($DOCUMENT_ROOT.BX_ROOT."/modules/main/include/prolog_admin_after.php");

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_FLT_NAME"),
		GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_FLT_PARENT_ID"),
		GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_FLT_ACTIVE"),
		"ID",
	)
);
?>
	<form name="form1" method="GET" action="<?=$APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?=LANG?>">
<?$oFilter->Begin();?>
	<tr>
		<td><?echo GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_FLT_NAME")?></td>
		<td><input type="text" name="find_name" size="40" value="<?echo htmlspecialcharsbx($find_name)?>"><?=ShowFilterLogicHelp()?></td>
	</tr>
	
	<tr>
		<td><?echo GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_FLT_PARENT_ID")?></td>
		<td>
			<select name="find_parent_id" >
				<?
				$bsections = $gSection->GetList(array("id" => "desc"), array("ACTIVE" => array("Y", "N")));
				while($arSection = $bsections->GetNext()):
					?><option value="<?echo $arSection["ID"]?>"<?if($arSection["ID"]==$parent_id)echo " selected"?><?if($arSection["ACTIVE"] != "Y"):?> style="color: #888;"<?endif;?>><?=$arSection["NAME"]?></option><?
				endwhile;
				?>
			</select>
		</td>
	</tr>
	
	<tr>
		<td><?echo GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_FLT_ACTIVE")?></td>
		<td><select name="find_active">
			<option value=""><?echo GetMessage("ARTDEPO_GALLERY_ALBUM_LIST_FLT_ALL")?></option>
			<option value="Y"<?if($find_active == "Y") echo " selected"?>><?echo GetMessage("ARTDEPO_GALLERY_ALBUM_ACTIVE_YES")?></option>
			<option value="N"<?if($find_active == "N") echo " selected"?>><?echo GetMessage("ARTDEPO_GALLERY_ALBUM_ACTIVE_NO")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td>ID</td>
		<td><input type="text" name="find_id" size="13" value="<?echo htmlspecialcharsbx($find_id)?>"></td>
	</tr>
<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"form1"));
$oFilter->End();
?>
	</form>


<?
$lAdmin->DisplayList();
?>


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

<?require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");?>
