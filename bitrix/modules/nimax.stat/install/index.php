<?
// Подключаем модуль (выполняем код в файле include.php)
CModule::IncludeModule('nimax.stat');

// Подключаем языковые константы
IncludeModuleLangFile(__FILE__);

class nimax_stat extends CModule {

    var $MODULE_ID = "nimax.stat";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    /**
     * Инициализация модуля для страницы "Управление модулями"
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
     * Устанавливаем модуль
     */
    public function DoInstall() {
        if( !$this->InstallDB() || !$this->InstallEvents() || !$this->InstallFiles() ) {
            return;
        }

        RegisterModule( $this->MODULE_ID );
    }

    /**
     * Удаляем модуль
     */
    public function DoUninstall() {
        if( !$this->UnInstallDB() || !$this->UnInstallEvents() || !$this->UnInstallFiles() ) {
            return;
        }
        try{
            // Удаление счетчиков и настроек
            $NSO = new Nimax_Stat_Option();
            $NSO->deleteOption();
        }
        catch(Exception $e){
            return;
        }
        UnRegisterModule( $this->MODULE_ID );
    }

    /**
     * Добавляем почтовые события
     *
     * @return bool
     */
    public function InstallEvents() {
        return true;
    }

    /**
     * Удаляем почтовые события
     *
     * @return bool
     */
    public function UnInstallEvents() {
        return true;
    }

    /**
     * Копируем файлы административной части
     *
     * @return bool
     */
    public function InstallFiles() {
        return true;
    }

    /**
     * Удаляем файлы административной части
     *
     * @return bool
     */
    public function UnInstallFiles() {
        return true;
    }

    /**
     * Добавляем таблицы в БД
     *
     * @return bool
     */
    public function InstallDB() {
        return true;
    }

    /**
     * Удаляем таблицы из БД
     *
     * @return bool
     */
    public function UnInstallDB() {
        return true;
    }
}