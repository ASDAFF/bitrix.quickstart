<?php

class REES46
{
	const EVENT_VIEW        = 'view';
	const EVENT_CART        = 'cart';
	const EVENT_REMOVE_FROM_CART = 'remove_from_cart';
	const EVENT_PURCHASE    = 'purchase';
	const EVENT_RATE        = 'rate';

	const RECOMMEND_INTERESTING = 'interesting';
	const RECOMMEND_ALSO_BOUGHT = 'also_bought';
	const RECOMMEND_SEE_ALSO    = 'see_also';
	const RECOMMEND_POPULAR     = 'popular';
	const RECOMMEND_SIMILAR     = 'similar';
	const RECOMMEND_RECENTLY_VIEWED = 'recently_viewed';

	private $shop_id;
	private $session_id;
	private $user_id;
	private $base_url;
	private $use_async_send;

	/**
	 * @var Pest
	 */
	private $rest;

	/**
	 * @param string $base_url REES46 base url (http[s]://api.rees46.com)
	 * @param string $shop_id shop uniqid
	 * @param string $session_id user's session id (should be taken from REES46 javascript)
	 * @param string $user_id user's uniqid in the shop
	 */
	public function __construct($base_url, $shop_id, $session_id, $user_id = null)
	{
		$this->shop_id    = $shop_id;
		$this->session_id = $session_id;
		$this->user_id    = $user_id;
		$this->base_url   = $base_url;

		// disable fsockopen send as it sometimes fails
		// $this->use_async_send = function_exists('fsockopen') ? true : false;
		$this->use_async_send = false;

		$this->rest = new Pest($base_url);
		$this->rest->curl_opts[CURLOPT_TIMEOUT_MS] = 1000;
	}

	/**
	 * @param string $event event type
	 * @param REES46PushItem[]|REES46PushItem $items
	 * @param string $order_id for purchase
	 *
	 * @return bool success
	 *
	 * @throws REES46Exception on incorrect item type
	 */
	public function pushEvent($event, $items, $order_id = null)
	{
		if (is_array($items) === false) {
			$items = array($items);
		}

		$data = array(
			'items'     => array(),
			'ssid'      => $this->session_id,
			'event'     => $event,
			'shop_id'   => $this->shop_id,
		);

		if ($this->user_id) {
			$data['user_id'] = $this->user_id;
		}

		if ($order_id) {
			$data['order_id'] = $order_id;
		}

		$k = 0;
		foreach ($items as $item) {
			if ($item instanceof REES46PushItem === false) {
				throw new REES46Exception('Item should be an instance of REES46PushItem');
			}

			$data['item_id'][$k] = $item->item_id;

			if ($item->category) {
				$data['category'][$k] = $item->category;
			}

			if ($item->price) {
				$data['price'][$k] = $item->price;
			}

			if ($item->is_available) {
				$data['is_available'][$k] = $item->is_available ? 1 : 0;
			}

			if ($item->rating) {
				$data['rating'][$k] = $item->rating;
			}

			if ($item->category) {
				$data['amount'][$k] = $item->category;
			}

			$k++;
		}

		$data['count'] = $k;

		if ($this->use_async_send) {
			$this->postAsync('push', $data);
		} else {
			$this->rest->post('push', $data);
		}
	}

	public function recommend($recommender, $params = array())
	{
		$data = array(
			'ssid'              => $this->session_id,
			'recommender_type'  => $recommender,
			'shop_id'           => $this->shop_id,
		);

		if ($this->user_id) {
			$data['user_id'] = $this->user_id;
		}

		if (isset($params['item_id'])) {
			$data['item_id'] = $params['item_id'];
		}

		if (isset($params['category'])) {
			$data['category'] = $params['category'];
		}

		if (isset($params['cart'])) {
			$data['cart_item_id'] = array();

			foreach ($params['cart'] as $id) {
				$data['cart_item_id'][] = $id;
			}

			$data['cart_count'] = count($data['cart_item_id']);
		}

		$result = $this->rest->get('recommend', $data);

		return json_decode($result);
	}

	/**
	 * shortcut for pushEvent('view')
	 *
	 * @param $items
	 */
	public function pushView($items)
	{
		$this->pushEvent(self::EVENT_VIEW, $items);
	}

	/**
	 * shortcut for pushEvent('cart')
	 *
	 * @param $items
	 */
	public function pushCart($items)
	{
		$this->pushEvent(self::EVENT_CART, $items);
	}

	/**
	 * shortcut for pushEvent('remove_from_cart')
	 *
	 * @param $items
	 */
	public function pushRemoveFromCart($items)
	{
		$this->pushEvent(self::EVENT_VIEW, $items);
	}

	/**
	 * shortcut for pushEvent('purchase')
	 *
	 * @param $items
	 * @param string $order_id
	 */
	public function pushPurchase($items, $order_id = null)
	{
		$this->pushEvent(self::EVENT_VIEW, $items, $order_id);
	}

	/**
	 * shortcut for pushEvent('rate')
	 *
	 * @param $items
	 */
	public function pushRate($items)
	{
		$this->pushEvent(self::EVENT_VIEW, (array)$items);
	}

	public function recommendInteresting()
	{
		return $this->recommend(self::RECOMMEND_INTERESTING);
	}

	public function recommendAlsoBought($item_id)
	{
		return $this->recommend(self::RECOMMEND_ALSO_BOUGHT, array(
			'cart' => array($item_id),
		));
	}

	public function recommendSeeAlso(array $cart)
	{
		return $this->recommend(self::RECOMMEND_SEE_ALSO, array(
			'cart' => $cart,
		));
	}

	public function recommendPopular($category = null)
	{
		$params = array();

		if ($category !== null) {
			$params['category'] = $category;
		}

		return $this->recommend(self::RECOMMEND_POPULAR, $params);
	}

	public function recommendSimilar($item_id, $cart = array())
	{
		return $this->recommend(self::RECOMMEND_SIMILAR, array(
			'item_id' => $item_id,
			'cart' => $cart,
		));
	}

	public function recommendRecentlyViewed($cart = array())
	{
		return $this->recommend(self::RECOMMEND_RECENTLY_VIEWED, array(
			'cart' => $cart,
		));
	}

	/**
	 * Faster version of Pest::post() without returning a value
	 *
	 * @param $url
	 * @param $data
	 * @throws REES46Exception
	 */
	private function postAsync($url, $data)
	{
		$post_data = http_build_query($data);

		// url normalisation from Pest
		if (strncmp($url, $this->base_url, strlen($this->base_url)) != 0) {
			$url = rtrim($this->base_url, '/') . '/' . ltrim($url, '/');
		}

		$parts = parse_url($url);

		if ($parts === false) {
			throw new REES46Exception('Unparsable URL');
		}

		$socket = fsockopen($parts['host'],
			isset($parts['port'])?$parts['port']:80,
			$errno, $errstr, 300);

		if ($socket === false) {
			throw new REES46Exception($errstr);
		}

		$out = "POST ".$parts['path']." HTTP/1.1\r\n";
		$out.= "Host: ".$parts['host']."\r\n";
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out.= "Content-Length: ".strlen($post_data)."\r\n";
		$out.= "\r\n";
		$out.= $post_data;

		fwrite($socket, $out);
		fclose($socket);
	}
}

class REES46PushItem
{
	/**
	 * @var int item uniqid
	 */
	public $item_id;

	/**
	 * @var string category uniqid
	 */
	public $category;

	/**
	 * @var float price
	 */
	public $price;

	/**
	 * @var bool is_available
	 */
	public $is_available;

	/**
	 * @var int rating [1..5] (for rate)
	 */
	public $rating;

	/**
	 * @var int amount (for purchase)
	 */
	public $amount;

	public function __construct($item_id, array $data = array())
	{
		$this->item_id = $item_id;

		if (isset($data['category'])) {
			$this->category = $data['category'];
		}

		if (isset($data['price'])) {
			$this->price = $data['price'];
		}

		if (isset($data['is_available'])) {
			$this->is_available = $data['is_available'];
		}

		if (isset($data['rating'])) {
			$this->rating = $data['rating'];
		}

		if (isset($data['amount'])) {
			$this->category = $data['amount'];
		}
	}
}

class REES46Exception extends RuntimeException {}
