<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arParams['AJAX_POST']=='Y' && ($_REQUEST["save_product_review"] == "Y"))
{
	$response = ob_get_clean();
	$JSResult = array();
	$FHParser = new CForumSimpleHTMLParser($response);

	$statusMessage = $FHParser->getTagHTML('div[class=reviews-note-box]');
	$JSResult['statusMessage'] = $statusMessage;

	if ((empty($_REQUEST["preview_comment"]) || $_REQUEST["preview_comment"] == "N")) // message added
	{
		$result = intval($arResult['RESULT']);

		if (
			(
				(isset($_REQUEST['pageNumber']) && intval($_REQUEST['pageNumber']) != $arResult['PAGE_NUMBER']) || 
				(isset($_REQUEST['pageCount']) && intval($_REQUEST['pageCount']) != $arResult['PAGE_COUNT'])
			) && 
			$result > 0)
		{
			$messagePost = $FHParser->getTagHTML('div[class=reviews-block-inner]');
			$messageNavigation = $FHParser->getTagHTML('div[class=reviews-navigation-box]');

			$JSResult += array(
				'status' => true,
				'allMessages' => true,
				'message' => $messagePost,
				'messageID' => $result,
				'navigation' => $messageNavigation,
				'pageNumber' => $arResult['PAGE_NUMBER'],
				'pageCount' => $arResult['PAGE_COUNT']
			);
			if (strlen($messagePost) <1)
				$JSResult += array('reload' => true);
		} 
		else 
		{
			$JSResult['allMessages'] = false;
			if ($result == false)
			{
				$JSResult += array(
					'status' => false,
					'error' => $arError[0]['title']
				);
			}
			else 
			{
				$messagePost = $FHParser->getTagHTML('table[id=message'.$result.']');
				$JSResult += array(
					'status' => true,
					'messageID' => $result,
					'message' => $messagePost
				);
				if (strlen($messagePost) <1)
					$JSResult += array('reload' => true);
			}
		}
	}
	else // preview
	{
		if (empty($arError))
		{
			$messagePreview = $FHParser->getTagHTML('div[class=reviews-preview]');
			$JSResult += array(
				'status' => true,
				'previewMessage' => $messagePreview,
				);
		}
		else
		{
			$JSResult += array(
				'status' => false,
				'error' => $arError[0]['title']
			);
		}
	}

	$APPLICATION->RestartBuffer();
	$res = CUtil::PhpToJSObject($JSResult);
	echo "<script>top.SetReviewsAjaxPostTmp(".$res.");</script>";
	die();
}
?>
