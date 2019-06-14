<?php
	
	namespace Webprofy\Bitrix\Attribute\Action\Update;

	use Webprofy\Bitrix\Attribute\Action\UpdateAction;

	class PhpAction extends UpdateAction{
		protected static $operatorsData = array(
			array('php', 'Заменить с помощью кода')
		);

		public function __construct($operator = null){
			parent::__construct($operator);
			if($operator == 'php'){
				$this->value->setLimit(array(
					array(
						'name' => 'Код',
						'type' => 'textarea',
						'value' => file_get_contents(__DIR__.'/PhpAction.code.example.txt')
					)
				));
			}
		}

		function run($current, $update, $element){
			switch($this->operators->getId()){
				case 'php':
					return eval($update[0]);
					return $value;
			}
		}
	}