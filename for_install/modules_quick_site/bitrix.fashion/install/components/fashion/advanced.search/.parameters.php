<?
    CModule::IncludeModule('iblock');

        $arComponentParameters = array(
        'GROUPS' => array(
            /*'CACHE_PARAMS' => array(
                'NAME' => '�����������',
            ),*/
            'NAV_PARAMS' => array(
                'NAME' => '������������ ���������',
            ),
        ),
        'PARAMETERS' => array(
			'SEARCH_IN_CATALOG' => array(
                'PARENT' => 'BASE',
                'NAME' => '������ �� ���������',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            'SEARCH_IN_CONTENT' => array(
                'PARENT' => 'BASE',
                'NAME' => '������ �� ��������',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            'SEARCH_SEPARATELY' => array(
                'PARENT' => 'BASE',
                'NAME' => '������� ���������� ������ �� ������',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            
            'CHECK_IN_BASKET' => array(
                'PARENT' => 'BASE',
                'NAME' => '��� ������� ������ �� �������� ��������� ��� ������� � �������',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            
            'NEED_NAV' => array(
                'PARENT' => 'BASE',
                'NAME' => '��������� ��������� ������ �� ��������',
                'TYPE' => 'CHECKBOX',
                'REFRESH' => 'N',
            ),
            'NAV_CATALOG' => array(
                'PARENT' => 'NAV_PARAMS',
                'NAME' => '����� ����������� �� �������� ��������',
                'TYPE' => 'STRING',
                'DEFAULT' => '12',
            ),
            'NAV_CONTENT' => array(
                'PARENT' => 'NAV_PARAMS',
                'NAME' => '����� ����������� �� �������� ��������',
                'TYPE' => 'STRING',
                'DEFAULT' => '12',
            ),
            'NAV_TEMPLATE' => array(
                'PARENT' => 'NAV_PARAMS',
                'NAME' => '������ ������ ��� ������������ ���������',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            'SORT_CATALOG' => array(
                'PARENT' => 'BASE',
                'NAME' => '���� ��� ���������� �� ��������',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            'SORT_DIRECT' => array(
                'PARENT' => 'BASE',
                'NAME' => '����������� ����������',
                'TYPE' => 'STRING',
                'DEFAULT' => 'asc',
            ),
            
            'SORT_CONTENT' => array(
                'PARENT' => 'BASE',
                'NAME' => '���� ��� ���������� �� ��������',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            'SORT_CONTENT_DIRECT' => array(
                'PARENT' => 'BASE',
                'NAME' => '����������� ����������',
                'TYPE' => 'STRING',
                'DEFAULT' => '',
            ),
            
            'CATALOG_LIMIT' => array(
                'PARENT' => 'BASE',
                'NAME' => '���������� ��������� �������� �� ������ �������� ������',
                'TYPE' => 'STRING',
                'DEFAULT' => '4',
            ),
            'CONTENT_LIMIT' => array(
                'PARENT' => 'BASE',
                'NAME' => '���������� ��������� �������� �� ������ �������� ������',
                'TYPE' => 'STRING',
                'DEFAULT' => '5',
            ),
        ),
    );
?>
