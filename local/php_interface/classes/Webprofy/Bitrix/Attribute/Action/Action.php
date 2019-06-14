<?php

	namespace Webprofy\Bitrix\Attribute\Action;

	use Webprofy\Bitrix\Attribute\Value;
	use Webprofy\Bitrix\Attribute\Action\Operators;
	
	abstract class Action{
		protected
			$id,
			$value;

		public
			$operators = null;

		protected static $operatorsData = null;
		static function getOperatorsData(){
			return static::$operatorsData;
		}

		function getId(){
			$id = end(explode('\\', get_class($this)));
			if($this->operators instanceof Operators){
				$id .= $this->operators->getId();
			}
			return $id;
		}

		function __construct($operator = null){
			if(($od = self::getOperatorsData()) !== null){
				$this->operators = new Operators($od, $operator);
			}
			$this->value = new Value();
		}

		function checkAttribute($attribute){
			return true;
		}

		function getName(){
			if($this->operators instanceof Operators){
				return $this->operators->getName();
			}
			return 'untitled';
		}

		function getValue(){
			return $this->value;
		}

		function getJson(){
			return array(
				'name' => $this->getName(),
				'id' => $this->getId(),
				'value' => $this->value->getJson(),
			);
		}
	}