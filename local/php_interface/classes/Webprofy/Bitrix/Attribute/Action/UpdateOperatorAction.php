<?php
	
	namespace Webprofy\Bitrix\Attribute\Action;

	use Webprofy\Bitrix\Attribute\Action\UpdateAction;

	class UpdateOperatorAction extends UpdateAction{
		protected static $operators = array(
			array('symbol', 'Название'),
		);

		protected
			$operator;

		static function getOperators(){
			return array_map(function($a){
				return $a[0];
			}, self::$operators);
		}


		static function getOperatorNames($operator = null){
			$result = array();
			foreach(self::$operators as $a){
				if($operator == null){
					$result[$a[0]] = $a[1];
					continue;
				}

				if($a[0] == $operator){
					return $a[1];
				}
			}

			if($operator !== null){
				return null;
			}
			
			return $result;
		}
		
		function getName(){
			return sprintf('[%s] %s', $this->operator, self::getOperatorNames($this->operator));
		}

		function getId(){
			return parent::getId().$this->operator;
		}

		function __construct($operator){
			if(!in_array($operator, self::getOperators())){
				throw new \Exception('Undefined operator: '.$operator);
			}
			$this->operator = $operator;
		}
	}
