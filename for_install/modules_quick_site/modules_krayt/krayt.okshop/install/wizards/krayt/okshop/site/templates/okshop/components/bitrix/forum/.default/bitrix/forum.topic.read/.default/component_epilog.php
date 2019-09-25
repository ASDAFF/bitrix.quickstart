<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arParams['AJAX_POST'] == 'Y' && $arParams['ACTION'] == 'REPLY')
{
	$response = ob_get_clean();
	$JSResult = array();
	$FHParser = new CForumSimpleHTMLParser($response);

	$statusMessage = $FHParser->getTagHTML('div[class=forum-note-box]');
	$JSResult['statusMessage'] = $statusMessage;

	if ($_POST["MESSAGE_MODE"] != "VIEW")
	{
		$result = intval($arResult['RESULT']);
		if ($result === 0)
		{
			$JSResult += array(
				'status' => false,
				'error' => $strErrorMessage
			);
		}
		else 
		{
			if ((isset($_REQUEST['pageNumber']) && intval($_REQUEST['pageNumber']) != $arResult['PAGE_NUMBER'])) // user is not on the last forum messages page
			{
				$messagePost = $FHParser->getInnerHTML('<!--FORUM_INNER-->', '<!--FORUM_INNER_END-->');
				$messageNavigation = $FHParser->getTagHTML('div[class=forum-navigation-box]');

				$JSResult += array(
					'status' => true,
					'allMessages' => true,
					'message' => $messagePost,
					'navigation' => $messageNavigation,
					'pageNumber' => $arResult['PAGE_NUMBER']
				);
			}
			else 
			{
				$JSResult['allMessages'] = false;
				$messagePost = $FHParser->getInnerHTML('<!--MSG_'.$result.'-->', '<!--MSG_END_'.$result.'-->');
				$JSResult += array(
					'status' => true,
					'messageID' => $result,
					'message' => $messagePost
				);
			}
			if (strpos($JSResult['message'], "ForumInitSpoiler") !== false)
			{
				$fname = $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix/forum.interface/templates/spoiler/script.js";
				if (file_exists($fname))
					$JSResult['message'] =
						'<script src="/bitrix/components/bitrix/forum.interface/templates/spoiler/script.js?'.filemtime($fname).'" type="text/javascript"></script>'.
						"\n".$JSResult['message'];
			}
			if (strpos($JSResult['message'], "onForumImageLoad") !== false)
			{
				$SHParser = new CForumSimpleHTMLParser($APPLICATION->GetHeadStrings());
				$scripts = $SHParser->getInnerHTML('<!--LOAD_SCRIPT-->', '<!--END_LOAD_SCRIPT-->');

				if ($scripts !== "")
					$JSResult['message'] = $scripts."\n".$JSResult['message'];
			}
		}
	}
	else // preview post
	{
		if (strlen($arResult["ERROR_MESSAGE"]) < 1)
		{
			$messagePreview = $FHParser->getTagHTML('div[class=forum-preview]');
			$JSResult += array(
				'status' => true,
				'previewMessage' => $messagePreview,
				);
			if (strpos($JSResult['previewMessage'], "ForumInitSpoiler") !== false)
			{
				$fname = $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix/forum.interface/templates/spoiler/script.js";
				if (file_exists($fname))
					$JSResult['previewMessage'] =
						'<script src="/bitrix/components/bitrix/forum.interface/templates/spoiler/script.js?'.filemtime($fname).'" type="text/javascript"></script>'.
							$JSResult['previewMessage'];
			}
			if (strpos($JSResult['previewMessage'], "onForumImageLoad") !== false)
			{
				$SHParser = new CForumSimpleHTMLParser($APPLICATION->GetHeadStrings());
				$scripts = $SHParser->getInnerHTML('<!--LOAD_SCRIPT-->', '<!--END_LOAD_SCRIPT-->');

				if ($scripts !== "")
					$JSResult['previewMessage'] = $scripts."\n".$JSResult['previewMessage'];
			}
		}
		else
		{
			$JSResult += array(
				'status' => false,
				'error' => $arResult["ERROR_MESSAGE"]
			);
		}
	}

	$APPLICATION->RestartBuffer();
	$res = CUtil::PhpToJSObject($JSResult);
	echo "<script>top.SetForumAjaxPostTmp(".$res.");</script>";
	die();
}
?>