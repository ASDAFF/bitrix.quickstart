<?php
class CLWTools {
	
	/* 
	��������� ������� ��������� ������� � ���������� ���������
	������ �������� $arRequiredElement - ������ ������ ������������ ��������� ��� ������� $arChecked
	����������:
		false - ���� ��������� ������� �� �����
		array (������ ���������) - ���� ������������ ����������� ������������ ��������
		true - ���� ��� ������������ �������� ������������
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