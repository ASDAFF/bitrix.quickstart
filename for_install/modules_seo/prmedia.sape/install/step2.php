<?
IncludeModuleLangFile(__FILE__);
$errors = array();

if(!$_FILES['file']['name']) $errors[] = 'You didnt load file';

$sape_id = substr($_FILES['file']['name'], 0, 32);

copy($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/sape.zip');

if(unzip($_SERVER['DOCUMENT_ROOT'].'/sape.zip', $_SERVER['DOCUMENT_ROOT'].'/'.$sape_id))
{
	unlink($_SERVER['DOCUMENT_ROOT'].'/sape.zip');
	unlink($_SERVER['DOCUMENT_ROOT'].'/'.$sape_id);
	unlink($_SERVER['DOCUMENT_ROOT'].'/'.$sape_id.$sape_id.'.php');
	rename($_SERVER['DOCUMENT_ROOT'].'/'.$sape_id.$sape_id, $_SERVER['DOCUMENT_ROOT'].'/'.$sape_id);
	chmod($_SERVER['DOCUMENT_ROOT'].'/'.$sape_id.'/sape.php', 0644);
	chmod($_SERVER['DOCUMENT_ROOT'].'/'.$sape_id, 0777);
}
else
{
	$errors[] = 'Cannot unzip arhive';	
}


if(count($errors) == 0)
{
	if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/prmedia')) mkdir($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/prmedia');
	
	CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/prmedia.sape/install/components/prmedia", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/prmedia", true, true);
	
	RegisterModule("prmedia.sape");
	COption::SetOptionString('prmedia.sape', 'sape_id', $sape_id);
	unlink($_SERVER['DOCUMENT_ROOT'].'/bitrix/themes/.default/images/prmedia.sape_screen.jpg');
	echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
	
}
else
{
	$mes = '';
	foreach($errors as $strError) $mes = $mes.$strError.'<br />';
	echo CAdminMessage::ShowMessage($mes);
}


function unzip($file,$dir)
{
    if(!file_exists($dir)) mkdir($dir,0777);
	
    $zip_handle = zip_open($file);
    
    if (is_resource($zip_handle)) 
	{
        while($zip_entry = zip_read($zip_handle))
		{
            if ($zip_entry) 
			{
                $zip_name=zip_entry_name($zip_entry);
                $zip_size=zip_entry_filesize($zip_entry);
				
                if(($zip_size==0)&&($zip_name[strlen($zip_name)-1]=='/'))
				{
                    mkdir($dir.$zip_name, 0777);
                }
                else
				{
                    zip_entry_open($zip_handle, $zip_entry, 'r');
                    $fp=fopen($dir.$zip_name,'wb+');
                    fwrite($fp,zip_entry_read($zip_entry, $zip_size),$zip_size);
                    fclose($fp);
                    chmod($dir.$zip_name,0777);
                    zip_entry_close($zip_entry);
                }
            }
        }
        return true;
    }
    else{
    	zip_close($zip_handle);
        return false;
    }
}

?>

<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANG?>">
	<input type="submit" name="" value="<?=GetMessage("MOD_BACK")?>">
<form>