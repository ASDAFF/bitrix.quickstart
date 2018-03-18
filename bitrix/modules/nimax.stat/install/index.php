<?
// ���������� ������ (��������� ��� � ����� include.php)
CModule::IncludeModule('nimax.stat');

// ���������� �������� ���������
IncludeModuleLangFile(__FILE__);

class nimax_stat extends CModule {

    var $MODULE_ID = "nimax.stat";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    /**
     * ������������� ������ ��� �������� "���������� ��������"
     */
    public function nimax_stat() {

        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->PARTNER_NAME = "Nimax";
        $this->PARTNER_URI = "http://nimax.ru/";

        $this->MODULE_NAME = GetMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('MODULE_DESC');
    }

    /**
     * ������������� ������
     */
    public function DoInstall() {
        if( !$this->InstallDB() || !$this->InstallEvents() || !$this->InstallFiles() ) {
            return;
        }

        RegisterModule( $this->MODULE_ID );
    }

    /**
     * ������� ������
     */
    public function DoUninstall() {
        if( !$this->UnInstallDB() || !$this->UnInstallEvents() || !$this->UnInstallFiles() ) {
            return;
        }
        try{
            // �������� ��������� � ��������
            $NSO = new Nimax_Stat_Option();
            $NSO->deleteOption();
        }
        catch(Exception $e){
            return;
        }
        UnRegisterModule( $this->MODULE_ID );
    }

    /**
     * ��������� �������� �������
     *
     * @return bool
     */
    public function InstallEvents() {
        return true;
    }

    /**
     * ������� �������� �������
     *
     * @return bool
     */
    public function UnInstallEvents() {
        return true;
    }

    /**
     * �������� ����� ���������������� �����
     *
     * @return bool
     */
    public function InstallFiles() {
        return true;
    }

    /**
     * ������� ����� ���������������� �����
     *
     * @return bool
     */
    public function UnInstallFiles() {
        return true;
    }

    /**
     * ��������� ������� � ��
     *
     * @return bool
     */
    public function InstallDB() {
        return true;
    }

    /**
     * ������� ������� �� ��
     *
     * @return bool
     */
    public function UnInstallDB() {
        return true;
    }
}