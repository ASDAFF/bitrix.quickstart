<?
interface DSocialPostParamsInterface
{
	public function GetID();
	public function GetUrl();
	public function GetName($length = 250, $bFormat=true);
	public function GetPreviewText($length = 0);
	public function GetDetailText($length = 0);
	public function GetImageUrl();
	public function GetExtID();
	public function SetID($var);
	public function SetUrl($var);
	public function SetName($var);
	public function SetPreviewText($var);
	public function SetDetailText($var);
	public function SetImageUrl($var);
	public function SetExtID($var);

	public function GetReplaceParam();
}
class DSocialPostParams implements DSocialPostParamsInterface
{
	private $params = array();
	private $bUTF;
	private $settings;
	private $replace;
	private $entityId;

	public function __construct($settings, $replaceParams, $ID, $sUrl, $sName, $sPreviewText, $sDetailText, $sImgUrl, $sExtID="", $entityId="") {

		$this->bUTF = defined("BX_UTF") && BX_UTF == true;

		$this->settings = $settings;
		$this->entityId = $entityId;
		$this->replace = $this->PrepareReplaceParams($replaceParams);

		$this->SetID($ID);
		$this->SetUrl($sUrl);
		$this->SetName($sName);
		$this->SetPreviewText($sPreviewText);
		$this->SetDetailText($sDetailText);
		$this->SetImageUrl($sImgUrl);
		$this->SetExtID($sExtID);
	}

	public function PrepareReplaceParams($params) {

		$new = array();
		if(!is_array($params))
			$params = array();
		foreach ($params as $k => $v) {
			if (substr($k, 0, 1) == "~") continue;

			if (is_array($v)) {
				foreach ($v as $propCode => $propValue) {
					$propValue = CIBlockFormatProperties::GetDisplayValue($params, $propValue, "news_out");

					if (!empty($propValue["DISPLAY_VALUE"])) {
						if (is_array($propValue["DISPLAY_VALUE"]))
							$propValue["DISPLAY_VALUE"] = strip_tags(implode(" / ", $propValue["DISPLAY_VALUE"]));
					}

					if (!empty($propValue["CODE"]))
						$new["PROPERTY_".$propValue["CODE"]."_NAME"] = $new["PROPERTY_".ToUpper($propValue["CODE"])."_NAME"] = $new["PROPERTY_".$propValue["ID"]."_NAME"] = $propValue["NAME"];

					$new["PROPERTY_".$propValue["CODE"]."_VALUE"] = $new["PROPERTY_".ToUpper($propValue["CODE"])."_VALUE"] = $new["PROPERTY_".$propValue["ID"]."_VALUE"] = $propValue["DISPLAY_VALUE"];
				}
			}
			else {
				if ($this->entityId == 'livejournal' && in_array($k, array('PREVIEW_TEXT', 'DETAIL_TEXT'))) {
					$new[$k] = trim($v);
				} else {
					$new[$k] = trim(strip_tags($v));
				}
			}
		}

		return $new;

	}

	public function Prepare($var, $template = false, $length = 0) {

		global $APPLICATION;

		if (is_array($template) && count($template) == 1) {
			$var = str_replace("#".array_pop(array_keys($template))."#", $var, array_pop($template));
			$this->Replace($var);
		}

		if ($length > 0 && strlen($var) > 0) {
			$var = TruncateText($var, $length);
		}

		if (!$this->bUTF)
			$var = $APPLICATION->ConvertCharset($var, "Windows-1251", "UTF-8");

		return urlencode(html_entity_decode(str_replace("\r\n\r\n", "\r\n", $var), ENT_COMPAT, "UTF-8"));
	}

	public function Replace(&$var) {

		$this->replace["SITE_SERVER_NAME"] = SITE_SERVER_NAME;
		$this->replace["SITE_ID"] = SITE_ID;
		$this->replace["LANGUAGE_ID"] = LANGUAGE_ID;
		$this->replace["SITE_TEMPLATE_ID"] = SITE_TEMPLATE_ID;
		$this->replace["SITE_DIR"] = SITE_DIR;

		$arReplace = $this->replace;
		$events = GetModuleEvents(DSocialMediaPoster::$MODULE_ID, "OnBuildPostParamsReplace");
		while($arEvent = $events->Fetch()) {
			/**
			 *  AddEventHandler("defa.socialmediaposter", "OnBuildPostParamsReplace", "OnBuildPostParamsReplace");
			 *  function OnBuildPostParamsReplace($arReplace) {}
			 */
			ExecuteModuleEvent($arEvent, $arReplace);
		}

		$keys = array_keys($arReplace);
		foreach ($keys as $k => $v)
			$keys[$k] = "#".$v."#";

//		array_walk($keys, function(&$value, $key) { $value = "#".$value."#"; });

		$var = str_replace($keys, array_values($arReplace), $var);
	}

	public function SetID($var) {
		$this->params["ID"] = intval($var);
	}

	public function SetExtID($var) {
		$this->params["EXT_ID"] = $var;
	}

	public function SetUrl($var) {
		$this->params["URL"] = trim($var);
	}

	public function SetName($var) {
		$this->params["NAME"] = trim($var);
	}

	public function SetPreviewText($var) {
		$this->params["PREVIEW_TEXT"] = trim($var);
	}

	public function SetDetailText($var) {
		$this->params["DETAIL_TEXT"] = trim($var);
	}

	public function SetImageUrl($var) {
		$this->params["IMG_URL"] = trim($var);
	}

	public function GetID() {
		return $this->params["ID"];
	}

	public function GetExtID() {
		return $this->params["EXT_ID"];
	}

	public function GetReplaceParam($param = "") {
		return $this->replace[$param];
	}

	public function GetUrl() {
		return $this->Prepare($this->params["URL"]);
	}

	public function GetName($length = 250, $bFormat=true) {
		return $bFormat ? $this->Prepare($this->params["NAME"], array("NAME" => $this->settings->TEMPLATE_NAME), $length) : $this->Prepare($this->params["NAME"]);
	}

	public function GetPreviewText($length = 0) {
		return $this->Prepare($this->params["PREVIEW_TEXT"], array("PREVIEW_TEXT" => $this->settings->TEMPLATE_PREVIEW_TEXT), $length);
	}

	public function GetDetailText($length = 0) {
		return $this->Prepare($this->params["DETAIL_TEXT"], array("DETAIL_TEXT" => $this->settings->TEMPLATE_DETAIL_TEXT), $length);
	}

	public function GetImageUrl() {
		return $this->Prepare($this->params["IMG_URL"]);
	}

}
?>