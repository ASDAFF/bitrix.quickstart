<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    $cp = $this->__component; // ������ ����������

    if (is_object($cp) and is_array($arResult['ITEMS']))
    {
        $cp->arResult['ITEMS'] = $arResult['ITEMS'];   
        //��������
        $cp->SetResultCacheKeys(array('ITEMS'));
        //������� � ������ ��� ������� � ������� � �������
        $arResult['ITEMS'] = $cp->arResult['ITEMS'];
    }
?>