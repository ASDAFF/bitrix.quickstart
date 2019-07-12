<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!defined("WIZARD_SITE_DIR"))
    return;

$wizard =& $this->GetWizard();
$installDemoData = $wizard->GetVar('installDemoData');
$install_news = $wizard->GetVar('install_news');
$install_pages = $wizard->GetVar('install_pages');
$tableExist = $wizard->GetVar('tableExist');

if (CModule::IncludeModule('webdoka.smartrealt') && ($installDemoData == 'Y'))
{
    if ($tableExist!='Y')
    {
        $DEMO_LICENSE_KEY = '955AA5E0-9F9A-B907-CB21-21607EF525A9';
        
        $oCatalogElement = new SmartRealt_CatalogElement();
        $oCatalogElementPhoto = new SmartRealt_CatalogElementPhoto(); 

        //Сохраним текущий лицензионный ключ
        if (empty($_SESSION['SMARTREALT_WIZARD']['CURRENT_LICENSE_KEY']))
            $_SESSION['SMARTREALT_WIZARD']['CURRENT_LICENSE_KEY'] = COption::GetOptionString('webdoka.smartrealt', 'TOKEN', '');
        $CURRENT_LICENSE_KEY = $_SESSION['SMARTREALT_WIZARD']['CURRENT_LICENSE_KEY'];
        
        //Отключим агенты
        if (!isset($_SESSION['SMARTREALT_WIZARD']['bDisableModuleAgents']))
        {
            $_SESSION['SMARTREALT_WIZARD']['bDisableModuleAgents'] = true;
            
            SmartRealt_Common::DisableModuleAgents();
        }

        //Проверим нужно ли использовать демо-ключ
        if (empty($_SESSION['SMARTREALT_WIZARD']['USE_DEMO_LICENSE_KEY']))
        {
            $_SESSION['SMARTREALT_WIZARD']['USE_DEMO_LICENSE_KEY'] = true; 
            if (!empty($CURRENT_LICENSE_KEY))
            {
                try
                {
                    $oWebService = SmartRealt_WebService::GetInstance();
                    $iObjectsUpdateCount = $oWebService->GetObjectsCount();
                    $_SESSION['SMARTREALT_WIZARD']['USE_DEMO_LICENSE_KEY'] = $iObjectsUpdateCount == 0;
                }
                catch (Exception $e) { }    
            } 
        }
        $bUseDemoKey = $_SESSION['SMARTREALT_WIZARD']['USE_DEMO_LICENSE_KEY'];
        
        //Установим демо-ключ
        if ($bUseDemoKey)
        {
            COption::SetOptionString('webdoka.smartrealt', 'TOKEN', $DEMO_LICENSE_KEY); 
            SmartRealt_Options::Clear(); //очищаем кеш настроек чтобы применился новый ключ
            SmartRealt_WebService::Clear();
        }
        
        switch ($sAction)
        {
            //Загрузка объектов
            case 'OBJECTS':
                    $offset = 0;
                    $step = 20;
                    $result = $step;
                    while ($result == $step)
                    {
                        $result = $oCatalogElement->LoadFromWebservice('', $offset, $step, false);
                        $offset += $result;
                    }
                break; 
            //Загрузка фотографий
            case 'PHOTOS':
                    if ($iStep == 1)
                        unset($_SESSION['SMARTREALT_WIZARD']['iPhotosUpdateCount']);
                    
                    if (strlen($_SESSION['SMARTREALT_WIZARD']['iPhotosUpdateCount']) == 0)
                    { 
                        $oWebService = SmartRealt_WebService::GetInstance();
                        $_SESSION['SMARTREALT_WIZARD']['iPhotosUpdateCount'] = $oWebService->GetPhotosCount("");
                    }
                    $iPhotosUpdateCount = $_SESSION['SMARTREALT_WIZARD']['iPhotosUpdateCount'];
                    
                    $iPhotosDBCount = $oCatalogElementPhoto->GetCount();
                    $iStepPhotoCount = ceil($iPhotosUpdateCount/10);
                    $offset = $iPhotosDBCount;
                    $result = 1;
                    while ($offset < $iPhotosDBCount + $iStepPhotoCount && $offset < $iPhotosUpdateCount && $result > 0)
                    {
                        $result = $oCatalogElementPhoto->LoadFromWebservice('', $offset, $iStepPhotoCount, false);
                        $offset += $result;
                    }
                break;    
        }
        
        if ($iStep == 10)
        {
            // возвращаем ключ
            if ($bUseDemoKey)
            {
                COption::SetOptionString('webdoka.smartrealt', 'TOKEN', $CURRENT_LICENSE_KEY);
            }    
            
            //активируем агентов
            if ($_SESSION['SMARTREALT_WIZARD']['bDisableModuleAgents'] == true)
            {
                SmartRealt_Common::DisableModuleAgents(false);
            }
            
            unset($_SESSION['SMARTREALT_WIZARD']);
        }    
    } 
}
?>
