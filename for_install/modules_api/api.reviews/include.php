<?
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class CApiReviews
{
	/**
	 * @var CBitrixComponent $ob
	 *
	 * @return string
	 */
	public static function getTemplateFile($ob)
	{

		$templateFile = '';
		if($ob->initComponentTemplate()) {
			//$templateFolder = &$ob->getTemplate()->GetFolder();
			$templateFile   = $_SERVER['DOCUMENT_ROOT'] . $ob->getTemplate()->GetFile();
		}

		return $templateFile;
	}

	public static function getElementRating($ELEMENT_ID, $SITE_ID = SITE_ID)
	{
		$filter = array(
			 '=ACTIVE' => 'Y',
			 '=ELEMENT_ID' => $ELEMENT_ID,
			 '=SITE_ID' => $SITE_ID,
		);

		$rsRating = \Api\Reviews\ReviewsTable::getList(array(
			 'select' => array('RATING'),
			 'filter' => $filter,
		));

		$rating  = 0;
		$arItems = array();

		while($arItem = $rsRating->fetch()) {
			$rating += (int)$arItem['RATING'];

			$arItems[] = $arItem;
		}

		$countItems    = count($arItems);
		$averageRating = ($countItems > 0) ? round(($rating / $countItems), 1) : $countItems;
		$fullRating    = ($countItems > 0) ? round(($rating / $countItems)*20, 1) : $countItems;

		$arReturn = array(
			 'RATING'  => $averageRating,
			 'PERCENT' => $fullRating . '%',
			 'COUNT'   => $countItems,
		);

		return $arReturn;
	}

	public static function getElementRatingHtml($id, $text = '', $bUseCss = true, $theme = 'flat', $color = 'orange1')
	{
		if(!$id)
			return '';

		$rating_html = '';

		if($bUseCss) {
			$rating_html .= '
				<style type="text/css">
				.api-element-rating{position: relative; overflow: hidden}
				.api-element-rating .api-stars-empty{height:21px;width:110px;display:inline-block;}
				.api-element-rating .api-stars-full{height:21px;display:block;width:0;}
				.api-element-rating .api-stars-empty, .api-element-rating .api-stars-full{background-image:url("/bitrix/images/api.reviews/'.$theme.'/'.$color.'/sprite.png");background-repeat:no-repeat;}
				.api-element-rating .api-stars-empty{background-position:0 -15px;}
				.api-element-rating .api-stars-full{background-position:0 -36px;}
				.api-element-rating .api-average{margin-top:5px}
				</style>
			';
		}

		$arRating = self::getElementRating($id);

		$title = Loc::getMessage('ARI_AVERAGE_RATING_TITLE', array(
			 '#RATING#' => $arRating['RATING'], '#COUNT#' => $arRating['COUNT'],
		));

		$rating_html .= '<div class="api-element-rating">';
		$rating_html .= '<div class="api-stars-empty" title="'. $title .'">
											<div class="api-stars-full" style="width:'. $arRating['PERCENT'] .'"></div>
										</div>';

		if($text && strlen($text)>0)
		{
			$text = str_replace(
				 array('#RATING#', '#COUNT#'),
				 array($arRating['RATING'], $arRating['COUNT']),
				 $text
			);
			$rating_html .= '<div class="api-average">'.$text.'</div>';
		}

		$rating_html .= '</div>';

		return $rating_html;
	}
}
?>