<?php
	
	namespace Webprofy\Bitrix\Attribute\Action\Compare;
	
	use Webprofy\Bitrix\Attribute\Action\CompareAction;

	class EqualAction extends CompareAction{
		protected static $operatorsData = array(
			array('>', 'Больше'),
			array('=', 'Равно'),
			array('<', 'Меньше'),
			array('>=', 'Больше или равно'),
			array('!=', 'Не равно'),
			array('<=', 'Меньше или равно'),
		);

		function __construct($operator){
			parent::__construct($operator);
			$this->value->setCanMany($operator == '=');
		}

		function checkAttribute($attribute){
			if(in_array($this->operators->getId(), array(
				'=', '!='
			))){
				return true;
			}
			return in_array($attribute->getValueType(), array(
				'number'
			));
		}

		function run(&$code, &$value){
			$s = $this->operators->getId();
			if($s != '='){
				$code = $s.$code;
			}
		}
	}