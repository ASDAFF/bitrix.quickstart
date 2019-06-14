<?php

	namespace Webprofy\Bitrix\Attribute;
	
	class PropertyAttribute extends Attribute{
		function createData(){
			return	\WP::bit(array(
				'of' => 'properties',
				'f' => array(
					'ID' => $this->id
				),
				'one' => 'f'
			));
		}

		static function getType(){
			return 'property';
		}

		private static $valueTypesByCode = array(
			'number' => array(
				'N',
				'F',
				'S:UserID',
				'S:FileMan',
				'N:SASDCheckboxNum',
				'N:Sequence',
			),
			'list' => array(
				'L',
				'G',
				'E',
				'G:SectionAuto',
				'N:SASDSection',
				'S:TopicID',
				'E:SKU',
				'E:EList',
				'S:ElementXmlID',
				'E:EAutocomplete',
				'S:directory',
			)
		);

		function getPropertyType(){
			$full = $this->f('PROPERTY_TYPE');
			if(strlen($this->f('USER_TYPE'))){
				$full .= ':'.$this->f('USER_TYPE');
			}
			return $full;
		}

		function getValueType(){
			$code = $this->getPropertyType();
			foreach(self::$valueTypesByCode as $type => $codes){
				if(in_array($code, $codes)){
					return $type;
				}
			}
			return 'string';
		}

		function getActionCode(){
			return 'PROPERTY_'.$this->f('CODE');
		}

		function getElementValueType(){
			return 'property';
		}

		function getListValues(){
			switch($this->getPropertyType()){
				case 'L':
					return \WP::bit(array(
						'of' => 'list-values',
						'f' => array(
							'PROPERTY_ID' => $this->f('ID')
						),
						'map' => function($d, $f){
							return array(
								'id' => $f['ID'],
								'name' => $f['VALUE']
							);
						}
					));
					break;
			}
		}
	}