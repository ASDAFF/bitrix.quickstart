<?
AddEventHandler("defa.socialmediaposter", "OnBuildPosterList", Array("DSocialPosterVkontakteEntity", "OnBuildPosterList"));

class DSocialPosterVkontakteEntity
	extends DSocialPosterBaseEntity
	implements DSocialPosterEntity
{
	public static function OnBuildPosterList()
	{
		return new self();
	}
	public static function GetID()
	{
		return "vkontakte";
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
			"PHONE_4DIGITS" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PHONE_4DIGITS"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PHONE_4DIGITS_DESC_VKONTAKTE"),
				"TYPE" => "text"
			),
			"PAGE_ID" => array(
				"NAME" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID"),
				"DESCRIPTION" => GetMessage("SOCIALMEDIAPOSTER_ENTITY_PARAM_PAGE_ID_DESC_VKONTAKTE"),
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

		if ($h["my_id"] == 0 || strlen($h["post_hash"]) == 0 || strlen($h["user_id"]) == 0) {
			$this->Authorize($connection, $settings);
			$h = $this->GetParams($connection, $settings);
		}

		if ($h["my_id"] == 0 || strlen($h["post_hash"]) == 0 || strlen($h["user_id"]) == 0) {
			$this->Log(1);
			return false;
		}

		return $h;

	}
	public function GetParams(DSocialPosterConnection $connection, DSocialPosterParams $settings)
	{

		$result = $connection->Send("http://vk.com/public".$settings->PAGE_ID);

		if (substr_count($result, "security_check") > 0 && strlen($settings->PHONE_4DIGITS) > 0)
		{
			preg_match_all("/{act: 'security_check'.*to: '([^']+)'.*hash: '([^']+)'}/i", $result, $hashes);

			$connection->Send("http://vk.com/login.php", "act=security_check&code=".$settings->PHONE_4DIGITS."&to=".$hashes[1][0]."&al_page=2&hash=".$hashes[2][0]);
			$result = $connection->Send("http://vk.com/public".$settings->PAGE_ID);
		}

		preg_match_all("/\"post_hash\":\"(\w+)\"/i", $result, $f1);
		preg_match_all("/\"uid\":(\d+),/i", $result, $f2);		preg_match_all("/\"(?:group|public)_id\":(\d+)/i", $result, $f3);

		$return = array(
			"post_hash" => $f1[1][0],
			"user_id" => $f2[1][0],
			"my_id" => intval($f3[1][0])
		);

		return $return;

	}
	public function Authorize(DSocialPosterConnection $connection, DSocialPosterParams $settings) {

		$connection->Send(
			"http://login.vk.com/?act=login",
			"act=login&q=1&al_frame=1&expire=&captcha_sid=&captcha_key=&from_host=vk.com&email=".urlencode($settings->LOGIN)."&pass=".urlencode($settings->PASSWORD)
		);

		return true;
	}
	public function UploadImage(DSocialPosterConnection $connection, $pageURL, $imgURL, $uploadURL = "http://vk.com/share.php")
	{
		$connection->SetHandleOption(CURLOPT_REFERER, $uploadURL);

		$q = "act=a_photo&url=".$pageURL."&image=".$imgURL."&extra=0&index=1";
		$result = $connection->Send($uploadURL, $q);

		if (preg_match("/onUploadDone/i", $result, $o)) {
			$result = str_replace(array("\"", " "), "", $result);

			preg_match_all("/{user_id:(\d+),photo_id:(\d+)}/i", $result, $out);
			$f = $out[1][0]."_".$out[2][0];

			return $f;
		}
		else {
			$this->Log(5);
			return false;
		}
	}

	protected function GetUploadParams(DSocialPosterConnection $connection, DSocialPosterParams $settings, $albumID=null)
	{
		$arResult = array();
		if($albumID === true)
		{
			$result = $connection->Send("http://vk.com/al_video.php", "act=upload_box&al=1&oid=-".$settings->PAGE_ID);
			if(preg_match('#<form.+?id="video_share_form".+?action="([^\'"]+)".+?</form>#is', $result, $m))
			{
				$arResult["URL"] = $m[1];
				$arResult["PARAMS"] = $this->ParseForm($m[0]);
				if(preg_match('/extend\(sharedVideo.*?({.+?})/i', $result, $m))
				{
					$arr = CUtil::JsObjectToPhp(str_replace('\n', '', $m[1]));
					foreach(array("hash", "oid", "to_video") as $key)
						$arResult["PARAMS"]['shared_'.$key] = $arr[$key];
				}
			}
		}
		elseif(null != $albumID)
		{
			$result = $connection->Send("http://vk.com/album-".$settings->PAGE_ID."_".$albumID);
			if(preg_match('/cur\.flashLiteUrl\s*=\s*\'(http[^\']+)\'/i', $result, $m))
				$arResult["URL"] = $m[1];

			if(preg_match('/cur\.flashLiteVars\s*=\s*({.+?};)/i', $result, $m))
				$arResult["PARAMS"] = CUtil::JsObjectToPhp($m[1]);
		}
		else
		{
			$result = $connection->Send("http://vk.com/photos.php", "act=a_choose_photo_box&al=1&scrollbar_width=16&to_id=-".$settings->PAGE_ID);
			if(preg_match('#\'(http://cs([0-9]+)\.vk\.com/upload\.php)\'#', $result, $m))
				$arResult["URL"] = $m[1];

			if(preg_match("/({\"act\":\"do_add\"[^}]+})/i", $result, $m))
				$arResult["PARAMS"] = CUtil::JsObjectToPhp($m[1]);
		}

		return count($arResult) == 2 ? $arResult : null;
	}

	public function UploadImagePhoto2Album(DSocialPosterConnection $connection, DSocialPosterParams $settings, $imgURL, $albumID)
	{
		if($params = $this->GetUploadParams($connection, $settings, $albumID))
		{
			$connection->Send($params["URL"].'?'.http_build_query($params["PARAMS"]), array("photo" => "@".$imgURL), DSocialPosterConnectionReturnStates::BODY_ONLY);
			return true;
		}
		return false;
	}

	public function UploadImagePhoto(DSocialPosterConnection $connection, $settings, $imgURL, $uploadURL = "http://vk.com/photos.php")
	{
		$imgURL = urldecode($imgURL);
		$imgURL = $_SERVER['DOCUMENT_ROOT'].substr($imgURL, strpos($imgURL, "/", 7));
		if($params = $this->GetUploadParams($connection, $settings))
		{
			$result = $connection->Send($params["URL"]."?".http_build_query(array_merge($params["PARAMS"], array("ajx" => "1"))), array("photo" => "@".$imgURL), DSocialPosterConnectionReturnStates::BODY_ONLY);
			if($arr = CUtil::JsObjectToPhp($result))
			{
				$result = $connection->Send("http://vk.com/al_photos.php", array_merge($arr, array("al" => 1, "act" => "choose_uploaded")));
				if(preg_match("/>([0-9]+_[0-9]+)</i", $result, $m))
					return $m[1];

				$this->Log(6);
				return false;
			}
		}
		return false;
	}

	public function PostMessage(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $entityParams)
	{
		parent::PostMessage($connection, $settings, $entityParams);

		if ($settings->IS_USE != "Y")
			return false;

		$hash = $this->GetHash($connection, $settings);

		if (!$hash["post_hash"])
		{
			$this->Log(1);
			return false;
		}

		$u = $entityParams->GetUrl();
		$m = $entityParams->GetPreviewText();
		$t = $entityParams->GetName();
		$d = $entityParams->GetDetailText();
		$i = $entityParams->GetImageUrl();

		if (strlen($i) > 0) {
			$img = $this->UploadImage($connection, $u, $i);
			$img2 = $this->UploadImagePhoto($connection, $settings, $i);
		}

		$result = $connection->Send("https://vk.com/al_wall.php", "act=post&official=1&al=1&extra=0&extra_data=&type=all&hash=".$hash["post_hash"]."&message=".$m."&to_id="."-".$settings->PAGE_ID."&attach1=".$img2."&attach1_type=photo&attach2=".$img."&attach2_type=share&url=".$u."&title=".$t."&description=".$d);

		if (!empty($result) && substr_count($result, "post-".$settings->PAGE_ID) > 0)
		{
			return true;
		}
		else {
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

		if (!$hash["post_hash"])
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
				return $this->UploadImagePhoto2Album($connection, $settings, $arFile["tmp_name"], $albumID);
		}
		return false;
	}

	protected function CheckAlbumExists($connection, DSocialPosterParams $settings, $albumID)
	{
		if(strlen($albumID))
		{
			$result = $connection->Send("http://vk.com/album-".$settings->PAGE_ID."_".$albumID);
			if(strpos($result, 'photos_upload_area') === false)
				$albumID = '';
		}
		return $albumID;
	}

	protected function CreateAlbum($connection, $hash, DSocialPosterParams $settings, $name, $description="")
	{
		$result = $connection->Send("http://vk.com/al_photos.php", 'act=new_album_box&al=1&oid=-'.$settings->PAGE_ID);
		if(preg_match('/hash:\s*\'([^\']+)\'/', $result, $m))
		{
			$result = $connection->Send("http://vk.com/al_photos.php", "act=new_album&al=1&comm=false&desc=".$description."&hash=".$m[1]."&oid="."-".$settings->PAGE_ID."&only=&title=".$name."&view=false");
			if(preg_match('/album-'.$settings->PAGE_ID.'_(\d+)/', $result, $m))
				return $m[1];
		}
		return false;
	}

	protected function CheckVideoAlbumExists($connection, DSocialPosterParams $settings, $albumID)
	{
		if(strlen($albumID))
		{
			$result = $connection->Send("http://vk.com/video?gid=".$settings->PAGE_ID."&section=album_".$albumID);
			if(!preg_match('/section\s*:\s*["\']?album_'.$albumID.'["\']?/i', $result, $m))
				$albumID = '';
		}
		return $albumID;
	}

	protected function CreateVideoAlbum($connection, $hash, DSocialPosterParams $settings, $name, $description="")
	{
		$result = $connection->Send("http://vk.com/al_video.php", 'act=edit_album&al=1&oid=-'.$settings->PAGE_ID);
		if(preg_match('/hash:\s*\'([^\']+)\'/', $result, $m))
		{
			$result = $connection->Send("http://vk.com/al_video.php", "act=do_edit_album&al=1&comm=false&desc=".$description."&hash=".$m[1]."&oid="."-".$settings->PAGE_ID."&only=&title=".$name."&view=false");
			if(preg_match('/album_(\d+)/', $result, $m))
				return $m[1];
		}
		return false;
	}

	public function PostVideo(DSocialPosterConnection $connection, DSocialPosterParams $settings, DSocialPostParams $albumParams, DSocialPostParams $postParams, &$sCreatedAlbum)
	{
		parent::PostMessage($connection, $settings, $postParams);

		if ($settings->IS_USE != "Y")
			return false;

		$hash = $this->GetHash($connection, $settings);

		if (!$hash["post_hash"])
		{
			$this->Log(1);
			return false;
		}

		$url = urlDecode($postParams->GetName());
		if(!preg_match('#^http://#', $url))
			return false;

		if(strlen($albumParams->GetID()))
		{
			$albumID = self::CheckVideoAlbumExists($connection, $settings, $albumParams->GetExtID());
			if(empty($albumID))
			{
				if($sCreatedAlbum = $this->CreateVideoAlbum($connection, $hash, $settings, $albumParams->GetName(0, false)))
					$albumParams->SetExtID($sCreatedAlbum);
			}
			return $this->UploadVideo2Album($connection, $settings, $url, $albumParams->GetExtID());
		}
		$video_id = $this->UploadVideo($connection, $settings, $url, $albumID);
		return !empty($video_id);
	}

	public function UploadVideo(DSocialPosterConnection $connection, DSocialPosterParams $settings, $url)
	{
		$params = $this->GetUploadParams($connection, $settings, true);

		// get video info
		$arParsedVideoResult = null;
		$result = $connection->Send($params["URL"], array_merge($params["PARAMS"], array("url" => $url)));
		if(preg_match('#onParseDone\s*\(({.+?})\);#si', $result, $m))
			$arParsedVideoResult = CUtil::JsObjectToPhp($m[1]);

		// upload image
		$arImageUploadResult = null;
		$connection->SetHandleOption(CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));
		if($arParsedVideoResult)
		{
			$result = $connection->Send(
				"http://vk.com/share.php",
				array(
					"act" => "a_photo",
					"extra" => $arParsedVideoResult["extra"],
					"image" => $arParsedVideoResult["images"][0],
					"url" => $arParsedVideoResult["url"],
				)
			);
			if(preg_match('#onUploadDone\s*\(.*?({.+?})\);#si', $result, $m))
				$arImageUploadResult = CUtil::JsObjectToPhp($m[1]);
		}

		//upload video
		if($arImageUploadResult && $arParsedVideoResult)
		{
			$result = $connection->Send(
				"http://vk.com/al_video.php",
				array(
					"act" => "save_external",
					"al" => 1,
					"description" => $arParsedVideoResult["description"],
					"domain" => $arParsedVideoResult["domain"],
					"extra" => $arParsedVideoResult["extra"],
					"extra_data" => $arParsedVideoResult["extraData"],
					"gid" => $settings->PAGE_ID,
					"hash" => $params["PARAMS"]["shared_hash"],
					"image_url" => $arParsedVideoResult["images"][0],
					"oid" => $params["PARAMS"]["shared_oid"],
					"photo_id" => $arImageUploadResult["photo_id"],
					"photo_owner_id" => $arImageUploadResult["user_id"],
					"privacy_video" => "",
					"share_text" => $arParsedVideoResult["description"],
					"share_title" => $arParsedVideoResult["title"],
					"title" => $arParsedVideoResult["title"],
					"to_video" => 1,
					"url" => $arParsedVideoResult["url"],
				)
			);
			if(preg_match('#({.+?})#s', $result, $m))
			{
				$res = CUtil::JsObjectToPhp($m[1]);
				return $res["video_id"];
			}
		}
		return false;

	}

	public function UploadVideo2Album(DSocialPosterConnection $connection, DSocialPosterParams $settings, $url, $albumID)
	{
		$video_id = $this->UploadVideo($connection, $settings, $url);
		return $video_id ? $this->MoveVideo2Album($connection, $settings, $video_id, $albumID) : false;
	}

	protected function MoveVideo2Album(DSocialPosterConnection $connection, DSocialPosterParams $settings, $video_id, $album_id)
	{
		$result = $connection->Send("http://vk.com/video?act=edit&gid=".$settings->PAGE_ID);
		if(preg_match('#"moveHash"\s*:\s*"([^"]+)"#', $result, $m))
		{
			$result = $connection->Send(
				"http://vk.com/al_video.php",
				array(
					"act" => "move_to_album",
					"al" => 1,
					"album_id" => $album_id,
					"vid" => $video_id,
					"hash" => $m[1],
					"oid" => "-".$settings->PAGE_ID
				)
			);
		}
		return true;
	}

	protected function ParseVideo(DSocialPosterConnection $connection, DSocialPosterParams $settings, $url)
	{
		$params = $this->GetUploadParams($connection, $settings, true);
		$result = $connection->Send($params["URL"], array_merge($params["PARAMS"], array("url" => $url)));
		if(preg_match('#onParseDone\s*\(({.+?})\);#si', $result, $m))
			return CUtil::JsObjectToPhp($m[1]);
		return false;
	}
}
?>