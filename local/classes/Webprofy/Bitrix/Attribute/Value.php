<?php

	namespace Webprofy\Bitrix\Attribute;

	use Webprofy\Bitrix\Attribute\Attributes;
	use Webprofy\Bitrix\Attribute\Attribute;
	
	class Value{
		protected
			$many,
			$other,

			$canMany,
			$canOther,

			$values,
			$type = 'string',
			$limit;

		function setType($type){
			$this->type = $type;
			return $this;
		}

		function isOther(){
			return $this->other;
		}

		function getType(){
			return $this->type;
		}

		function set($values){
			$this->values = $values;
			return $this;
		}

		function getAttributes($iblock){
			$as = new Attributes($iblock);
			if(!$this->canOther || !$this->other){
				return $as;
			}

			foreach($this->values as $value){
				$as->add(
					Attribute::generate($value, $iblock)
				);
			}
			return $as;
		}

		function get($element = null){
			$values = array();
			foreach($this->values as $value){
				$values[] = $this->parseOne($value, $element);
			}

			if(!$this->many && !$this->limit){
				return $values[0];
			}

			return $values;
		}

		protected function parseOne($value, $element = null){
			if($this->canOther && $this->other && $element){
				return $element->get(
					$value['type'],
					$value['id']
				);
			}
			return $value;
		}

		function setLimit($limit){
			$this->limit = $limit;
			return $this;
		}
/*
		function setMany($many){
			$this->many = $many;
			return $this;
		}

		function setOther($other){
			$this->other = $other;
			return $this;
		}
*/
		function setJson($info){
			$this->values = $info['values'];
			$this->type = $info['type'];
			$this->many = $info['many'];
			$this->other = $info['other'];
		}

		function setCanOther($canOther){
			$this->canOther = $canOther;
			return $this;
		}

		function setCanMany($canMany){
			$this->canMany = $canMany;
			return $this;
		}

		function getJson(){
			return array(
				'canMany' => $this->canMany,
				'canOther' => $this->canOther,
				'_many' => $this->many,
				'_other' => $this->other,
				'limit' => $this->limit,
				'values' => $this->values,
				'type' => $this->type
			);
		}
	}