<?
$APPLICATION->AddPanelButton(
    Array(
        "ID" => "400", //���������� ������������ ������
        "TEXT" => "������������� ����� ����",
        "MAIN_SORT" => 1000, //������ ���������� ��� ����� ������
        "SORT" => 10, //���������� ������ ������
        "HREF" => "/bitrix/admin/fileman_file_edit.php?path=%2Finclude%2Fmenu%2Flinks.php&full_src=Y", //��� javascript:MyJSFunction())
        "ALT" => "������������� ����� ����", //������ �������
        ),
    $bReplace = false //�������� ������������ ������?
);	
?>