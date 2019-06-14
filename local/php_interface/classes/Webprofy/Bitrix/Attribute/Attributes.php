<?php

	namespace Webprofy\Bitrix\Attribute;

	use Webprofy\Bitrix\Attribute\GeneralAttributes;
	
	class Attributes extends GeneralAttributes{
		function getJson(){
			$result = array();
			$this->each(function($attribute) use (&$result){
				$result[] = $attribute->getJson();
			});
			return $result;
		}

		function getByTypeAndId($type, $id){
			$attributes = new Attributes($this->iblock);
			$this->each(function($attribute) use ($type, $id, $attributes){
				if($attribute->getId() != $id || $attribute->getType() != $type){
					return;
				}
				$attributes->add($attribute);
			});
			return $attributes;
		}

		function fill(){
			return $this;
		}

		function getListValues(){
			return array();
		}
		
		function getSelectFields(){
			$f = array();
			$this->each(function($attribute) use (&$f){
				$value = $attribute->getValue()->get();
				$code = $attribute->getActionCode('filter');
				$attribute->getAction()->run($code, $value);

				$f[$code] = $value;
			});
			return $f;
		}
	}