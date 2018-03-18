<?php

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');

class Rees46Func
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

		$shop_id = self::shopId();

		if ($shop_id === false) {
			return;
		}

		?>
			<script type="text/javascript" src="http://cdn.rees46.com/rees46_script.js"></script>
			<script type="text/javascript">
				$(function(){
					REES46.init('<?= $shop_id ?>', <?= $USER->GetId() ?: 'undefined' ?>, function () {
						var date = new Date(new Date().getTime() + 365*24*60*60*1000);
						document.cookie = 'rees46_session_id=' + REES46.ssid + '; path=/; expires='+date.toUTCString();

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
	 * Get current shop id from the settings
	 *
	 * @return string|false
	 */
	private static function shopId()
	{
		$shop_id = COption::GetOptionString(mk_rees46::MODULE_ID, 'shop_id', false);

		return empty($shop_id) ? false : $shop_id;
	}

	/**
	 * get item params for view push
	 *
	 * @param $id
	 * @return array
	 */
	private static function getItemArray($id)
	{
		$libProduct    = new CCatalogProduct();
		$libIBlockElem = new CIBlockElement();
		$libPrice      = new CPrice();

		$item = $libProduct->GetByID($id);

		// maybe we have complex item, let's find its first child entry
		if ($item === false) {
			$list = $libIBlockElem->GetList(
				array(
					'ID' => 'ASC',
				),
				array(
					'PROPERTY_CML2_LINK' => $id,
				));

			if ($itemBlock = $list->Fetch()) {
				$item = $libProduct->GetByID($itemBlock['ID']);
			} else {
				return null; // c'est la vie
			}
			// now $item points to the earliest child
		} else { // we have simple item or child
			$itemBlock  = $libIBlockElem->GetByID($id)->Fetch();

			$itemFull = $libProduct->GetByIDEx($id);

			if (!empty($itemFull['PROPERTIES']['CML2_LINK']['VALUE'])) {
				$id = $itemFull['PROPERTIES']['CML2_LINK']['VALUE'];
			} // set id of the parent if we have child
		}

		$return = array(
			'item_id' => intval($id),
		);

		if (empty($item)) {
			return null;
		}

		$price = $libPrice->GetBasePrice($itemBlock['ID']);

		if (!empty($itemBlock['IBLOCK_SECTION_ID'])) {
			$return['category'] = $itemBlock['IBLOCK_SECTION_ID'];
		}

		$has_price = false;
		if (!empty($price['PRICE'])) {
			$return['price'] = $price['PRICE'];
			$has_price = true;
		}

		if (isset($item['QUANTITY'])) {
			$quantity = $item['QUANTITY'] > 0;
			$return['is_available'] = ($quantity && $has_price) ? 1 : 0;
		}

		if (self::getIncludeNonAvailable()) {
			$return['is_available'] = 1;
		}

		return $return;
	}

	/**
	 * get item params for view or cart push from basket id
	 *
	 * @param $id
	 * @return array|bool
	 */
	private static function getBasketArray($id)
	{
		$libBasket = new CSaleBasket();
		$item = $libBasket->GetByID($id);

		return self::GetItemArray($item['PRODUCT_ID']);
	}

	/**
	 * push data via javascript (insert corresponding script tag)
	 *
	 * @param $action
	 * @param $data
	 * @param $order_id
	 */
	private static function jsPushData($action, $data, $order_id = null)
	{
		?>
			<script>
				if (typeof(REES46) == 'undefined') {
					if (typeof(window.ReesPushData) == 'undefined') {
						window.ReesPushData = [];
					}

					window.ReesPushData.push({
						action: '<?= $action ?>',
						data: <?= json_encode($data) ?>
						<?= $order_id !== null ? ', order_id: '. $order_id : '' ?>
					});
				} else {
					REES46.addReadyListener(function() {
						REES46.pushData('<?= $action ?>', <?= json_encode($data) ?> <?= $order_id !== null ? ', '. $order_id : '' ?>);
					});
				}
			</script>
		<?php
	}

	/**
	 * push data via curl
	 *
	 * @param $action
	 * @param $data
	 * @param $order_id
	 */
	private static function restPushData($action, $data, $order_id = null)
	{
		global $USER;

		$shop_id = self::shopId();

		if ($shop_id === false) {
			return;
		}

		if (isset($_COOKIE['rees46_session_id'])) {
			$ssid = $_COOKIE['rees46_session_id'];
		} else {
			return;
		}

		$rees = new REES46(self::BASE_URL, $shop_id, $ssid, $USER->GetID());

		try {
			$rees->pushEvent($action, $data, $order_id);
		} catch (REES46Exception $e) {
			error_log($e->getMessage());
			// do nothing at the time
		} catch (Pest_Exception $e) {
			error_log($e->getMessage());
			// do nothing at the time
		}
	}

	/**
	 * push view event
	 *
	 * @param $item_id
	 */
	public static function view($item_id)
	{
		$item = self::getItemArray($item_id);

		self::jsPushData('view', $item);
	}

	/**
	 * push add to cart event
	 *
	 * @see install/index.php
	 * @param $basket_id
	 */
	public static function cart($basket_id)
	{
		$item = self::getBasketArray($basket_id);
		self::restPushData('cart', new REES46PushItem($item['item_id'], $item));
	}

	/**
	 * get item_ids in the current cart
	 *
	 * @return array
	 */
	public static function getCartItemIds()
	{
		$ids = array();

		foreach (self::getOrderItems(null) as $item) {
			$ids []= $item['PRODUCT_ID'];
		}

		return $ids;
	}

	/**
	 * push remove from cart event
	 *
	 * @see install/index.php
	 * @param $basket_id
	 */
	public static function removeFromCart($basket_id)
	{
		$item = self::getBasketArray($basket_id);
		self::restPushData('remove_from_cart', new REES46PushItem($item['item_id'], $item));
	}

	/**
	 * callback for purchase event
	 *
	 * @see install/index.php
	 * @param $order_id
	 */
	public static function purchase($order_id)
	{
		$items = array();

		foreach (self::getOrderItems($order_id) as $item) {
			$pushItem = new REES46PushItem($item['PRODUCT_ID']);
			$pushItem->amount = $item['QUANTITY'];
			$items []= $pushItem;
		}

		self::restPushData('purchase', $items, $order_id);
	}

	/**
	 * get item data for order or current cart
	 *
	 * @param int $order_id send null for current cart
	 * @return array
	 */
	private static function getOrderItems($order_id = null)
	{
		$items = array();

		$libBasket = new CSaleBasket();

		if ($order_id !== null) {
			$list = $libBasket->GetList(array(), array('ORDER_ID' => $order_id));
		} else {
			$list = $libBasket->GetList(array(),
				array(
					'FUSER_ID' => $libBasket->GetBasketUserID(),
					'LID' => SITE_ID,
					'ORDER_ID' => false,
				)
			);
		}

		while ($item = $list->Fetch()) {
			$itemData = self::getItemArray($item['PRODUCT_ID']);
			$item['PRODUCT_ID'] = $itemData['item_id']; // fix ID for complex items
			$items []= $item;
		}

		return $items;
	}

	/**
	 * get real item id for complex product
	 */
	public static function getRealItemID($item_id)
	{
		$arr = self::getItemArray($item_id);
		if ($arr) {
			return $arr['item_id'];
		} else {
			return null;
		}
	}

	/**
	 * @param array|Traversable $item_ids
	 * @return array
	 */
	public static function getRealItemIDsArray($item_ids)
	{
		$ids = array();

		foreach($item_ids as $id) {
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
					$(function() {
						<?= $js ?>
					});
				</script>
			<?php
		} else {
			self::$handleJs .= $js;
		}
	}

	public static function getImageWidth()
	{
		return COption::GetOptionInt(mk_rees46::MODULE_ID, 'image_width', mk_rees46::IMAGE_WIDTH_DEFAULT);
	}

	public static function getImageHeight()
	{
		return COption::GetOptionInt(mk_rees46::MODULE_ID, 'image_height', mk_rees46::IMAGE_HEIGHT_DEFAULT);
	}

	public static function getIncludeNonAvailable()
	{
		return COption::GetOptionInt(mk_rees46::MODULE_ID, 'recommend_nonavailable', 0);
	}
}
