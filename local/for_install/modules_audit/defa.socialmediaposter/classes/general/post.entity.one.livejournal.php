<?
AddEventHandler("defa.socialmediaposter", "OnBuildPosterList", Array("DSocialPosterLiveJournalEntity", "OnBuildPosterList"));

class DSocialPosterLiveJournalEntity 
	extends DSocialPosterBaseEntity
	implements DSocialPosterEntity
{
	public static function OnBuildPosterList()
	{
		return new self();
	}	
	public static function GetID()
	{
		return "livejournal";
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
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_LIVEJOURNAL"), 
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_LIVEJOURNAL"), 
				"TYPE" => "text"
			),
			"TEMPLATE_NAME" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME"), 
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME_DESC").GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_ALL_DESC"), 
				"TYPE" => "string",
				"DEFAULT_VALUE" => "#NAME#"
			),
			"TEMPLATE_DETAIL_TEXT" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_DETAIL_TEXT"), 
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_DETAIL_TEXT_DESC").GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_ALL_DESC"), 
				"TYPE" => "textarea",
				"DEFAULT_VALUE" => "#PREVIEW_TEXT#\r\n\r\n\r\n#IMG_TAG#\r\n\r\n\r\n#DETAIL_TEXT#\r\n\r\n\r\n<a target=\"_blank\" href=\"#HOST##DETAIL_PAGE_URL#\">#HOST##DETAIL_PAGE_URL#</a>"
			),
		);
	}
	public function GetHash(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{
	}
	public function GetParams(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{
	}
	public function Authorize(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{
	}
	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $entityParams)
	{

		parent::PostMessage($connection, $settings, $entityParams);

		if ($settings->IS_USE != "Y")
			return false;

		$t = urldecode($entityParams->GetName());
		$i = $entityParams->GetImageUrl();
		$d = $entityParams->GetDetailText(); $d = str_replace("#IMG#", urldecode($i), urldecode($d)); $d = str_replace("#IMG_TAG#", str_replace(array(' width="', ' height="'), array(' xwidth="', ' xheight="'), ShowImage(urldecode($i))), urldecode($d));

		$query = '<?xml version="1.0"?><methodCall><methodName>LJ.XMLRPC.postevent</methodName><params><param><value>
					<struct>
						<member><name>username</name><value><string>'.$settings->LOGIN.'</string></value></member>
						<member><name>password</name><value><string>'.$settings->PASSWORD.'</string></value></member>
						'.((strlen($settings->PAGE_ID) > 0)?'<member><name>usejournal</name><value><string>'.$settings->PAGE_ID.'</string></value></member>':'').'
						<member><name>event</name><value><string><![CDATA['.$d.']]></string></value></member>
						<member><name>subject</name><value><string>'.$t.'</string></value></member>
						<member><name>lineendings</name><value><string>pc</string></value></member>
						<member><name>year</name><value><int>'.date("Y").'</int></value></member>
						<member><name>mon</name><value><int>'.date("m").'</int></value></member>
						<member><name>day</name><value><int>'.date("d").'</int></value></member>
						<member><name>hour</name><value><int>'.date("G").'</int></value></member>
						<member><name>min</name><value><int>'.date("i").'</int></value></member>
					</struct></value></param></params></methodCall>'; 


		$connection->SetHandleOption(CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8"));
		$result = $connection->Send("http://www.livejournal.com/interface/xmlrpc", $query);

		if (substr_count($result, "<fault>") == 0)
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