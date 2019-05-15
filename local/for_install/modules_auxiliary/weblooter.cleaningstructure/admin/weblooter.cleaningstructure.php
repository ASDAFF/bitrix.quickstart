<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$MODULE_PATH = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/weblooter.cleaningstructure';
$APPLICATION->SetTitle(GetMessage('H1'));

if(isset($_REQUEST['STEP'])){
    $arResult = array();
    $arParams = $_REQUEST;
    switch($_REQUEST['STEP']){
        case '1':
            set_time_limit(0);
            CModule::IncludeModule('main');
            $q=CFile::GetList(array(),array());
            $arCFileList = array();
            while($s=$q->Fetch()){
                $arCFileList[$s['ID']]=CFile::GetPath($s['ID']);
            }

            global $arParams;
            $arParams['DIR_IGNORE'] = explode(';',$arParams['DIR_IGNORE']);
            $arParams['DIR_IGNORE'] = array_diff($arParams['DIR_IGNORE'],array(''));
            $arParams['FILE_IGNORE'] = explode(';',$arParams['FILE_IGNORE']);
            $arParams['FILE_IGNORE'] = array_diff($arParams['FILE_IGNORE'],array(''));

            $arResult['FILE_SIZE']=0;
            $arResult['ALL_FILE_SIZE']=0;
            $arSearchUploads = array();
            function SearchInDir($DirObg){
                $dh = opendir($DirObg);
                while ($file = readdir($dh)) {
                    if( ($file!=='.') && ($file!=='..') ){
                        if(is_file($DirObg.$file)){
                            global $arResult;
                            $arResult['ALL_FILE_SIZE']+=filesize($DirObg.$file);
                            global $arParams;
                            if(!in_array($file,$arParams['FILE_IGNORE'])){
                                global $arSearchUploads;
                                $arSearchUploads[]=str_replace($_SERVER['DOCUMENT_ROOT'],'',$DirObg.$file);
                            }

                        }elseif(is_dir($DirObg.$file.'/')){
                            global $arParams;
                            if(!in_array($file,$arParams['DIR_IGNORE'])){
                                SearchInDir($DirObg.$file.'/');
                            }
                        }
                    }
                }
            }
            SearchInDir($_SERVER['DOCUMENT_ROOT'].$arParams['UPLOAD_DIRECTORY']);
            $arResult['FILES_LIST'] = array_diff($arSearchUploads,$arCFileList);
            foreach($arResult['FILES_LIST'] as $TmpFile){
                $arResult['FILE_SIZE']+=filesize($_SERVER['DOCUMENT_ROOT'].$TmpFile);
            }
            $arResult['FILE_SIZE'] = round( (($arResult['FILE_SIZE']/1024)/1024), 2);
            $arResult['ALL_FILE_SIZE'] = round( (($arResult['ALL_FILE_SIZE']/1024)/1024), 2);
            break;
        case '2':
            $arFilesList = explode(';',$_REQUEST['FILES_LIST']);
            foreach($arFilesList as $File){
                unlink($_SERVER['DOCUMENT_ROOT'].$File);
            }
            break;
    }
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
    <div class="adm-detail-block">
        <div class="adm-detail-tabs-block">
        <span class="adm-detail-tab <?=(!isset($_REQUEST['STEP']))?'adm-detail-tab-active':'" onclick="location.href=\''.$APPLICATION->GetCurPageParam('',array('STEP','FILE_IGNORE','DIR_IGNORE','UPLOAD_DIRECTORY')).'\''?>"><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_NASTROYKA_SKANIROVAN")?></span>
        <span class="adm-detail-tab <?
        if( (isset($_REQUEST['STEP'])) && ($_REQUEST['STEP'] < 1) ){
            echo 'adm-detail-tab-disable';}
        elseif( (isset($_REQUEST['STEP'])) && ($_REQUEST['STEP'] == 1) ){
            echo 'adm-detail-tab-active';}
        elseif( (isset($_REQUEST['STEP'])) && ($_REQUEST['STEP'] > 1) ){
            echo '" onclick="location.href=\''.$APPLICATION->GetCurPageParam('STEP=1',array('STEP','FILE_IGNORE','DIR_IGNORE','UPLOAD_DIRECTORY','FILES_LIST')).'\'';}
        else{echo 'adm-detail-tab-disable';}
            ?>"><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_REZULQTAT_SKANIROVAN")?></span>
            <span class="adm-detail-tab <?
            if( (isset($_REQUEST['STEP'])) && ($_REQUEST['STEP'] < 2) ){
                echo 'adm-detail-tab-disable';}
            elseif( (isset($_REQUEST['STEP'])) && ($_REQUEST['STEP'] == 2) ){
                echo 'adm-detail-tab-active';}
            elseif( (isset($_REQUEST['STEP'])) && ($_REQUEST['STEP'] > 2) ){
                echo '" onclick="location.href=\''.$APPLICATION->GetCurPageParam('STEP=2',array('STEP','FILE_IGNORE','DIR_IGNORE','UPLOAD_DIRECTORY','FILES_LIST')).'\'';}
            else{echo 'adm-detail-tab-disable';}
            ?>"><?=GetMessage("WEBLOOTER_CLEANINGSTRUCTURE_REZULQTAT_CISTKI_SIS")?></span>
        </div>
        <div class="adm-detail-content-wrap">
            <?
            if(!isset($_REQUEST['STEP']) || (empty($_REQUEST['STEP']))){
                require($MODULE_PATH.'/include_steps/step_start.php');
            }elseif(isset($_REQUEST['STEP'])){
                require($MODULE_PATH.'/include_steps/step'.$_REQUEST['STEP'].'.php');
            }
            ?>
        </div>
    </div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>