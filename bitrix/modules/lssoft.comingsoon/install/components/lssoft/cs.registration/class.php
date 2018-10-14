<?php

class CLssoftCsRegistration extends CBitrixComponent {

	public $sErrorMail=null;
	public $sErrorLogin=null;
	public $sError=null;
	public $sMail='';
	public $sLogin='';

    public function executeComponent() {
    	global $APPLICATION;
    	if (isset($_POST['submit'])) {
    		if ($this->SubmitRegistration()) {
    			LocalRedirect($APPLICATION->GetCurPage().'?CSP=registration&CSPA=successful');
    		}
    	}
    	
    	
    	$arDefaultVariableAliases = array('CS_PAGE_ACTION'=>'CSPA','CS_CONFIRM_KEY'=>'CSCK');
		$arComponentVariables = array('CS_PAGE_ACTION','CS_CONFIRM_KEY');
		
		$arVariables = array();
		$arVariableAliases=CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases,$arParams["VARIABLE_ALIASES"]);
		CComponentEngine::InitComponentVariables(false,$arComponentVariables,$arVariableAliases,$arVariables);
		
		$sAction=isset($arVariables['CS_PAGE_ACTION']) ? $arVariables['CS_PAGE_ACTION'] : 'index';
		if (!in_array($sAction,array('index','successful','confirm'))) {
			$sAction='index';
		}
		
		if ($sAction=='confirm') {
			if (isset($arVariables['CS_CONFIRM_KEY']) and is_string($arVariables['CS_CONFIRM_KEY'])) {
				if (!CModule::IncludeModule("iblock")) {
					LocalRedirect($APPLICATION->GetCurPage());
				}
				// Смотрим ключ
				$res=CIBlock::GetList(Array(), Array("CODE"=>'ls_cs_user'));
    			if ($aIBlock=$res->Fetch()) {
    				if ($aInvite=CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$aIBlock['ID'],'PROPERTY_KEY'=>$arVariables['CS_CONFIRM_KEY']))->Fetch()) {
						CIBlockElement::SetPropertyValueCode($aInvite['ID'],'CONFIRM',1);
					} else {
						LocalRedirect($APPLICATION->GetCurPage());
					}
    			} else {
    				LocalRedirect($APPLICATION->GetCurPage());
    			}
			} else {
				LocalRedirect($APPLICATION->GetCurPage());
			}
		}
    
        $this->includeComponentTemplate($sAction);
    }
    
    public function SubmitRegistration() {
    	if (!CModule::IncludeModule("iblock")) {
    		$this->sError='Не удалось подключить модуль инфоблоков';
    		return false;
    	}
    	$res=CIBlock::GetList(Array(), Array("CODE"=>'ls_cs_user'));
    	if (!($aIBlock=$res->Fetch())) {
    		$this->sError='Не удалось найти инфоблок';
    		return false;
    	}

		$bNeedLogin=false;
		if (isset($this->arParams['INVITE_NEED_LOGIN']) and $this->arParams['INVITE_NEED_LOGIN']=='Y') {
			$bNeedLogin=true;
			$this->sLogin=isset($_POST['login']) ? (string)$_POST['login'] : false;
		}

    	// Проверяем корректность емайла
    	$this->sMail=isset($_POST['mail']) ? (string)$_POST['mail'] : false;
    	if (!check_email($this->sMail,true)) {
    		$this->sErrorMail='Проверьте формат емайл адреса';
    		return false;
    	}
    	if (COption::GetOptionString('main','new_user_email_uniq_check','N')=='Y') {
    		if (CUser::GetList($by='id',$order='desc',array('=EMAIL'=>$this->sMail))->Fetch()) {
    			$this->sErrorMail='E-mail уже занят другим пользователем';
    			return false;
    		}
    	}
    	if (CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$aIBlock['ID'],'NAME'=>$this->sMail))->Fetch()) {
    		$this->sErrorMail='E-mail уже занят другим пользователем';
    		return false;
    	}

		if ($bNeedLogin) {
			// Проверяем корректность логин
			if ($this->sLogin!=trim($this->sLogin)) {
				$this->sErrorLogin='Логин содержит крайние пробелы';
				return false;
			}
			if (strlen($this->sLogin)<3) {
				$this->sErrorLogin='Логин должен быть не менее 3 символов';
				return false;
			}
			if (CUser::GetByLogin($this->sLogin)->Fetch()) {
				$this->sErrorLogin='Логин уже занят другим пользователем';
				return false;
			}
			if (CIBlockElement::GetList(array(),array('IBLOCK_ID'=>$aIBlock['ID'],'PROPERTY_LOGIN'=>$this->sLogin))->Fetch()) {
				$this->sErrorLogin='Логин уже занят другим пользователем';
				return false;
			}
		}
    	
    	// Сохраняем
    	$sKey=md5(uniqid(mt_rand(),true));

    	$oElement=new CIBlockElement;
		$aProp=array(
			'SITE'=>SITE_ID,
			'KEY'=>$sKey
		);
		if ($bNeedLogin) {
			$aProp['LOGIN']=$this->sLogin;
		}

		$aData = Array(
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID"      => $aIBlock['ID'],
			"PROPERTY_VALUES"=> $aProp,
			"NAME"           => $this->sMail,
			"ACTIVE"         => "Y",
		);

		if($oElement->Add($aData)) {
		
			$aFilter = Array(
				"TYPE_ID"=> 'LS_CS_REGISTRATION_CONFIRM',
				"SITE_ID"=> SITE_ID,
				"ACTIVE"=> "Y",
			);
			if ($aMsg=CEventMessage::GetList($by="id",$order="desc",$aFilter)->Fetch()) {
				if ($aSite=CSite::GetByID(SITE_ID)->Fetch()) {
					if ($aSite['DIR']=='/') {
						$aSite['DIR']='';
					}
					CEvent::SendImmediate('LS_CS_REGISTRATION_CONFIRM',SITE_ID,array('EMAIL_TO'=>$this->sMail,'LINK_CONFIRM'=>$aSite['DIR'].'/?CSP=registration&CSPA=confirm&CSCK='.$sKey),'N',$aMsg['ID']);
				}
			}
			return true;
		} else {
  			$this->sError='Возникла ошибка: '.$oElement->LAST_ERROR;
    		return false;
		}
    }
};