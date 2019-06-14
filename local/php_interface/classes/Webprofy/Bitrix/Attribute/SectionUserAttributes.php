<?php

	namespace Webprofy\Bitrix\Attribute;

	use Webprofy\Bitrix\Attribute\SectionUserAttribute;
	
	class SectionUserAttributes extends Attributes{
		function fill(){
			$id = "IBLOCK_".$this->iblock->getId()."_SECTION";
			$fields = $GLOBALS['USER_FIELD_MANAGER']->GetUserFields($id, 0, LANGUAGE_ID);
			foreach($fields as $id => $data){
				$data['CODE'] = $id;
				$sua = new SectionUserAttribute($id);
				$sua->setData($data);
				$this->add($sua);
			}

			return $this;
		}
	}