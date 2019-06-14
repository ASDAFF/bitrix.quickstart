<?php

	namespace Webprofy\Bitrix\Attribute;
	
	use Webprofy\Bitrix\Attribute\Action\Update\SetAction;

	use Webprofy\Bitrix\Attribute\Action\Compare\BetweenAction;
	use Webprofy\Bitrix\Attribute\Action\Compare\EqualAction;

	class SectionUserAttribute extends Attribute{
		function __construct($id){
			parent::__construct($id);
			$this->setData(array(
				'CODE' => $id // mother fucking bitrix logic))
			));
		}

		function getActionCode(){
			return $this->f('CODE');
		}

		function getCode(){
			return preg_replace('/^UF_/', '', $this->f('CODE'));
		}

		function getName(){
			return $this->f('EDIT_FORM_LABEL');
		}

		static function getType(){
			return 'section-user';
		}
	}