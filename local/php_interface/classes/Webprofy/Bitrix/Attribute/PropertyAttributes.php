<?php

	namespace Webprofy\Bitrix\Attribute;
	use \WP;
	
	class PropertyAttributes extends Attributes{
		function fill(){
			$pas = $this;

			WP::bit(array(
				'of' => 'properties',
				'f' => array(
					'iblock' => $this->iblock->getId()
				),
				'each' => function($d, $f) use ($pas){
					$pa = new PropertyAttribute($f['ID']);
					$pa->setData($f);
					$pas->add($pa);
				}
			));

			return $this;
		}		
	}