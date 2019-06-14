<?
class GFloodWizardTools {

	function GetFolderList($parent_folder) {
	
		$arFileList = scandir($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_DATA_DIR.$parent_folder);
		$arFolders = Array();
		if(is_array($arFileList))
			foreach($arFileList as $file) 
				if(!in_array($file,Array(".","..")) && is_dir($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_DATA_DIR.$parent_folder."/".$file))
					$arFolders[] = $file;
		return $arFolders;
	
	}

	function GetFileList($parent_folder) {
	
		$arFileList = scandir($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_DATA_DIR.$parent_folder);
		$arFiles = Array();
		if(is_array($arFileList))
			foreach($arFileList as $file) 
				if(!in_array($file,Array(".","..")) && is_file($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_DATA_DIR.$parent_folder."/".$file))
					$arFiles[] = $file;
		return $arFiles;
	
	}

	function GetRandomFile($type_folder,$arFolders) {

		$arFiles = Array();	
		foreach($arFolders as $folder) {
			$arFileList = scandir($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_DATA_DIR.$type_folder."/".$folder);
			if(is_array($arFileList))
				foreach($arFileList as $file) 
					if(!in_array($file,Array(".","..")) && is_file($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_DATA_DIR.$type_folder."/".$folder."/".$file))
						$arFiles[] = WIZ_IBF_DATA_DIR.$type_folder."/".$folder."/".$file;
		}
					
		if(count($arFiles)>0) return $arFiles[rand(0,(count($arFiles)-1))];
		else return false;
	
	}

	function GetRandomImage($arFolders) {

		$max_iterations = 10;
		
		do {
			$max_iterations--;
			$random_image_file = GFloodWizardTools::GetRandomFile("images",$arFolders);
			if($random_image_file===false) return false;
		} while (!CFile::IsImage($random_image_file) && $max_iterations>=0);
		
		return $random_image_file;
		
	}
	
	
	function GetRandomParagraph($file,$clause_count=1,$bRenew=false) {
		
		static $arParagraphs;
		if(!is_array($arParagraphs) || $bRenew)
			$arParagraphs = array();			
		if(strlen($file)<=0)
			return;
			
		$filename=$_SERVER["DOCUMENT_ROOT"].WIZ_IBF_DATA_DIR."texts/".$file.".php";			
			
		$paragraph = "";
		if(array_key_exists($file, $arParagraphs)) {
			$paragraph = $arParagraphs[$file];
		} else { 
			if(!is_file($filename))
				return "";
			$paragraphs = false;
			require($filename);
			$paragraph = $paragraphs[rand(0,(count($paragraphs)-1))];
			$arParagraphs[$file] = $paragraph;
		}
		$phrases = preg_split("/\.+/",preg_replace("/[\.]+$/","",$paragraph));
		if($clause_count==1)
			return $phrases[0];
		$new_paragraph = "";
		$count = 0;
		foreach($phrases as $phrase) {
			if(!!$new_paragraph)
				$new_paragraph .= " ";
			$new_paragraph .= $phrase.".";
			$count++;
			if($count>=$clause_count)
				break;
		}
		return $new_paragraph;
	}
	
	function StoreVars($arVars) {
	
		if(!is_array($arVars)) $arVars = Array();
	
		$vars_data = "<?\n\$VARS = ".var_export($arVars,true).";\n?>";		
	
		$handle = fopen($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_VARS_DIR."vars.php", "w");
		fwrite($handle, $vars_data);
		fclose($handle);
	
	}
	
	function GetVars() {
	
		$VARS = Array();
	
		$handle = fopen($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_VARS_DIR."vars.php", "r");
		$vars_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_VARS_DIR."vars.php"));
		fclose($handle);
		
		ob_start();
		eval("?>".$vars_data."<?");
		$err = ob_get_contents();
		ob_end_clean();

		return $VARS;
	
	}
	
	function AddErrorToLog($str_error) {
	
		if(strlen($str_error)<=0) return;
	
		if(!is_array($_SESSION["WIZ_IBF_ERROR_LOG"])) $_SESSION["WIZ_IBF_ERROR_LOG"] = Array();
		
		$found_key = false;
		foreach($_SESSION["WIZ_IBF_ERROR_LOG"] as $k=>$arError) 
			if($arError["ERROR"]==$str_error)
				$found_key = $k;

		if($found_key===false) {
			$_SESSION["WIZ_IBF_ERROR_LOG"][] = Array(
				"ERROR"=>$str_error,
				"COUNT"=>1,
			);
		} else {
			$_SESSION["WIZ_IBF_ERROR_LOG"][$found_key]["COUNT"]++;
		}
	
	}

	function GetErrorLog() {

		if(is_array($_SESSION["WIZ_IBF_ERROR_LOG"])) return $_SESSION["WIZ_IBF_ERROR_LOG"];
		else return Array();
	
	}

	function StoreParentSections($arCurParentSections) {
	
		if($arCurParentSections===false) {
			$arCurParentSections = Array();
			for($i=1;$i<=4;$i++) $arCurParentSections[$i] = Array();
		}
	
		$vars_data = "<?\n\$arCurParentSections = ".var_export($arCurParentSections,true).";\n?>";		

		$handle = fopen($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_VARS_DIR."sections.php", "w");
		fwrite($handle,$vars_data);
		fclose($handle);
	
	}

	function GetParentSections(&$arCurParentSections) {
	
		$arCurParentSections = Array();
	
		$handle = fopen($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_VARS_DIR."sections.php", "r");
		$vars_data=fread($handle, filesize($_SERVER["DOCUMENT_ROOT"].WIZ_IBF_VARS_DIR."sections.php"));
		fclose($handle);
		
		ob_start();
		eval("?>".$vars_data."<?");
		$err = ob_get_contents();
		ob_end_clean();

		if(!is_array($arCurParentSections) || count($arCurParentSections)!=4) {
		
			$arCurParentSections = Array();
			for($i=1;$i<=4;$i++) $arCurParentSections[$i] = Array();
			
		}
	
	}

}

?>