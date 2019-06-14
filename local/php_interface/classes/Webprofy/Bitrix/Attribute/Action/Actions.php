<?php

	namespace Webprofy\Bitrix\Attribute\Action;
	
	use Webprofy\General\Container;
	use Webprofy\Bitrix\Attribute\Action\Operators;

	class Actions extends Container{
		protected
			$fillActions = array();

		function getJson(){
			$result = array();
			$this->each(function($action) use (&$result){
				$result[] = $action->getJson();
			});
			return $result;
		}

		function clearByAttribute($attribute){
			$this->eachRemove(function($action) use ($attribute){
				return !$action->checkAttribute($attribute);
			});
			return $this;
		}

		function fill(){
			foreach($this->fillActions as $class){ // Action
				$od = $class::getOperatorsData();
				if($od == null){
					$this->add(new $class());
					continue;
				}
				$operators = new Operators($od);
				$ids = $operators->getIds();
				foreach($ids as $id){
					$this->add(new $class($id));
				}
			}
			return $this;
		}
	}