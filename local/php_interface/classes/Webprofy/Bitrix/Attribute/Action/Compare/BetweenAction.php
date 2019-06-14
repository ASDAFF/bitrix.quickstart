<?php
	
	namespace Webprofy\Bitrix\Attribute\Action\Compare;

	use Webprofy\Bitrix\Attribute\Action\CompareAction;

	class BetweenAction extends CompareAction{
		function __construct($operator = null){
			parent::__construct($operator);
			$this->value
				->setLimit(array(
					array(
						'name' => 'От',
					),
					array(
						'name' => 'До',
					)
				))
				->setType('number');
		}
			
		function run(&$code, &$value){
			if(!is_array($value) || count($value) != 2){
				throw new \Exception('Waiting for $value = array(..., ...). Got: '.print_r($value, 1));
			}
			$code = '><'.$code;
		}

		function getName(){
			return 'Между двумя значениями';
		}

		function checkAttribute($attribute){
			return in_array($attribute->getValueType(), array(
				'number'
			));
		}
	}