<?
###################################################
# askaron.agents module		                      #
# Copyright (c) 2011-2013 Askaron Systems ltd.    #
# http://askaron.ru                               #
# mailto:mail@askaron.ru                          #
###################################################

IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
require_once( "prolog.php" );

$module_id = "askaron.agents";
$install_status = CModule::IncludeModuleEx($module_id);

$RIGHT = $APPLICATION->GetGroupRight($module_id);
$RIGHT_W = ($RIGHT>="W");
$RIGHT_R = ($RIGHT>="R");

if ($RIGHT_R)
{	
	if (
		$REQUEST_METHOD=="POST"
		&& strlen($Update)>0
		&& $RIGHT_W
		&& check_bitrix_sessid()
	)
	{
		if ( isset($_REQUEST[ "check_agents" ]) && $_REQUEST[ "check_agents" ] == "Y" )
		{
			COption::SetOptionString("main", "check_agents", "Y" );
		}
		else
		{
			COption::SetOptionString("main", "check_agents", "N" );				
		}
	}	


	if (
		$REQUEST_METHOD=="POST"
		&& $RIGHT_W
		&& strlen($RestoreDefaults)>0
		&& check_bitrix_sessid()
	)
	{
		COption::RemoveOption("askaron.agents");
		COption::RemoveOption("main", "check_agents");

		$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"), $get_users_amount = "N");
		while($zr = $z->Fetch())
		{
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
		}
	}

	$check_agents = COption::GetOptionString("main", "check_agents", "Y");

	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
		array("DIV" => "edit3", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
	);

	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	$tabControl->Begin();

	$rowIndex = 0;

	?>

	<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post()?>
		<?$tabControl->BeginNextTab();?>
		<tr>
			<td width="100%" style="" colspan="2">
				<?	
				//demo (2)
				if ( $install_status == 2 )
				{
					CAdminMessage::ShowMessage(
						Array(
							"TYPE"=>"OK",
							"MESSAGE" => GetMessage("askaron_agents_prolog_status_demo"),
							"DETAILS"=> GetMessage("askaron_agents_prolog_buy_html"),
							"HTML"=>true
						)
					);
				}
				elseif( $install_status == 3 )
				{
					//demo expired (3)
					CAdminMessage::ShowMessage(
						Array(
							"TYPE"=>"ERROR",
							"MESSAGE" => GetMessage("askaron_agents_prolog_status_demo_expired"),
							"DETAILS"=> GetMessage("askaron_agents_prolog_buy_html"),
							"HTML"=>true
						)
					);	
				}					
				?>	
			</td>
		</tr>
		<tr>
			<td valign="top" width="40%" class="field-name"><?=GetMessage("askaron_agents_check_agents")?></td>
			<td valign="top" width="50%">

				<input
					type="radio" 
					value="Y"
					id="askaron_agents_check_agents_Y"
					<?if ($check_agents == "Y"):?>
						checked="checked"
					<?endif?>
					name="check_agents"
				/>					

				<label for='askaron_agents_check_agents_Y'><?=GetMessage("askaron_agents_check_agents_Y")?></label><br />

				<input
					type="radio" 
					value="N"
					id="askaron_agents_check_agents_N"
					<?if ($check_agents == "N"):?>
						checked="checked"
					<?endif?>						
					name="check_agents"
				/>										
				<label for='askaron_agents_check_agents_N'><?=GetMessage("askaron_agents_check_agents_N")?></label><br />

				<?
				if( $install_status == 3 )
				{
					//demo expired (3)
					CAdminMessage::ShowMessage(
						Array(
							"TYPE"=>"ERROR",
							"MESSAGE" => GetMessage("askaron_agents_prolog_status_demo_expired"),
							"DETAILS"=> GetMessage("askaron_agents_all_agents_not_work"),
							"HTML"=>true
						)
					);	
				}
				?>
				
			</td>				
		</tr>	
		<tr>
			<td valign="top" width="100%" colspan="2">
				<?=BeginNote();?>
					<?=GetMessage("askaron_agents_check_agents_help", array("#LANG#" => LANG ) );?>
				<?=EndNote();?>	
			</td>				
		</tr>			

		<?$tabControl->BeginNextTab();?>
		<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
		<?$tabControl->Buttons();?>		
		<input <?if(!$RIGHT_W) echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
		<input <?if(!$RIGHT_W) echo "disabled" ?> type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
		<?$tabControl->End();?>
	</form>

	<?=BeginNote();?>
		<?
			$server_version = GetMessage("askaron_agents_not_vmbitrix");
			
			$vm_version = getenv("BITRIX_VA_VER");
			
			if ( strlen( $vm_version ) > 0 )
			{
				$server_version = GetMessage("askaron_agents_vmbitrix")." ".$vm_version;
			}
		?>

		<?=GetMessage("askaron_agents_cron_help", array("#DOCUMENT_ROOT#" => $_SERVER["DOCUMENT_ROOT"], "#VMBITRIX#" => $server_version ) );?>
	<?=EndNote();?>	

	<?=BeginNote();?>
		<?=GetMessage("askaron_agents_check_agents_mail");?>
	<?=EndNote();?>	

<?
}

?>