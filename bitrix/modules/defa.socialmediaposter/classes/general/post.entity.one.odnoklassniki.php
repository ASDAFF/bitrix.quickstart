<?
AddEventHandler("defa.socialmediaposter", "OnBuildPosterList", Array("DSocialPosterOdnoklassnikiEntity", "OnBuildPosterList"));

class DSocialPosterOdnoklassnikiEntity
	extends DSocialPosterBaseEntity
	implements DSocialPosterEntity
{
	public static function OnBuildPosterList()
	{
		return new self();
	}
	public static function GetID()
	{
		return "odnoklassniki";
	}
	public static function GetName()
	{
		return GetMessage("SOCIALMEDIAPOSTER_ENTITY_".ToUpper(self::GetID()));
	}
	public function GetSettingsMap()
	{
		return array(
			"LOGIN" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_LOGIN"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_LOGIN_DESC"),
				"TYPE" => "string"
			),
			"PASSWORD" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PASSWORD"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PASSWORD_DESC"),
				"TYPE" => "password"
			),
			"PAGE_ID" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_ODNOKLASSNIKI"),
				"TYPE" => "text"
			),
			"TEMPLATE_NAME" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME_DESC").GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_ALL_DESC"),
				"TYPE" => "string",
				"DEFAULT_VALUE" => "#NAME#"
			),
			"TEMPLATE_PREVIEW_TEXT" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_PREVIEW_TEXT"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_PREVIEW_TEXT_DESC").GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_ALL_DESC"),
				"TYPE" => "textarea",
				"DEFAULT_VALUE" => "#PREVIEW_TEXT#"
			),
			"TEMPLATE_DETAIL_TEXT" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_DETAIL_TEXT"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_DETAIL_TEXT_DESC").GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_ALL_DESC"),
				"TYPE" => "textarea",
				"DEFAULT_VALUE" => "#DETAIL_TEXT#"
			),
		);
	}
	public function GetHash(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		//list($h,$p,$g) = $this->GetParams($connection, $settings);

		if (strlen($h) == 0) {
			$this->Authorize($connection, $settings);
			list($h,$p,$g, $tkn) = $this->GetParams($connection, $settings);
		}

		if (strlen($h) == 0 || strlen($p) == 0 || strlen($g) == 0 || strlen($tkn) == 0)
			return false;

		return array($h, $p, $g, $tkn);

	}
	public function GetParams(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$result = $connection->Send("http://www.odnoklassniki.ru/group/".$settings->PAGE_ID."/topics/");
		preg_match_all('/groupId=([0-9a-z]+)/i', $result, $groupId);
		$groupId = $groupId[1][0];

		preg_match_all('/"s2":"([0-9a-z]+)"/i', $result, $postFormId);
		$postFormId = $postFormId[1][0];

		preg_match_all('/pageCtx=(\{([^\}]+)\})/i', $result, $pageCtx);
		$pageCtx = CUtil::JsObjectToPhp($pageCtx[1][0]);
		$gwtHash = $pageCtx["gwtHash"];

		preg_match_all("/ok.tkn.set\(\'([^\']+)\'\)/i", $result, $tkn);
		$tkn = $tkn[1][0];

		return array($groupId, $postFormId, $gwtHash, $tkn);

	}
	public function Authorize(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$return = $connection->Send("http://www.odnoklassniki.ru/");

		if (substr_count($return, "AnonymLogin") > 0) {

			preg_match_all('/anonymLogin&amp;tkn=([0-9]+)/i', $return, $tkn);
			$tkn = $tkn[1][0];


			$return = $connection->Send(
				"http://www.odnoklassniki.ru/dk?cmd=AnonymLogin&st.cmd=anonymLogin&tkn=".$tkn,
				"st.redirect=&st.posted=set&st.email=".urlencode($settings->LOGIN)."&st.password=".urlencode($settings->PASSWORD)."&st.remember=on&st.fJS=enabled&st.st.screenSize=1280+x+1024&st.st.browserSize=499&st.st.flashVer=&button_go=%D0%92%D0%BE%D0%B9%D1%82%D0%B8"
			);
		}

		return true;
	}
	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $entityParams)
	{

		parent::PostMessage($connection, $settings, $entityParams);

		if ($settings->IS_USE != "Y")
			return false;

		list($hash, $postFormId, $gwtHash, $token) = $this->GetHash($connection, $settings);

		if (strlen($hash) == 0 || strlen($postFormId) == 0 || strlen($gwtHash) == 0 || strlen($token) == 0)
		{
			$this->Log(1);
			return false;
		}

		$u = $entityParams->GetUrl();
		$m = $entityParams->GetPreviewText(1000);
		$t = $entityParams->GetName();
		$d = $entityParams->GetDetailText(3000);
		$i = $entityParams->GetImageUrl();

		$u = urldecode($u);

		/*
		 * ooh, odnoklassniki...
		 */
		$uReal = $uFake = $u;
		if (substr($u, -1) == "/")
		{
			$uReal = $u;
			$uFake = $u."/";
		}

		$sText = '';
		$text = urldecode($d);
		$text = preg_replace("/(\r\n)/", '', $text);

		$arText = explode('**************************', $text);
		foreach($arText as $k => $v)
		{
			$arText[$k] = '{"text":"'.$v.'"}';
		}
		$arText[] = '{"text":"'.urldecode($u).'"}';

		$sText = implode(',', $arText);
		$postData = '{"formType":"Group", "postDataList":['.$sText.'], "news":false, "toStatus":false}';

		$connection->SetHandleOption(CURLOPT_HTTPHEADER, array('TKN:'.$token));
		$connection->SetHandleOption(CURLOPT_REFERER, "http://www.odnoklassniki.ru/group/".$settings->PAGE_ID."/topics");
		$result = $connection->Send(
				"http://www.odnoklassniki.ru/group/".$settings->PAGE_ID."/topics?cmd=MediaTopicPost&gwt.requested=".$gwtHash."&st.cmd=altGroupForum&st.groupId=".$hash,
				"st.status.postpostForm=".$postFormId."&st.status.postgroupId=".$hash."&postingFormData=".urldecode($postData)
		);

		if (!empty($result) && substr_count($result, "\"ok\"") == 1)
		{
			return true;
		}
		else
		{
			$this->Log(10);
			return false;
		}
	}
}
?>