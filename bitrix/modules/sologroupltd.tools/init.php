<?
//���������� ������ ������ �� init.php ��������� ��������(����� ������� ���� �������� �� ����� ��������):
//	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/init.php")){require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/init.php");}

	if(!$pls_not_include_module){//������ ���������� ��������������� ��� ����������� ������ ������ ���
		if(CModule::IncludeModuleEx('sologroupltd.tools')) $pls_not_include_module = true;//���� �� 2 ���� ������ ����� ���������� ������ ��� �� ����
		// - � ��� ����� ��������� Warning
	}

//	if(!$pls_not_include_module){//���� ������ �� ���������� - ����������� ������������� �� �����
		$arFuncSoloTools = array('getibc','dump');//���������, �������� ����� ������� ��� ����� ����������
		foreach ($arFuncSoloTools as $ValueSoloTools) {
			if(!function_exists($ValueSoloTools)){
				if(file_exists   ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/functions_$ValueSoloTools.php")){
					require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sologroupltd.tools/functions_$ValueSoloTools.php");
				}
			}
		}
//	}
?>