<?
class COptimusTools{
	static $moduleClass = 'COptimus';

	function ___1595018847(){
		ob_start();
		$arData = $arModuleInfo = array();

		if(class_exists($moduleClass = self::$moduleClass)){
			if($moduleID = $moduleClass::moduleID){ // use $moduleClass::MODULE_ID or 'aspro.mshop' for debug
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/update_client.php') && file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/update_client_partner.php')){

					include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/update_client.php');
					$key = $arData['CLIENT']['LICENSE_KEY'] = CUpdateClient::GetLicenseKey();
					$key = base64_encode($key);

					$arUpdateList = CUpdateClient::GetUpdatesList($errorMessage, 'en', 'Y');
					$arClient = $arUpdateList['CLIENT'][0]['@'];
					if(isset($arClient) && is_array($arClient)){
						$arData['CLIENT']['CMS_EDITION'] = $arClient['LICENSE'];
						$arData['CLIENT']['CMS_EXPIRE_DATE'] = date('d.m.Y', strtotime($arClient['DATE_TO']));
						$arData['CLIENT']['NAME'] = urlencode($arClient['NAME']);
						$arData['CLIENT']['PARTNER_ID'] = $arClient['PARTNER_ID'];
					}

					$dbRes = CSite::GetList($by = 'id', $sort = 'asc', array('ACTIVE' => 'Y'));
					$arData['SETTINGS']['SITES_COUNT'] = $dbRes->SelectedRowsCount();

					if($obModule = CModule::CreateModuleObject($moduleID)){
						$arModuleInfo = array();
						$arRequestedModules = array($moduleID);
						$arData['MODULE']['SM_VERSION'] = SM_VERSION;
						$arData['MODULE']['MODULE_ID'] = $moduleID;
						$arData['MODULE']['MODULE_VERSION'] = $obModule->MODULE_VERSION;
						include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/update_client_partner.php');
						$arUpdateList = CUpdateClientPartner::GetUpdatesList($errorMessage, 'en', 'Y', $arRequestedModules, array('fullmoduleinfo' => 'Y'));
						if($arUpdateList && isset($arUpdateList['MODULE'])){
							foreach($arUpdateList['MODULE'] as $arModule){
								if($arModule['@']['ID'] === $moduleID){
									$arModuleInfo = $arModule['@'];
									break;
								}
							}
						}
						if($arModuleInfo){
							$arData['MODULE']['MODULE_EXPIRE_DATE'] = date('d.m.Y', strtotime($arModuleInfo['DATE_TO']));
						}
						$arData['MODULE']['OPTIONS'] = array();
						if($ob = new $moduleClass()){
							if(method_exists($ob, 'GetAdminOptionsValues')){
								$arData['MODULE']['OPTIONS'] = (array)$ob->GetAdminOptionsValues();
							}
						}
					}

					$arData['TIMESTAMP'] = time();
				}
			}
		}

		$result = $arData ? 'data='.self::___1596018847($arData, $key).'&key='.urlencode($key) : false;
		ob_get_clean();

		return $result;
	}

	function ___1596018847($data, $signKey = ''){
		$strData = serialize(self::___1590018847($data));
		$tmp = base64_encode($strData);

		if(strlen($signKey)){
			$signer = new \Bitrix\Main\Security\Sign\Signer;
			$signer->setKey(hash('sha512', $signKey));
			$tmp .= '.'.$signer->getSignature($strData);
		}

		return urlencode($tmp);
	}

	function ___1598018847($hash, $signKey = ''){
		$data = false;

		if(is_string($hash) && strlen($hash)){
			$tmp = urldecode($hash);


			if(($dotPos = strpos($tmp, '.')) === strrpos($tmp, '.')){
				if($bSigned = ($dotPos !== false)){
					$signature = substr($tmp, $dotPos + 1);
					$tmp = substr($tmp, 0, $dotPos);
				}
				$strData = base64_decode($tmp);

				if($bSigned && strlen($signKey)){
					try{
						$signer = new \Bitrix\Main\Security\Sign\Signer;
						$signer->setKey(hash('sha512', $signKey));
						if($signer->validate($strData, $signature)){
							$data = self::___1593018847(@unserialize($strData));
						}
					}
					catch(Exception $e){
						echo $e->getMessage();
					}
				}
				elseif(!strlen($signKey)){
					$data = self::___1593018847(@unserialize($strData));
				}
			}
		}

		return $data;
	}

	function ___1590018847($arData){
		if(is_array($arData)){
			$arResult = array();
			foreach($arData as $key => $value){
				$arResult[iconv(LANG_CHARSET, 'UTF-8', $key)] = self::___1590018847($value);
			}
		}
		else{
			$arResult = iconv(LANG_CHARSET, 'UTF-8', $arData);
		}

		return $arResult;
	}

	function ___1593018847($arData){
		if(is_array($arData)){
			$arResult = array();
			foreach($arData as $key => $value){
				$arResult[iconv('UTF-8', LANG_CHARSET, $key)] = self::___1593018847($value);
			}
		}
		else{
			$arResult = iconv('UTF-8', LANG_CHARSET, $arData);
		}

		return $arResult;
	}
}