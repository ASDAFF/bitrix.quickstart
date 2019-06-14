<?php

	namespace Webprofy\Bitrix\Attribute;
	
	use Webprofy\Bitrix\DataHolder;

	use Webprofy\Bitrix\Attribute\FieldAttribute;
	use Webprofy\Bitrix\Attribute\PropertyAttribute;

	use Webprofy\Bitrix\Attribute\Action\Action;
	use Webprofy\Bitrix\Attribute\Action\CompareActions;
	use Webprofy\Bitrix\Attribute\Action\UpdateActions;
	use Webprofy\Bitrix\Attribute\Action\UpdateAction;


	abstract class Attribute extends DataHolder{
		protected
			$compareActions,
			$updateActions,
			$iblock,
			$action;

		public
			$isForElementUpdate = true;

		function setIBlock($iblock){
			$this->iblock = $iblock;
			return $this;
		}

		static function generate($info, $iblock = null){
			if($info['type'] == 'field' && $iblock){
				$attribute = $iblock
					->getAttributes('elements', 'all')
					->filter(
						'getId',
						$info['id'],
						true
					);
			}
			else{
				$class = self::getAttributeClassByType($info['type']);
				$attribute = new $class($info['id']);
			}
			if(!empty($info['action'])){
				$attribute->setAction($info['action']);
			}
			return $attribute;
		}

		static function getAttributeClassByType($type){
			foreach(array(
				'Webprofy\\Bitrix\\Attribute\\FieldAttribute',
				'Webprofy\\Bitrix\\Attribute\\PropertyAttribute',
				'Webprofy\\Bitrix\\Attribute\\SectionFieldAttribute',
				'Webprofy\\Bitrix\\Attribute\\SectionUserAttribute',
			) as $class){
				if($class::getType() == $type){
					return $class;
				}
			}
			return null;
		}

		function __construct($id){
			parent::__construct($id);
		}

		static function getType(){
			return 'attribute';
		}

		function getCode(){
			return $this->f('CODE');
		}

		function getSelect(){
			return array(
				'code' => $this->getActionCode(),
				'name' => $this->getName(),
				'type' => $this->getType()
			);
		}

		function getJson(){
			return array(
				'id' => $this->getId(),
				'type' => $this->getType(),
				'name' => $this->getName(),
				'valueType' => $this->getValueType()
			);
		}

		function getActionCode($type = 'select'){
			return $this->f('CODE');
		}

		function getElementValueType(){
			return 'field';
		}

		function getAction(){
			return $this->action;
		}

		function isUpdater(){
			return ($this->getAction()) instanceof UpdateAction;
		}

		function getValueType(){
			if($this->getCode() == 'ID'){
				return 'number';
			}
			return 'string';
		}

		function getValue(){
			return $this->getAction()->getValue();
		}

		function getListValues(){
			
		}

		function setAction($action){
			if($action instanceof Action){
				$this->action = $action;
				return $this;
			}

			$id = null;
			$info = null;

			if(is_string($action)){
				$id = $action;
			}
			elseif(is_array($action)){
				$info = $action;
				$id = $info['id'];
			}

			if($id){
				$this->action = $this
					->getActions('all')
						->filter('getId', $id)
							->first();

				if($info){
					$this->getAction()->getValue()->setJson($info['values']);
				}
			}
			
			return $this;
		}

		function getActions($type){
			$actions = null;

			switch($type){
				case 'compare':
					$actions = new CompareActions();
					break;
					
				case 'update':
					$actions = new UpdateActions();
					break;

				default:
					return $this
						->getActions('compare')
							->extend($this->getActions('update'));
			}

			return $actions
				->fill()
				->clearByAttribute($this);
		}

		function getName(){
			$name = $this->f('NAME');
			if(empty($name)){
				return $this->getCode();
			}
			return $name;
		}

		protected $additional = false;
		function setAdditional($additional){
			$this->additional = $additional;
		}
		function getAdditional(){
			return $this->additional;
		}
	}