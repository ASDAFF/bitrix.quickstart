<?php

	namespace Webprofy\Bitrix\Attribute;

	use Webprofy\Bitrix\Attribute\SectionFieldAttribute;
	
	class SectionFieldAttributes extends Attributes{
		function fill(){

			$sfa = new SectionFieldAttribute('ID');
			$sfa->setData(array(
				'CODE' => 'ID',
				'NAME' => 'ID'
			));
			$this->add($sfa);

			$a = \CIBlock::GetArrayByID($this->iblock->getId());
			foreach($a['FIELDS'] as $id => $data){
				$data['CODE'] = $id;
				$sfa = new SectionFieldAttribute($id);
				$sfa->setData($data);
				$this->add($sfa);
			}
			return $this;
		}
	}