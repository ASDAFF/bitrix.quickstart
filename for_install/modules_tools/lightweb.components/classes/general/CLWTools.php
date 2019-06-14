<?php
class CLWTools {
	
	/* 
	Проверяет наличие элементов массива с указанными индексами
	Массив содержит $arRequiredElement - список ключей обязательных элементов для массива $arChecked
	Возвращает:
		false - если параметры указаны не верно
		array (массив элементов) - если обнаруженные недостающие обязательные элементы
		true - если все обязательные элементы присутствуют
	*/
	static function ArrayCheckElement($arRequiredElement, $arChecked){
		if (!is_array($arRequiredElement)){return false;}
		if (!is_array($arChecked)){return false;}
		$arCheckedElement=array();
		foreach($arRequiredElement as $RequiredElement){
			if (empty($arChecked[$RequiredElement])){
				$arCheckedElement[]=$RequiredElement;
			}
		}
		if (empty($arCheckedElement)){
			return true;
		} else {
			return $arCheckedElement;	
		}
	}
	
}