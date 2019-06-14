<?
	namespace Webprofy\Ajax;

	class Field{
		private static $errors = array(
			'required' => 'Не введено значение.',
			'filter' => 'Некорректное значение.',
			'captcha' => 'Неверно введена капча.',
			'login' => 'Неверное имя пользователя / пароль',
			'confirm' => 'Возникла неизвестная ошибка'
		);

		protected
			$name = '',
			$value = null,
			$valueSet = false,
			$required,
			$errorType = false,
			$template = '<input type="text" name="%NAME" value="%VALUE"/>';

		function __construct($name, $required = false){
			$this->name = $name;
			$this->required = $required;
		}

		function getName(){
			return $this->name;
		}

		function getValue(){
			if($this->valueSet == false){
				$this->setValue($_POST[$this->name]);
			}
			return $this->value;
		}

		function setValue($value = null){
			$this->value = $value;
			$this->valueSet = true;
		}

		function checkValue(){
			$value = $this->getValue();
			if(!$value){
				if($this->required){
					$this->errorType = 'required';
					return false;
				}
				else{
					return true;
				}
			}
			return $this->check($value);
		}

		function isBad(){
			return $this->errorType != false;
		}

		function getBadInfo(){
			return array(
				'type' => $this->errorType,
				'ru' => self::$errors[$this->errorType],
				'name' => $this->getName()
			);
		}

		function check($value){
			return true;
		}

		function setError($type){
			$this->errorType = $type;
		}

		function setTemplate($template){
			$this->template = strtr($template, array(
				'%PREV' => $this->template
			));	
		}

		function html(){
			return strtr($this->template, array(
				'%NAME' => $this->name,
				'%VALUE' => $this->getValue()
			));
		}
	}