# ��������� �������� �����
��� ��������� ��������, ���������� � ���������������� ����� ������, ������������� � ������������, �������� 2 ������:
* `getNormalValue($inputName, $siteId = '', $reload = false)` - ��� ��������� �������� �� ������� �������
* `getPresetValue($inputName, $presetId, $siteId = '', $reload = false)` - ��� ��������� �������� �� ������� �������

���������:
* `$inputName` � ��� ������ (������ �������� `name` ������)
* `$presetId` � ����� �������
* `$siteId` � ������������� �����
* `$reload` � ��� ������ ��������� �������� ����� �������� � ��� � ��� ��������� ���������� �� ��������� ������� �� ����. ���� �������� ����� true, �� �������� �������� ������� �� ����.

## ������ ��������
����� ������ ������ ������� ������ ��� ����� ��������, ��������, ��� ��� ������� � ����-������. 

��� ��������� �������� ����� ������� �������:
	
	public function getTextareaValueS1($reload = false)
	{
	    return $this->getNormalValue('input_textarea', 's1', $reload);
	}
��� ��������� �������� ����� ������� �������:

	public function getS1PresetColor($presetId, $reload = false)
	{
	    return $this->getPresetValue('preset_color', $presetId, 's1', $reload);
	}