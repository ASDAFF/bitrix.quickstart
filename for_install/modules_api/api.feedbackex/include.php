<?
/**
 * api.feedbackex
 *
 * NOTE: Requires PHP version 5.3 or later
 *
 * @package      API
 * @subpackage   CApiFeedbackEx
 * @link         https://tuning-soft.ru/shop/api.feedbackex/
 *
 * @author       Anton Kuchkovsky <support@tuning-soft.ru> (https://tuning-soft.ru)
 * @copyright    © 1984-2017 Tuning-Soft
 *
 * @license      http://opensource.org/licenses/MIT  MIT License
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Mail;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

Class CApiFeedbackEx
{
	/** @var bool */
	protected $isSuccess = true;

	/** @var array */
	protected $errors;


	public function isSuccess()
	{
		return $this->isSuccess;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	protected function addError($error)
	{
		$this->isSuccess = false;
		$this->errors[]  = $error;
	}


	public function Send(
		 $event,
		 $lid,
		 $arFields,
		 $arFieldsCodeName = array(),
		 $arParams = array(),
		 $user_mess = false,
		 $duplicate = 'N',
		 $message_id = '',
		 $files = array()
	)
	{
		$strFieldsNames = $sSiteName = "";

		$arCurSite = SiteTable::getList(array(
			'select' => array('EMAIL', 'SITE_NAME', 'SERVER_NAME'),
			'filter' => array('=LID' => $lid),
		))->fetch();


		$sSiteName   = ($arCurSite['SITE_NAME'] ? $arCurSite['SITE_NAME'] : Option::get("main", "site_name", $GLOBALS['SERVER_NAME']));
		$sServerName = ($arCurSite['SERVER_NAME'] ? $arCurSite['SERVER_NAME'] : Option::get("main", "server_name", $GLOBALS['SERVER_NAME']));

		$arFields['SUBJECT'] = str_replace(array('#SITE_NAME#', '#SERVER_NAME#'), array($sSiteName, $sServerName), $arFields['SUBJECT']);

		$email_from           = $arCurSite['EMAIL'] ? $arCurSite['EMAIL'] : Option::get('main', 'email_from', "info@" . $GLOBALS['SERVER_NAME']);
		$arParams['EMAIL_TO'] = ($arParams['EMAIL_TO'] ? $arParams['EMAIL_TO'] : $email_from);

		if($arParams['REPLACE_FIELD_FROM'] && $arParams['USER_EMAIL'] && !$user_mess)
			$email_from = $arParams['USER_EMAIL'];


		if($arFields && $arFieldsCodeName)
		{
			if($arParams['WRITE_MESS_FILDES_TABLE'])
			{
				$strFieldsNames .= '<table style="' . $arParams['WRITE_MESS_TABLE_STYLE'] . '"><tbody>';

				foreach($arFieldsCodeName as $code => $name)
				{
					$curVal = is_array($arFields[ $code ]) ? implode('<br>', $arFields[ $code ]) : $arFields[ $code ];
					$strFieldsNames .= '<tr>';
					$strFieldsNames .= '<td style="' . $arParams['WRITE_MESS_TABLE_STYLE_NAME'] . '">' . $name . '</td>';
					$strFieldsNames .= '<td style="' . $arParams['WRITE_MESS_TABLE_STYLE_VALUE'] . '">' . $curVal . '</td>';
					$strFieldsNames .= '</tr>';
				}

				$strFieldsNames .= '</tbody></table>';
			}
			else
			{
				foreach($arFieldsCodeName as $code => $name)
				{
					$strFieldsNames .= "\n<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE'] . "\">";
					$curVal = is_array($arFields[ $code ]) ? implode('<br>', $arFields[ $code ]) : $arFields[ $code ];
					$strFieldsNames .= "\n\t<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE_NAME'] . "\">" . $name . "</div>";
					$strFieldsNames .= "\n\t<div style=\"" . $arParams['WRITE_MESS_DIV_STYLE_VALUE'] . "\">" . $curVal . "</div>";
					$strFieldsNames .= "\n</div>";
				}
			}
		}


		$arEventFields['EMAIL_FROM']  = $email_from;
		$arEventFields['EMAIL_TO']    = ($user_mess ? $arParams['USER_EMAIL'] : $arParams['EMAIL_TO']);
		$arEventFields['SITE_NAME']   = $sSiteName;
		$arEventFields['SERVER_NAME'] = $sServerName;
		$arEventFields['WORK_AREA']   = $strFieldsNames;

		$arEventFields = array_merge($arFields, $arEventFields);


		//Old Event Interface
		//if(!CEvent::Send($event, $lid, $arEventFields, 'N', '', $files = array()))
			//$this->addError(Loc::getMessage('AFEX_INCLUDE_EVENT_SEND_ERROR'));


		//==============================================================================
		// NEW Event Interface
		//==============================================================================
		foreach(GetModuleEvents('main', 'OnBeforeEventAdd', true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$event, &$lid, &$arEventFields, &$message_id, &$files)) === false)
				return false;

		$arLocalFields = array(
			'EVENT_NAME' => $event,
			'C_FIELDS'   => $arEventFields,
			'LID'        => is_array($lid) ? implode(",", $lid) : $lid,
			'DUPLICATE'  => $duplicate != "N" ? "Y" : "N",
			'FILE'       => $files,
		);
		if(intval($message_id) > 0)
			$arLocalFields["MESSAGE_ID"] = intval($message_id);

		$result = Mail\Event::send($arLocalFields);

		$id = false;
		if($result->isSuccess())
		{
			$id = $result->getId();
		}
		else
			$this->addError(Loc::getMessage('AFEX_INCLUDE_EVENT_SEND_ERROR'));

		if(!empty($this->errors))
			return false;

		return $id;
	}

	public static function getFields($onlyKeys = false, $config_path = '')
	{
		$context = Application::getInstance()->getContext();

		$result = array();

		$arFields = (array)Loc::getMessage('AFEX_INCLUDE_FORM_FIELDS');

		if($config_path){
			require($_SERVER['DOCUMENT_ROOT'] . $config_path);

			if(Application::isUtfMode()){
				$arFields = Encoding::convertEncoding($arFields, 'windows-1251', $context->getCulture()->getCharset());
			}
		}

		if($onlyKeys) {
			foreach($arFields as $key => $arField)
				$result[ $key ] = $arField['NAME'];
		}
		else
			$result = $arFields;

		return $result;
	}

	public static function getFieldsKeys($arFields)
	{
		$result = array();
		foreach($arFields as $key => $val)
			$result[] = $key;

		return $result;
	}

	public static function excludeCacheParams($arParams)
	{
		$arExclude = array_flip(array(
			'EMAIL_TO','BCC','PAGE_TITLE','PAGE_URL','DIR_URL','DATETIME','MAIL_SUBJECT_ADMIN','MAIL_SUBJECT_USER'
		));

		foreach($arParams as $key => $val)
		{
			unset($arParams['~'.$key]);

			if(is_set($arExclude, $key))
				unset($arParams[$key]);
		}

		return $arParams;
	}

	/**
	 * FakeTranslit()
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function FakeTranslit($str)
	{
		$str = trim($str);

		$trans_from = explode(",", Loc::getMessage('AFEX_INCLUDE_TRANSLIT_FROM'));
		$trans_to   = explode(",", Loc::getMessage('AFEX_INCLUDE_TRANSLIT_TO'));

		$str = str_replace($trans_from, $trans_to, $str);

		$str = preg_replace('/\s+/u', '-', $str);

		return $str;
	}

	/**
	 * Get file size in bytes form K|M|G
	 *
	 * @param $val
	 *
	 * @return int
	 */
	public static function getFileSizeInBytes($val)
	{
		$val = trim($val);

		if(empty($val))
			return 0;

		preg_match('#([0-9]+)[\s]*([a-z]+)#i', $val, $matches);

		$last = '';
		if(isset($matches[2]))
		{
			$last = $matches[2];
		}

		if(isset($matches[1]))
		{
			$val = (int)$matches[1];
		}

		switch(ToUpper($last))
		{
			case 'T':
			case 'TB':
				$val *= pow(1024, 4);
				break;

			case 'G':
			case 'GB':
				$val *= pow(1024, 3);
				break;

			case 'M':
			case 'MB':
				$val *= pow(1024, 2);
				break;

			case 'K':
			case 'KB':
				$val *= 1024;
				break;

			default:
				$val *= 1;
		}

		return (int)$val;
	}

	public static function incComponentLang($cp_obj)
	{
		global $MESS;

		$templateFile = '/bitrix/components/api/feedbackex/templates/uikit';
		if($cp_obj->InitComponentTemplate())
		{
			$template     = &$cp_obj->GetTemplate();
			$templateFile = $template->GetFolder();
		}

		CComponentUtil::__IncludeLang($templateFile, 'template.php');

		return $MESS;
	}
}

?>