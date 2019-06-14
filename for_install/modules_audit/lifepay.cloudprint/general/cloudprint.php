<?
use Bitrix\Main\Config\Option;

class CLifepayEvent{
    
    static $MODULE_ID = "lifepay.cloudprint";
	
	public static function SalePrintHandler(\Bitrix\Sale\Payment $payment, $oldValue)
    {

        $paid = $payment->getField('PAID');
        $paySystemId = $payment->getPaymentSystemId();
		
		/* пиздец долбоебизм 
        $order_number = isset($_SESSION['ORDER_NUMBER']) ? $_SESSION['ORDER_NUMBER'] : $_REQUEST["ID"];
        if(!($arOrder = CSaleOrder::GetList(array(), array('ACCOUNT_NUMBER' => $order_number))->Fetch()))
        $arOrder = CSaleOrder::GetByID($order_number);
		*/

        $savePaySystem = Option::get(self::$MODULE_ID, 'pay_systems', '');  
        $arSavePaySystem =  strlen($savePaySystem) > 0 ? unserialize($savePaySystem) : Array();

        if( !in_array($paySystemId, $arSavePaySystem) ) {
            return false;
        }
        if ($paid == 'N') {
            return false;
        }

        $orderId = $payment->getField('ORDER_ID');
		$arOrder = CSaleOrder::GetByID($orderId);

        $order = \Bitrix\Sale\Order::load($orderId);
        $orderProperties = $order->getPropertyCollection();
        $arProps = $orderProperties->getArray();
        $deliveryPrice = $order->getDeliveryPrice();

        $moduleParams = [
            "login" => Option::get(self::$MODULE_ID, 'api_login', ''),
            "apikey" => Option::get(self::$MODULE_ID, 'api_key', ''),
            "target_serial" => Option::get(self::$MODULE_ID, 'printer_number', ''),
            "mode" =>  Option::get(self::$MODULE_ID, 'printer_mode', ''),
            "test" => Option::get(self::$MODULE_ID, 'test_mode', '')
        ];

        // Налог на позицию. Возможные значения: 
        $arTax = [
            'none' => 'none',
            0 => 'vat0',
            10 => 'vat10',
            18 => 'vat18',
        ];

        // products
        $arrProducts = [];
        $products = CSaleBasket::GetList( Array("ID"=>"ASC"), Array("ORDER_ID"=>$orderId) );
        while ($arItems = $products->Fetch())
        {

            $product = [
                //'name' => iconv('cp1251', 'utf8', $arItems['NAME']),
				'name' => $arItems['NAME'],
                'quantity' =>$arItems['QUANTITY'],

                // передаем цену со скидкой
                'price' => round($arItems['PRICE'], 2),

                'tax' => $arTax[$arItems['VAT_RATE']*100]
            ];

// убираем скидки
//            if(isset($arItems['DISCOUNT_VALUE'])) {
//                $product['discount'] = [
//                    'type' => 'percent',
//                    'value' => (int)preg_replace('~[^0-9]+~','',$arItems['DISCOUNT_VALUE'])
//                ];
//            }

            $arrProducts[] = $product;
        }

        if($deliveryPrice > 0){
            $arrProducts[] = array(
                //'name' => iconv('cp1251', 'utf8', GetMessage("LIFEPAY_CLOUDPRINT_DOSTAVKA")),
				'name' => GetMessage("LIFEPAY_CLOUDPRINT_DOSTAVKA"),
                'quantity' => 1,
                'price' => $deliveryPrice,
                'tax' => $arTax[0],
            );
        }

        $phone = $orderProperties->getPhone()->getValue();
		
		if (!$arOrder['USER_EMAIL']) {
			$arOrder['USER_EMAIL'] = $orderProperties->getUserEmail()->getValue();
		}
		
		
        $phone = preg_replace("/[^0-9]/", '', $phone);
        if(strlen($phone) == 11) {
            $checkFormat = $phone{0};
            if($checkFormat == 8) {
                $phone = '7'.(substr($phone, 1));
            }
        } else if(strlen($phone) == 10) {
            $phone = '7'.$phone;
        } else {
            $phone = 0;
        }
        $q_date = new DateTime();
        $requestData = [
            'type' => 'payment',
            'ext_id' => $orderId.'__moshoztorg.ru',
            'card_amount' => '#',        
            'customer_email' => $arOrder['USER_EMAIL'],
            'customer_phone' => $phone,
            "login" => $moduleParams["login"],
            "apikey" => $moduleParams["apikey"],
            "target_serial" => $moduleParams["target_serial"],
            "mode" =>  $moduleParams["mode"],
            "purchase" => [
                'products' => $arrProducts,
            ]
        ];
        $ch = curl_init('https://sapi.life-pay.ru/cloud-print/create-receipt');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData,JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if(Option::get(self::$MODULE_ID, 'test_mode', '')) {

		
        }

    } 
}

?>