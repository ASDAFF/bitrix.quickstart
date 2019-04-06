<?php

namespace Rees46;

use Rees46\Bitrix\Data;

class Functions
{
	const BASE_URL = 'http://api.rees46.com';

	private static $jsIncluded = false;
	private static $handleJs = '';

	/**
	 * insert script tags for Rees46
	 */
	public static function includeJs()
	{
		global $USER;

		$shop_id = Options::getShopID();

		if (!$shop_id) {
			return;
		}

		?>
			<script type="text/javascript" src="http://cdn.rees46.com/rees46_script2.js"></script>
			<script type="text/javascript">
				$(function () {
					REES46.init('<?= $shop_id ?>', <?= $USER->GetId() ?: 'undefined' ?>, function () {
						if (typeof(window.ReesPushData) != 'undefined') {
							for (i = 0; i < window.ReesPushData.length; i++) {
								var pd = window.ReesPushData[i];

								if (pd.hasOwnProperty('order_id')) {
									REES46.pushData(pd.action, pd.data, pd.order_id);
								} else {
									REES46.pushData(pd.action, pd.data);
								}
							}
						}

						<?= self::$handleJs ?>
					});
				});
			</script>
		<?php

		self::$jsIncluded = true;
	}

	/**
	 * push data via javascript (insert corresponding script tag)
	 *
	 * @param $action
	 * @param $data
	 * @param $order_id
	 */
	public static function jsPushData($action, $data, $order_id = null)
	{
		$json = self::jsonEncode($data);

		?>
			<script>
				if (typeof(REES46) == 'undefined') {
					if (typeof(window.ReesPushData) == 'undefined') {
						window.ReesPushData = [];
					}

					window.ReesPushData.push({
						action: '<?= $action ?>',
						data: <?= $json ?>
						<?= $order_id !== null ? ', order_id: '. $order_id : '' ?>
					});
				} else {
					REES46.addReadyListener(function () {
						REES46.pushData('<?= $action ?>', <?= $json ?> <?= $order_id !== null ? ', '. $order_id : '' ?>);
					});
				}
			</script>
		<?php
	}

	public static function cookiePushData($action, $data)
	{
		switch ($action) {
			case 'cart':
				$cookie = 'rees46_track_cart';
				break;

			case 'remove_from_cart':
				$cookie = 'rees46_track_remove_from_cart';
				break;

			case 'purchase':
				$cookie = 'rees46_track_purchase';
				break;

			default:
				error_log('Unknown action type: '. $action);
				return;
		}

		setcookie($cookie, json_encode($data), strtotime('+1 hour'), '/');
	}

	public static function cookiePushPurchase($data, $order_id = null)
	{
		self::cookiePushData('purchase', array(
			'items' => $data,
			'order_id' => $order_id,
		));
	}

	/**
	 * get item_ids in the current cart
	 *
	 * @return array
	 */
	public static function getCartItemIds()
	{
		$ids = array();

		foreach (Data::getOrderItems(null) as $item) {
			$ids []= $item['PRODUCT_ID'];
		}

		return $ids;
	}

	/**
	 * get real item id for complex product
	 */
	public static function getRealItemID($item_id)
	{
		$arr = Data::getItemArray($item_id);
		if ($arr) {
			return $arr['item_id'];
		} else {
			return null;
		}
	}

	/**
	 * @param array|\Traversable $item_ids
	 * @return array
	 */
	public static function getRealItemIDsArray($item_ids)
	{
		$ids = array();

		foreach ($item_ids as $id) {
			$real_id = self::getRealItemID($id);

			if ($real_id) {
				$ids[] = $real_id;
			}
		}

		return $ids;
	}

	/**
	 * run js after includeJs
	 * @param $js
	 */
	public static function handleJs($js)
	{
		if (self::$jsIncluded) {
			?>
				<script>
					$(function () {
						<?= $js ?>
					});
				</script>
			<?php
		} else {
			self::$handleJs .= $js;
		}
	}

	public static function showRecommenderCSS()
	{
		global $APPLICATION;
		static $css_sent = false;

		$shop_id = Options::getShopID();
		if($APPLICATION && $shop_id && $css_sent === false) {
			$APPLICATION->AddHeadString('<link href="http://rees46.com/shop_css/'. $shop_id .'" rel="stylesheet" />');
		}
		$css_sent = true;

//		$prefix = SITE_DIR ?: '/';
//
//		if ($APPLICATION && $css_sent === false) {
//			$APPLICATION->AddHeadString('<link href="'. $prefix .'include/rees46-handler.php?action=css" rel="stylesheet" />');
//			$css_sent = true;
//		}
	}

	/**
	 * Unfortunately JSON_UNESCAPED_UNICODE is available only in PHP 5.4 and later
	 *
	 * @param $array
	 * @return string JSON
	 */
	private static function jsonEncode($array)
	{
		$js_array = true;
		$prev_key = -1;

		$result = array();

		foreach ($array as $key => $value) {
			if ($js_array && is_numeric($key) && $key == $prev_key + 1) {
				$prev_key = $key;
			} else {
				$js_array = false;
			}

			if       (is_array($value)) {
				$value = self::jsonEncode($value);
			} elseif ($value === true) {
				$value = 'true';
			} elseif ($value === false) {
				$value = 'false';
			} elseif ($value === null) {
				$value = 'null';
			} elseif (is_numeric($value)) {
				// leave as it is
			} else {
				$value = '"'.addslashes($value).'"';
			}

			$key = '"'.addslashes($key).'"';

			$result[$key] = $value;
		}

		if ($js_array) {
			$json = '[' . implode(',', $result) . ']';
		} else {
			$jsonHash = array();
			foreach ($result as $key => $value) {
				$jsonHash []= "$key:$value";
			}
			$json = '{'. implode(',', $jsonHash) .'}';
		}

		return $json;
	}

	/**
	 * Old events for compatibility
	 */

	/**
	 * @deprecated Rees46\Events::view
	 */
	public static function view($item_id)               { Events::view($item_id); }
	/**
	 * @deprecated Rees46\Events::purchase
	 */
	public static function purchase($order_id)          { Events::purchase($order_id); }
}
