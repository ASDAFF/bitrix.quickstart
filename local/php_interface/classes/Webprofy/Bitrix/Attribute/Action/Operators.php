<?php
	
	namespace Webprofy\Bitrix\Attribute\Action;

	use Webprofy\General\Container;

	class Operators{
		protected
			$operators = array(),
			$active = null;

		function __construct($operators, $setId = null){
			foreach($operators as $operator){
				list($id, $name) = $operator;
				$this->operators[$id] = array(
					'name' => $name,
					'id' => $id,
				);
			}
			if(func_num_args() == 2){
				$this->setId($setId);
			}
		}

		function getOperator($id = null){
			if($id == null){
				return $this->active;
			}
			return @$this->operators[$id];
		}

		function setId($id){
			$operator = $this->getOperator($id);
			if(!$operator){
				throw new \Exception('Wrong operator '.$id);
			}
			$this->active = $operator;
			return true;
		}

		function getIds(){
			return array_map(function($operator){
				return $operator['id'];
			}, $this->operators);
		}

		function getNames(){
			$result = array();

			foreach($this->operators as $operator){
				$result[$operator['id']] = $operator['name'];
			}
			
			return $result;
		}

		function getId(){
			$operator = $this->getOperator();
			return $operator ? $operator['id'] : null;
		}

		function getName($id = null){
			$operator = $this->getOperator($id);
			return $operator ? sprintf('[%s] %s', $operator['id'], $operator['name']) : null;
		}

	}
