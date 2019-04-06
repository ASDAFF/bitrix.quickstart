<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
?>
<?
$APPLICATION->SetTitle(GetMessage("MODULE_ADMIN_TITLE_DESC"));
CModule::IncludeModule('iblock');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if ($_SERVER['REQUEST_METHOD']=="POST" && strlen($_POST['Update'])>0)
{
	if ($_POST["email_check"]!="Y")
		$email_check="N";
	if ($_POST["phone_check"]!="Y")
		$phone_check="N";
	if ($_POST["iblock_check"]!="Y")
		$iblock_check="N";
	
	
	COption::SetOptionString("d2mg.ordercall","email_use",$email_check);
	COption::SetOptionString("d2mg.ordercall","email",htmlspecialcharsEx($_POST['email_text']));
	COption::SetOptionString("d2mg.ordercall","event_mess_id",intval($_POST['event_mess_text']));
	
	COption::SetOptionString("d2mg.ordercall","phone_use",$phone_check);
	COption::SetOptionString("d2mg.ordercall","phone",htmlspecialcharsEx($_POST['phone_text']));
	
	COption::SetOptionString("d2mg.ordercall","LSMS_login",htmlspecialcharsEx($_POST['LSMS_login']));
	COption::SetOptionString("d2mg.ordercall","LSMS_api_key",htmlspecialcharsEx($_POST['LSMS_api_key']));
	COption::SetOptionString("d2mg.ordercall","LSMS_sender_name",htmlspecialcharsEx($_POST['LSMS_sender_name']));
	
	COption::SetOptionString("d2mg.ordercall","iblock_use",$iblock_check);
	COption::SetOptionString("d2mg.ordercall","iblock_id",htmlspecialcharsEx($_POST['iblock_text']));
}
?>
<?
	$email_check=COption::GetOptionString("d2mg.ordercall","email_use");
	$email=COption::GetOptionString("d2mg.ordercall","email");
	$event_mess_id=COption::GetOptionString("d2mg.ordercall","event_mess_id");
	
	$phone_check=COption::GetOptionString("d2mg.ordercall","phone_use");
	$phone=COption::GetOptionString("d2mg.ordercall","phone");
	
	$LSMS_login=COption::GetOptionString("d2mg.ordercall","LSMS_login");
	$LSMS_api_key=COption::GetOptionString("d2mg.ordercall","LSMS_api_key");
	$LSMS_sender_name=COption::GetOptionString("d2mg.ordercall","LSMS_sender_name");
		
	$iblock_use=COption::GetOptionString("d2mg.ordercall","iblock_use");
	// Получаем ID текущего инфоблока
	$cur_iblock_id=COption::GetOptionString("d2mg.ordercall","iblock_id");	
	
	//Получаем список всех активных инфоблоков
	$iblock_res = CIBlock::GetList(
		Array(),
		Array(
			'ACTIVE'=>'Y',
		),
		true
	);	
?>
<style>
	td.head 
	{ 
		border-bottom: 1px solid gray; 
		text-align: center;
	}
	td.body
	{ 
		border-bottom: 1px solid gray;
	}
	.parametr_disc
	{
		width:50%;		
	}
	.parametr_act
	{
		width:10%;
		text-align: center;
	}
	.parametr_value
	{
		width:40%;
	}
</style>
<div class="edit-form">
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit-form">
	<tr class="top">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr>
		<td class="left"><div class="empty"></div></td>
		<td class="content">
			<table cellspacing="0" class="edit-tabs" width="100%">
				<tr>
					<td class="tab-indent"><div class="empty"></div></td>

					<td title="<?=GetMessage("PROPERTIES_TITLE_DESC")?>" id="tab_cont_edit1" class="tab-container-selected">
						<table cellspacing="0">
							<tr>
								<td class="tab-left-selected" id="tab_left_edit1"><div class="empty"></div></td>
								<td class="tab-selected" id="tab_edit1"><?=GetMessage("PROPERTIES_DESC")?></td>
								<td class="tab-right-last-selected" id="tab_right_edit1"><div class="empty"></div></td>
							</tr>
						</table>
					</td>
					<td width="100%" align="right"><a href="<?=__FILE__?>"></a></td>
				</tr>
			</table>
				<form method="post" action="" class="ordercall_form_setting" name="ordercall_form_setting">
				<table cellspacing="0" class="edit-tab">
					<tr>
						<td>						
								<div id="edit1" class="edit-tab-inner"><div style="height: 100%;">
									<table cellpadding="0" cellspacing="0" border="0" class="edit-tab-title">
										<tr style="display:none;">
											<td colspan="2" class="delimiter delimiter-top"><div class="empty"></div></td>
										</tr>
										<tr>

											<td class="icon"><div id="ib_settings"></div></td>

											<td class="title"><?=GetMessage("MODULE_PARAMETERS_DESC")?></td>
										</tr>
										<tr>
											<td colspan="2" class="delimiter"><div class="empty"></div></td>
										</tr>
									</table>

									<table cellpadding="0" cellspacing="0" border="0" class="edit-table" id="edit1_edit_table">									
																	
										<tr>
											<td class="head parametr_act"><?=GetMessage("ACT_HEADER_DESC")?></td>
											<td class="head parametr_disc"><?=GetMessage("PARAMETER_HEADER_DESC")?></td>
											<td class="head parametr_value"><?=GetMessage("VALUE_HEADER_DESC")?></td>
										</tr>
										<tr>
											<td class="body parametr_act" valign="top">
												<input class="email" type="checkbox" value="Y" name="email_check" <?=($email_check=="Y")?"checked":""?>>
											</td>
											<td class="body parametr_disc" valign="top"><label><?=GetMessage("EMAIL_DESC")?></label>:</td>											
											<td class="body parametr_value" valign="top">
												<input class="email_text" name="email_text" value="<?=$email?>">
											</td>
										</tr>
										<tr>
											<td class="body parametr_act" valign="top"></td>
											<td class="body parametr_disc" valign="top"><label><?=GetMessage("EVENT_MESS_DESC")?></label>:</td>											
											<td class="body parametr_value" valign="top">
												<input class="event_mess_text" name="event_mess_text" value="<?=$event_mess_id?>">
											</td>
										</tr>
										<tr>
											<td class="body parametr_act" valign="top">
												<input class="littleSMS " type="checkbox" value="Y" name="phone_check" <?=($phone_check=="Y")?"checked":""?>>
											</td>
											<td class="body parametr_disc" valign="top"><label><?=GetMessage("LITTLESMS_DESC")?></label>:</td>
											<td class="body parametr_value" valign="top">
												<input class="phone_text" name="phone_text" value="<?=$phone?>">
											</td>
										</tr>
										<tr>
											<td class="body parametr_act" valign="top"></td>
											<td class="body parametr_disc" valign="top" ></td>
											<td class="body parametr_value" valign="top">
												<?=GetMessage("LSMS_LOGIN_DESC")?>:<br>
												<input class="LSMS_login" name="LSMS_login" value="<?=$LSMS_login?>"><br>
												<?=GetMessage("LSMS_API_KEY_DESC")?>:<br>
												<input class="LSMS_api_key" name="LSMS_api_key" value="<?=$LSMS_api_key?>"><br>
												<?=GetMessage("SMS_SENDER_DESC")?>:<br>
												<input class="LSMS_sender_name" name="LSMS_sender_name" value="<?=$LSMS_sender_name?>"><br>
											</td>
										</tr>
										<tr>
											<td class="parametr_act" valign="top">
												<input class="iblock" type="checkbox" value="Y" name="iblock_check" <?=($iblock_use=="Y")?"checked":""?>>
											</td>
											<td class="parametr_disc" valign="top"><label><?=GetMessage("IBLOCK_DESC")?></label>:</td>											
											<td class="parametr_value" valign="top">
												<select class="iblock_text" name="iblock_text">
													<?while($iblock_ar_res = $iblock_res->Fetch()):?>
														<option value="<?=$iblock_ar_res['ID']?>" <?=($iblock_ar_res['ID'] == $cur_iblock_id)?'selected':''?> ><?=$iblock_ar_res['NAME']?></option>
													<?endwhile;?>
												</select>
											</td>
										</tr>											
									</table>
								</div>
								</div>								
						</td>
					</tr>
				</table>
				<div class="buttons">	
					<input type="submit" name="Update" value="<?=GetMessage("BUTTON_VAL_DESC")?>" title="<?=GetMessage("BUTTON_TITLE_DESC")?>">
				</div>
			</form>
		</td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr class="bottom">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
</table>
</div>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>