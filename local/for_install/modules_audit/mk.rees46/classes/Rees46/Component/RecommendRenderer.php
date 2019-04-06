<?php

namespace Rees46\Component;

use CCatalogProduct;
use CFile;
use CModule;
use CCatalogDiscount;
use Rees46\Options;

IncludeModuleLangFile(__FILE__);

class RecommendRenderer
{
	/**
	 * handler for include/rees46-recommender.php, render recommenders
	 */
	public static function run()
	{
		CModule::IncludeModule('catalog');
		CModule::IncludeModule('sale');

		$recommended_by = '';

		// get recommender name
		if (isset($_REQUEST['recommended_by'])) {
			$recommender = strval($_REQUEST['recommended_by']);
			$recommended_by = '?recommended_by='. urlencode($recommender);

			switch ($recommender) {
				case 'see_also':
					$recommender_title = GetMessage('REES_INCLUDE_SEE_ALSO');
					break;
				case 'recently_viewed':
					$recommender_title = GetMessage('REES_INCLUDE_RECENTLY_VIEWED');
					break;
				case 'also_bought':
					$recommender_title = GetMessage('REES_INCLUDE_ALSO_BOUGHT');
					break;
				case 'similar':
					$recommender_title = GetMessage('REES_INCLUDE_SIMILAR');
					break;
				case 'interesting':
					$recommender_title = GetMessage('REES_INCLUDE_INTERESTING');
					break;
				case 'popular':
					$recommender_title = GetMessage('REES_INCLUDE_POPULAR');
					break;
				default:
					$recommender_title = '';
			}
		}

		$libCatalogProduct = new CCatalogProduct();
		$libFile = new CFile();

		// render items
		if (isset($_REQUEST['recommended_items']) && is_array($_REQUEST['recommended_items']) && count($_REQUEST['recommended_items']) > 0) {

			$found_items = 0;

			$html = '';
			$html .= '<div class="recommender-block-title">' . $recommender_title . '</div>';
			$html .= '<div class="recommended-items">';

			foreach ($_REQUEST['recommended_items'] as $item_id) {
				$item_id = intval($item_id);
				$item = $libCatalogProduct->GetByIDEx($item_id);
				$price = array_pop($item['PRICES']);

				if ($price['PRICE'] == 0) {
					continue;
				}

				$final_price = $price['PRICE'];

				// Получаем скидки на товары
				$discounts = CCatalogDiscount::GetDiscountByProduct($item_id);
				if($discounts && is_array($discounts)) {
					$max_discount = 0;
					foreach($discounts as $discount) {
						if($discount['ACTIVE'] == 'Y' && $discount['VALUE'] > $max_discount) {
							$max_discount = $discount['VALUE'];
						}
					}
					if($max_discount > 0) {
						$final_price -= $max_discount;					}
				}

				$link = $item['DETAIL_PAGE_URL'] . $recommended_by;
				$picture = $item['DETAIL_PICTURE'] ?: $item['PREVIEW_PICTURE'];

				if ($picture === null) {
					continue;
				}

				$file = $libFile->ResizeImageGet($picture, array(
					'width'  => Options::getImageWidth(),
					'height' => Options::getImageHeight()
				), BX_RESIZE_IMAGE_PROPORTIONAL, true);

				$html .= '<div class="recommended-item">
					<div class="recommended-item-photo">
						<a href="' . $link . '"><img src="' . $file['src'] . '" class="item_img"/></a>
					</div>
					<div class="recommended-item-title">
						<a href="' . $link . '">' . $item['NAME'] . '</a>
					</div>
					<div class="recommended-item-price">
						' . $final_price . '
						' . GetMessage('REES_INCLUDE_CURRENCY') . '
					</div>
					<div class="recommended-item-action">
						<a href="' . $link . '">' . GetMessage('REES_INCLUDE_MORE') . '</a>
					</div>
				</div>';

				$found_items++;

			}

			//= $price['CURRENCY']

			$html .= '</div>';

			if($found_items > 0) {
				echo $html;
			}

		}
	}
}
