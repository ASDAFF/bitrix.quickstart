<?
IncludeModuleLangFile(__FILE__); // � menu.php ����� ��� �� ����� ������������ �������� �����

if($APPLICATION->GetGroupRight("form")>"D") // �������� ������ ������� � ������ ���-����
{
  // ���������� ������� ����� ����
  $aMenu = array(
    "parent_menu" => "global_menu_settings", // �������� � ������ "���������"
    "sort"        => 100,                    // ��� ������ ����
    "url"         => "webmechanic.landing_landing.php?lang=".LANGUAGE_ID,  // ������ �� ������ ����
    "text"        => GetMessage('webmechanic_menu_title'),       // ����� ������ ����
    "title"       => GetMessage('webmechanic_menu_title'), // ����� ����������� ���������
    "icon"        => "form_menu_icon", // ����� ������
    "page_icon"   => "form_page_icon", // ������� ������
    "items_id"    => "menu_webforms",  // ������������� �����
    "items"       => array(),          // ��������� ������ ���� ���������� ����.
  );

 

  // ������ ���������� ������
  return $aMenu;
}
// ���� ��� �������, ������ false
return false;
?>