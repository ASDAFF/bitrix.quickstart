<?
AddEventHandler("defa.socialmediaposter", "OnBuildPosterList", Array("DSocialPosterGooglePlusEntity", "OnBuildPosterList"));

class DSocialPosterGooglePlusEntity
	extends DSocialPosterBaseEntity
	implements DSocialPosterEntity
{
	public static function OnBuildPosterList()
	{
		return new self();
	}
	public static function GetID()
	{
		return "googleplus";
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
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_GOOGLE"),
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
		$h = $this->GetParams($connection, $settings);

		if (strlen($h) == 0) {
			$this->Authorize($connection, $settings);
			$h = $this->GetParams($connection, $settings);
		}

		if (strlen($h) == 0)
			return false;

		return $h;
	}
	public function GetParams(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{
		$ret = $connection->Send("https://plus.google.com/".(strlen($settings->PAGE_ID)==0?"u/0/":"b/".$settings->PAGE_ID."/"));
		preg_match("/csi\",\"([^\"]+)\"/si", $ret, $hash);

		return $hash[1];
	}
	public function Authorize(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$post = "Email=".urlencode($settings->LOGIN)."&Passwd=".urlencode($settings->PASSWORD)."&PersistentCookie=yes&dnConn=https://accounts.youtube.com&dsh=&hl=en&pstMsg=1&rmShown=1&service=oz&signIn=Войти";

		$res = $connection->Send("https://accounts.google.com/LoginAuth", $post);
		preg_match("/GALX=(.*?);Path/si", $res, $GALX);

		if (empty($GALX[1]))
		{
			preg_match("/name=\"GALX\".*?value=\"([^\"]+)\"/si", $res, $GALX);
		}

		$res = $connection->Send("https://accounts.google.com/LoginAuth", $post."&GALX=".$GALX[1]);

		return true;
	}
	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $entityParams)
	{

		parent::PostMessage($connection, $settings, $entityParams);

		if ($settings->IS_USE != "Y")
			return false;

		$hash = $this->GetHash($connection, $settings);

		if (!$hash) {
			$this->Log(1);
			return false;
		}

		$u = $entityParams->GetUrl();
		$m = str_replace(array("\r\n", "\n"), " ", urldecode($entityParams->GetPreviewText(350)));
		$t = $entityParams->GetName();
		$d = str_replace(array("\r\n", "\n"), " ", urldecode($entityParams->GetDetailText(750)));
		$i = $entityParams->GetImageUrl();
		$h = DSocialMediaPoster::GetHost($resElement["IBLOCK_ID"], true);

		$result = $connection->Send(
				"https://plus.google.com/".(strlen($settings->PAGE_ID)==0?"u/0":"b/".$settings->PAGE_ID)."/_/sharebox/post/?spam=20&_reqid=7".(MakeTimeStamp(date("d.m.Y H:i:s")) - MakeTimeStamp(date("d.m.Y")))."&rt=j",
				'spar='.urlencode('["'.addslashes(urldecode($d)).'","oz:'.$settings->PAGE_ID.'.'.
						dechex(intval(microtime(true)*1000)).'.0",null,null,null,null,"[\"[null,null,null,\\\\\"'.
						htmlspecialchars(urldecode($t)).'\\\\\",null,null,null,null,null,[],null,null,null,null,null,null,null,null,null,null,null,\\\\\"'.
						htmlspecialchars(urldecode($m)).'\\\\\",null,null,[null,\\\\\"'.
						urldecode($u).'\\\\\",null,\\\\\"text/html\\\\\",\\\\\"document\\\\\"],null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,[[null,\\\\\"//s2.googleusercontent.com/s2/favicons?domain='.
						$h.'\\\\\",null,null],[null,\\\\\"//s2.googleusercontent.com/s2/favicons?domain='.
						$h.'\\\\\",null,null]],null,null,null,null,null,[[null,\\\\\"\\\\\",\\\\\"http://google.com/profiles/media/provider\\\\\",\\\\\"\\\\\"]]]\",\"[null,null,null,null,null,[null,\\\\\"'.
						urldecode($i).'\\\\\"],null,null,null,[],null,null,null,null,null,null,null,null,null,null,null,null,null,null,[null,\\\\\"'.
						urldecode($u).'\\\\\",null,\\\\\"'.'\\\\\",\\\\\"photo\\\\\",null,null,null,null,null,null,null,null,null],null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,[[null,\\\\\"'.
						urldecode($i).'\\\\\",null,null],[null,\\\\\"'.
						urldecode($i).'\\\\\",null,null]],null,null,null,null,null,[[null,\\\\\"images\\\\\",\\\\\"http://google.com/profiles/media/provider\\\\\",\\\\\"\\\\\"]]]\"]",null,"{\"aclEntries\":[{\"scope\":{\"scopeType\":\"anyone\",\"name\":\"\\u0412\\u0441\\u0435\",\"id\":\"anyone\",\"me\":true,\"requiresKey\":false},\"role\":20},{\"scope\":{\"scopeType\":\"anyone\",\"name\":\"\\u0412\\u0441\\u0435\",\"id\":\"anyone\",\"me\":true,\"requiresKey\":false},\"role\":60}]}",true,[],false,false,null,[],false,false,null,null,null,null,null,null,null,null,null,null,false,false,false]')."&at=".urlencode($hash)
		);

		if (preg_match("/HTTP\/1\.[0-1]? 200 OK/", substr($result, 0, 30)) > 0)
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
