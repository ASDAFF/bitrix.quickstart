<?

use Bitrix\Main,
	 Bitrix\Main\Loader,
	 Bitrix\Main\SiteTable,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Application,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Config\Option,
	 Bitrix\Sale\Order,
	 Bitrix\Sale\Helpers,
	 Bitrix\Sale\Helpers\Admin\OrderEdit;

Loc::loadMessages(__FILE__);

global $APPLICATION;

if(!Loader::includeModule("sale")) {
	$APPLICATION->ThrowException(Loc::getMessage('AOS_SALE_MODULE_ERROR'));
	return false;
}

Class CApiOrderStatus
{
	const MODULE_ID = 'api.orderstatus';

	protected static $allowAdminForms = array(
		 '/bitrix/admin/sale_order_view.php',
		 '/bitrix/admin/sale_order_edit.php',
	);

	public static function initForm(&$form)
	{
		global $DB, $APPLICATION;

		$ORDER_ID = intval($_REQUEST['ID']);

		if($ORDER_ID && in_array($APPLICATION->GetCurPage(), self::$allowAdminForms)) {
			if($arOrder = self::getOrderFields($ORDER_ID)) {
				CJSCore::Init(array('jquery'));
				$APPLICATION->SetAdditionalCSS('/bitrix/js/api.orderstatus/styles.css');
				$APPLICATION->AddHeadScript('/bitrix/js/api.orderstatus/jquery.apiUpload.js');

				//1. Order status history tab
				ob_start();
				require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/api.orderstatus/tools/get_history.php");
				$historyContent = ob_get_contents();
				ob_end_clean();

				$tabHistory            = Loc::getMessage('AOS_INCLUDE_TAB_HISTORY');
				$tabHistory['CONTENT'] = $historyContent;
				$form->tabs[]          = $tabHistory;



				//Активные шлюзы
				$arGateway  = $arSmsStatus = array();
				$resGateway = Api\OrderStatus\SmsGatewayTable::getList(array(
					 'order'  => array('SORT' => 'ASC', 'ID' => 'ASC'),
					 'filter' => array('ACTIVE' => 'Y'),
				));
				if($arGateway = $resGateway->fetch()) {
					$gatewayParams = unserialize($arGateway['PARAMS']);
					$balance       = Api\OrderStatus\SMS::getBalance($gatewayParams);

					//2. Order status SMS history tab
					ob_start();
					require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/api.orderstatus/tools/get_sms_history.php");
					$smsHistoryContent = ob_get_contents();
					ob_end_clean();

					$tabSmsHistory            = Loc::getMessage('AOS_INCLUDE_TAB_SMS_HISTORY');
					$tabSmsHistory['CONTENT'] = $smsHistoryContent;
					$form->tabs[]             = $tabSmsHistory;


					//SMS-status
					$resSmsStatus = Api\OrderStatus\SmsStatusTable::getList(array(
						 'order'  => array('SORT' => 'ASC', 'ID' => 'ASC'),
						 'filter' => array('ACTIVE' => 'Y', '?SITE_ID' => $arOrder['SITE_ID']),
					));
					while($result = $resSmsStatus->fetch())
						$arSmsStatus[ $result['STATUS_ID'] ] = self::replaceMacros($arOrder, $result['DESCRIPTION']);
				}



				$statusContent = '';
				$isOrderView   = ($APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_view.php');


				//Статусы заказа
				$arStatus = array();
				$dbRes    = CSaleStatus::GetList(array(), array('LID' => LANGUAGE_ID), false, false, array('ID', 'NAME', 'DESCRIPTION'));
				while($arr = $dbRes->Fetch())
					$arStatus[ $arr['ID'] ] = $arr;

				if($arStatus) {
					foreach($arStatus as &$status)
						$status['DESCRIPTION'] = self::replaceMacros($arOrder, $status['DESCRIPTION']);
				}

				//Файлы привязанные к заказу
				$strFileList = '';
				if($arOrderFiles = Api\OrderStatus\FileTable::getOrderFiles($ORDER_ID)) {
					foreach($arOrderFiles as $arFile) {
						$fileId   = $arFile['ID'];
						$fileName = $arFile['ORIGINAL_NAME'];
						$fileSize = CFile::FormatSize($arFile['FILE_SIZE']);
						$fileExt  = pathinfo($fileName, PATHINFO_EXTENSION);
						$fileUrl  = '/upload/' . $arFile['SUBDIR'] . '/' . $arFile['FILE_NAME'];

						$strFileList .= '<li>';
						$strFileList .= '<div class="api-progress-bar"><div style="width: 100%;" rel="100" class="api-progress"></div><div class="api-icon-cancel" onclick="AOS_DeleteOrderFile(this,' . $fileId . ')"></div></div>';
						$strFileList .= '<div class="api-file-label">
													<span class="api-file-ext-' . $fileExt . '"></span>
													<a target="_blank" href="' . $fileUrl . '" class="api-file-name">' . $fileName . '</a>
													<span class="api-file-size">' . $fileSize . '</span>
											   </div>';
						$strFileList .= '</li>';
					}
				}


				//Шаблоны E-mail писем
				$arStatusTpl = array();
				$dbStatusTpl = \Api\OrderStatus\TemplateTable::getList(array(
					 'select' => array('ID', 'NAME', 'STATUS_ID', 'DESCRIPTION'),
					 'filter' => array('ACTIVE' => 'Y'),
				));
				while($arr = $dbStatusTpl->fetch())
					$arStatusTpl[ $arr['ID'] ] = $arr;

				if($arStatusTpl) {
					foreach($arStatusTpl as &$statusTpl)
						$statusTpl['DESCRIPTION'] = self::replaceMacros($arOrder, $statusTpl['DESCRIPTION']);

					$statusContent .= '
					<tr class="api-sale-custom-status-tpl" style="display:none">
						<td class="adm-detail-content-cell-l"></td>
						<td class="adm-detail-content-cell-r">
							<select id="STATUS_TPL_ID"></select>
						</td>
					</tr>';
				}

				$str_duplicate_to_sms = '';
				if($arGateway) {
					$str_duplicate_to_sms = '
						<tr>
							<td class="adm-detail-content-cell-l"></td>
							<td class="adm-detail-content-cell-r">
								<div style="margin-bottom: 5px; position:relative;">
									<input id="AOS_DUPLICATE_TO_SMS" class="adm-designed-checkbox" type="checkbox" name="AOS_DUPLICATE_TO_SMS" value="Y" style="vertical-align:middle">
									<label for="AOS_DUPLICATE_TO_SMS" class="adm-designed-checkbox-label"></label> ' . Loc::getMessage('AOS_INCLUDE_DUPLICATE_TO_SMS') . '<br>
								</div>
							</td>
						</tr>
					';
				}

				$statusContent .= '
					<tr class="aos-message">
						<td class="adm-detail-content-cell-l">' . Loc::getMessage('AOS_INCLUDE_EMAIL_MESSAGE_LABEL') . '</td>
						<td class="adm-detail-content-cell-r">
							<textarea name="AOS_MESSAGE" id="AOS_MESSAGE"></textarea>
						</td>
					</tr>
					' . $str_duplicate_to_sms . '
					<tr class="api-sale-custom-checkbox ' . ($isOrderView ? 'api-order-view' : '') . '">
						<td class="adm-detail-content-cell-l"></td>
						<td class="adm-detail-content-cell-r">
							<div style="margin-bottom: 5px; position:relative;">
								<input id="AOS_NOT_SEND_EMAIL" class="adm-designed-checkbox" type="checkbox" name="AOS_NOT_SEND_EMAIL" value="Y" style="vertical-align:middle">
								<label for="AOS_NOT_SEND_EMAIL" class="adm-designed-checkbox-label"></label> ' . Loc::getMessage('AOS_INCLUDE_NOT_SEND_EMAIL') . '<br>
							</div>
							<div style="margin-bottom: 5px; position:relative;">
								<input id="AOS_ATTACH_FILE" class="adm-designed-checkbox" type="checkbox" name="AOS_ATTACH_FILE" value="Y" style="vertical-align:middle">
								<label for="AOS_ATTACH_FILE" class="adm-designed-checkbox-label"></label> ' . Loc::getMessage('AOS_INCLUDE_ATTACH_FILE') . '<br>
							</div>
						</td>
					</tr>
					<tr class="ts-sale-custom-submit">
						<td class="adm-detail-content-cell-l"></td>
						<td class="adm-detail-content-cell-r">
							<div style="margin-bottom: 25px; position:relative;">
								<input type="button" value="' . Loc::getMessage('AOS_INCLUDE_BTN_SEND_MESSAGE') . '" onclick="AOS_SendStatusComment(this);">
							</div>
						</td>
					</tr>';

				if($arGateway) {
					$statusContent .= '
						<tr class="aos-sms-tr aos-sms-message">
							<td class="adm-detail-content-cell-l">' . Loc::getMessage('AOS_INCLUDE_SMS_MESSAGE_LABEL') . '</td>
							<td class="adm-detail-content-cell-r">
							' . $balance . '<br>
								<textarea name="AOS_SMS_MESSAGE" id="AOS_SMS_MESSAGE"></textarea>
							</td>
						</tr>
						<tr class="aos-sms-tr aos-sms-options ' . ($isOrderView ? 'api-order-view' : '') . '">
							<td class="adm-detail-content-cell-l"></td>
							<td class="adm-detail-content-cell-r">
								<div>   
									<input id="AOS_SEND_SMS" class="adm-designed-checkbox" type="checkbox" name="AOS_SEND_SMS" value="Y" style="vertical-align:middle">
									<label for="AOS_SEND_SMS" class="adm-designed-checkbox-label"></label> ' . Loc::getMessage('AOS_INCLUDE_SEND_SMS_CHECKBOX') . '
								</div>
							</td>
						</tr>
						<tr class="aos-sms-tr aos-sms-button">
							<td class="adm-detail-content-cell-l"></td>
							<td class="adm-detail-content-cell-r">
								<div style="margin-bottom:15px;position:relative;">
									<input type="button" value="' . Loc::getMessage('AOS_INCLUDE_BTN_SEND_SMS_MESSAGE') . '" onclick="AOS_SendSMS(this);">
								</div>
							</td>
						</tr>';
				}

				$statusContent .= '
					<tr class="ts-sale-custom-submit">
						<td class="adm-detail-content-cell-l"></td>
						<td class="adm-detail-content-cell-r">
							<div class="api-upload ' . ($isOrderView ? 'api-order-view' : '') . '">
								<div class="api-upload-drop">
									<span>' . Loc::getMessage('AOS_INCLUDE_UPLOAD_DROP_TEXT') . '</span>
									<input class="api-upload-file" type="file" name="file" multiple="multiple">
								</div>
								<ul class="api-file-list">' . $strFileList . '</ul>
								<span class="adm-input-file api-upload-button"><span>' . Loc::getMessage('AOS_INCLUDE_BTN_UPLOAD') . '</span></span>
							</div>
						</td>
					</tr>';
				?>
				<script type="text/javascript">

					var arStatus = <?=Json::encode($arStatus);?>;
					var arStatusTpl = <?=Json::encode($arStatusTpl);?>;
					var arSmsStatus = <?=Json::encode($arSmsStatus);?>;

					var extraData = {
						'sessid': BX.bitrix_sessid(),
						'ORDER_ID': <?=$ORDER_ID?>,
						'STATUS_ID': ''
					};

					//E-mail
					function AOS_SendStatusComment(obj) {
						//Show loader
						$(obj).addClass('adm-btn-load').attr('disabled', true).after('<div style="top: 50%; margin-top: -10px; left: 100px;" class="adm-btn-load-img"></div>');

						if ($('#AOS_MESSAGE').length) {
							var AOS_MESSAGE = $('#AOS_MESSAGE').val();

							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: '/bitrix/admin/api_orderstatus_send_message.php',
								data: {
									sessid: BX.bitrix_sessid(),
									ORDER_ID: <?=$ORDER_ID?>,
									STATUS_ID: $('#STATUS_ID').val(),
									AOS_ATTACH_FILE: $('#AOS_ATTACH_FILE:checked').val(),
									MESSAGE: AOS_MESSAGE,
								},
								timeout: 20000,
								error: function (request, error) {
									alert('<?=Loc::getMessage('AOS_INCLUDE_SEND_MESSAGE_ERROR')?>');

									//Remove loader
									$(obj).removeClass('adm-btn-load').attr('disabled', false).next('.adm-btn-load-img').detach();
								},
								success: function (data) {

									//Remove loader
									$(obj).removeClass('adm-btn-load').attr('disabled', false).next('.adm-btn-load-img').detach();

									$('#aos_history_edit_table > tbody > tr').load(
										 '/bitrix/admin/api_orderstatus_get_history.php',
										 {
											 ID:<?=$ORDER_ID?>
										 },
										 function () {
											 if (data.message.length) {
												 alert(data.message);
											 }
											 else
												 alert('aos.data.message.empty');
										 }
									);

									//console.log(data)
								}
							});
						}
					}

					function AOS_DeleteOrderFile(element, file_id) {
						var LI = $(element).closest('li');

						if (!file_id) {
							LI.hide(200).remove();
						}
						else if (confirm("<?=Loc::getMessage('AOS_INCLUDE_CONFIRM_DELETE')?>")) {
							$.ajax({
								type: 'POST',
								method: 'POST',
								dataType: 'json',
								url: '/bitrix/admin/api_orderstatus_upload.php',
								data: {
									'sessid': BX.bitrix_sessid(),
									'action': 'delete',
									'ORDER_ID': <?=$ORDER_ID?>,
									'FILE_ID': file_id,
								},
								async: true,
								timeout: 20000,
								error: function (request, error) {
									if (error.length)
										alert('Error! ' + error);
								},
								success: function (data) {
									LI.hide().remove();
								}
							});
						}

						return false;
					}

					function AOS_ChangeStatus() {
						var statusId = $('#STATUS_ID').find('option:selected').val();

						if (!$.isEmptyObject(arStatusTpl)) {
							var optionEmpty = '<option value=""><?=Loc::getMessage('AOS_INCLUDE_EMPTY_EMAIL_OPTION')?></option>';
							var optionList = '';
							for (var i in arStatusTpl) {
								if (arStatusTpl[i].STATUS_ID == statusId)
									optionList += '<option value="' + arStatusTpl[i].ID + '">' + arStatusTpl[i].NAME + '</option>';
							}

							if (optionList.length)
								$('#STATUS_TPL_ID').html(optionEmpty + optionList).closest('tr').show();
							else
								$('#STATUS_TPL_ID').html('').closest('tr').hide();

						}

						if (!$.isEmptyObject(arSmsStatus)) {
							$('#AOS_SMS_MESSAGE').val(arSmsStatus[statusId]);
						}
					}

					function AOS_ChangeStatusTpl() {
						if (!$.isEmptyObject(arStatusTpl)) {
							var tplId = $('#STATUS_TPL_ID').find('option:selected').val();
							var statusId = $('#STATUS_ID').find('option:selected').val();

							if (tplId) {
								$('#AOS_MESSAGE').val(arStatusTpl[tplId].DESCRIPTION);
							}
							else {
								$('#AOS_MESSAGE').val(arStatus[statusId].DESCRIPTION);
							}
						}
					}

					//SMS
					function AOS_SendSMS(obj) {
						//Show loader
						$(obj).addClass('adm-btn-load').attr('disabled', true).after('<div style="top: 50%; margin-top: -10px; left: 100px;" class="adm-btn-load-img"></div>');

						if ($('#AOS_SMS_MESSAGE').length) {
							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: '/bitrix/admin/api_orderstatus_send_sms.php',
								data: {
									sessid: BX.bitrix_sessid(),
									orderId: <?=$ORDER_ID?>,
									siteId: '<?=$arOrder['SITE_ID']?>',
									statusId: $('#STATUS_ID').val(),
									phone: '<?=$arOrder['ORDER_PHONE']?>',
									message: $('#AOS_SMS_MESSAGE').val()
								},
								timeout: 20000,
								error: function (request, error) {
									alert('<?=Loc::getMessage('AOS_INCLUDE_SEND_MESSAGE_ERROR')?>');

									//Remove loader
									$(obj).removeClass('adm-btn-load').attr('disabled', false).next('.adm-btn-load-img').detach();
								},
								success: function (data) {

									//Remove loader
									$(obj).removeClass('adm-btn-load').attr('disabled', false).next('.adm-btn-load-img').detach();

									$('#aos_sms_history_edit_table > tbody > tr').load(
										 '/bitrix/admin/api_orderstatus_get_sms_history.php',
										 {
											 ID:<?=$ORDER_ID?>
										 },
										 function () {
											 if (data.message.length) {
												 alert(data.message);
											 }
											 else
												 alert('aos.data.message.empty');
										 }
									);
								}
							});
						}
					}

					jQuery(document).ready(function ($) {

						$(document).on('change', '#STATUS_TPL_ID', function () {
							AOS_ChangeStatusTpl();
						});

						$(document).on('change', '#STATUS_ID', function () {

							var activeStatusID = $(this).find('option:selected').val();
							extraData.STATUS_ID = activeStatusID;

							if (!$.isEmptyObject(arStatus) && activeStatusID.length) {
								$('#AOS_MESSAGE').val(arStatus[activeStatusID].DESCRIPTION);
								AOS_ChangeStatus();
							}
							else
								alert('<?=Loc::getMessage('AOS_INCLUDE_STATUS_FALSE')?>');
						});

						$('#STATUS_ID').parent('td').parent('tr').after('<?=CUtil::JSEscape($statusContent)?>');

						$('#STATUS_ID').change();

						var smsMessage = $('#AOS_SMS_MESSAGE').val();
						$(document).on('change', '#AOS_DUPLICATE_TO_SMS', function () {
							var emailMessage = $('#AOS_MESSAGE').val();
							if ($(this).is(':checked')) {
								$('#AOS_SMS_MESSAGE').val(emailMessage);
								$('#AOS_SEND_SMS').attr('checked', true);
							} else {
								$('#AOS_SMS_MESSAGE').val(smsMessage);
								$('#AOS_SEND_SMS').attr('checked', false);
							}
						});

						$('#tab_order_edit_table').find('textarea').each(function () {
							var offset = this.offsetHeight - this.clientHeight;
							var resizeTextarea = function (el) {
								$(el).css('height', 'auto').css('height', el.scrollHeight + offset);
							};
							$(this).on('keyup input', function () { resizeTextarea(this); }).removeAttr('data-autoresize');
							resizeTextarea(this);
						});

						<?if(!$isOrderView):?>
						$('.api-upload').apiUpload({
							url: '/bitrix/admin/api_orderstatus_upload.php',
							extraData: extraData
						});
						<?endif?>
					});

				</script>
				<?
			}
		}
	}

	public static function OnOrderStatusSendEmail($orderId, &$eventName, &$arFields, $statusId)
	{
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		$siteId        = ($request->get('SITE_ID') ? $request->get('SITE_ID') : $request->get('LID'));
		$userId        = intval($GLOBALS['USER']->GetID());
		$bNotSendEmail = $request->get('AOS_NOT_SEND_EMAIL') == 'Y';
		$bAttachFile   = $request->get('AOS_ATTACH_FILE') == 'Y';
		$dateCreate    = \Bitrix\Main\Type\DateTime::createFromTimestamp(time());

		//HistoryTable data
		$arAddFields = array(
			 'ORDER_ID'    => $orderId,
			 'USER_ID'     => $userId,
			 'DATE_CREATE' => $dateCreate,
			 'STATUS'      => $statusId,
			 'DESCRIPTION' => ($request['AOS_MESSAGE'] ? $request['AOS_MESSAGE'] : $arFields['ORDER_DESCRIPTION']),
			 'LID'         => ($siteId ? $siteId : 's1'),
			 'MAIL'        => ($bNotSendEmail ? 'N' : 'Y'),
			 'FILES'       => ($bAttachFile ? 'Y' : 'N'),
		);

		$arAddFields['DESCRIPTION'] = CApiOrderStatus::getFormatText($arAddFields['DESCRIPTION']);

		$arOrderFields = self::getOrderFields($orderId);
		if($arAddFields['DESCRIPTION'])
			$arAddFields['DESCRIPTION'] = self::replaceMacros($arOrderFields, $arAddFields['DESCRIPTION']);

		$arOrderBlocks = self::getOrderBlocks($orderId);
		if($arAddFields['DESCRIPTION'])
			$arAddFields['DESCRIPTION'] = self::replaceMacros($arOrderBlocks, $arAddFields['DESCRIPTION']);

		$arFields['ID']                = $orderId;
		$arFields['ORDER_DESCRIPTION'] = $arAddFields['DESCRIPTION'];
		$arFields['AOS_ATTACH_FILES']  = ($bAttachFile ? 'Y' : 'N');
		//$arFields['AOS_SEND_SMS']        = ($bSendSms ? 'Y' : 'N');

		\Api\OrderStatus\HistoryTable::add($arAddFields);

		//SMS
		$bSendSms = $request->get('AOS_SEND_SMS') == 'Y';
		if($bSendSms) {
			$errors  = array();
			$message = $request->get('AOS_SMS_MESSAGE');
			$phone   = $arOrderFields['ORDER_PHONE'];
			$data    = array(
				 'GATEWAY_ID' => null,
				 'SMS_ID'     => null,
				 'SMS_ERROR'  => null,
			);


			if($message)
				$message = self::replaceMacros($arOrderFields, $message);

			if($phone && $message && $siteId) {
				$result = \Api\OrderStatus\SMS::send($phone, $message, $siteId);
				if($result->isSuccess()) {
					$data = $result->getData();
				}
				else {
					$data['SMS_ERROR'] = join("\n", $result->getErrorMessages());
				}
			}
			else {
				if(!$phone)
					$errors[] = Loc::getMessage('AOS_INCLUDE_SEND_SMS_ERROR_PHONE');

				if(!$message)
					$errors[] = Loc::getMessage('AOS_INCLUDE_SEND_SMS_ERROR_MESSAGE');

				if(!$siteId)
					$errors[] = Loc::getMessage('AOS_INCLUDE_SEND_SMS_ERROR_SITE');

				$data['SMS_ERROR'] = join("\n", $errors);
			}

			//Add Status History
			\Api\OrderStatus\SmsHistoryTable::add(array(
				 'ORDER_ID'    => $orderId,
				 'USER_ID'     => $userId,
				 'SITE_ID'     => ($siteId ? $siteId : 's1'),
				 'STATUS_ID'   => $statusId,
				 'DATE_CREATE' => $dateCreate,
				 'GATEWAY_ID'  => $data['GATEWAY_ID'],
				 'SMS_ID'      => $data['SMS_ID'],
				 'SMS_ERROR'   => $data['SMS_ERROR'],
				 'SMS_TEXT'    => CApiOrderStatus::getFormatText($message),
			));
		}


		if($bNotSendEmail)
			return false;
	}

	public static function OnOrderNewSendEmail($orderId, &$eventName, &$arFields)
	{
		$arFields['ID'] = $orderId;
	}

	public static function OnBeforeEventAdd(&$event, &$lid, &$arFields, &$message_id = '', &$files = array(), &$languageId = '')
	{
		$arOptions = \Api\OrderStatus\OptionTable::getOtions($lid);

		$arEventType = ($arOptions['EVENT_TYPE'] ? explode(',', $arOptions['EVENT_TYPE']) : false);
		if(!$arEventType || ($arEventType && in_array($event, $arEventType))) {
			$arFields['AOS_MACROS_REPLACE'] = 'Y';

			if($arOptions['MAIL_REPLACE'] == 'Y') {
				$arFields['AOS_MAIL_REPLACE'] = 'Y';
				$arFields['AOS_MAIL_HEADER']  = $arOptions['MAIL_HEADER'];
				$arFields['AOS_MAIL_CONTENT'] = $arOptions['MAIL_CONTENT'];
				$arFields['AOS_MAIL_FOOTER']  = $arOptions['MAIL_FOOTER'];
			}

			if($event == 'SALE_NEW_ORDER') {
				$arFields['AOS_MAIL_SALE_NEW_ORDER']         = $arOptions['MAIL_SALE_NEW_ORDER'];
				$arFields['AOS_MAIL_SALE_NEW_ORDER_SUBJECT'] = trim($arOptions['MAIL_SALE_NEW_ORDER_SUBJECT']);
			}

			$arMailFields = array();
			if(substr($event, 0, 4) == 'SALE' && $arFields['ORDER_ID'] && $arFields['ID']) {
				$arMailFields = self::getOrderFields($arFields['ID']);

				$arFields['AOS_MAIL_BLOCKS'] = self::getOrderBlocks($arFields['ID']);
			}
			else {
				$arMailFields = self::getDefaultFields($lid);
			}

			$arFields = array_merge($arFields, $arMailFields);

			if($arFields['ID']) {
				if($arFields['AOS_ATTACH_FILES'] == 'Y') {
					$row = Api\OrderStatus\FileTable::getList(array(
						 'filter' => array('=ORDER_ID' => $arFields['ID']),
					))->fetch();

					if($arFilesId = explode(',', $row['FILE_ID']))
						$files = $arFilesId;
				}
			}
		}
	}

	public static function OnBeforeEventSend(&$arFields, &$eventMessage)
	{
		if($arFields['AOS_MACROS_REPLACE'] == 'Y') {

			if($eventMessage['BODY_TYPE'] == 'text') {
				//$eventMessage['MESSAGE'] = str_replace(PHP_EOL, "<br />\n", $eventMessage['MESSAGE']);
				$eventMessage['MESSAGE']   = nl2br($eventMessage['MESSAGE']);
				$eventMessage['BODY_TYPE'] = 'html';
			}

			if($arFields['AOS_MAIL_BLOCKS']) {

				//ТЕЛО ПИСЬМА НОВОГО ЗАКАЗА из настроек модуля, заменяет штатный шаблон письма о новом заказе + макросы блоков
				if($arFields['AOS_MAIL_SALE_NEW_ORDER'])
					$eventMessage['MESSAGE'] = self::replaceMacros($arFields['AOS_MAIL_BLOCKS'], $arFields['AOS_MAIL_SALE_NEW_ORDER']);

				//Обработчик макросов блоков во всех письмах с типом SALE
				$eventMessage['MESSAGE'] = self::replaceMacros($arFields['AOS_MAIL_BLOCKS'], $eventMessage['MESSAGE']);
				unset($arFields['AOS_MAIL_BLOCKS']);
			}

			if($arFields['AOS_MAIL_SALE_NEW_ORDER_SUBJECT'])
				$eventMessage['SUBJECT'] = $arFields['AOS_MAIL_SALE_NEW_ORDER_SUBJECT'];
		}

		if($arFields['AOS_MAIL_REPLACE'] == 'Y') {
			if($arFields['AOS_MAIL_CONTENT'] && strpos($arFields['AOS_MAIL_CONTENT'], '#WORK_AREA#') !== false) {
				$eventMessage['MESSAGE'] = str_replace('#WORK_AREA#', $eventMessage['MESSAGE'], $arFields['AOS_MAIL_CONTENT']);
			}

			$eventMessage['MESSAGE'] = $arFields['AOS_MAIL_HEADER'] . $eventMessage['MESSAGE'] . $arFields['AOS_MAIL_FOOTER'];
		}


		if($arFields['AOS_MACROS_REPLACE'] == 'Y') {
			$eventMessage['MESSAGE']     = self::replaceMacros($arFields, $eventMessage['MESSAGE']);
			$eventMessage['MESSAGE_PHP'] = $eventMessage['MESSAGE'];
		}
	}




	//==============================================================================
	// Блок "Оплата"
	//==============================================================================
	protected static function getPaySystemParams($paySystemId)
	{
		static $result = array();

		if(!isset($result[ $paySystemId ])) {
			$data = array();
			if($paySystemId > 0) {
				$data = \Bitrix\Sale\Internals\PaySystemActionTable::getRow(array(
					 'select' => array('ID', 'NAME', 'LOGOTIP', 'HAVE_RESULT', 'RESULT_FILE', 'ACTION_FILE'),
					 'filter' => array('ID' => $paySystemId),
				));
			}

			$result[ $paySystemId ] = $data;
		}

		return $result[ $paySystemId ];
	}

	protected static function getOrderPaySystemParams(Order $order, $options)
	{
		$arPayments        = array();
		$paymentCollection = $order->getPaymentCollection();

		/** @var \Bitrix\Sale\Payment $payment */
		foreach($paymentCollection as $payment) {
			$fields               = $payment->getFieldValues();
			$fields['PAY_SYSTEM'] = self::getPaySystemParams($fields['PAY_SYSTEM_ID']);

			$fields['DATE_BILL']           = (string)$fields['DATE_BILL'];
			$fields['DATE_PAID']           = (string)$fields['DATE_PAID'];
			$fields['PAY_VOUCHER_DATE']    = (string)$fields['PAY_VOUCHER_DATE'];
			$fields['DATE_RESPONSIBLE_ID'] = (string)$fields['DATE_RESPONSIBLE_ID'];
			$fields['DATE_ALLOW_DELIVERY'] = (string)$fields['DATE_ALLOW_DELIVERY'];
			$fields['SALE_URL']            = (string)$options['SALE_URL'];

			$arPayments[] = $fields;
		}


		return $arPayments;
	}


	//==============================================================================
	// Блок "Информация по оплатам"
	//==============================================================================
	protected static function getOrderFinanceParams(Order $order)
	{
		$currencyBudget = 0;
		if($order->getUserId() > 0) {
			$res            = \CSaleUserAccount::getList(
				 array(),
				 array(
						'USER_ID'  => $order->getUserId(),
						'CURRENCY' => $order->getCurrency(),
						'LOCKED'   => 'N',
				 ),
				 false,
				 false,
				 array(
						'CURRENT_BUDGET',
				 )
			);
			$userAccount    = $res->Fetch();
			$currencyBudget = $userAccount['CURRENT_BUDGET'];
		}

		$payable = $order->getPrice() - $order->getSumPaid();
		$price   = $order->getPrice();
		$sumPaid = $order->getSumPaid();
		$data    = array(
			 'PRICE'        => ($price) ? $price : 0,
			 'SUM_PAID'     => ($sumPaid) ? $sumPaid : 0,
			 'PAYABLE'      => ($payable >= 0) ? $payable : 0,
			 'CURRENCY'     => $order->getCurrency(),
			 'BUYER_BUDGET' => $currencyBudget,
			 'STATUS_ID'    => $order->getField('STATUS_ID'),
		);

		return $data;
	}


	//==============================================================================
	// Блок "Покупатель"
	//==============================================================================
	protected static function getOrderBuyerParams(Order $order, $readonly = false)
	{
		$result             = array();
		$propertyCollection = $order->getPropertyCollection();

		foreach($propertyCollection->getGroups() as $group) {

			/** @var \Bitrix\Sale\PropertyValue $property */
			//foreach($propertyCollection->getGroupProperties($group['ID']) as $property) {
			foreach($propertyCollection->getPropertiesByGroupId($group['ID']) as $property) {
				$propertyId    = $property->getId();
				$propertyValue = $property->getValue();
				if($readonly && empty($propertyValue))
					continue;

				$group['PROPERTIES'][ $propertyId ] = array(
					 'NAME'  => $property->getName(),
					 'VALUE' => (($readonly) ? $property->getViewHtml() : $property->getEditHtml()),
				);
			}

			$result['GROUP'][ $group['ID'] ] = $group;
		}

		$result['GROUP'][] = array(
			 'ID'    => 'USER_DESCRIPTION',
			 'VALUE' => $order->getField('USER_DESCRIPTION'),
		);

		return $result;
	}


	//==============================================================================
	// Блок "Отгрузка"
	//==============================================================================
	/**
	 * @param \Bitrix\Sale\Shipment $shipment
	 *
	 * @return float|int
	 * @throws Main\ArgumentNullException
	 * @throws SystemException
	 */
	protected static function getDeliveryPrice(\Bitrix\Sale\Shipment $shipment)
	{
		$totalPrice = 0;

		if($shipment->getDeliveryId()) {
			$service = Bitrix\Sale\Delivery\Services\Manager::getObjectById($shipment->getDeliveryId());
			if($service && !$service->canHasProfiles()) {
				$extraServices        = $shipment->getExtraServices();
				$extraServicesManager = $service->getExtraServices();
				$extraServicesManager->setValues($extraServices);//Bitrix\Sale\Delivery\ExtraServices
				$result     = $service->calculate($shipment);
				$totalPrice = $result->getPrice();
			}
		}
		return $totalPrice;
	}

	//\Bitrix\Sale\Helpers\Admin\Blocks\OrderShipmentStatus::getShipmentStatusList();
	protected static function getShipmentStatusList()
	{
		$context = Application::getInstance()->getContext();
		$lang    = $context->getLanguage();

		$shipmentStatuses = array();
		$dbRes            = \Bitrix\Sale\Internals\StatusTable::getList(array(
			 'select' => array('ID', 'NAME' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME'),
			 'filter' => array(
					'=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => $lang,
					'=TYPE'                                             => 'D',
			 ),
		));

		while($shipmentStatus = $dbRes->fetch())
			$shipmentStatuses[ $shipmentStatus['ID'] ] = $shipmentStatus['NAME'] . ' [' . $shipmentStatus['ID'] . ']';

		return $shipmentStatuses;
	}

	//bitrix/modules/sale/admin/order.php SHIPMENT
	protected static function getOrderShipmentParams(Order $order, $options, $error = false, $needRecalculate = true)
	{
		global $USER;
		static $users = array();
		$result = array(
			 'SALE_URL'        => $options['SALE_URL'],
			 'SHIPMENT_STATUS' => self::getShipmentStatusList(),
			 'SHIPMENT'        => array(),
		);


		$shipmentCollection = $order->getShipmentCollection();

		/** @var \Bitrix\Sale\Shipment $shipment */
		foreach($shipmentCollection as $shipment) {
			if($shipment->isSystem())
				continue;

			if($error) {
				$fields = array();
			}
			else {
				$fields                      = $shipment->getFieldValues();
				$fields['DELIVERY_STORE_ID'] = $shipment->getStoreId();
				$fields["EXTRA_SERVICES"]    = $shipment->getExtraServices();
				$fields["STORE"]             = $shipment->getStoreId();
			}


			if($fields['DELIVERY_DOC_DATE']) {
				$date                        = new \Bitrix\Main\Type\Date($fields['DELIVERY_DOC_DATE']);
				$fields['DELIVERY_DOC_DATE'] = $date->toString();
			}

			if($fields['DATE_INSERT']) {
				$dateInset             = new \Bitrix\Main\Type\Date($fields['DATE_INSERT']);
				$fields['DATE_INSERT'] = $dateInset->toString();
			}

			$empDeductedId = $fields['EMP_DEDUCTED_ID'];
			if($empDeductedId > 0) {
				if(!array_key_exists($empDeductedId, $users))
					$users[ $empDeductedId ] = $USER->GetByID($empDeductedId)->Fetch();
				$fields['EMP_DEDUCTED_ID_NAME']      = $users[ $empDeductedId ]['NAME'];
				$fields['EMP_DEDUCTED_ID_LAST_NAME'] = $users[ $empDeductedId ]['LAST_NAME'];
			}

			$empAllowDeliveryId = $fields['EMP_ALLOW_DELIVERY_ID'];
			if($empAllowDeliveryId > 0) {
				if(!array_key_exists($empAllowDeliveryId, $users))
					$users[ $empAllowDeliveryId ] = $USER->GetByID($empAllowDeliveryId)->Fetch();
				$fields['EMP_ALLOW_DELIVERY_ID_NAME']      = $users[ $empAllowDeliveryId ]['NAME'];
				$fields['EMP_ALLOW_DELIVERY_ID_LAST_NAME'] = $users[ $empAllowDeliveryId ]['LAST_NAME'];
			}

			$empCanceledId = $fields['EMP_CANCELED_ID'];
			if($empCanceledId > 0) {
				if(!array_key_exists($empCanceledId, $users))
					$users[ $empCanceledId ] = $USER->GetByID($empCanceledId)->Fetch();
				$fields['EMP_CANCELLED_ID_NAME']      = $users[ $empCanceledId ]['NAME'];
				$fields['EMP_CANCELLED_ID_LAST_NAME'] = $users[ $empCanceledId ]['LAST_NAME'];
			}

			$empMarkedId = $fields['EMP_MARKED_ID'];
			if($empMarkedId > 0) {
				if(!array_key_exists($empMarkedId, $users))
					$users[ $empMarkedId ] = $USER->GetByID($empMarkedId)->Fetch();
				$fields['EMP_MARKED_ID_NAME']      = $users[ $empMarkedId ]['NAME'];
				$fields['EMP_MARKED_ID_LAST_NAME'] = $users[ $empMarkedId ]['LAST_NAME'];
			}

			/** @var \Bitrix\Sale\Order $order */
			//$order = $shipment->getCollection()->getOrder();
			$fields['CURRENCY'] = $order->getCurrency();

			$fields['CALCULATED_PRICE'] = self::getDeliveryPrice($shipment);
			if($fields['CUSTOM_PRICE_DELIVERY'] == 'Y' && $fields['ID'] <= 0)
				$fields['BASE_PRICE_DELIVERY'] = $shipment->getField('BASE_PRICE_DELIVERY');

			$discounts   = OrderEdit::getDiscountsApplyResult($order, $needRecalculate);
			$shipmentIds = $order->getDiscount()->getShipmentsIds();

			foreach($shipmentIds as $shipmentId) {
				if($shipmentId == $shipment->getId())
					$fields['DISCOUNTS'] = $discounts;
			}

			/** @var \Bitrix\Sale\Delivery\Services\Base $delivery */
			$delivery = $shipment->getDelivery();

			if(!is_null($delivery)) {

				$fields['HAS_TRACKING'] = strlen($delivery->getTrackingClass()) > 0 ? true : false;
				//$fields['DELIVERY_ADDITIONAL_INFO_EDIT'] = $delivery->getAdditionalInfoShipmentEdit($shipment);
				//$fields['DELIVERY_ADDITIONAL_INFO_VIEW'] = $delivery->getAdditionalInfoShipmentView($shipment);
			}

			$result['SHIPMENT'][] = $fields;
		}

		return $result;
	}


	//==============================================================================
	// Блок "Параметры заказа" (statusorder)
	//==============================================================================
	public static function getUserInfo($userId)
	{
		static $users = array();

		$userId = intval($userId);
		if($userId <= 0)
			return array('ID' => 0, 'NAME' => '', 'LOGIN' => '');

		if(isset($users[ $userId ]))
			return $users[ $userId ];

		$user = Main\UserTable::getList(array(
			 'select' => array('ID', 'LOGIN', 'NAME'),
			 'filter' => array('=ID' => $userId),
		))->fetch();

		if($user)
			$users[ $userId ] = $user;
		else
			$user = array('ID' => 0, 'NAME' => '', 'LOGIN' => '');

		return $user;
	}

	//bitrix/modules/sale/lib/helpers/admin/blocks/orderstatus.php
	public static function getStatusesList($userId, $orderStatus = false)
	{
		if($orderStatus === false)
			$orderStatus = \Bitrix\Sale\OrderStatus::getInitialStatus();

		//$result = \Bitrix\Sale\OrderStatus::getAllowedUserStatuses($userId, $orderStatus);
		$result = array();

		if(empty($result[ $orderStatus ])) {
			$dbRes = \Bitrix\Sale\Internals\StatusTable::getList(array(
				 'select' => array(
						'ID',
						'NAME'        => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.NAME',
						'DESCRIPTION' => 'Bitrix\Sale\Internals\StatusLangTable:STATUS.DESCRIPTION',
				 ),
				 'filter' => array(
						'=Bitrix\Sale\Internals\StatusLangTable:STATUS.LID' => LANGUAGE_ID,
						'=ID'                                               => $orderStatus,
				 ),
			));

			if($status = $dbRes->fetch())
				$result = array($orderStatus => $status['NAME']) + $result; //$result = array($orderStatus => $status);
		}

		return $result;
	}

	protected static function getOrderStatusParams(Order $order)
	{
		static $result = null;

		if($result === null) {
			$creator = static::getUserInfo($order->getField("CREATED_BY"));

			if(strlen($order->getField("CREATED_BY")) > 0)
				$creatorName = OrderEdit::getUserName($order->getField("CREATED_BY"), $order->getSiteId());
			else
				$creatorName = "";

			if(strlen($order->getField("EMP_CANCELED_ID")) > 0)
				$cancelerName = OrderEdit::getUserName($order->getField("EMP_CANCELED_ID"), $order->getSiteId());
			else
				$cancelerName = "";

			$sourceName = "";

			if(strlen($order->getField('XML_ID')) > 0) {
				$dbRes = \Bitrix\Sale\TradingPlatform\OrderTable::getList(array(
					 'filter' => array(
							'ORDER_ID' => $order->getId(),
					 ),
					 'select' => array('SOURCE_NAME' => 'TRADING_PLATFORM.NAME'),
				));

				if($tpOrder = $dbRes->fetch())
					$sourceName = $tpOrder['SOURCE_NAME'];
			}

			$result = array(
				 "ID"                => $order->getId(),
				 "ORDER_ID"          => $order->getField('ACCOUNT_NUMBER'),
				 "DATE_INSERT"       => $order->getDateInsert()->toString(),
				 "DATE_UPDATE"       => $order->getField('DATE_UPDATE')->toString(),
				 "CREATOR_USER_NAME" => $creatorName,
				 "CREATOR_USER_ID"   => $creator["ID"],
				 "STATUS_ID"         => $order->getField('STATUS_ID'),
				 "CANCELED"          => $order->getField("CANCELED"),
				 "EMP_CANCELED_NAME" => $cancelerName,
				 "SOURCE_NAME"       => $sourceName,
			);

			if(intval($order->getField('AFFILIATE_ID')) > 0) {
				$result["AFFILIATE_ID"] = intval($order->getField('AFFILIATE_ID'));

				$dbAffiliate = \CSaleAffiliate::GetList(
					 array(),
					 array("ID" => $result["AFFILIATE_ID"]),
					 false,
					 false,
					 array("ID", "USER_ID")
				);

				if($arAffiliate = $dbAffiliate->Fetch()) {
					$result["AFFILIATE_USER_ID"] = $arAffiliate["USER_ID"];
					$result["AFFILIATE_NAME"]    = OrderEdit::getUserName($arAffiliate["USER_ID"], $order->getSiteId());
				}
				else {
					$result["AFFILIATE_USER_ID"] = 0;
					$result["AFFILIATE_NAME"]    = "-";
				}
			}
		}

		return $result;
	}



	//==============================================================================
	// Блок "Состав заказа" (basket)
	//==============================================================================
	protected static function getProductFields($arElementId)
	{
		if(!Loader::includeModule('iblock'))
			return array();

		if(empty($arElementId))
			return array();

		$result   = array();
		$arSelect = array('ID', 'IBLOCK_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE');

		$res = \CIBlockElement::getList(
			 array(),
			 array('=ID' => array_unique($arElementId)),
			 false,
			 false,
			 $arSelect
		);
		while($arItem = $res->Fetch()) {
			$arItem['RESIZE_PICTURE'] = array();
			$picture                  = ($arItem['DETAIL_PICTURE'] ? $arItem['DETAIL_PICTURE'] : $arItem['PREVIEW_PICTURE']);

			if($picture) {
				$arFileTmp                = \CFile::ResizeImageGet($picture, array('width' => '64', 'height' => '64'));
				$arItem['RESIZE_PICTURE'] = array_change_key_case($arFileTmp, CASE_UPPER);
			}

			$result[ $arItem['ID'] ] = array(
				 'PREVIEW_PICTURE' => $arItem['PREVIEW_PICTURE'],
				 'DETAIL_PICTURE'  => $arItem['DETAIL_PICTURE'],
				 'RESIZE_PICTURE'  => $arItem['RESIZE_PICTURE'],
			);
		}

		return $result;
	}

	protected static function getProductProps(&$arElementLink, $arElementId, $bCatalog)
	{
		Loader::includeModule('iblock');

		$arCatOptions          = \Api\OrderStatus\OptionTable::getRow(array(
			 'filter' => array('=NAME' => 'CATALOG'),
		));
		$iblockAllPropertyList = unserialize($arCatOptions['VALUE']);

		$iblockGroup  = array();
		$itemIterator = \Bitrix\Iblock\ElementTable::getList(array(
			 'select' => array('ID', 'IBLOCK_ID'),
			 'filter' => array('@ID' => $arElementId),
		));
		while($item = $itemIterator->fetch()) {
			$iblockGroup[ $item['IBLOCK_ID'] ] = $item['IBLOCK_ID'];
		}

		//Всем товарам корзины ищем свойства, т.к. они могут быть из разных каталогов с разными свойствами
		if($arElementLink && $iblockGroup) {
			foreach($iblockGroup as $iblockId) {
				$propertyList = array();

				if($iblockPropertyList = $iblockAllPropertyList[ $iblockId ]) {

					//Все свойства товара
					if($iblockPropertyList == 'all') {
						$propertyIterator = Bitrix\Iblock\PropertyTable::getList(array(
							 'select' => array('ID'),
							 'filter' => array('=IBLOCK_ID' => $iblockId),
							 'order'  => array('SORT' => 'ASC', 'NAME' => 'ASC'),
						));
						while($property = $propertyIterator->fetch()) {
							$propertyList[] = $property['ID'];
						}
					}
					else {
						//Только выбранные свойства
						$propertyList = explode(',', $iblockPropertyList);
					}
				}

				if($propertyList) {
					$filter         = array('ID' => $arElementId, 'IBLOCK_ID' => $iblockId);
					$propertyFilter = array('ID' => $propertyList);
					CIBlockElement::GetPropertyValuesArray($arElementLink, $iblockId, $filter, $propertyFilter);
				}
			}

			foreach($arElementLink as &$arItem) {
				if($arItem['PROPERTIES']) {
					if($bCatalog)
						CCatalogDiscount::SetProductPropertiesCache($arItem['PRODUCT_ID'], $arItem['PROPERTIES']);

					foreach($arItem['PROPERTIES'] as $pid => &$prop) {
						if(isset($arItem['DISPLAY_PROPERTIES'][ $pid ]))
							continue;

						if($prop['VALUE']) {
							$arProperty = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, 'catalog_out');

							$arItem['DISPLAY_PROPERTIES'][ $pid ] = $arProperty;
						}
					}
				}
			}
		}
	}

	//bitrix/modules/sale/lib/helpers/admin/blocks/orderbasket.php
	//bitrix/modules/sale/lib/helpers/admin/orderedit.php
	//Bitrix\Sale\Helpers\Admin\Blocks\OrderBasket::prepareData
	protected static function getOrderBasketParams(Order $order, $options)
	{
		$arElementId = array();
		$bCatalog    = Loader::includeModule('catalog');

		$arResult = array(
			//'bUsingVat'   => false,
			'SALE_URL'     => $options['SALE_URL'],
			'BASKET_ITEMS' => array(),
		);

		//$arResult['MAX_DIMENSIONS'] = array();
		//$arResult['ITEMS_DIMENSIONS'] = array();

		$orderDiscount = $order->getDiscount();

		$discounts = array();
		if($orderDiscount) {
			$discounts = OrderEdit::getDiscountsApplyResult($order, false);

			if(!$discounts)
				$discounts = OrderEdit::getDiscountsApplyResult($order, true);
		}


		/** @var \Bitrix\Sale\BasketItem $basketItem */
		foreach($order->getBasket() as $basketItem) {
			$basketCode   = $basketItem->getBasketCode();
			$arBasketItem = $basketItem->getFieldValues();

			$arBasketItem['DATE_INSERT'] = $order->getDateInsert()->toString();
			$arBasketItem['DATE_UPDATE'] = $order->getField('DATE_UPDATE')->toString();

			if($basketItem->getVatRate() > 0) {
				//$arResult['bUsingVat']     = 'Y';
				$arBasketItem['VAT_VALUE'] = $basketItem->getVat();
			}
			$arBasketItem['QUANTITY']        = $basketItem->getQuantity();
			$arBasketItem['PRICE_FORMATED']  = SaleFormatCurrency($basketItem->getPrice(), $order->getCurrency());
			$arBasketItem['WEIGHT_FORMATED'] = roundEx(doubleval($basketItem->getWeight() / $options['WEIGHT_KOEF']), SALE_WEIGHT_PRECISION) . ' ' . $options['WEIGHT_UNIT'];
			$arBasketItem['DISCOUNT_PRICE']  = $basketItem->getDiscountPrice();

			if(($basketItem->getDiscountPrice() + $basketItem->getPrice()) > 0)
				$arBasketItem['DISCOUNT_PRICE_PERCENT'] = $basketItem->getDiscountPrice() * 100 / ($basketItem->getDiscountPrice() + $basketItem->getPrice());
			else
				$arBasketItem['DISCOUNT_PRICE_PERCENT'] = 0;

			$arBasketItem['DISCOUNT_PRICE_PERCENT_FORMATED'] = roundEx($arBasketItem['DISCOUNT_PRICE_PERCENT'], SALE_VALUE_PRECISION) . '%';
			$arBasketItem['BASE_PRICE_FORMATED']             = SaleFormatCurrency($basketItem->getBasePrice(), $order->getCurrency());

			if(isset($discounts['RESULT']['BASKET'][ $basketCode ]) && is_array($discounts['RESULT']['BASKET'][ $basketCode ])) {
				foreach($discounts['RESULT']['BASKET'][ $basketCode ] as $discount)
					$arBasketItem['DISCOUNTS'][] = (is_array($discount['DESCR']) ? $discount['DESCR']['BASKET'] : $discount['DESCR']);
			}

			/*
			if(isset($discounts["PRICES"]["BASKET"][$basketCode]))
				$arBasketItem["PRICE"] = $discounts["PRICES"]["BASKET"][$basketCode]["PRICE"];
			else
				$arBasketItem["PRICE"] = $arBasketItem->getPrice();
			*/


			/*
			$arDim = unserialize($basketItem->getField('DIMENSIONS'));
			if (is_array($arDim))
			{
				$arResult['MAX_DIMENSIONS'] = CSaleDeliveryHelper::getMaxDimensions(
					array(
						$arDim['WIDTH'],
						$arDim['HEIGHT'],
						$arDim['LENGTH']
					),
					$arResult['MAX_DIMENSIONS']);

				$arResult['ITEMS_DIMENSIONS'][] = $arDim;
			}
			*/


			$arBasketItem['PROPS'] = array();
			/** @var \Bitrix\Sale\BasketPropertiesCollection $propertyCollection */
			$propertyCollection = $basketItem->getPropertyCollection();
			$propList           = $propertyCollection->getPropertyValues();
			foreach($propList as $key => &$prop) {
				if($prop['CODE'] == 'CATALOG.XML_ID' || $prop['CODE'] == 'PRODUCT.XML_ID')
					continue;

				$prop                    = array_filter($prop, array('CSaleBasketHelper', 'filterFields'));
				$arBasketItem['PROPS'][] = $prop;
			}

			//Find iblock element id
			$arElementId[ $arBasketItem['PRODUCT_ID'] ] = $arBasketItem['PRODUCT_ID'];
			if($bCatalog && $arParent = \CCatalogSku::GetProductInfo($arBasketItem['PRODUCT_ID'])) {

				$arBasketItem['PARENT_ID']      = $arParent['ID'];
				$arElementId[ $arParent['ID'] ] = $arParent['ID'];
			}

			$arBasketItem['SUM_BASE']           = $basketItem->getBasePrice() * $basketItem->getQuantity();
			$arBasketItem['SUM_BASE_FORMATED']  = SaleFormatCurrency($arBasketItem['SUM_BASE'], $order->getCurrency());
			$arBasketItem['SUM_TOTAL']          = $basketItem->getPrice() * $basketItem->getQuantity();
			$arBasketItem['SUM_TOTAL_FORMATED'] = SaleFormatCurrency($arBasketItem['SUM_TOTAL'], $order->getCurrency());

			$arBasketItem['PROPERTIES']         = array();
			$arBasketItem['DISPLAY_PROPERTIES'] = array();


			$itemKey = $arBasketItem['PRODUCT_ID'];

			$arResult['BASKET_ITEMS'][ $itemKey ] = $arBasketItem;
			$arElementLink[ $itemKey ]            = &$arResult['BASKET_ITEMS'][ $itemKey ];
		}


		//Получим свойства товара
		self::getProductProps($arElementLink, $arElementId, $bCatalog);


		//Получим изображения товара
		$arProductFields = self::getProductFields($arElementId);
		foreach($arResult['BASKET_ITEMS'] as &$arItem) {
			$arProduct = $arProductFields[ $arItem['PRODUCT_ID'] ];
			$arParent  = $arProductFields[ $arItem['PARENT_ID'] ];

			if(!$arProduct['RESIZE_PICTURE'] && $arItem['PARENT_ID']) {
				if($arParent['RESIZE_PICTURE'])
					$arProduct = $arParent;
			}

			if($arProduct) {
				$arItem = array_merge($arItem, $arProduct);
			}
		}


		return $arResult;
	}

	//bitrix/modules/sale/lib/helpers/admin/blocks/orderbasket.php
	//\Bitrix\Sale\Helpers\Admin\Blocks\OrderBasket::prepareData
	public function getBasketPrices(\Bitrix\Sale\Basket $basket, $discounts = null)
	{
		static $result = null;

		if($result === null) {
			$basketPrice     = 0;
			$basketDiscount  = 0;
			$basketPriceBase = 0;
			$arDiscounts     = array();

			if($basket) {
				$items = $basket->getBasketItems();

				/** @var \Bitrix\Sale\BasketItem $item */
				foreach($items as $item) {
					$basketCode = $item->getBasketCode();

					if(isset($discounts['PRICES']['BASKET'][ $basketCode ])) {
						$priceBase       = roundEx($discounts['PRICES']['BASKET'][ $basketCode ]['BASE_PRICE'], SALE_VALUE_PRECISION);
						$price           = roundEx($discounts['PRICES']['BASKET'][ $basketCode ]['PRICE'], SALE_VALUE_PRECISION);
						$basketPriceBase += $priceBase * $item->getQuantity();
						$basketPrice     += $price * $item->getQuantity();

						if(!$item->isCustomPrice())
							$basketDiscount += $discounts['PRICES']['BASKET'][ $basketCode ]['DISCOUNT'] * $item->getQuantity();
					}

					if(isset($discounts['RESULT']['BASKET'][ $basketCode ]) && is_array($discounts['RESULT']['BASKET'][ $basketCode ])) {
						foreach($discounts['RESULT']['BASKET'][ $basketCode ] as $discount)
							$arDiscounts[] = (is_array($discount['DESCR']) ? $discount['DESCR']['BASKET'] : $discount['DESCR']);
					}
					/*
					if(isset($discounts["PRICES"]["BASKET"][$basketCode]))
					{
						$arDiscounts["PRICE_BASE"] = roundEx($discounts["PRICES"]["BASKET"][$basketCode]["BASE_PRICE"], SALE_VALUE_PRECISION);
						$arDiscounts["PRICE"] = roundEx($discounts["PRICES"]["BASKET"][$basketCode]["PRICE"], SALE_VALUE_PRECISION);
					}
					*/
				}
			}

			$result = array(
				 'PRICE_BASKET'             => roundEx($basketPriceBase, SALE_VALUE_PRECISION),
				 'PRICE_BASKET_DISCOUNTED'  => roundEx($basketPrice, SALE_VALUE_PRECISION),
				 'PRICE_DISCOUNT'           => roundEx($basketDiscount, SALE_VALUE_PRECISION),
				 'BASKET_DISCOUNT_FORMATED' => implode('<br>', $arDiscounts),
			);
		}

		return $result;
	}

	public static function getOrderTotalPrices(Order $order, $basket, $needRecalculate = true)
	{
		$result = array(
			 'PRICE_TOTAL'               => $order->getPrice(),
			 'TAX_VALUE'                 => $order->getTaxValue(),
			 'PRICE_DELIVERY_DISCOUNTED' => $order->getDeliveryPrice(),
			 'SUM_PAID'                  => $order->getSumPaid(),
		);

		$result['SUM_UNPAID'] = $result['PRICE_TOTAL'] - $result['SUM_PAID'];

		if(!$result['PRICE_DELIVERY_DISCOUNTED'])
			$result['PRICE_DELIVERY_DISCOUNTED'] = 0;

		if(!$result['TAX_VALUE'])
			$result['TAX_VALUE'] = 0;


		$orderDiscount = $order->getDiscount();

		$discountsList = array();
		if($orderDiscount) {
			$discountsList = OrderEdit::getDiscountsApplyResult($order, $needRecalculate);

			if(!$discountsList)
				$discountsList = OrderEdit::getDiscountsApplyResult($order, true);
		}


		if(isset($discountsList['PRICES']['DELIVERY']['DISCOUNT']))
			$result['DELIVERY_DISCOUNT'] = $discountsList['PRICES']['DELIVERY']['DISCOUNT'];
		else
			$result['DELIVERY_DISCOUNT'] = 0;

		$result['PRICE_DELIVERY'] = $result['PRICE_DELIVERY_DISCOUNTED'] + $result['DELIVERY_DISCOUNT'];

		//Basket prices
		$basketData = self::getBasketPrices($basket, $discountsList);
		$result     = array_merge($result, $basketData);

		return $result;
	}

	protected static function getOrderTotalParams(Order $order, $options)
	{
		$basket      = $order->getBasket();
		$currency    = $order->getCurrency();
		$totalPrices = self::getOrderTotalPrices($order, $basket, false);

		if($basket)
			$weight = $basket->getWeight();
		else
			$weight = 0;


		$arResult = array(
			 'PRICE_BASKET'          => floatval($totalPrices['PRICE_BASKET']),
			 'PRICE_BASKET_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['PRICE_BASKET']), $currency, true),

			 'PRICE_BASKET_DISCOUNTED'          => floatval($totalPrices['PRICE_BASKET_DISCOUNTED']),
			 'PRICE_BASKET_DISCOUNTED_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['PRICE_BASKET_DISCOUNTED']), $currency, true),

			 'PRICE_DISCOUNT'          => floatval($totalPrices['PRICE_DISCOUNT']),
			 'PRICE_DISCOUNT_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['PRICE_DISCOUNT']), $currency, true),

			 'PRICE_DELIVERY'          => floatval($totalPrices['PRICE_DELIVERY']),
			 'PRICE_DELIVERY_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['PRICE_DELIVERY']), $currency, true),

			 'PRICE_DELIVERY_DISCOUNTED'          => floatval($totalPrices['PRICE_DELIVERY_DISCOUNTED']),
			 'PRICE_DELIVERY_DISCOUNTED_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['PRICE_DELIVERY_DISCOUNTED']), $currency, true),

			 'TAX_VALUE'          => floatval($totalPrices['TAX_VALUE']),
			 'TAX_VALUE_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['TAX_VALUE']), $currency, true),

			 'BASKET_WEIGHT'          => roundEx(floatval($weight / $options['WEIGHT_KOEF']), SALE_WEIGHT_PRECISION),
			 'BASKET_WEIGHT_FORMATED' => roundEx(floatval($weight / $options['WEIGHT_KOEF']), SALE_WEIGHT_PRECISION) . " " . $options['WEIGHT_UNIT'],

			 'BASKET_DISCOUNT_FORMATED' => $totalPrices['BASKET_DISCOUNT_FORMATED'],

			 'SUM_PAID'          => floatval($totalPrices['SUM_PAID']),
			 'SUM_PAID_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['SUM_PAID']), $currency, true),

			 'SUM_UNPAID'          => floatval($totalPrices['SUM_UNPAID']),
			 'SUM_UNPAID_FORMATED' => \CCurrencyLang::CurrencyFormat(floatval($totalPrices['SUM_UNPAID']), $currency, true),
		);


		/*
		array(
			"PRICE" => массив_параметров_минимальной_цены,
			"DISCOUNT_PRICE" => минимальная_цена_в_базовой_валюте,
			"DISCOUNT" => массив_параметров_первой_из_примененных_скидок_торгового_каталога,
			"DISCOUNT_LIST" => массив_скидок_действующих_на_товар_в_порядке_применения,
			"RESULT_PRICE" => array(
		       "BASE_PRICE" => полная (без скидок) цена товара,
			    "DISCOUNT_PRICE"  => цена со скидками,
			    "DISCOUNT"  => итоговая скидка (разница между BASE_PRICE и DISCOUNT_PRICE)
			    "PERCENT" => итоговая скидка в процентах
			    "CURRENCY"  => валюта результата
		    )
		)

		$arResult = array(
			//минимальная_цена
			'ORDER_PRICE'          => $order->getPrice() - $basket->getPrice(),
			'ORDER_PRICE_FORMATED' => SaleFormatCurrency($basket->getPrice(), $order->getCurrency()),

			//полная (без скидок) цена товара
			'BASE_PRICE'          => $basket->getBasePrice(),
			'BASE_PRICE_FORMATED' => SaleFormatCurrency($basket->getBasePrice(), $order->getCurrency()),

			//цена со скидками
			'DISCOUNT_PRICE'          => $order->getDiscountPrice(),
			'DISCOUNT_PRICE_FORMATED' => SaleFormatCurrency($order->getDiscountPrice(), $order->getCurrency()),

			//цена доставки
			'DELIVERY_PRICE'          => $order->getDeliveryPrice(),
			'DELIVERY_PRICE_FORMATED' => SaleFormatCurrency($order->getDeliveryPrice(), $order->getCurrency()),

			//вес заказа
			'ORDER_WEIGHT'          => $basket->getWeight(),
			'ORDER_WEIGHT_FORMATED' => roundEx(doubleval($basket->getWeight() / $options['WEIGHT_KOEF']), SALE_WEIGHT_PRECISION) . ' ' . $options['WEIGHT_UNIT'],

			//итоговая цена заказа со всеми скидками
			'ORDER_TOTAL_PRICE'          => $order->getPrice(),
			'ORDER_TOTAL_PRICE_FORMATED' => SaleFormatCurrency($order->getPrice(), $order->getCurrency()),
		);
		*/

		return $arResult;
	}


	//==============================================================================
	// Подключает компоненты блоков
	//==============================================================================
	protected static function getOrderBlockView($data, $cpName, $cpTemplate = '')
	{
		if(!$data || !$cpName)
			return false;


		//block.status
		if($cpName == 'status') {
			$data = array(
				 'ORDER'  => $data,
				 'STATUS' => self::getStatusesList($data['CREATOR_USER_ID'], $data['STATUS_ID']),
			);
		}

		ob_start();
		$GLOBALS['APPLICATION']->includeComponent(
			 'api:orderstatus.block.' . $cpName,
			 $cpTemplate,
			 array('DATA' => $data),
			 null,
			 array('HIDE_ICONS' => 'Y')
		);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}


	//==============================================================================
	// Блок "Хедер" (header)
	//==============================================================================
	protected static function getOrderHeaderParams(Order $order, $options)
	{
		$orderFields = self::getOrderFields($order->getId());
		$html        = self::replaceMacros($orderFields, $options['MAIL_SALE_NEW_ORDER_HEADER']);
		$subject     = self::replaceMacros($orderFields, $options['MAIL_SALE_NEW_ORDER_SUBJECT']);

		$result = array(
			 'HTML'    => $html,
			 'SUBJECT' => $subject,
		);

		unset($orderFields, $html, $subject);

		return $result;
	}

	//==============================================================================
	// Блок "Футер" (footer)
	//==============================================================================
	protected static function getOrderFooterParams(Order $order, $options)
	{
		$orderFields = self::getOrderFields($order->getId());
		$html        = self::replaceMacros($orderFields, $options['MAIL_SALE_NEW_ORDER_FOOTER']);

		$result = array(
			 'HTML' => $html,
		);

		unset($orderFields, $html);

		return $result;
	}


	protected static function loadOrder($ORDER_ID)
	{
		if(!$ORDER_ID)
			return false;

		static $order = null;

		if($order === null)
			$order = Order::load($ORDER_ID);

		return $order;
	}

	public static function getOrderBlocks($ORDER_ID)
	{
		if(!$ORDER_ID)
			return false;

		/** @var \Bitrix\Sale\Order $order */
		$order  = self::loadOrder($ORDER_ID);
		$siteId = $order->getSiteId();

		$arFields = array();

		// Настройки модуля
		$arOptions = \Api\OrderStatus\OptionTable::getOtions($siteId);

		$arOptions['WEIGHT_UNIT'] = htmlspecialcharsbx(Option::get('sale', 'weight_unit', false, $siteId));
		$arOptions['WEIGHT_KOEF'] = htmlspecialcharsbx(Option::get('sale', 'weight_koef', 1, $siteId));


		// Блок "Хедер" (header)
		$arOrderHeader            = self::getOrderHeaderParams($order, $arOptions);
		$arFields['BLOCK_HEADER'] = self::getOrderBlockView($arOrderHeader, 'header');


		// Блок "Футер" (footer)
		$arOrderFooter            = self::getOrderFooterParams($order, $arOptions);
		$arFields['BLOCK_FOOTER'] = self::getOrderBlockView($arOrderFooter, 'footer');


		// Блок "Оплата" (payment)
		$arOrderPaySystem          = self::getOrderPaySystemParams($order, $arOptions);
		$arFields['BLOCK_PAYMENT'] = self::getOrderBlockView($arOrderPaySystem, 'payment');


		// Блок "Информация по оплатам" (financeinfo)
		$arOrderFinance            = self::getOrderFinanceParams($order);
		$arFields['BLOCK_FINANCE'] = self::getOrderBlockView($arOrderFinance, 'finance');


		// Блок "Покупатель" (buyer)
		$arOrderBuyer            = self::getOrderBuyerParams($order, true);
		$arFields['BLOCK_BUYER'] = self::getOrderBlockView($arOrderBuyer, 'buyer');


		// Блок "Отгрузка" (delivery)
		$arOrderShipment            = self::getOrderShipmentParams($order, $arOptions, false, false);
		$arFields['BLOCK_SHIPMENT'] = self::getOrderBlockView($arOrderShipment, 'shipment');


		// Блок "Параметры заказа" (statusorder)
		//$arOrderStatus = self::getOrderStatusParams($order, $USER);
		//$arFields['BLOCK_STATUS'] = self::getOrderStatusView($arOrderStatus);


		// Блок "Итог стоимости заказа" (total)
		$arOrderTotal            = self::getOrderTotalParams($order, $arOptions);
		$arFields['BLOCK_TOTAL'] = self::getOrderBlockView($arOrderTotal, 'total');


		// Блок "Состав заказа" (basket)
		$arOrderBasket            = self::getOrderBasketParams($order, $arOptions);
		$arFields['BLOCK_BASKET'] = self::getOrderBlockView(array_merge($arOrderTotal, $arOrderBasket), 'basket');


		unset($arOrderHeader, $arOrderFooter, $arOrderPaySystem, $arOrderFinance, $arOrderBuyer, $arOrderShipment, $arOrderTotal, $arOrderBasket, $arOptions);

		return $arFields;
	}

	protected static function getOrderSiteFields($siteId)
	{
		$result = array();

		if($siteId) {
			$arSite = SiteTable::getList(array(
				 'select' => array('EMAIL', 'SITE_NAME', 'SERVER_NAME'),
				 'filter' => array('=LID' => $siteId),
			))->fetch();

			$result = array(
				 'SITE_NAME'   => ($arSite['SITE_NAME'] ? $arSite['SITE_NAME'] : Option::get('main', 'site_name')),
				 'SITE_EMAIL'  => ($arSite['EMAIL'] ? $arSite['EMAIL'] : Option::get('main', 'email_from')),
				 'SERVER_NAME' => ($arSite['SERVER_NAME'] ? $arSite['SERVER_NAME'] : Option::get('main', 'server_name')),
			);

			$scheme = \Bitrix\Main\Context::getCurrent()->getRequest()->isHttps() ? 'https://' : 'http://';
			//$result['SERVER_URL']  = (CMain::IsHTTPS() ? "https://" : "http://") . rtrim($result['SERVER_NAME'], '/');
			$result['SERVER_URL'] = $scheme . trim($result['SERVER_NAME']);

			unset($rsSites, $arSite);
		}

		return $result;
	}


	//bitrix/modules/sale/lib/notify.php::getUserName()
	protected static function getOrderUserName(Order $order)
	{
		$userName = "";

		/** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
		if($propertyCollection = $order->getPropertyCollection()) {
			if($propPayerName = $propertyCollection->getPayerName()) {
				$userName = $propPayerName->getValue();
			}
			else {
				/** @var \Bitrix\Sale\PropertyValue $orderProperty */
				foreach($propertyCollection as $orderProperty) {
					if($orderProperty->getField('CODE') == 'NAME' || $orderProperty->getField('CODE') == 'FIO') {
						$userName = $orderProperty->getValue();
						break;
					}
				}
			}
		}

		$userName = trim($userName);

		if(!$userName || $userName == '-') {
			$userRes = Main\UserTable::getList(array(
				 'select' => array('ID', 'LOGIN', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'EMAIL'),
				 'filter' => array('=ID' => $order->getUserId()),
			));
			if($userData = $userRes->fetch()) {
				$userName = \CUser::FormatName(\CSite::GetNameFormat(null, $order->getSiteId()), $userData, true);
			}
		}

		return $userName;
	}

	//bitrix/modules/sale/lib/notify.php::getUserEmail()
	protected static function getOrderUserEmail(Order $order)
	{
		$userEmail = "";

		/** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
		if($propertyCollection = $order->getPropertyCollection()) {
			if($propUserEmail = $propertyCollection->getUserEmail()) {
				$userEmail = $propUserEmail->getValue();
			}
			else {
				/** @var \Bitrix\Sale\PropertyValue $orderProperty */
				foreach($propertyCollection as $orderProperty) {
					if($orderProperty->getField('CODE') == 'EMAIL') {
						$userEmail = $orderProperty->getValue();
						break;
					}
				}
			}
		}

		$userEmail = trim($userEmail);

		if(empty($userEmail) || $userEmail == '-' || !check_email($userEmail)) {
			$userRes = Main\UserTable::getList(array(
				 'select' => array('ID', 'EMAIL'),
				 'filter' => array('=ID' => $order->getUserId()),
			));
			if($userData = $userRes->fetch()) {
				$userEmail = $userData['EMAIL'];
			}
		}

		return $userEmail;
	}

	protected static function getOrderUserPhone(Order $order)
	{
		$userPhone = "";

		/** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
		if($propertyCollection = $order->getPropertyCollection()) {
			if($propUserPhone = $propertyCollection->getPhone()) {
				$userPhone = $propUserPhone->getValue();
			}
			else {
				/** @var \Bitrix\Sale\PropertyValue $orderProperty */
				foreach($propertyCollection as $orderProperty) {
					if($orderProperty->getField('CODE') == 'PHONE') {
						$userPhone = $orderProperty->getValue();
						break;
					}
				}
			}
		}

		return $userPhone;
	}

	protected static function getOrderUserAddress(Order $order)
	{
		$userAddress = "";

		/** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
		if($propertyCollection = $order->getPropertyCollection()) {

			//Использовать как почтовый индекс
			$propUserZip = trim($propertyCollection->getDeliveryLocationZip());
			if($propUserZip && $propUserZip != '-') {
				$userAddress .= $propUserZip->getValue() . ', ';
			}

			//Использовать как местоположение
			if($propUserLocation = $propertyCollection->getDeliveryLocation()) {
				$code = $propUserLocation->getValue();

				if($code) {
					$arLocations = \Bitrix\Sale\Location\LocationTable::getList(array(
						 'filter' => array(
								'=CODE'                          => $code,
								'=PARENTS.NAME.LANGUAGE_ID'      => LANGUAGE_ID,
								'=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
						 ),
						 'select' => array(
								'I_ID'           => 'PARENTS.ID',
								'I_NAME_RU'      => 'PARENTS.NAME.NAME',
								'I_TYPE_CODE'    => 'PARENTS.TYPE.CODE',
								'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME',
						 ),
						 'order'  => array(
								'PARENTS.DEPTH_LEVEL' => 'asc',
						 ),
					))->fetchAll();

					if($arLocations) {
						foreach($arLocations as $arLocation) {
							$location = $arLocation[ 'I_NAME_' . ToUpper(LANGUAGE_ID) ] . ', ';
							if(strlen($location) > 0)
								$userAddress .= $location;
						}
					}
				}
			}

			//Является адресом
			if($propUserAddress = $propertyCollection->getAddress()) {
				$userAddress .= $propUserAddress->getValue();
			}
		}

		return $userAddress;
	}



	protected static function getDefaultFields($siteId)
	{
		if(!$siteId)
			return false;

		$result = array();

		if($arOptions = \Api\OrderStatus\OptionTable::getOtions($siteId)) {
			foreach($arOptions as $key => $val) {
				if(substr($key, 0, 5) == 'SALE_')
					$result[ $key ] = $val;
			}
		}

		$arSiteFields = self::getOrderSiteFields($siteId);

		$arMacros  = array();
		$resMacros = \Api\OrderStatus\MacrosTable::getList();
		while($macros = $resMacros->fetch())
			$arMacros[ $macros['NAME'] ] = $macros['VALUE'];

		if($arMacros) {
			foreach($arMacros as $key => &$val) {
				$val = self::replaceMacros($arSiteFields, $val);
				$val = self::replaceMacros($arOptions, $val);
			}
		}

		$result = array_merge($result, $arSiteFields, $arMacros);

		return $result;
	}

	// /bitrix/modules/sale/admin/order_view.php
	public static function getOrderFields($ORDER_ID)
	{
		if(!$ORDER_ID)
			return false;

		/** @var \Bitrix\Sale\Order $order */
		$order    = self::loadOrder($ORDER_ID);
		$siteId   = $order->getSiteId();
		$arOrder  = $order->getFieldValues();
		$arStatus = self::getStatusesList(false, $arOrder['STATUS_ID']);

		//\Bitrix\Sale\Notify::sendOrderNew()
		//bitrix/modules/sale/lib/helpers/order.php
		//bitrix/components/bitrix/sale.order.payment/component.php
		//bitrix/modules/sale/lib/order.php::getHash()
		$result = array(
			 'ID'             => $arOrder['ID'],
			 'SITE_ID'        => $siteId,
			 'USER_ID'        => $order->getUserId(),
			 'ORDER_ID'       => $order->getField('ACCOUNT_NUMBER'),
			 'ORDER_PRICE'    => SaleFormatCurrency($order->getPrice(), $order->getCurrency()),
			 'ORDER_PAYABLE'  => SaleFormatCurrency($order->getPrice() - $order->getSumPaid(), $order->getCurrency()),
			 'ORDER_PAID'     => SaleFormatCurrency($order->getSumPaid(), $order->getCurrency()),
			 'ORDER_STATUS'   => $arStatus[ $arOrder['STATUS_ID'] ],
			 'ORDER_DATE'     => $order->getDateInsert()->toString(),
			 'ORDER_USER'     => self::getOrderUserName($order), //Использовать как имя плательщика
			 'ORDER_PHONE'    => self::getOrderUserPhone($order), //Является телефоном
			 'ORDER_ADDRESS'  => self::getOrderUserAddress($order), //Является адресом
			 'ORDER_EMAIL'    => static::getOrderUserEmail($order), //Использовать как E-Mail
			 'PAYMENT_PRICE'  => SaleFormatCurrency($order->getPrice() - $order->getDeliveryPrice(), $order->getCurrency()),
			 'DELIVERY_PRICE' => SaleFormatCurrency($order->getDeliveryPrice(), $order->getCurrency()),
			 'DISCOUNT_PRICE' => SaleFormatCurrency($order->getDiscountPrice(), $order->getCurrency()),
			 //Ключи для оплаты заказа по ссылке
			 'HASH'           => method_exists($order, 'getHash') ? $order->getHash() : self::getHash($order),
			 'PAYMENT_ID'     => $order->getPaymentSystemId(),
			 //'PUBLIC_LINK'    => Bitrix\Sale\Helpers\Order::getPublicLink($order),
			 //'PUBLIC_LINK'    => class_exists(\Bitrix\Sale\Helpers\Order::class) ? Bitrix\Sale\Helpers\Order::getPublicLink($order) : ''
		);


		$publicUrl = Bitrix\Sale\Helpers\Order::isAllowGuestView($order) ? Bitrix\Sale\Helpers\Order::getPublicLink($order) : "";

		$result['PUBLIC_LINK']      = $publicUrl; //Old sale 16+
		$result['ORDER_PUBLIC_URL'] = $publicUrl; //New sale 17+

		$result['EMAIL'] = $result['ORDER_EMAIL'];
		$result['PRICE'] = $order->getPrice();

		$options = self::getDefaultFields($siteId);
		if($options) {
			foreach($options as $key => &$val) {
				$val = self::replaceMacros($result, $val);
			}
		}

		$return = array_merge($result, $options);

		return $return;
	}


	public static function replaceMacros($arFields, $strReturn)
	{
		foreach($arFields as $key => $val) {
			$strReturn = str_replace('#' . $key . '#', $val, $strReturn);
		}

		if($arFields['ORDER_ID']) {
			$md5_order = md5($arFields['ID'] . $arFields['ORDER_ID'] . $arFields['ORDER_DATE'] . $arFields['ORDER_EMAIL']);
			$strReturn = str_replace('md5=order', 'md5=' . $md5_order, $strReturn);
		}

		return $strReturn;
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public static function getFormatText($text = '')
	{
		return (preg_match('/<[\/\!]*?[^<>]*?>/im' . BX_UTF_PCRE_MODIFIER, $text) ? $text : nl2br($text));
	}


	/////////////////////////////////////////////////////////
	/// SALE MODULE COMPATIBLE FUNCTIONS
	/////////////////////////////////////////////////////////

	/**
	 * @param \Bitrix\Sale\Order $order
	 *
	 * @return string
	 */
	public static function getHash(Order $order)
	{
		$dateInsert = $order->getDateInsert()->setTimeZone(new \DateTimeZone("Europe/Moscow"));
		$timestamp  = $dateInsert->getTimestamp();
		return md5(
			 $order->getId() .
			 $timestamp .
			 $order->getUserId() .
			 $order->getField('ACCOUNT_NUMBER')
		);
	}

	/*public static function getPublicLink(Order $order)
	{
		$context = Application::getInstance()->getContext();
		$scheme = $context->getRequest()->isHttps() ? 'https' : 'http';
		$siteData = SiteTable::getList(array(
			 'filter' => array('LID' => $order->getSiteId()),
		));
		$site = $siteData->fetch();

		$paths = unserialize(Option::get("sale", "allow_guest_order_view_paths"));
		$path =  htmlspecialcharsbx($paths[$site['LID']]);

		if (isset($path) && strpos($path, '#order_id#'))
		{
			$accountNumber = urlencode(urlencode($order->getField('ACCOUNT_NUMBER')));
			$path = str_replace('#order_id#', $accountNumber,$path);
			if (strpos($path, '/') !== 0)
			{
				$path = '/'.$path;
			}

			$path .= (strpos($path, '?')) ? '&' : "?";
			$path .= "access=".self::getHash($order);
		}
		else
		{
			return "";
		}

		return $scheme.'://'.$site['SERVER_NAME'].$path;
	}*/


	//---------- PUBLIC API ----------//

	/**
	 * Проверяет по номеру заказа ORDER_ID и хэшу md5 совпадение с владельцем заказа, если все ok вернет поля заказа
	 *
	 * @param $request
	 *
	 * @return array|bool
	 */
	/*public static function checkOrderForPay($request){

		$result = false;

		$orderId = htmlspecialcharsbx($request['ORDER_ID']);

		$arFields = static::getOrderFields($orderId);

		if($arFields['ORDER_ID'] && $request['md5']){
			$md5_order = md5($arFields['ID'].$arFields['ORDER_ID'].$arFields['ORDER_DATE'].$arFields['ORDER_EMAIL']);

			if($md5_order == $request['md5'])
				$result = $arFields;
		}


		return $result;
	}*/
}

?>