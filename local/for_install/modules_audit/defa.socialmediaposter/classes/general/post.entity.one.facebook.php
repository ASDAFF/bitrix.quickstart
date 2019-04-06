<?
AddEventHandler("defa.socialmediaposter", "OnBuildPosterList", Array("DSocialPosterFacebookEntity", "OnBuildPosterList"));

class DSocialPosterFacebookEntity
	extends DSocialPosterBaseEntity
	implements DSocialPosterEntity
{
	public static function OnBuildPosterList()
	{
		return new self();
	}
	public static function GetID()
	{
		return "facebook";
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
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_FACEBOOK"),
				"TYPE" => "text"
			),
			"PAGE_OR_GROUP_SELECT" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_FACEBOOK_PAGE_OR_GROUP"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_DESC_FACEBOOK_PAGE_OR_GROUP"),
				"TYPE" => "selectbox",
				"SELECT_VALUES" => array(
					"group" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_FACEBOOK_GROUP"),
					"page" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_SELECT_FACEBOOK_PUBLIC_PAGE")
				)
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

		if (strlen($h["fb_dtsg"]) == 0 || strlen($h["app_id"]) == 0 || strlen($h["appid"]) == 0 || strlen($h["user"]) == 0) {
			$this->Authorize($connection, $settings);
			$h = $this->GetParams($connection, $settings);
		}

		if (strlen($h["fb_dtsg"]) == 0 || strlen($h["appid"]) == 0 || strlen($h["user"]) == 0)
			return false;

		return $h;

	}
	public function GetParams(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$result = $connection->Send("https://www.facebook.com/sharer/sharer.php?u=defa.ru&s=100&p[title]=&p[summary]=&p[images][0]=");

		preg_match_all('/name="fb_dtsg" value="(\w+)"/i', $result, $fb_dtsg);
		preg_match_all('/name="app_id" value="(\w+)"/i', $result, $app_id);
		preg_match_all('/name="appid" value="(\w+)"/i', $result, $appid);
		preg_match_all('/"viewer":(\w+),/i', $result, $user);

		$return = array(
			"fb_dtsg" => $fb_dtsg[1][0],
			"app_id" =>$app_id[1][0],
			"appid" => $appid[1][0],
			"user" => $user[1][0]
		);

		return $return;

	}
	public function Authorize(DSocialPosterConnection $connection, DSocialPosterParams $settings) {

		$connection->Send(
			"https://www.facebook.com/login.php?login_attempt=1",
			"default_persistent=1&persistent=1&charset_test=%E2%82%AC%2C%C2%B4%2C%E2%82%AC%2C%C2%B4%2C%E6%B0%B4%2C%D0%94%2C%D0%84&locale=en_US&email=".urlencode($settings->LOGIN)."&pass=".urlencode($settings->PASSWORD)."&pass_placeholder="
		);

		return true;
	}

	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $entityParams)
	{
		parent::PostMessage($connection, $settings, $entityParams);

		if ($settings->IS_USE != "Y")
			return false;

		$hash = $this->GetHash($connection, $settings);

		if (!$hash["appid"])
		{
			$this->Log(1);
			return false;
		}


		$u = $entityParams->GetUrl();
		$m = $entityParams->GetPreviewText(); $m = str_replace("%26%2339%3B", urlencode("'"), $m);
		$t = $entityParams->GetName();
		$d = $entityParams->GetDetailText();
		$i = $entityParams->GetImageUrl();

		$post_data = array(
			"__a" => "1",
			"fb_dtsg" => $hash["fb_dtsg"],
			"xhpc_context" => "profile",
			"xhpc_ismeta" => "1",
			"xhpc_fbx" => "1",
			"xhpc_timeline" => "",
			"xhpc_composerid" => "u_0_w",
			"xhpc_targetid" => $settings->PAGE_ID,
			"xhpc_message_text" => urldecode($m),
			"xhpc_message" => urldecode($m),
			"is_explicit_place" => "",
			"composertags_place" => "",
			"composertags_place_name" => "",
			"composer_session_id" => "",
			"composertags_city" => "",
			"disable_location_sharing" => "false",
			"composer_predicted_city" => "",
			"nctr[_mod]" => "pagelet_group_composer",
			"phstamp" => "1658165103778510469426"
			//"__user" => $hash["user"],
			//"__req" => "f",
		);

		// post public page by default
		$u = urldecode($u);

		unset($post_data['xhpc_fbx']);

		$post_data['xhpc_composerid'] = 'u_0_2i';
		$post_data['xhpc_timeline'] = '1';
		$post_data['aktion'] = 'post';
		$post_data['app_id'] = '2309869772';
		$post_data['scheduled'] = '0';
		$post_data['UITargetedPrivacyWidget'] = '80';
		$post_data['nctr[_mod]'] = 'pagelet_timeline_recent';
		$post_data['__req[_mod]'] = 'i';
		$post_data['__req'] = '5';

		$post_data['attachment[params][urlInfo][canonical]'] = $u;
		$post_data['attachment[params][urlInfo][final]'] = $u;
		$post_data['attachment[params][urlInfo][user]'] = $u;
		$post_data['attachment[params][favicon]'] = '';
		$post_data['attachment[params][title]'] = urldecode($t);
		$post_data['attachment[params][summary]'] = urldecode($d);
		$post_data['attachment[params][images][0]'] = urldecode($i);
		$post_data['attachment[params][medium]'] = '106';
		$post_data['attachment[params][url]'] = $u;
		$post_data['attachment[type]'] = '100';
		
		//$result = $connection->Send("https://www.facebook.com/ajax/profile/composer.php", http_build_query($post_data));
		$result = $connection->Send("https://www.facebook.com/ajax/updatestatus.php", http_build_query($post_data));
		
		if (!empty($result) && substr_count($result, "errorSummary") == 0 && preg_match("/HTTP\/1\.[0-1]? 200/", substr($result, 0, 30)) > 0)
		{
			return true;
		}
		else
		{
			$this->Log(10);
			return false;
		}
	}

	public function PostPhoto(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $albumParams, DSocialPostParams $entityParams, &$sCreatedAlbum)
	{
		parent::PostMessage($connection, $settings, $entityParams);

		if ($settings->IS_USE != "Y")
			return false;

		$hash = $this->GetHash($connection, $settings);

		if (!$hash["app_id"])
		{
			$this->Log(1);
			return false;
		}

		$albumID = self::CheckAlbumExists($connection, $settings, $albumParams->GetExtID());
		if(empty($albumID))
		{
			if($sCreatedAlbum = $this->CreateAlbum($connection, $hash, $settings, $albumParams->GetName(0, false)))
				$albumParams->SetExtID($sCreatedAlbum);
		}

		$albumID = $albumParams->GetExtID();
		if(!empty($albumID) && ($url = $entityParams->GetImageUrl()))
		{
			if($arFile = CFile::MakeFileArray(urlDecode($url)))
			{
				$arPostFields = array(
					"return" => '/media/set/edit/a.'.$albumID.'.'.rand(1000, 2000).'.'.$settings->PAGE_ID.'/',
					"fb_dtsg" => $hash["fb_dtsg"],
					"fbid" => $albumID,
					"file1" => '@'.$arFile["tmp_name"]
				);
				$result = $connection->Send('https://www.facebook.com/media/upload/photos/simple/receive', $arPostFields, DSocialPosterConnectionReturnStates::BODY_ONLY);
				return strlen($result) > 0;
			}
		}
		return false;
	}

	protected function CheckAlbumExists($connection, DSocialPosterParams $settings, $albumID)
	{
		if(strlen($albumID))
		{
			$result = CUtil::JsObjectToPhp($connection->Send('https://graph.facebook.com/'.$albumID, array(), DSocialPosterConnectionReturnStates::BODY_ONLY));
			if( !(is_array($result) && $result["id"] == $albumID) )
				$albumID = "";
		}
		return $albumID;
	}

	protected function CreateAlbum($connection, $hash, DSocialPosterParams $settings, $name, $description="")
	{
		$result = $connection->Send('https://www.facebook.com/albums/create.php', 'fb_dtsg='.$hash["fb_dtsg"].'&id='.$settings->PAGE_ID.'&create=1&session_id=&simple=1&name='.$name.'&location_data=&location=');
		if(preg_match('#simple\\\/\?set=a\.(\d+)\.#', $result, $m))
			return $m[1];
		return false;
	}

	public function PostVideo(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $albumParams, DSocialPostParams $postParams, &$sCreatedAlbum)
	{
		parent::PostMessage($connection, $settings, $postParams);

		if ($settings->IS_USE != "Y")
			return false;

		$url = urlDecode($postParams->GetName());
		if(!preg_match('#^https://#', $url))
			return false;

		$res = $this->UploadVideo($connection, $settings, $url);
	}

	public function UploadVideo(DSocialPosterConnection $connection, DSocialPosterParams $settings, $url)
	{
		$hash = $this->GetHash($connection, $settings);
		if (!$hash["app_id"])
		{
			$this->Log(1);
			return false;
		}

		// get video info
		$arPostFields = array_merge(
			array(
				"__a" => "1",
				"__user" => $hash["user"],
				'nctr[_mod]' => "pagelet_timeline_recent",
				"xhpc" => "composerTourStart",
				"targetid" => $settings->PAGE_ID,
			),
			array(
				"scrape_url" => $url,
				"alt_scrape_url" => $url,
			)
		);
		$arParsedVideoResult = null;
		$result = $connection->Send(
			'https://www.facebook.com/ajax/metacomposer/attachment/link/scraper.php?'.http_build_query($arPostFields),
			"",
			DSocialPosterConnectionReturnStates::BODY_ONLY
		);
		if(
			preg_match('#({.+?})$#si', $result, $m)
			&& ($result = CUtil::JsObjectToPhp($m[1]))
		)
		{
			if(
				(preg_match('#Composer\.configure\s*\(({.+?})\);#si', $result["onload"][0], $m))
				&& ($result = CUtil::JsObjectToPhp($m[1]))
			)
			{
				$arParsedVideoResult = $this->ParseForm(str_replace(array('\"', '\/'), array('"', '/'), $result["metaContent"]));
			}
		}

		//upload video
		if($arParsedVideoResult)
		{
			$arPostFields = array_merge(
				$arParsedVideoResult,
				array(
					"__a" => 1,
					"__user" => $hash["user"],
					"UITargetedPrivacyWidget" => 80,
					"fb_dtsg" => $hash["fb_dtsg"],

					"composertags_place" => "",
					"composertags_place_name" => "",

					"xhpc_context" => "profile",
					"xhpc_fbx" => "",
					"xhpc_ismeta" => 1,
					"xhpc_message" => $url,
					"xhpc_message_text" => $url,
					"xhpc_targetid" => $settings->PAGE_ID,
//					"xhpc_timeline" => 1,

					"xhpc_composerid" => randString(8),
				)
			);
			$result = $connection->Send(
				'https://www.facebook.com/ajax/profile/composer.php',
				$arPostFields,
				DSocialPosterConnectionReturnStates::BODY_ONLY
			);
			if(
				preg_match('#({.+?})$#si', $result, $m)
				&& ($result = CUtil::JsObjectToPhp($m[1]))
			)
			{
				if(!is_set($result, "error"))
					return true;
			}
		}
		return false;
	}
}
?>
