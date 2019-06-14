<?php
	
	namespace Webprofy\Bitrix\Attribute\Action\Update;

	use Webprofy\Bitrix\Attribute\Action\UpdateAction;

	class StringAction extends UpdateAction{
		protected static $operatorsData = array(
			array('+s', 'Добавить строку в конец'),
			array('s+', 'Добавить строку в начало'),
			array('^$', 'Заменить по регулярному выражению')
		);

		public function __construct($operator = null){
			parent::__construct($operator);
			if($operator == '^$'){
				$this->value->setLimit(array(
					array(
						'name' => 'Выражение',
						'type' => 'text',
						'value' => '/Названи(е|я)/'
					),
					array(
						'name' => 'Замена',
						'type' => 'text',
						'value' => '$0 -> Новое названи$1'
					),
				));
			}
			else{
				$this->value->setCanOther(true);
			}
		}

		function checkAttribute($attribute){
			return in_array($attribute->getValueType(), array(
				'string'
			));
		}

		function run($current, $update, $element){
			switch($this->operators->getId()){
				case '+s':
					return $current.$update;

				case 's+':
					return $update.$current;

				case '^$':
					return preg_replace(
						$update[0],
						$update[1],
						$current
					);
			}
		}
	}