<?
AddEventHandler("defa.socialmediaposter", "OnBuildPosterList", Array("DSocialPosterTwitterEntity", "OnBuildPosterList"));

class DSocialPosterTwitterEntity
	extends DSocialPosterBaseEntity
	implements DSocialPosterEntity
{
	public static function OnBuildPosterList()
	{
		return new self();
	}
	public static function GetID()
	{
		return "twitter";
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
			"TEMPLATE_NAME" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_NAME_DESC").GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_TEMPLATE_ALL_DESC"),
				"TYPE" => "string",
				"DEFAULT_VALUE" => "#NAME#"
			),
		);
	}
	public function GetHash(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$h = $this->GetParams($connection, $settings);

		if (strlen($h["token"]) == 0) {
			$this->Authorize($connection, $settings);
			$h = $this->GetParams($connection, $settings);
		}

		if (strlen($h["token"]) == 0) {
			// LOG
			return false;
		}

		return $h;

	}
	public function GetParams(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$result = $connection->Send("https://twitter.com/");
		if (substr_count($result, "remember_me") > 0)
			return false;

		preg_match('/input type="hidden" value="(.*?)" name="authenticity_token"/', $result, $token);

		$return = array(
			"token" => $token[1]
		);

		return $return;

	}
	public function ShortenLink(DSocialPosterConnection $connection, $url)
	{

		$connection->SetHandleOption(CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		$result = $connection->Send("https://www.googleapis.com/urlshortener/v1/url", CUtil::PhpToJSObject(array("longUrl" => urldecode($url))));

		$result = preg_match_all("/\{[^\}]+}/", $result, $hash);
		$result = CUtil::JsObjectToPhp($hash[0][0]);

		if (strlen($result["id"]) > 0)
			return urlencode($result["id"]);

		return false;

	}
	public function Authorize(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$result = $connection->Send("https://twitter.com/?_twitter_noscript=1");

		preg_match('/input type="hidden" value="(.*?)" name="authenticity_token"/', $result, $token);

		$connection->SetHandleOption(CURLOPT_HTTPHEADER, array(
			"Content-Type: application/x-www-form-urlencoded",
		));

		$result = $connection->Send(
			"https://twitter.com/sessions",
			"authenticity_token=".urlencode($token[1])."&remember_me=1&return_to_ssl=true&session[username_or_email]=".urlencode($settings->LOGIN)."&session[password]=".urlencode($settings->PASSWORD)
		);

		return true;
	}
	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $entityParams)
	{
		parent::PostMessage($connection, $settings, $entityParams);

		if ($settings->IS_USE != "Y")
			return false;

		$hash = $this->GetHash($connection, $settings);
		if (!$hash["token"])
		{
			$this->Log(1);
			return false;
		}

		$u = $entityParams->GetUrl();
		$u = $this->ShortenLink($connection, $u);

		$t = $entityParams->GetName(117-strlen($u));

		$t = urldecode($t);
		$u = urldecode($u);

		$m = urlencode($t." ".$u);


		$connection->SetHandleOption(CURLOPT_REFERER, "https://twitter.com/");
		$connection->SetHandleOption(CURLOPT_HTTPHEADER, Array("Pragma:"));

		$result = $connection->Send(
			"https://twitter.com/i/tweet/create",
			"authenticity_token=".$hash["token"]."&place_id=&status=".$m,
			DSocialPosterConnectionReturnStates::BODY_ONLY
		);

		$result = CUtil::JsObjectToPhp($result);

		if (!empty($result) && is_set($result, "tweet_id"))
		{
			return true;
		}
		elseif(is_set($result, "message") && count($result) == 1)
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