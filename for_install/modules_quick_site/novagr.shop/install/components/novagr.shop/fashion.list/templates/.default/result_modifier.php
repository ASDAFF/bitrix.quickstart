<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

    $cp = $this->__component; // ������ ����������

    if (is_object($cp) and is_array($arResult['ITEMS']))
    {
        $cp->arResult['ITEMS'] = $arResult['ITEMS'];
        $cp->arResult['ORIGINAL'] = $arResult['ORIGINAL'];
        //��������
        $cp->SetResultCacheKeys(array('ITEMS','ORIGINAL'));
        //������� � ������ ��� ������� � ������� � �������
        $arResult['ITEMS'] = $cp->arResult['ITEMS'];
        $arResult['ORIGINAL'] = $cp->arResult['ORIGINAL'];
    }
?>