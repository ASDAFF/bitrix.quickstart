<?
/**
 * @author     Victor Kulyabin <v.kulyabin@clientlab.ru>
 * @copyright  2019 Victor Kulyabin
 * @version    1.0.0
 * @link       https://github.com/clientlab/a4b.clientlabform
 */

require_once  $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/a4b.clientlabform/classes/libs/ReCaptcha/autoload.php';

IncludeModuleLangFile(__FILE__);


if (!class_exists("ClientlabForm"))
{
	class ClientlabForm
	{
		const MODULE_ID = 'a4b.clientlabform';
		const MODULE_PATH = '/bitrix/modules/a4b.clientlabform/';

		public $formResult = '';
		public $formName = '';
		public $errors = array();
		public $sids = array();
		public $re_site_key = '';
		public $re_seret_key = '';

		public function __construct($request) {
			$this->formResult = $this->prepareFormResult($request);
			$this->formName = $request['form_name'];

			$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());

			while ($res = $arQuery->Fetch())
			{
				array_push($this->sids, $res["ID"]);
			}


			$this->re_site_key = COption::GetOptionString("a4b.clientlabform", "RE_SITE_KEY");
			$this->re_seret_key = COption::GetOptionString("a4b.clientlabform", "RE_SEC_KEY");

		}


		public function checkRecaptchaResponce($responce){

			$recaptcha = new \ReCaptcha\ReCaptcha($this->re_seret_key);
			$resp = $recaptcha->verify($responce, $_SERVER['REMOTE_ADDR']);

			if (!$resp->isSuccess()){
				$this->Errors['recaptcha_fail'];
				return "false";
			}else{
				return "true";
			}
		}


		public function sendMail($mailTemplateId){

			$msg = array();

			$msg["FIELDS_TABLE"] = $this->makeResultTable();

			foreach ($this->formResult as $key => $value) {
				$msg[$value['field_name']] = $value['field_value'];
			}

			$res = CEvent::SendImmediate(
				COption::GetOptionString(self::MODULE_ID, "iblock_id"),
				$this->sids,
				$msg,
				"Y",
				$mailTemplateId
			);

			if ($res=="Y") {
				$this->OnEmailSent();
				return "true";
			}else{
				$this->Errors['send_mail'];
				$this->OnEmailSentError();
				return "false";
			}

		}

		public function insertAdminScripts(){
			global $APPLICATION;
			$APPLICATION->SetAdditionalCSS($_SERVER['DOCUMENT_ROOT']."/bitrix/components/a4b/clientlab.form/parameters.css", true);
			$APPLICATION->SetAdditionalCSS($_SERVER['DOCUMENT_ROOT']."/bitrix/components/a4b/clientlab.form/templates/.default/lib/bootstrap/bootstrap.css", true);
			$APPLICATION->AddHeadScript("/bitrix/components/a4b/clientlab.form/templates/.default/lib/jquery/jquery.js");
			$APPLICATION->AddHeadScript("/bitrix/components/a4b/clientlab.form/templates/.default/lib/jquery/jquery-ui.js");
			$APPLICATION->AddHeadScript("/bitrix/components/a4b/clientlab.form/templates/.default/lib/form-builder/form-builder.js");
			$APPLICATION->AddHeadScript("/bitrix/components/a4b/clientlab.form/parameters.js");

		}

		public function insertGlobalScripts(){
			global $APPLICATION;
			$APPLICATION->AddHeadScript("/bitrix/components/a4b/clientlab.form/templates/.default/lib/referer/referer.js");
			$APPLICATION->AddHeadString('<meta name="cmsmagazine" content="eb5a6fa4dfcd6e79d367222fbcf8513b" />',true);
		}

		public function saveToIBlock(){
			$arFields = array(
					"MODIFIED_BY"    => COption::GetOptionString(self::MODULE_ID, "user_id"),
					"IBLOCK_SECTION_ID" => false,
					"IBLOCK_ID"      => COption::GetOptionString(self::MODULE_ID, "iblock_id"),
					"NAME"           => $this->formName,
					"ACTIVE"         => "Y",
					"PREVIEW_TEXT"   => "",
					"DETAIL_TEXT"    =>  $this->makeResultTable(),
				);

			if (CModule::IncludeModule("iblock")) {
				$blockEl = new CIBlockElement;
				$res = $blockEl->Add(
					$arFields
				);
			}

			if ($res) {
				$this->OnIBlockAdd();
				return "true";
			}else{
				$this->Errors['iblock_save'];
				$this->OnIBlockAddError();
				return "false";
			}
		}

		private function OnEmailSent(){
			foreach(GetModuleEvents("a4b.clientlabform", "OnEmailSent", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array(&$this->formResult, &$this->Errors))===false)
				{
					if($err = $APPLICATION->GetException())
					{
						$result_message = array("MESSAGE"=>$err->GetString()."<br>", "TYPE"=>"ERROR");
					}
					else
					{
						$APPLICATION->ThrowException("Unknown login error");
						$result_message = array("MESSAGE"=>"Unknown login error"."<br>", "TYPE"=>"ERROR");
					}

					$bOk = false;
					break;
				}
			}
		}

		private function OnIBlockAdd(){
			foreach(GetModuleEvents("a4b.clientlabform", "OnIBlockAdd", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array(&$this->formResult, &$this->Errors))===false)
				{
					if($err = $APPLICATION->GetException())
					{
						$result_message = array("MESSAGE"=>$err->GetString()."<br>", "TYPE"=>"ERROR");
					}
					else
					{
						$APPLICATION->ThrowException("Unknown login error");
						$result_message = array("MESSAGE"=>"Unknown login error"."<br>", "TYPE"=>"ERROR");
					}

					$bOk = false;
					break;
				}
			}
		}

		private function OnEmailSentError(){
			foreach(GetModuleEvents("a4b.clientlabform", "OnEmailSentError", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array(&$this->formResult, &$this->Errors))===false)
				{
					if($err = $APPLICATION->GetException())
					{
						$result_message = array("MESSAGE"=>$err->GetString()."<br>", "TYPE"=>"ERROR");
					}
					else
					{
						$APPLICATION->ThrowException("Unknown login error");
						$result_message = array("MESSAGE"=>"Unknown login error"."<br>", "TYPE"=>"ERROR");
					}

					$bOk = false;
					break;
				}
			}
		}

		private function OnIBlockAddError(){
			foreach(GetModuleEvents("a4b.clientlabform", "OnIBlockAddError", true) as $arEvent)
			{
				if(ExecuteModuleEventEx($arEvent, array(&$this->formResult, &$this->Errors))===false)
				{
					if($err = $APPLICATION->GetException())
					{
						$result_message = array("MESSAGE"=>$err->GetString()."<br>", "TYPE"=>"ERROR");
					}
					else
					{
						$APPLICATION->ThrowException("Unknown login error");
						$result_message = array("MESSAGE"=>"Unknown login error"."<br>", "TYPE"=>"ERROR");
					}

					$bOk = false;
					break;
				}
			}
		}

		private function makeResultTable(){


			$FIELDS_TABLE = '<table>';
			foreach ($this->formResult as $key => $value) {
				$FIELDS_TABLE .= "<tr>";
				if ($value['field_label']!='') {
					$FIELDS_TABLE .= "<td>".$value['field_label']."</td>";
				}else{
					$FIELDS_TABLE .= "<td>".$value['field_name']."</td>";
				}

				if ($value['field_value_label']!='') {
					$FIELDS_TABLE .= "<td>".$value['field_value_label']."</td>";
				}else{
					if ($value['is_file']==="Y"){
					    if  ($value['field_value']){
						    $FIELDS_TABLE .= "<td><a target='_blank' href='".$_SERVER['HTTP_ORIGIN'].$value['field_value']."' >Upload ".$key."</a></td>";
                        }
					}else{
						$FIELDS_TABLE .= "<td>".$value['field_value']."</td>";
					}
				}
				$FIELDS_TABLE .= "</tr>";
			}

			$FIELDS_TABLE .= '</table>';

			$FIELDS_TABLE .= $this->getUtmInfo();

			return html_entity_decode($FIELDS_TABLE);
		}

		private function prepareFormResult($request){

			$fields = json_decode($request['form_fields'], true);

			$result = array();

			function getValueLabel($value, $field){
				$res = '';
				if (is_array($field['values'])) {
					foreach ($field['values'] as $f => $fv) {
						if ($fv['value'] == $value) {
							$res = $fv['label'];
						}
					}
				}
				return $res;
			}

			foreach ($fields as $f => $field) {
				foreach ($request as $key => $value) {
					//if (preg_replace('/[-_]/i',"",$field['name']) == $key) {
					if (preg_replace('/[-_]/i',"",$field['name']) == preg_replace('/[-_]/i',"",$key) ) {
						if  ($field['type'] === "file"){
							if (is_array($value)){
								foreach ($value as $f => $fv){
									$url = CFile::GetPath($fv);
									array_push($result, array(
										"field_name" => $f,
										"field_value" => $url,
										"field_value_label" => getValueLabel($value, $field),
										"field_label" => $field['label'],
										"is_file" => "Y"
									));
								}
							}else{
								$url = CFile::GetPath($value);
								array_push($result, array(
									"field_name" => $key,
									"field_value" => $url,
									"field_value_label" => getValueLabel($value, $field),
									"field_label" => $field['label'],
									"is_file" => "Y"
								));
							}
						}else{
							array_push($result, array(
								"field_name" => $key,
								"field_value" => $value,
								"field_value_label" => getValueLabel($value, $field),
								"field_label" => $field['label'],
								"is_file" => "N"
							));
						}
					}
				}
			}

			return $result;
		}

		private function getUtmInfo(){
			$res = "";

			$ref = htmlspecialchars(trim($_COOKIE["ref"]));
			$reftext = rawurldecode(trim($_COOKIE["stext"]));

			$source = htmlspecialchars(trim($_COOKIE["source"]));
			$medium = htmlspecialchars(trim($_COOKIE["medium"]));
			$term = rawurldecode(trim($_COOKIE["term"]));
			$campaign = htmlspecialchars(trim($_COOKIE["campaign"]));
			
			$ip = "";
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}

			$res .= GetMessage('UTM_TITLE');


			$arUtm = array(
				array(
					'label' => GetMessage('UTM_SOURCE'),
					'value' => $ref." ". $source
				),
				array(
					'label' => GetMessage('UTM_TERM'),
					'value' => $term ." ". $reftext
				),
				array(
					'label' => "utm_medium",
					'value' => $medium
				),
				array(
					'label' => "utm_source",
					'value' => $source
				),
				array(
					'label' => "utm_campaign",
					'value' => $campaign
				),
				array(
					'label' => GetMessage('UTM_IP'),
					'value' => $ip
				)
			);

			$res .= "<table>";
			foreach ($arUtm as $key => $value) {
				$res .= "<tr>"  ."<td>".$value['label']."</td>" ."<td>".$value['value']."</td>" . "</tr>";
			}

			$res .= "</table>";

			return $res;
		}
	}
}
?>