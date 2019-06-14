# �������� ������ \Rover\Fadmin\Options
* [���������](#���������)
* [����](#����)
* [������](#������)

## ���������
��������� �������� �������� �������, ������� ����� ���� ���������� � ������� � �������������� ���������������� ������ ��� ����������� ��������������� �������.
### `EVENT__BEFORE_GET_REQUEST = 'beforeGetRequest'`
���������� ����� ���������� ��������. �� ��������� ����������. ���� ���������� `false`, �� ������� �� ��������������.
### `EVENT__BEFORE_REDIRECT_AFTER_REQUEST = 'beforeRedirectAfterRequest'`
���������� ����� ���������� ����� ��������. �� ��������� ����������. ���� ���������� `false`, �������� �� ������������.
### `EVENT__BEFORE_ADD_VALUES_FROM_REQUEST = 'beforeAddValuesFromRequest'`
���������� ����� ����������� ������ �� ��������. �� ��������� ����������. ���� ���������� `false` ������ �� �����������.
### `EVENT__BEFORE_ADD_VALUES_TO_TAB_FROM_REQUEST = 'beforeAddValuesToTabFromRequest'`
���������� ����� ����������� ������ �� �������� � ������ ���. ��������� ���������:
* `tab` - ������ \Rover\Fadmin\Tab ����, � ������� ��������� �������� �������� �������.

���� ���������� `false` ������ � ��� �� �����������.
### `EVENT__AFTER_ADD_VALUES_FROM_REQUEST = 'afterAddValuesFromRequest'`
����������� ����� ����������� ������ �� ��������. �� ��������� ����������.
### `EVENT__BEFORE_ADD_PRESET = 'beforeAddPreset'`
���������� ����� ����������� ������ �������. ��������� � ��������� ������ ���������:
* `siteId` - id �����, ��� �������� ����������� ������
* `value` - ��� �������, ���������� �� �������

���� �� ������ ������ ������ ��� �������, �� ��������� ��� � ����� `name`. ���� ���������� `false`, �� ������ �� �����������.
### `EVENT__AFTER_ADD_PRESET = 'afterAddPreset'`
���������� ����� ���������� ������ �������. ��������� � ��������� ������ ���������:
* `siteId` - id �����, ��� �������� ����������� ������
* `value` - ��� �������, ���������� �� �������
* `name`  - �������� ���, ������� ����� ���� ������ � `EVENT__BEFORE_ADD_PRESET`   
* `id`    - id �������

### `EVENT__BEFORE_REMOVE_PRESET = 'beforeRemovePreset'`
���������� ����� ��������� �������. ��������� � ��������� ������ ���������:                                	
* `siteId` - id �����, ��� �������� ����������� ������
* `id`    - id �������
    
���� ���������� `false`, �� ������ �� ���������. 
### `EVENT__AFTER_REMOVE_PRESET    = 'afterRemovePreset'`
���������� ����� �������� �������. �������� �������� `siteId` ���������� �������.
### `EVENT__BEFORE_MAKE_PRESET_TAB = 'beforeMakePresetTab'`
���������� ����� ��������� ���� �������. ��������� � ��������� ������ ���������:
* `tab` - ������ \Rover\Fadmin\Tab ����-������� �������
* `presetId`    - id �������

��������� �������� ��������� ����-������� ����� ��������� ���� ��� �������. ���� ���������� `false`, �� ��� �� ���������.  
### `EVENT__AFTER_MAKE_PRESET_TAB = 'afterMakePresetTab'`
���������� ����� �������� ���� �������. ��������� � ��������� ������ ���������:
* `tab` - ������ \Rover\Fadmin\Tab ������� �������

### `EVENT__BEFORE_SHOW_TAB = 'beforeShowTab'`
���������� ����� ������� ���� � ���������������� �����. �������� ��������� `tab`, ���������� ������ \Rover\Fadmin\Tab ���������� ����. ���� ���������� `false`, �� ��� �� ������������.  
### `EVENT__AFTER_GET_TABS = 'afterGetTabs'`
���������� ����� ��������� ������� ���� ������������ �����. �������� ��������� `tabs`, ���������� ������ �������� \Rover\Fadmin\Tab ������������ �����.  
### `EVENT__BEFORE_GET_TAB_INFO = 'beforeGetTabInfo'`
���������� ����� �������� ���������� ��� ������� ����. ��������� � ��������� ������ ���������:
* `name` - ��� ���� � �������
* `label` - �������� ������� ����
* `icon` - ������ ����
* `description` - �������� ����

## ����
### `protected $moduleId`
������������� �������� ������
### `protected \Rover\Fadmin\Engine\TabMap $tabMap`  
������ ��������� �����
### `protected \Rover\Fadmin\Engine\Message $message` 
������ ��������� ���������������� ���������
### `protected $cache = array()`
��� �������� ����� ������
### `protected \Rover\Fadmin\Engine\Settings $settings`
������ ��������� �������� fadmin
### `protected static $instances = array()`
�������� Fadmin-� ��� ������� �� ������������ �������
## ������
### `public static getInstance($moduleId)`
���������� ������ `\Rover\Fadmin\Options` ��� ������ � ��������������� `$moduleId`
### `protected __construct($moduleId)`
������� ������ `\Rover\Fadmin\Options` ��� ������ � ��������������� `$moduleId`
### `public runEvent($name, &$params = array())` 
��������� ���������� ������� `\Rover\Fadmin\Options`.
* `$name` - ��� �������
* `$params` - ���������, ������������ � �������

### `public getPresetsCount($siteId = '')`
���������� ���-�� ������������ ��������. ���� ��� ������ ��������� ���������������, �� �� `$siteId` - ���-�� �������� ��� ������� �����.
### `public getAllTabsInfo()` 
���������� ���������� (��� � �������, ��� �������, ��������, ������) ��� ���� ������������ �����
### `abstract public getConfig()`
�����, ������������ ������������. ������ ���� ������������� � ������ �������.
### `public getModuleId()`
���������� ������������� ������, ��� �������� ������ ������ `\Rover\Fadmin\Options`
### `public static getFullName($name, $presetId = '', $siteId = '')`
���������� ������ ���������� ��� ��������, ���������� ����� ������� � ���� (���� �������)
### `public getPresetValue($inputName, $presetId, $siteId = '', $reload = false)`
���������� �������� ����� �������