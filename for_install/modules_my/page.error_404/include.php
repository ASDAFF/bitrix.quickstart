<?
class CErr404{

  function handler404(){

    if(defined('ERROR_404') && ERROR_404 == 'Y' && !defined('ADMIN_SECTION')){

      $template = COption::GetOptionString('page.error_404', 'template_'.SITE_ID, FALSE, SITE_ID);

      if(!$template)
        return;

      global $APPLICATION;
      $APPLICATION->RestartBuffer();

      include $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.$template.'/header.php';
      if(file_exists($_SERVER['DOCUMENT_ROOT'].'/404.php'))
        include $_SERVER['DOCUMENT_ROOT'].'/404.php';
      include $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.$template.'/footer.php';

    }

  }

}
