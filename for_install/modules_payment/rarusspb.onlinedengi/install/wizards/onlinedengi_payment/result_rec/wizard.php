<?
/**
 *
 * Модуль платежного сервиса OnlineDengi для CMS 1С Битрикс.
 * @copyright Сервис OnlineDengi http://www.onlinedengi.ru/ (ООО "КомФинЦентр"), 2010
 *
 */
 
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class CWizardCommonErrors {
	function CheckForErrors(&$oWizard) {
		$bError = false;
		if(!CModule::IncludeModule('sale')) {
			$oWizard->SetError(GetMessage('WZ_ONLINEDENGI_SALEMODULE_ERR'));
			$bError = true;
		} elseif(!CModule::IncludeModule('rarusspb.onlinedengi')) {
			$oWizard->SetError(GetMessage('WZ_ONLINEDENGI_MODULE_ERR'));
			$bError = true;
		} elseif(!file_exists($_SERVER['DOCUMENT_ROOT'].ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT_PATH)) {
			$oWizard->SetError(GetMessage('WZ_ONLINEDENGI_MODULE_FILE_ERR'));
			$bError = true;
		}
		$saleModulePermissions = $GLOBALS['APPLICATION']->GetGroupRight('sale');
		if($saleModulePermissions <= 'D') {
			$oWizard->SetError(GetMessage('WZ_ONLINEDENGI_ACCESS_DENIED_ERR'));
			$bError = true;
		}

		if($bError) {
			$oWizard->SetNextStep('FinalStep');
		}
		return $bError;
	}
}

//start wizard
class Start extends CWizardStep {
        function InitStep() {
                $this->SetTitle(GetMessage('WZ_ONLINEDENGI_START_TITLE'));
                $this->SetNextStep('GetPaymentParamsStep');
                $this->SetStepID('Start');
                $this->SetCancelStep('CancelStep');
        }

        function ShowStep() {
                $this->content .= GetMessage('WZ_ONLINEDENGI_START_CONTENT_1');
                CWizardCommonErrors::CheckForErrors($this);
        }
}

class GetPaymentParamsStep extends CWizardStep {
        function InitStep() {
                $this->SetTitle(GetMessage('WZ_ONLINEDENGI_GetPaymentParamsStep_TITLE'));
                $this->SetNextStep('ReportStep');
                $this->SetStepID('GetPaymentParamsStep');
                $this->SetCancelStep('CancelStep');
        }

        function OnPostForm() {
                $wizard =& $this->GetWizard();
                if($wizard->IsNextButtonClick()) {
                        $payment_id = $wizard->GetVar('payment_id');
                        if(empty($payment_id)) {
                                $this->SetError(GetMessage('WZ_ONLINEDENGI_GetPaymentParamsStep_ERR1'), 'payment_id');
                        }
                }
        }

        function ShowStep() {
                $this->content .= '<h3>'.GetMessage('WZ_ONLINEDENGI_GetPaymentParamsStep_H1_TITLE').'</h3>';
                $bErrors = CWizardCommonErrors::CheckForErrors($this);
		if(!$bErrors) {
			$arValues = array(
				'0' => GetMessage('WZ_ONLINEDENGI_SELECT')
			);
			$arFilter = array(
				'PS_ACTIVE' => 'Y', 
				'%ACTION_FILE' => '%/onlinedengi_payment%'
			);
			$arSelect = array(
				'ID', 
				'NAME', 
				'PAY_SYSTEM_ID', 
				'PERSON_TYPE_ID', 
				'PT_NAME', 
				'PT_LID', 
				'PS_LID'
			);
			$rsItems = CSalePaySystemAction::GetList(array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);
			while($arItem = $rsItems->GetNext(false, false)) {
				$arValues[$arItem['ID']] = '['.$arItem['PAY_SYSTEM_ID'].'] '.$arItem['NAME'].': ['.$arItem['PERSON_TYPE_ID'].'] '.$arItem['PT_NAME'].' ('.$arItem['PT_LID'].')';
			}
			unset($rsItems);
	                //$this->content .= '<div>'.$this->ShowSelectField('payment_id', $arValues, array('style' => 'width:500px;')).'</div>';
	                $this->content .= '<div>'.$this->ShowSelectField('payment_id', $arValues).'</div>';
	                //$this->content .= '<span class="required">*</span>';
		}
                //$this->content .= '<div style="margin: 30px 0 0 0;"><span class="required">*</span>'.GetMessage('WZ_ONLINEDENGI_REQUIRED').'</div>';
        }
}

class ReportStep extends CWizardStep {
        function InitStep() {
                $this->SetTitle(GetMessage('WZ_ONLINEDENGI_ReportStep_TITLE'));
                $this->SetPrevStep('SetDelPropCodeStep');
                $this->SetStepID('ReportStep');
                $this->SetNextStep('FinalStep');
                //$this->SetCancelStep('FinalStep');
                $this->SetNextCaption(GetMessage('WZ_ONLINEDENGI_FINAL_BUTTON_TITLE'));
                $this->SetPrevCaption(GetMessage('WZ_ONLINEDENGI_AGAIN_BUTTON_TITLE'));
        }

        function ShowStep() {
                $bErrors = CWizardCommonErrors::CheckForErrors($this);
		if(!$bErrors) {
        	        $wizard =& $this->GetWizard();
                	$payment_id = $wizard->GetVar('payment_id');
	                if(!empty($payment_id)) {
				$arFilter = array(
					'PS_ACTIVE' => 'Y', 
					'%ACTION_FILE' => '%/onlinedengi_payment%',
					'ID' => $payment_id
				);
				$arSelect = array(
					'ID', 
					'NAME', 
					'PAY_SYSTEM_ID', 
					'PERSON_TYPE_ID', 
					'PT_NAME', 
					'PT_LID', 
					'PS_LID',
					'PARAMS'
				);
				$rsItems = CSalePaySystemAction::GetList(array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);
				if($arItem = $rsItems->GetNext()) {
					$rsSites = CSite::GetByID($arItem['PT_LID']);
					if($arSite = $rsSites->Fetch()) {
						$arItem['PARAMS'] = CSalePaySystemAction::UnSerializeParams($arItem['~PARAMS']);
						// !!!
						// Путь к скрипту
						// !!!
						$sUrl = str_replace(
							array('#PROTOCOL#', '#SERVER_NAME#', '#PAY_SYSTEM_ID#', '#PERSON_TYPE_ID#'),
							array('http', $arSite['SERVER_NAME'], $arItem['PAY_SYSTEM_ID'], $arItem['PERSON_TYPE_ID']),
							ONLINEDENGI_PAYMENT_RESPONSE_SCRIPT
						);

						$this->content .= '<div>';
						$this->content .= GetMessage('WZ_ONLINEDENGI_ReportStep_CONTENT_1', array('#VALUE#' => '<input type="text" size="65" readonly="readonly" value="'.htmlspecialchars($sUrl).'" />'));
						$this->content .= '</div>';
						
						$this->content .= '<div>';
						$this->content .= GetMessage('WZ_ONLINEDENGI_ReportStep_CONTENT_5', array('#VALUE#' => '<input type="text" size="65" readonly="readonly" value="'.htmlspecialchars(ONLINEDENGI_PAYMENT_SUCCESS_SCRIPT).'" />'));
						$this->content .= '</div>';
						
						$this->content .= '<div>';
						$this->content .= GetMessage('WZ_ONLINEDENGI_ReportStep_CONTENT_6', array('#VALUE#' => '<input type="text" size="65" readonly="readonly" value="'.htmlspecialchars(ONLINEDENGI_PAYMENT_FAIL_SCRIPT).'" />'));
						$this->content .= '</div>';						

						$this->content .= '<div>';
						$this->content .= GetMessage('WZ_ONLINEDENGI_ReportStep_CONTENT_2', array('#VALUE#' => '<input type="text" size="65" readonly="readonly" value="'.htmlspecialchars($arItem['PARAMS']['ONLINEDENGI_PROJECT']['VALUE']).'" />'));
						$this->content .= '</div>';

						$this->content .= '<div>';
						$this->content .= GetMessage('WZ_ONLINEDENGI_ReportStep_CONTENT_3', array('#VALUE#' => '<input type="text" size="65" readonly="readonly" value="'.htmlspecialchars($arItem['PARAMS']['ONLINEDENGI_SOURCE']['VALUE']).'" />'));
						$this->content .= '</div>';

						$this->content .= '<div>';
						$this->content .= GetMessage('WZ_ONLINEDENGI_ReportStep_CONTENT_4', array('#VALUE#' => '<input type="text" size="65" readonly="readonly" value="'.htmlspecialchars($arItem['PARAMS']['ONLINEDENGI_SECRET_KEY']['VALUE']).'" />'));
						$this->content .= '</div>';
					} else {
		                                $this->SetError(GetMessage('WZ_ONLINEDENGI_ReportStep_ERR2'));
					}
					unset($rsSites);
				} else {
	                                $this->SetError(GetMessage('WZ_ONLINEDENGI_GetPaymentParamsStep_ERR1'));
				}
				unset($rsItems);
        	        } else {
                                $this->SetError(GetMessage('WZ_ONLINEDENGI_GetPaymentParamsStep_ERR1'));
        	        }
		}
        }
}

//FinalStep wizard //Final
class FinalStep extends CWizardStep {
        function InitStep() {
                $this->SetTitle(GetMessage('WZ_ONLINEDENGI_FINALSTEP_TITLE'));
                $this->SetStepID('FinalStep');
                $this->SetCancelStep('FinalStep');
                $this->SetCancelCaption(GetMessage('WZ_ONLINEDENGI_FINAL_BUTTON_TITLE'));
        }

        function ShowStep() {
                $this->content .= GetMessage('WZ_ONLINEDENGI_FINALSTEP_CONTENT_1');
        }
}

//cancel installation step
class CancelStep extends CWizardStep {
        function InitStep() {
                $this->SetTitle(GetMessage('WZ_ONLINEDENGI_CANCEL_TITLE'));
                $this->SetStepID('CancelStep');
                $this->SetCancelStep('CancelStep');
                $this->SetCancelCaption(GetMessage('WZ_ONLINEDENGI_CANCEL_BUTTON_TITLE'));
        }

        function ShowStep() {
                $this->content .= GetMessage('WZ_ONLINEDENGI_CANCEL_CONTENT');
        }
}
