<?php
/**
 * Interface to be implemented by local shop model that prepares data for SheeplaProxy class
 * @author Orba (biuro{at}orba.pl)
 *
 */
interface ISheeplaProxyDataModel
{
	/**
	* Gets and returns new orders for Sheepla system
	* * the $order structure is (new - please change your code to use it)
	* (the old structure is still accepted but will be deprecated in the future)
	* array(
	* 	0 => array(
	* 'orderValue' 				=> '100.00', 							// total order price in float
	* 	'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 	'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 	'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 	'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 	'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 	'externalCountryId' 		=> '1', 							// country id from the shop
	* 	'externalBuyerId' 			=> '25', 							// client id from the shop
	* 	'externalOrderId' 			=> '1829', 							// order id from the shop
	* 	'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 	'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 	'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  'deliveryPrice'				=> 20.10,							// the delivery cost
	*  'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 	'deliveryOptions' 			=> array (
	* 		'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 		'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 		'plInPost'  => array (
	* 			'popId'		=> '12', 									// InPost's "paczkomat" id
	* 			'popName' 	=> 'WAW115', 								// InPost's "paczkomat" code name
	* 		),
	* 		'ruShopLogistics' => array(
	* 			'metroStationId' => 3									// information for ShopLogistics about metro station
	* 		)
	* 	),
	* 	'deliveryAddress' 			=> array (
	* 		'street' 		=> 'Plac Defilad 1', 						// delivery address street
	* 		'zipCode' 		=> '00-001', 								// delivery address street
	* 		'city' 			=> 'Warszawa', 								// delivery address city
	* 		'countryAlpha2Code' 	=> 'PL', 							// the country 2 letters
	* 		'firstName' 	=> 'Adam', 									// Recipient first name
	* 		'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 		'phone' 		=> '600123123', 							// Recipient phone number
	* 		'email' 		=> 'adam.kowalski@polska.pl' 				// Recipient e-mail adders
	* 	)
	* ),
	* 	1 => array( ...
	* 	)
	* )
	* 
	* the $order structure is (old still accepted)
	* array(
	* 	0 => array(
	* 		'orderValue' 				=> '100.00', 						// total order price in float
	* 		'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 		'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 		'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 		'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 		'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 		'externalCountryId' 		=> '1', 							// country id from the shop
	* 		'externalBuyerId' 			=> '25', 							// client id from the shop
	* 		'externalOrderId' 			=> '1829', 							// order id from the shop
	* 		'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 		'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 		'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  		'deliveryPrice'				=> 20.10,							// the delivery cost
	* 		 'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 		'deliveryOptions' 			=> array (
	* 			'cod' 		=> '1', 										// is it cash on delivery, can be 0 or 1
	* 			'insurance' => '0', 										// is there insurance on this orders packages, can be 0 or 1
	* 			'popId'		=> '12', 										// InPost's "paczkomat" id
	* 			'popName' 	=> 'WAW115' 									// InPost's "paczkomat" code name
	* 		),
	* 		'deliveryAddress' 			=> array (
	* 			'street' 		=> 'Plac Defilad 1', 					// delivery address street
	* 			'zipCode' 		=> '00-001', 								// delivery address street
	* 			'city' 			=> 'Warszawa', 								// delivery address city
	* 			'countryId' 	=> '1', 									// the country id from the sheepla (see SheeplaProxyDataModelAbstract)
	* 			'firstName' 	=> 'Adam', 								// Recipient first name
	* 			'lastName' 		=> 'Kowalski', 								// Recipient last name
	* 			'phone' 		=> '600123123', 							// Recipient phone number
	* 			'email' 		=> 'adam.kowalski@polska.pl' 					// Recipient e-mail adders
	* 		)
	* 	),
	* 	1 => 0 => array(
	* 		'orderValue' 				=> '100.00', 						// total order price in float
	* 		'orderValueCurrency' 		=> 'PLN', 							// ISO formated currency type
	* 		'externalDeliveryTypeId' 	=> '1', 							// shipment id from the shop
	* 		'externalDeliveryTypeName' 	=> 'Carrier', 						// shipment name from the shop
	* 		'externalPaymentTypeId' 	=> '2', 							// payment type id from the shop
	* 		'externalPaymentTypeName' 	=> 'PayPal', 						// payment type name from the shop
	* 		'externalCountryId' 		=> '1', 							// country id from the shop
	* 		'externalBuyerId' 			=> '25', 							// client id from the shop
	* 		'externalOrderId' 			=> '1829', 							// order id from the shop
	* 		'shipmentTemplate' 			=> '11', 							// shipment template id from the sheepla application
	* 		'comments' 					=> 'this is primary order', 		// additional comments about the order
	* 		'createdOn' 				=> '2004-02-12T15:19:21+00:00', 	// ISO 8601 date (added in PHP 5) (see http://php.net/manual/en/function.date.php format character 'c')
	*  		'deliveryPrice'				=> 20.10,							// the delivery cost
	* 		 'deliveryPriceCurrency'		=> 'PLN',							// the delivery currency
	* 		'deliveryOptions' 			=> array ( ......
	* @return array
	*/
	public function getOrders();
	
	/**
	 * Get's and returns configuration for Sheepla libs in the specific structure
	 * array(
	 *	'url' 	=> 'http://sheepla.pl:8080/',
	 *	'key' 	=> '38xcjni8e9v9wjnc'
	 *	)
	 * @return array
	 */
	public function getConfig();
	
	/**
	 * Count all shop orders
	 * it's used for first handshake transmition
	 */
	public function getCountAllOrders();
	
	public function getPaymentMethods();
	public function getShippingMethods();
}
