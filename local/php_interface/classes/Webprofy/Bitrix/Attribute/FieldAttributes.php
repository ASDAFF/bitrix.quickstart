<?php

	namespace Webprofy\Bitrix\Attribute;

	use Webprofy\Bitrix\Attribute\FieldAttribute\SectionAttribute;
	use Webprofy\Bitrix\Attribute\FieldAttribute\IdAttribute;
	
	class FieldAttributes extends Attributes{
		function fill(){
			$this->add(new IdAttribute());
			$this->add(new SectionAttribute());

			foreach(\CIBlock::GetFields($this->iblock->getId()) as $code => $f){
				if(strpos($code, 'SECTION') !== false){
					continue;
				}
				$f['CODE'] = $code;
				$fa = new FieldAttribute($code);
				$fa->setData($f);
				$this->add($fa);
			}
			return $this;
		}
	}