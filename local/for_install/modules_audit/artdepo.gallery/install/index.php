<?
IncludeModuleLangFile(__FILE__);

include ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/artdepo.gallery/classes/general/artdepo_gallery.php");

if (class_exists('artdepo_gallery')) return;

Class artdepo_gallery extends CModule
{
    var $MODULE_ID = "artdepo.gallery";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    
    var $errors;
    var $parent_section_id;

    function artdepo_gallery()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

		$this->MODULE_NAME = GetMessage("ADG_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("ADG_MODULE_DESC");
    
		$this->PARTNER_NAME = GetMessage("ADG_PARTNER_NAME");
		$this->PARTNER_URI = "http://www.artdepo.com.ua/";
    }

	function InstallDB($arParams = array())
	{
	    global $APPLICATION;
	    $this->errors = false;
	    
        // Create top root collection "module_gallery_multiupload"
        $root_collection_id = CArtDepoGallerySection::GetRootCollectionID();
        if (!$root_collection_id)
            $root_collection_id = CArtDepoGallerySection::CreateRootCollection();
        if (!$root_collection_id)
        {
            $this->errors = array(GetMessage("ADG_MODULE_ERROR_NO_ROOT_COLLECTION"));
            $APPLICATION->ThrowException(implode("<br>", $this->errors));
            return false;
        }
        
	    $install_demo_gallery = ($arParams["NEED_DEMO"] == "Y") ? true : false;
	    if ($install_demo_gallery)
	    {
	        $section_id = CMedialib::EditCollection(array(
                'id' => '',
                'name' => "Demo gallery section",
                'desc' => '',
                'keywords' => '',
                'parent' => $root_collection_id,
                'site' => LANGUAGE_ID,
                'type' => 1
            ));
            
            if (!$section_id)
	        {
	            $this->errors = array(GetMessage("ADG_MODULE_ERROR_NO_ROOT_COLLECTION"));
	            $APPLICATION->ThrowException(implode("<br>", $this->errors));
	            return false;
            }
            
            $this->parent_section_id = $section_id;
            
	        $src_top_dir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/upload";
	        foreach (scandir($src_top_dir) as $album_name) 
	        {
	            $album_dir = $src_top_dir."/".$album_name;
	            if ($album_name == "." || $album_name == ".." || !is_dir($album_dir))
	                continue;
                
                $this->CreateMedialibAlbum($album_name, $album_dir, $section_id);
	        }
	    }
	    RegisterModule($this->MODULE_ID);
        return true;
	}

	function UnInstallDB($arParams = array())
	{
	    UnRegisterModule($this->MODULE_ID);
		return true;
	}

	function InstallEvents()
	{
		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles($arParams = array())
	{
	    $module_inst_dir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install";
	    if($_ENV["COMPUTERNAME"]!='BX')
		{
	        CopyDirFiles($module_inst_dir."/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
	        CopyDirFiles($module_inst_dir."/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
	        CopyDirFiles($module_inst_dir."/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
	        CopyDirFiles($module_inst_dir."/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
        }
        // public
	    if(array_key_exists("public_dir", $arParams) && strlen($arParams["public_dir"]))
	    {
	        $bReWriteAdditionalFiles = ($arParams["public_rewrite"] == "Y");
			$rsSite = CSite::GetList(($by="sort"),($order="asc"));
			while ($site = $rsSite->Fetch())
			{
				$source = $module_inst_dir."/public/gallery/";
				$target = $site['ABS_DOC_ROOT'].$site["DIR"].$arParams["public_dir"]."/gallery/";
				if(file_exists($source))
				{
					CheckDirPath($target);
					$arFiles = array(
					    array("T" => "", "F" => "index.php"),
				        array("T" => "album/", "F" => "album/index.php"),
				    );
				    foreach($arFiles as $item)
				    {
				        $file = $item["F"];
			            CheckDirPath($target.$item["T"]);
				        if($bReWriteAdditionalFiles || !file_exists($target.$file))
				        {
				            $fh = fopen($source.$file, "rb");
				            $php_source = fread($fh, filesize($source.$file));
				            fclose($fh);
				            if(preg_match_all('/GetMessage\("(.*?)"\)/', $php_source, $matches))
						    {
							    IncludeModuleLangFile($source.$file, $site["LANGUAGE_ID"]);
							    foreach($matches[0] as $i => $text)
							    {
								    $php_source = str_replace(
									    $text,
									    '"'.GetMessage($matches[1][$i]).'"',
									    $php_source
								    );
							    }
						    }
						    $php_source = str_replace(
							    '"SECTION_ID" => "2",',
							    '"SECTION_ID" => "'.$this->parent_section_id.'",',
							    $php_source,
							    $cnt = 1
						    );
						    $fh = fopen($target.$file, "wb");
						    fwrite($fh, $php_source);
						    fclose($fh);
					    }
				    } // .foreach
				} // .if
			} // .while
	    }
		return true;
	}

	function UnInstallFiles()
	{
	    $module_inst_dir = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install";
		if($_ENV["COMPUTERNAME"]!='BX')
		{
			DeleteDirFiles($module_inst_dir."/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
			DeleteDirFilesEx("/bitrix/components/artdepo/gallery.album.list/");
			DeleteDirFilesEx("/bitrix/components/artdepo/gallery.photo.list/");
			DeleteDirFilesEx("/bitrix/js/".$this->MODULE_ID."/");
			DeleteDirFilesEx("/bitrix/themes/.default/icons/".$this->MODULE_ID."/");
			unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default/".$this->MODULE_ID.".css");
		}
		return true;
	}

	function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION, $step;
		$step = IntVal($step);
		if($step < 2)
		{
			$APPLICATION->IncludeAdminFile(GetMessage("ADG_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step1.php");
		}
		elseif($step == 2)
		{
			$db_install_ok = $this->InstallDB(array(
				"NEED_DEMO" => $_REQUEST["install_demo_gallery"],
			));
			if($db_install_ok)
			{
				$this->InstallEvents();
				$this->InstallFiles(array(
					"public_dir" => $_REQUEST["public_dir"],
					"public_rewrite" => $_REQUEST["public_rewrite"],
				));
			}
			$GLOBALS["errors"] = $this->errors;
			$APPLICATION->IncludeAdminFile(GetMessage("ADG_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/step2.php");
		}
	}

	function DoUninstall()
	{
	    UnRegisterModule($this->MODULE_ID);
	    $this->UnInstallDB();
	    $this->UnInstallFiles();
	}
	
	
	function CreateMedialibAlbum($album_name, $album_dir, $parent_id)
	{
	    $collection_id = CMedialib::EditCollection(array(
            'id' => '',
            'name' => $album_name,
            'desc' => '',
            'keywords' => '',
            'parent' => $parent_id,
            'site' => LANGUAGE_ID,
            'type' => 1
        ));
        if ($collection_id) foreach (scandir($album_dir) as $file_name)
        {
            $file_path = $album_dir . "/" . $file_name;
            if ($file_name == "." || $file_name == ".." || !is_file($file_path))
                continue;
            $file_path_rel = str_replace($_SERVER["DOCUMENT_ROOT"], "", $file_path, $cnt = 1);

            CMedialib::EditItem(array(
	            'lang' => LANGUAGE_ID,
	            'site' => false,
	            'id' => '',
	            'file' => '',
	            'path' => $file_path_rel,
	            'path_site' => '',
	            'source_type' => 'FD',
	            'name' => $file_name,
	            'desc' => '',
	            'keywords' => '',
	            'item_collections' => $collection_id
            ));
        }
        
        return true;
	}
}
?>
