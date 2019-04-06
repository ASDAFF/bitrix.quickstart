<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

/**
 * Разрешаем доступ только админам
 */
if (!$USER->IsAdmin()) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

IncludeModuleLangFile(__FILE__);

define("ADMIN_MODULE_NAME", "lssoft.comingsoon");
define("ADMIN_MODULE_ICON", "<a href=\"ls_cs_invite.php?lang=".LANGUAGE_ID."\">
<img src=\"/bitrix/images/lssoft.comingsoon/icon.png\" 
width=\"48\" height=\"48\" border=\"0\" alt=\"".GetMessage("LS_CS_INVITE_TITLE")."\"
title=\"".GetMessage("LS_CS_INVITE_TITLE")."\"></a>");


if (!CModule::IncludeModule("iblock") or !CModule::IncludeModule("lssoft.comingsoon")) {
	die();
}

$aData=array();
/**
 * Для каждого из сайта подсчитываем количество приглашений
 */
$res=CSite::GetDefList();
$aSiteIdItems=array();
while($aSite=$res->Fetch()) {
	$aDataItem=array();
	/**
	 * Формируем данные
	 */
	$aDataItem['SITE_ID']=$aSite['LID'];
	$aDataItem['SITE_NAME']=$aSite['NAME'] ? $aSite['NAME'].' ('.$aSite['LID'].')' : $aSite['LID'];
	$aDataItem['COUNT_INVITE']=CIBlockElement::GetList(array(),array('PROPERTY_SITE'=>$aSite['LID']),true);
	$aDataItem['COUNT_NOT_CONFIRM']=CIBlockElement::GetList(array(),array('PROPERTY_SITE'=>$aSite['LID'],'!=PROPERTY_CONFIRM'=>1),true);
	/**
	 * Дополнительно сохраняем список сайтов
	 */
	$aSiteIdItems[$aSite['LID']]=$aSite;
	$aData[]=$aDataItem;
}

/**
 * Обработка отправки формы
 */
if (isset($_POST['submit'])) {
	if (isset($_POST['site_id']) and isset($aSiteIdItems[$_POST['site_id']])) {
		$aSite=$aSiteIdItems[$_POST['site_id']];
		$bNeedRegistration=isset($_POST['make_registration']) ? true : false;
		/**
		 * Данные для статистики выполнения отправки приглашений
		 */
		$aResultSend=array(
			'count_send'=>0,
			'count_registration'=>0,
		);
		/**
		 * Делаем выборку приглашений (итерациями по 100 штук)
		 */
		$iPage=1;
		$res=CIBlockElement::GetList(array('created'=>'asc'),array('PROPERTY_SITE'=>$aSite['LID'],'PROPERTY_CONFIRM'=>1),false,array('iNumPage'=>$iPage,'nPageSize'=>100),array('ID','PROPERTY_LOGIN','NAME','PROPERTY_SITE'));
		while($res->NavPageCount>=$iPage) {
			while($aItem=$res->Fetch()) {
				/**
				 * Формируем данные для шаблона письма
				 */
				$aDataNotify=array(
					'EMAIL_TO'=>$aItem['NAME'],
					'URL_SITE'=>$aSite['DIR'],
				);
				if ($aDataNotify['URL_SITE']=='/') {
					$aDataNotify['URL_SITE']='';
				}
				/**
				 * Тип почтового уведомления
				 */
				$sNotifyType='LS_CS_INVITE_SEND';
				/**
				 * Проверяем необходимость регистрации
				 */
				if ($bNeedRegistration and $aItem['PROPERTY_LOGIN_VALUE']) {
					if ($aUser=CLsCsMain::RegisterUser($aItem['PROPERTY_LOGIN_VALUE'],$aItem['NAME'],$aSite['LID'])) {
						$sNotifyType='LS_CS_INVITE_SEND_REGISTRATION';
						$aDataNotify['USER_PASSWORD']=$aUser['PASSWORD'];
						$aDataNotify['USER_LOGIN']=$aUser['LOGIN'];
						$aResultSend['count_registration']++;
					}
				}
				/**
				 * Отправляем приглашение
				 */
				$aFilter = Array(
					"TYPE_ID"=> $sNotifyType,
					"SITE_ID"=> $aSite['LID'],
					"ACTIVE"=> "Y",
				);
				if ($aMsg=CEventMessage::GetList($by="id",$order="desc",$aFilter)->Fetch()) {
					CEvent::Send($sNotifyType,$aSite['LID'],$aDataNotify,'N',$aMsg['ID']);
					$aResultSend['count_send']++;
				}
			}
			$iPage++;
			$res=CIBlockElement::GetList(array('created'=>'asc'),array('PROPERTY_SITE'=>$aSite['LID'],'PROPERTY_CONFIRM'=>1),false,array('iNumPage'=>$iPage,'nPageSize'=>100),array('ID','PROPERTY_LOGIN','NAME','PROPERTY_SITE'));
		}
	}
}


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle(GetMessage("LS_CS_INVITE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<?php if (isset($aResultSend)) { ?>
<div class="adm-info-message-wrap adm-info-message-green">
	<div class="adm-info-message">
		<div class="adm-info-message-title"><?php echo GetMessage("LS_CS_INVITE_SEND_COMPLETE"); ?></div>
		<ul>
			<li><?php echo GetMessage("LS_CS_INVITE_SEND_COUNT_INVITE"); ?> &mdash; <?php echo $aResultSend['count_send']; ?></li>
			<li><?php echo GetMessage("LS_CS_INVITE_SEND_COUNT_REGISTRATION"); ?> &mdash; <?php echo $aResultSend['count_registration']; ?></li>
		</ul>
		<div class="adm-info-message-icon"></div>
	</div>
</div>
<?php } ?>


<?php foreach($aData as $aDataItem) { ?>
<form action="" method="post">
<table class="adm-filter-main-table">
		<tbody>
		<tr>
			<td class="adm-filter-main-table-cell">
				<div class="adm-filter-content adm-filter-content-first" id="tbl_rating_filter_content">
					<div class="adm-filter-content-table-wrap">
						<table cellspacing="0" style="display: table;" class="adm-filter-content-table" id="tbl_rating_filter">	
						<tbody>
							<tr>
								<td colspan="2"><b><?php echo GetMessage("LS_CS_INVITE_SITE"); ?> "<?php echo $aDataItem['SITE_NAME']; ?>":</b></td>
							</tr>
							<tr>
								<td width="50px"></td>
								<td>
									<p><?php echo GetMessage("LS_CS_INVITE_FORM_INVITES"); ?> &mdash; <b><?php echo($aDataItem['COUNT_INVITE']); ?></b></p>
									<p><?php echo GetMessage("LS_CS_INVITE_FORM_NOT_CONFIRM_MAIL"); ?> &mdash; <b><?php echo($aDataItem['COUNT_NOT_CONFIRM']); ?></b> (<?php echo GetMessage("LS_CS_INVITE_FORM_NOT_CONFIRM_MAIL_NOTICE"); ?>)</p>
									<p>
										 
										<label for="ls_cs_make_registration_<?php echo $aDataItem['SITE_ID']; ?>" style="margin-left: 0px;">
											<?php echo GetMessage("LS_CS_INVITE_FORM_NEED_REGISTRATION"); ?> &mdash;
										</label>
										<input type="checkbox" name="make_registration" value="1" id="ls_cs_make_registration_<?php echo $aDataItem['SITE_ID']; ?>"> (<?php echo GetMessage("LS_CS_INVITE_FORM_NEED_REGISTRATION_NOTICE"); ?>)
									</p>
								</td>
							</tr>
							<tr><td class="delimiter" colspan="2"><div class="empty"></div></td></tr>
						</tbody>
						</table>
					</div>
					
					<div class="adm-filter-bottom">
						<input type="hidden" name="site_id" value="<?php echo $aDataItem['SITE_ID']; ?>">
						<input type="submit" class="adm-btn adm-btn-big"  name="submit" value="<?php echo GetMessage("LS_CS_INVITE_FORM_SUBMIT"); ?>">
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
</form>
<br/><br/>
<?php } ?>
	
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>