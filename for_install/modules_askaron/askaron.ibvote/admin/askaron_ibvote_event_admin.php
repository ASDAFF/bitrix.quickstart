<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/askaron.ibvote/prolog.php");

IncludeModuleLangFile(__FILE__);

$RIGHT = $APPLICATION->GetGroupRight("askaron.ibvote");
if ($RIGHT == "D")
{
	$APPLICATION->AuthForm( GetMessage("ACCESS_DENIED") );
}
//CAskaronIbvoteEvent


$sTableID = "tbl_askaron_ibvote_event";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
  global $FilterArr, $lAdmin;
  foreach ($FilterArr as $f) global $$f;

  return count($lAdmin->arFilterErrors)==0;
}

// filter fields
$FilterArr = Array(
	"find_id",
	"find_element_id",
	"find_answer",
	"find_ip",
	"find_date_vote_1",
	"find_date_vote_2",
	"find_user_id",
	"find_stat_session_id",
);
				
$lAdmin->InitFilter($FilterArr);

if ( CheckFilter() )
{
	// short format make full.
	if ( strlen( $find_date_vote_2 ) == 10 )
	{
		$timestamp = MakeTimeStamp($find_date_vote_2, FORMAT_DATE) + 86400 - 1;
		
		$find_date_vote_2 = ConvertTimeStamp( $timestamp, "FULL" );
	}

	$arFilter = Array(
		"ID"    => $find_id,
		"ELEMENT_ID" => $find_element_id,
		"ANSWER"    => $find_answer,
		"IP"    => $find_ip,
		">=DATE_VOTE"    => $find_date_vote_1,
		"<=DATE_VOTE"    => $find_date_vote_2,
		"USER_ID"    => $find_user_id,
		"STAT_SESSION_ID"    => $find_stat_session_id,
	);

	foreach ( $arFilter as $key=>$value )
	{
		if ( $value === NULL )
		{
			unset( $arFilter[$key] );
		}
	}

}


if(($arID = $lAdmin->GroupAction()) && $RIGHT>="W")
{
	// Checkbox "All elements"
	if($_REQUEST['action_target']=='selected')
	{
		$cData = new CAskaronIbvoteEvent;
		$rsData = $cData->GetList(array($by=>$order), $arFilter);
			while($arRes = $rsData->Fetch())
				$arID[] = $arRes['ID'];
	}

	// List of Elements
	foreach($arID as $ID)
	{
		if(strlen($ID)<=0)
			continue;

		$ID = IntVal($ID);

		switch($_REQUEST['action'])
		{
			case "delete":
				@set_time_limit(0);
				$DB->StartTransaction();
				if(!CAskaronIbvoteEvent::Delete($ID))
				{
					$DB->Rollback();
					$lAdmin->AddGroupError(GetMessage("askaron_ibvote_event_del_err"), $ID);
				}
				$DB->Commit();
				break;
		}
	}
}

// List
$cData = new CAskaronIbvoteEvent();
$rsData = $cData->GetList( array($by=>$order), $arFilter );
$rsData = new CAdminResult($rsData, $sTableID);


$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("askaron_ibvote_event_nav")));


$bShowStatSessionColumn = CModule::IncludeModule("statistic");

$lAdmin->AddHeaders(array(
	array(
		"id"    =>"ID",
		"content"  =>"ID",
		"sort"     =>"id",
		"default"  =>true,
	),
	array(
		"id"		=> "ELEMENT_ID",
		"content"	=> GetMessage("askaron_ibvote_event_element_id"),
		"sort"		=> "element_id",
		"default"	=> true,
	),
	array(
		"id"		=> "ANSWER",
		"content"	=> GetMessage("askaron_ibvote_event_answer"),
		"sort"		=> "answer",
		"default"	=> true,
	),
	array(
		"id"		=> "DATE_VOTE",
		"content"	=> GetMessage("askaron_ibvote_event_date_vote"),
		"sort"		=> "date_vote",
		"default"	=> true,
	),
	array(
		"id"		=> "IP",
		"content"	=> "IP",
		"sort"		=> "ip",
		"default"	=> true,
	),
	array(
		"id"		=> "USER_ID",
		"content"	=> GetMessage("askaron_ibvote_event_user_id"),
		"sort"		=> "user_id",
		"default"	=> true,
	),
	array(  
		"id"		=> "STAT_SESSION_ID",
		"content"	=> GetMessage("askaron_ibvote_event_stat_session_id"),
		"sort"		=> "stat_session_id",
		"default"	=> $bShowStatSessionColumn,
	),
));

$arUsersName = array();
$arElementsName = array();

while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =&$lAdmin->AddRow($f_ID, $arRes);

	// find iblock element
	if ( intval( $arRes['ELEMENT_ID'] ) > 0 && !isset( $arElementsName[ $arRes['ELEMENT_ID'] ] ) )
	{
		if (CModule::IncludeModule("iblock") )
		{
			$arElementsFilter = array(
				'ID' => $arRes['ELEMENT_ID'],
			);

			$arElementsSelect = array(
				'ID',
				'IBLOCK_ID',
				'IBLOCK_TYPE_ID',		
				'IBLOCK_SECTION_ID',
				'NAME',
			);

			$rsElements = CIBlockElement::GetList(array('id' => 'desc'), $arElementsFilter, false, array('nTopCount' => 1 ),  $arElementsSelect);
			if ( $arElem = $rsElements->GetNext() )
			{	
				$url = 'iblock_element_edit.php?ID='.$arElem['ID'].'&amp;IBLOCK_ID='.$arElem['IBLOCK_ID'].'&amp;type='.$arElem['IBLOCK_TYPE_ID'].'&amp;find_section_section='.intval($arElem['IBLOCK_SECTION_ID']).'&amp;lang='.LANGUAGE_ID;
				$arElementsName[ $arElem['ID'] ] = '[<a href="'.$url.'">'.$arElem['ID'].'</a>] '.$arElem['NAME'];
			};		
		}
	}
	
	// find user
	if (intval( $arRes['USER_ID'] ) > 0 && !isset( $arUsersName[ $arRes['USER_ID'] ] ) )
	{
		$arUsersFilter = array(
			'ID' => $arRes['USER_ID'],
		);

		$arUsersParams = array(
			'NAV_PARAMS' => array(
				'nPageSize' => count($arUsersFilter),
			)
		);
		$rsUsers = CUser::GetList(($by_user="id"), ($order_user="desc"), $arUsersFilter);
		if ( $arUser = $rsUsers->GetNext() )
		{		
			$full_name = $arUser['NAME']; 
			if ( strlen($arUser['LAST_NAME']) > 0 )
			{
				if ( strlen($full_name) > 0 )
				{
					$full_name .='&nbsp;';
				}
				$full_name .= $arUser['LAST_NAME'];
			}
			
			$full_name_text = $arUser['LOGIN'].' ('.$full_name.')';
			$arUsersName[ $arUser['ID'] ] = '[<a href="user_edit.php?ID='.$arUser['ID'].'&amp;lang='.LANGUAGE_ID.'">'.$arUser['ID'].'</a>] '.$full_name_text;
		};		
	}	
	
	if (intval( $arRes['ELEMENT_ID'] ) > 0 && isset( $arElementsName[ $arRes['ELEMENT_ID'] ] ) )
	{
		$sHTML = $arElementsName[ $arRes['ELEMENT_ID'] ];
		$row->AddViewField("ELEMENT_ID", $sHTML );		
	}	

	if ( strlen( $arRes["IP"] ) > 0 )
	{		
		$sHTML = '<a href="http://whois.domaintools.com/'.htmlspecialchars($arRes["IP"]).'" target="_blank">'.htmlspecialchars($arRes["IP"]).'</a>';
		$row->AddViewField("IP", $sHTML);
	}


	if (intval( $arRes['USER_ID'] ) > 0 && isset( $arUsersName[ $arRes['USER_ID'] ] ) )
	{
		$sHTML = $arUsersName[ $arRes['USER_ID'] ];
		$row->AddViewField("USER_ID", $sHTML );		
	}	

	if ($bShowStatSessionColumn && intval( $arRes['STAT_SESSION_ID'] ) > 0 )
	{
		$sHTML = "<a href=\"session_list.php?find_id=".intval( $arRes['STAT_SESSION_ID'] )."&amp;set_filter=Y&amp;lang=".LANGUAGE_ID."\">".intval( $arRes['STAT_SESSION_ID'] )."</a>";
		$row->AddViewField("STAT_SESSION_ID", $sHTML);	
	}
	
	
	$arActions = Array();
  
	if ($RIGHT>="W")
	{
		// delete
		$arActions[] = array(
			"ICON"=>"delete",
			"TEXT"=>GetMessage("askaron_ibvote_event_del"),
			"ACTION"=>"if(confirm('".GetMessage('askaron_ibvote_event_del_conf')."')) ".$lAdmin->ActionDoGroup($f_ID, "delete")
		);
	}

	$row->AddActions($arActions);

}

$lAdmin->AddFooter(
  array(
    array("title"=>GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value"=>$rsData->SelectedRowsCount()),
    array("counter"=>true, "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value"=>"0"),
  )
);

if ($RIGHT>="W")
{
	$arActionTable = array(
		"delete"=>GetMessage("MAIN_ADMIN_LIST_DELETE"),
	);
	$lAdmin->AddGroupActionTable( $arActionTable );
}
$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);


// if ajax
$lAdmin->CheckListMode();

// Title
$APPLICATION->SetTitle(GetMessage("askaron_ibvote_event_title"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");


$arFilterFieldsNames = array(
	GetMessage("askaron_ibvote_event_f_element_id"),
	GetMessage("askaron_ibvote_event_f_answer"),
	'IP',
	GetMessage("askaron_ibvote_event_f_date_vote"),	
	GetMessage("askaron_ibvote_event_f_user_id"),	
);

if ($bShowStatSessionColumn)
{
	$arFilterFieldsNames[] = GetMessage("askaron_ibvote_event_f_stat_session_id");
}

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	$arFilterFieldsNames
);
?>
<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">
<?$oFilter->Begin();?>

<tr>
	<td>ID:</td>
	<td><input type="text" name="find_id" size="7" value="<?echo htmlspecialchars($find_id)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("askaron_ibvote_event_f_element_id")?>:</td>
	<td><input type="text" name="find_element_id" size="7" value="<?echo htmlspecialchars($find_element_id)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("askaron_ibvote_event_f_answer")?>:</td>
	<td><input type="text" name="find_answer" size="7" value="<?echo htmlspecialchars($find_answer)?>"></td>
</tr>
<tr>
	<td>IP:</td>
	<td><input type="text" name="find_ip" size="15" value="<?echo htmlspecialchars($find_ip)?>"></td>
</tr>
<tr>
	<td><?=GetMessage("askaron_ibvote_event_f_date_vote")?>:</td>
	<td><?echo CalendarPeriod("find_date_vote_1", $find_date_vote_1, "find_date_vote_2", $find_date_vote_2, "find_form", "Y")?></td>
</tr>
<tr>
	<td><?echo GetMessage("askaron_ibvote_event_f_user_id")?>:</td>
	<td><input type="text" name="find_user_id" size="7" value="<?=htmlspecialchars($find_user_id) ?>"></td>
</tr>

<?if ($bShowStatSessionColumn):?>
	<tr>
		<td><?echo GetMessage("askaron_ibvote_event_f_stat_session_id")?>:</td>
		<td><input type="text" name="find_stat_session_id" size="7" value="<?=htmlspecialchars($find_stat_session_id) ?>"></td>
	</tr>
<?endif?>

<?
$oFilter->Buttons(array("table_id"=>$sTableID,"url"=>$APPLICATION->GetCurPage(),"form"=>"find_form"));
$oFilter->End();
?>
</form>


<?
	// Show list
	$lAdmin->DisplayList();
?>
<?=BeginNote();?>
<?
echo GetMessage("askaron_ibvote_event_your_current_id").": ".'<a href="http://whois.domaintools.com/'.htmlspecialchars($_SERVER["REMOTE_ADDR"]).'" target="_blank">'.htmlspecialchars($_SERVER["REMOTE_ADDR"]).'</a>';
?>
<?=EndNote();?>


<?if ( CModule::IncludeModule("askaron.include") == 0 ):?>
	<?=GetMessage("askaron_ibvote_event_banner");?>
<?endif?>

<?if ( strlen( GetMessage("askaron_ibvote_event_api_help") ) > 0 ):?>
	<?=BeginNote();?>
		<?=GetMessage("askaron_ibvote_event_api_help");?>
	<?=EndNote();?>
<?endif?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>