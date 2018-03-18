<?
IncludeModuleLangFile(__FILE__);
Class CBitrixMpBuilder
{
	function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
	{
		if($GLOBALS['APPLICATION']->GetGroupRight("main") < "R")
			return;

		$MODULE_ID = basename(dirname(__FILE__));
		$aMenu = array(
			//"parent_menu" => "global_menu_services",
			"parent_menu" => "global_menu_settings",
			"section" => $MODULE_ID,
			"sort" => 50,
			"text" => $MODULE_ID,
			"title" => '',
//			"url" => "partner_modules.php?module=".$MODULE_ID,
			"icon" => "",
			"page_icon" => "",
			"items_id" => $MODULE_ID."_items",
			"more_url" => array(),
			"items" => array()
		);

		if (file_exists($path = dirname(__FILE__).'/admin'))
		{
			if ($dir = opendir($path))
			{
				$arFiles = array();

				while(false !== $item = readdir($dir))
				{
					if (in_array($item,array('.','..','menu.php')))
						continue;

					if (!file_exists($file = $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$MODULE_ID.'_'.$item))
						file_put_contents($file,'<'.'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.$MODULE_ID.'/admin/'.$item.'");?'.'>');

					$arFiles[] = $item;
				}

				sort($arFiles);
				$arTitles = array(
					'step1.php' => GetMessage("BITRIX_MPBUILDER_STRUKTURA_MODULA"),
					'step2.php' => GetMessage("BITRIX_MPBUILDER_VYDELENIE_FRAZ"),
					'step3.php' => GetMessage("BITRIX_MPBUILDER_REDAKTOR_KLUCEY"),
					'step4.php' => GetMessage("BITRIX_MPBUILDER_SOZDANIE_ARHIVA"),
					'step5.php' => GetMessage("BITRIX_MPBUILDER_SBORKA_OBNOVLENIY")
				);

				foreach($arFiles as $item)
					$aMenu['items'][] = array(
						'text' => $arTitles[$item],
						'url' => $MODULE_ID.'_'.$item,
						'module_id' => $MODULE_ID,
						"title" => "",
					);
			}
		}
		$aModuleMenu[] = $aMenu;
	}
}

class CBuilderLang
{
	function __construct($m_dir, $file, $lang_file)
	{
		$this->m_dir= $m_dir;
		$this->file = $file;
		if (!$str = file_get_contents($m_dir.$this->file))
			return false;
		if (GetStringCharset($str) == 'utf8')
			$str = $GLOBALS['APPLICATION']->ConvertCharset($str, 'utf8', 'cp1251');
		$this->str = $str;
		$this->lang_file = $lang_file;
		$this->bSiteUTF = defined('BX_UTF') && BX_UTF;

		$this->InPhp = '';
		$this->InHtml = 'InText';
		$this->InJs = '';
		$this->strQuoted = '';
		$this->strResultScript = '';

		if (file_exists($m_dir.$lang_file))
		{
			$str = file_get_contents($m_dir.$lang_file);
			if (GetStringCharset($str) == 'utf8')
			{
				$str = $GLOBALS['APPLICATION']->ConvertCharset($str, 'utf8', 'cp1251');
				file_put_contents($m_dir.$lang_file, $str);
			}
			include($m_dir.$lang_file);
			$this->MESS = $MESS;
		}
		else
		{
			if (!defined('BX_DIR_PERMISSIONS'))
				define('BX_DIR_PERMISSIONS', 0755);
			if (!file_exists($dir = dirname($m_dir.$lang_file)))
				mkdir($dir, BX_DIR_PERMISSIONS, true);
			$this->MESS = array();
		}
	}

	function Parse()
	{
		if (function_exists('mb_orig_strlen'))
			$l = mb_orig_strlen($this->str);
		elseif (function_exists('mb_strlen'))
			$l = mb_strlen($this->str, 'latin1');
		else
			$l = strlen($this->str);

		for($i=0;$i<$l;$i++)
		{
			$this->pos = $i;
			if (function_exists('mb_orig_substr'))
				$c = mb_orig_substr($this->str, $i, 1);
			elseif (function_exists('mb_substr'))
				$c = mb_substr($this->str, $i, 1, 'latin1');
			else
				$c = substr($this->str, $i, 1);

			if ($this->InPhp) // PHP
			{
				if ($Esc)
					$Esc = 0;
				elseif ($this->InPhp == 'InDoubleQuotes' && $c == '"')
				{
					$bSkipNext = $this->EndQuotedString();
					$this->InPhp = 'InCode';
				}
				elseif ($this->InPhp == 'InSingleQuotes' && $c == "'")
				{
					$bSkipNext = $this->EndQuotedString();
					$this->InPhp = 'InCode';
				}
				elseif ($this->InPhp == 'InMultiLineComment')
				{
					if ($prev_c.$c == '*/')
						$this->InPhp = 'InCode';
				}
				elseif (($this->InPhp == 'InCode' || $this->InPhp == 'InLineComment') && $prev_c.$c == '?'.'>')
					$this->InPhp = '';
				elseif ($this->InPhp == 'InLineComment')
				{
					if ($c == "\n")
						$this->InPhp = 'InCode';
				}
				elseif ($this->InPhp == 'InCode')
				{
					if ($c == '#' || $prev_c.$c == '//')
						$this->InPhp = 'InLineComment';
					elseif ($prev_c.$c == '/*')
						$this->InPhp = 'InMultiLineComment';
					elseif ($c == '"')
						$this->InPhp = 'InDoubleQuotes';
					elseif ($c == "'")
						$this->InPhp = 'InSingleQuotes';
				}
				elseif ($this->InPhp == 'InSingleQuotes' || $this->InPhp == 'InDoubleQuotes')
				{
					if ($c == '\\')
						$Esc = 1;
				}
			}
			else // HTML
			{
				if ($prev_c.$c == '<?')
				{
					$this->InPhp = 'InCode';
					$this->strResultScript .= $this->strLowPrefix;
					$this->strLowPrefix = '';
					$this->InHtml = $this->InHtmlLast;
				}
				elseif ($this->InJs) // JavaScript || CSS
				{
					if ($this->InJs == 'InStyle')
					{
						if ($prev_c.$c == '</')
							$this->InJs = '';
					}
					elseif ($this->InJs == 'InLineComment')
					{
						if ($c == "\n")
							$this->InJs = 'InCode';
					}
					elseif ($this->InJs == 'InMultiLineComment')
					{
						if ($prev_c.$c == '*/')
							$this->InJs = 'InCode';
					}
					elseif ($this->InJs == 'InCode')
					{
						if ($prev_c.$c == '</')
							$this->InJs = '';
						elseif ($c == '"')
							$this->InJs = 'InDoubleQuotes';
						elseif ($c == "'")
							$this->InJs = 'InSingleQuotes';
						elseif ($prev_c.$c == '//')
							$this->InJs = 'InLineComment';
						elseif ($prev_c.$c == '/*')
							$this->InJs = 'InMultiLineComment';
					}
					else // InQuotes
					{
						if ($Esc)
							$Esc = 0;
						elseif ($c == '\\')
							$Esc = 1;
						elseif ($this->InJs == 'InSingleQuotes')
						{
							if ($c == "'")
							{
								$this->EndQuotedString();
								$this->InJs = 'InCode';
							}
						}
						elseif ($this->InJs == 'InDoubleQuotes')
						{
							if ($c == '"')
							{
								$this->EndQuotedString();
								$this->InJs = 'InCode';
							}
						}
					}
				}
				else // Pure HTML
				{
					if ($this->InHtml == 'InTagName')
					{
						if ($c == ' ' || $c == "\t" || $c == '>')
						{
							if ($tag == 'script')
							{
								$this->InJs = 'InCode';
								$this->InHtml = 'InText';
							}
							elseif ($tag == 'style')
							{
								$this->InJs = 'InStyle';
								$this->InHtml = 'InText';
							}
							elseif ($c == '>')
								$this->InHtml = 'InText';
							else
								$this->InHtml = 'InTag';
						}
						else
							$tag .= strtolower($c);
					}
					elseif ($this->InHtml == 'InTag' && $c == '>')
						$this->InHtml = 'InText';
					elseif ($this->InHtml == 'InTag' && $c == "'")
						$this->InHtml = 'InSingleQuotes';
					elseif ($this->InHtml == 'InTag' && $c == '"')
						$this->InHtml = 'InDoubleQuotes';
					elseif ($this->InHtml == 'InSingleQuotes' && $c == "'")
					{
						$this->EndQuotedString();
						$this->InHtml = 'InTag';
					}
					elseif ($this->InHtml == 'InDoubleQuotes' && $c == '"')
					{
						$this->EndQuotedString();
						$this->InHtml = 'InTag';
					}
					elseif ($this->InHtml == 'InText' && $c == '<')
					{
						$this->EndQuotedString();
						$this->InHtmlLast = $this->InHtml;
						$this->InHtml = 'InTagName';
						$tag = '';
					}
				}
			}
			$prev_c = $c;

			if (!$bSkipNext && !$this->Collect($c))
				$this->strResultScript .= $c;
			$bSkipNext = 0;
		}
		$this->strResultScript .= $this->strLowPrefix;
	}

	function Collect($c)
	{
		$bCollect = strpos($this->InHtml.$this->InJs.$this->InPhp, 'Quotes') || ($this->InHtml == 'InText' && !$this->InJs && !$this->InPhp);
		if ($bCollect)
		{
			if (($o = ord($c)) > 127)
				$this->bTranslate = 1;
			if ($this->bTranslate)
			{
				if ($c == '<' || $o <= 127 && $this->strLow)
				{
					$this->strLow .= $c;
				}
				else
				{
					$this->strQuoted .= $this->strLow.$c;
					$this->strLow = '';
				}
			}
			else
				$this->strLowPrefix .= $c;
			return true;
		}
		return false;
	}

	function EndQuotedString()
	{
		$bCutRight = strlen($this->strLow);

		if ($strMess = $this->strQuoted.($bCutRight ? '' : $this->strLow))
		{
			$key = $this->GetLangKey($strMess);
			$this->MESS[$key] = $strMess;
			$prefix = '<'.'?=';
			$postfix = '?'.'>';
			if ($this->InPhp)
			{
				$quote = $this->InPhp == 'InSingleQuotes' ? "'" : '"';
				if ($this->strLowPrefix == "'" || $this->strLowPrefix == '"') // delete quotes 
				{
					$prefix = '';
					$this->strLowPrefix = '';
				}
				else
					$prefix = $quote.'.';
				$postfix = $bCutRight ? ".".$quote : "";
			}
			$this->strResultScript .= $this->strLowPrefix.$prefix.'GetMessage'.($this->InJs ? 'JS' : '').'("'.$key.'")'.$postfix.($bCutRight ? $this->strLow : '');

			$this->bTranslate = 0;
			$this->strQuoted = '';
			$this->strLow = '';
			$this->strLowPrefix = '';

			return !$bCutRight; // true => skip next quote
		}

		$this->strResultScript .= $this->strLowPrefix;
		$this->strLowPrefix = '';
	}

	function GetLangKey($strMess)
	{
		if (is_array($this->MESS))
			foreach($this->MESS as $key => $val)
				if ($val == $strMess)
					return $key;

		if (function_exists('mb_orig_substr'))
			$key = mb_orig_substr($strMess,0,20);
		elseif (function_exists('mb_substr'))
			$key = mb_substr($strMess,0,20,'latin1');
		else
			$key = substr($strMess,0,20);

		$key = preg_replace("/[^\xa8\xb8\xc0-\xdf\xe0-\xff]/",' ',$key);
		$key = trim($key);

		$from_u	= GetMessage("BITRIX_MPBUILDER_YCUKENGSSZHQFYVAPROL");
		$to 	= 'YCUKENGSSZHQFYVAPROLDJEACSMITQBUEEYCUKENGSSZHQFYVAPROLDJEACSMITQBU';

		static $from;
		if (!$from)
		{
			if ($this->bSiteUTF)
				$from = $GLOBALS['APPLICATION']->ConvertCharset($from_u, 'utf8', 'cp1251');
			else
				$from = $from_u;
		}

		$key = strtr($key,$from,$to);
		$key = preg_replace('/ +/','_',$key);
		$new_key = $this->strLangPrefix.$key;

		while($this->MESS[$new_key] && $this->MESS[$new_key] != $strMess)
			$new_key = $this->strLangPrefix.$key.(++$i);

		return $new_key;
	}

	function Save()
	{
		$str = "<"."?\n";
		foreach($this->MESS as $key=>$val)
			$str .= '$MESS["'.$key.'"] = "'.str_replace('"','\\"',str_replace('\\','\\\\',$val)).'";'."\n";
		$str .= "?".">";

		if ($this->bSiteUTF)
			$str = $GLOBALS['APPLICATION']->ConvertCharset($str, 'cp1251', 'utf8');

		if (!file_put_contents($this->m_dir.$this->lang_file, $str))
			return false;

		$prefix = '';
		if (preg_match('#^/admin#', $this->file) && !preg_match('/(require|include).+prolog_admin/', $this->strResultScript))
			$prefix = '<'.'?php'."\n".
			'require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");'."\n".
			'IncludeModuleLangFile(__FILE__);'."\n".
			'?'.'>';

		if ($this->bSiteUTF)
			$this->strResultScript = $GLOBALS['APPLICATION']->ConvertCharset($this->strResultScript, 'cp1251', 'utf8');

		if (!file_put_contents($this->m_dir.$this->file, $prefix.$this->strResultScript))
			return false;

		return true;
	}
}

class CTarBuilder
{
	var $gzip;
	var $file;
	var $err = array();
	var $res;
	var $Block = 0;
	var $BlockHeader;
	var $path;
	var $FileCount = 0;
	var $DirCount = 0;
	var $ReadBlockMax = 2000;
	var $ReadBlockCurrent = 0;
	var $header = null;
	var $ArchiveSizeMax;
	var $lang = '';
	const BX_EXTRA = 'BX0000';


	##############
	# WRITE 
	# {
	function openWrite($file)
	{
		if (!isset($this->gzip) && (substr($file,-3)=='.gz' || substr($file,-4)=='.tgz'))
			$this->gzip = true;

		if ($this->ArchiveSizeMax > 0)
		{
			while(file_exists($file1 = $this->getNextName($file)))
				$file = $file1;

			$size = 0;
			if (($size = $this->getArchiveSize($file)) >= $this->ArchiveSizeMax)
			{
				$file = $file1;
				$size = 0;
			}
			$this->ArchiveSizeCurrent = $size;
		}
		return $this->open($file, 'a');
	}

	function createEmptyGzipExtra($file)
	{
		if (file_exists($file))
			return false;

		if (!($f = gzopen($file,'wb')))
			return false;
		gzwrite($f,'');
		gzclose($f);

		$data = file_get_contents($file);

		if (!($f = fopen($file, 'w')))
			return false;

		$ar = unpack('A3bin0/A1FLG/A6bin1',substr($data,0,10));
		if ($ar['FLG'] != 0)
			return $this->Error('Error writing extra field: already exists');

		$EXTRA = chr(0).chr(0).chr(strlen(self::BX_EXTRA)).chr(0).self::BX_EXTRA;
		fwrite($f,$ar['bin0'].chr(4).$ar['bin1'].chr(strlen($EXTRA)).chr(0).$EXTRA.substr($data,10));
		fclose($f);
		return true;
	}

	function writeBlock($str)
	{
		$l = strlen($str);
		if ($l!=512)
			return $this->Error('TAR_WRONG_BLOCK_SIZE'.$l);

		if ($this->ArchiveSizeMax && $this->ArchiveSizeCurrent >= $this->ArchiveSizeMax)
		{
			$file = $this->getNextName();
			$this->close();

			if (!$this->open($file,$this->mode))
				return false;

			$this->ArchiveSizeCurrent = 0;
		}

		if ($res = $this->gzip ? gzwrite($this->res, $str) : fwrite($this->res,$str))
		{
			$this->Block++;
			$this->ArchiveSizeCurrent+=512;
		}

		return $res;
	}

	function writeHeader($ar)
	{
		$header0 = pack("a100a8a8a8a12a12", $ar['filename'], decoct($ar['mode']), decoct($ar['uid']), decoct($ar['gid']), decoct($ar['size']), decoct($ar['mtime']));
		$header1 = pack("a1a100a6a2a32a32a8a8a155", $ar['type'],'','','','','','', '', $ar['prefix']);

		$checksum = pack("a8",decoct($this->checksum($header0.'        '.$header1)));
		$header = pack("a512", $header0.$checksum.$header1);
		return $this->writeBlock($header) || $this->Error('TAR_ERR_WRITE_HEADER');
	}

	function addFile($f)
	{
		$f = str_replace('\\', '/', $f);
		$path = substr($f,strlen($this->path) + 1);
		if ($path == '')
			return true;
		if (strlen($path)>512)
			return $this->Error('TAR_PATH_TOO_LONG',htmlspecialchars($path));

		$ar = array();

		if (is_dir($f))
		{
			$ar['type'] = 5;
			$path .= '/';
		}
		else
			$ar['type'] = 0;

		$info = stat($f);
		if ($info)
		{
			if ($this->ReadBlockCurrent == 0) // read from start
			{
				$ar['mode'] = 0777 & $info['mode'];
				$ar['uid'] = $info['uid'];
				$ar['gid'] = $info['gid'];
				$ar['size'] = $ar['type']==5 ? 0 : $info['size'];
				$ar['mtime'] = $info['mtime'];


				if (strlen($path)>100) // Long header
				{
					$ar0 = $ar;
					$ar0['type'] = 'L';
					$ar0['filename'] = '././@LongLink';
					$ar0['size'] = strlen($path);
					if (!$this->writeHeader($ar0))
						return false;
					$path .= str_repeat(chr(0),512 - strlen($path));

					if (!$this->writeBlock($path))
						return false;
					$ar['filename'] = substr($path,0,100);
				}
				else
					$ar['filename'] = $path;

				if (!$this->writeHeader($ar))
					return false;
			}

			if ($ar['type']==0 && $info['size']>0) // File
			{
				if (!($rs = fopen($f, 'rb')))
					return $this->Error('TAR_ERR_FILE_READ',htmlspecialchars($f));

				if ($this->ReadBlockCurrent)
					fseek($rs, $this->ReadBlockCurrent * 512);

				$i = 0;
				while(!feof($rs) && ('' !== $str = fread($rs,512)))
				{
					$this->ReadBlockCurrent++;
					if (feof($rs) && ($l = strlen($str)) && $l < 512)
						$str .= str_repeat(chr(0),512 - $l);

					if (!$this->writeBlock($str))
					{
						fclose($rs);
						return $this->Error('TAR_ERR_FILE_WRITE',htmlspecialchars($f));
					}

					if ($this->ReadBlockMax && ++$i >= $this->ReadBlockMax)
					{
						fclose($rs);
						return true;
					}
				}
				fclose($rs);
				$this->ReadBlockCurrent = 0;
			}
			return true;
		}
		else
			return $this->Error('TAR_ERR_FILE_NO_ACCESS',htmlspecialchars($f));
	}

	# }
	##############

	##############
	# BASE 
	# {
	function open($file, $mode='r')
	{
		$this->file = $file;
		$this->mode = $mode;

		if ($this->gzip) 
		{
			if(!function_exists('gzopen'))
				return $this->Error('TAR_NO_GZIP');
			else
			{
				if ($mode == 'a' && !file_exists($file) && !$this->createEmptyGzipExtra($file))
					return false;
				$this->res = gzopen($file,$mode."b");
			}
		}
		else
			$this->res = fopen($file,$mode."b");

		return $this->res;
	}

	function close()
	{
		if ($this->gzip)
		{
			gzclose($this->res);

			if ($this->mode == 'a')
			{
				$f = fopen($this->file, 'rb+');
#				fseek($f, -4, SEEK_END);
				fseek($f, 18);
				fwrite($f, pack("V", $this->ArchiveSizeCurrent));
				fclose($f);
			}
		}
		else
			fclose($this->res);
	}

	function getNextName($file = '')
	{
		if (!$file)
			$file = $this->file;
		static $CACHE;
		$c = &$CACHE[$file];

		if (!$c)
		{
			$l = strrpos($file, '.');
			$num = substr($file,$l+1);
			if (is_numeric($num))
				$file = substr($file,0,$l+1).++$num;
			else
				$file .= '.1';
			$c = $file;
		}
		return $c;
	}

	function checksum($str)
	{
		static $CACHE;
		$checksum = &$CACHE[md5($str)];
		if (!$checksum)
		{
//			$str = pack("a512",$str);
			for ($i = 0; $i < 512; $i++)
				if ($i>=148 && $i<156)
					$checksum += 32; // ord(' ')
				else
					$checksum += ord($str[$i]);
		}
		return $checksum;
	}

	function getArchiveSize($file = '')
	{
		if (!$file)
			$file = $this->file;
		static $CACHE;
		$size = &$CACHE[$file];

		if (!$size)
		{
			if (!file_exists($file))
				$size = 0;
			else
			{
				if ($this->gzip)
				{
					$f = fopen($file, "rb");
		#			fseek($f, -4, SEEK_END);
					fseek($f, 18);
					$size = end(unpack("V", fread($f, 4)));
					fclose($f);
				}
				else
					$size = filesize($file);
			}
		}
		return $size;
	}

	function Error($err_code, $str = '')
	{
//		echo '<pre>';debug_print_backtrace();echo '</pre>';
//		echo '<pre>';print_r($this);echo '</pre>';

		$this->err[] = self::GetMessage($err_code).' '.$str;
		return false;
	}

	function xmkdir($dir)
	{
		if (!file_exists($dir))
		{
			$upper_dir = dirname($dir);
			if (!file_exists($upper_dir) && !self::xmkdir($upper_dir))
				return false;

			return mkdir($dir);
		}

		return is_dir($dir);
	}

	function GetMessage($code)
	{
		static $arLang;

		if (!$arLang)
		{
			$arLang = array(
				'TAR_WRONG_BLOCK_SIZE' => 'Wrong block size: ',
				'TAR_ERR_FORMAT' => 'Archive is corrupted, wrong block: ',
				'TAR_EMPTY_FILE' => 'Filename is empty, wrong block: ',
				'TAR_ERR_CRC' => 'Checksum error on file: ',
				'TAR_ERR_FOLDER_CREATE' => 'Can\'t create folder: ',
				'TAR_ERR_FILE_CREATE' => 'Can\'t create file: ',
				'TAR_ERR_FILE_OPEN' => 'Can\'t open file: ',
				'TAR_ERR_FILE_SIZE' => 'File size is wrong: ',
				'TAR_ERR_WRITE_HEADER' => 'Error writing header',
				'TAR_PATH_TOO_LONG' => 'Path is too long: ',
				'TAR_ERR_FILE_READ' => 'Error reading file: ',
				'TAR_ERR_FILE_WRITE' => 'Error writing file: ',
				'TAR_ERR_FILE_NO_ACCESS' => 'No access to file: ',
				'TAR_NO_GZIP' => 'Function &quot;gzopen&quot; is not available',
			);
		}
		return $arLang[$code];
	}

	# }
	##############
}

function BuilderGetFiles($path, $arFilter = array(), $bAllFiles = false, $recursive = false)
{
	static $len;
	if (!$recursive || !$len)
		$len = strlen($path);

	$retVal = array();
	if ($dir = opendir($path))
	{
		while(false !== $item = readdir($dir))
		{
			if (in_array($item, array_merge(array('.', '..', '.svn', '.hg', '.git'),$arFilter)))
				continue;
			if (is_dir($f = $path.'/'.$item))
				$retVal = array_merge($retVal, BuilderGetFiles($f, $arFilter, $bAllFiles, true));
			else
			{
				if ($bAllFiles || substr($f,-4) == '.php')
					$retVal[] = str_replace('\\','/',substr($f,$len));
			}
		}
		closedir($dir);
	}
	return $retVal;
}

function BuilderRmDir($path)
{
	if (!is_dir($path))
		return;
	$dir = opendir($path);
	while(false !== $item = readdir($dir))
	{
		if ($item == '.' || $item == '..')
			continue;
		$f = $path.'/'.$item;
		if (is_dir($path.'/'.$item))
			BuilderRmDir($f);
		else
			unlink($f);
	}
	closedir($dir);
	rmdir($path);
}

function GetStringCharset($str)
{ 
	global $APPLICATION;
	if (preg_match("/[\xe0\xe1\xe3-\xff]/",$str))
		return 'cp1251';
	$str0 = $APPLICATION->ConvertCharset($str, 'utf8', 'cp1251');
	if (preg_match("/[\xe0\xe1\xe3-\xff]/",$str0,$regs))
		return 'utf8';
	return 'ascii';
}

function GetLangPath($file, $m_dir)
{
	$lang = '/lang/ru';
	if (preg_match('#^(/install/components/[^/]+)(/[^/]+)(/?.*)$#',$file,$regs))
	{
		if (file_exists($m_dir.$regs[1].$regs[2].'/component.php')) // with namespace
		{
			$c_dir = $regs[1].$regs[2];
			$c_path = $regs[3];
		}
		else
		{
			$c_dir = $regs[1];
			$c_path = $regs[2].$regs[3];
		}

		if (preg_match('#^(/templates/[^/]+/[^/]+/[^/]+/[^/]+)(/.+)$#',$c_path,$regs)) // complex
			return $c_dir.$regs[1].$lang.$regs[2];
		elseif (preg_match('#^(/templates/[^/]+)(/.+)$#',$c_path,$regs)) // template
			return $c_dir.$regs[1].$lang.$regs[2];
		else // component
			return $c_dir.$lang.$c_path;

		if (preg_match('#^(/install/components/[^/]+/[^/]+/templates/[^/]+/[^/]+/[^/]+/[^/]+)(/.+)$#',$file,$regs))
			$lang_file = $regs[1].'/lang/ru'.$regs[2];
		elseif (preg_match('#^(/install/components/[^/]+/[^/]+/templates/[^/]+)(/.+)$#',$file,$regs))
			$lang_file = $regs[1].'/lang/ru'.$regs[2];
		elseif (preg_match('#^(/install/components/[^/]+/[^/]+)(/.+)$#',$file,$regs))
			$lang_file = $regs[1].'/lang/ru'.$regs[2];
		elseif (preg_match('#^(/install/components/[^/]+)(/.+)$#',$file,$regs))
			$lang_file = $regs[1].'/lang/ru'.$regs[2];
	}
	else
		$lang_file = '/lang/ru'.$file;
	return $lang_file;
}

function VersionUp($num)
{
	$ar = explode('.',$num);
	if (count($ar) == 3)
		return $ar[0].'.'.$ar[1].'.'.(++$ar[2]);
	return $num;
}

function GetMess($f)
{
	$MESS = false;
	if (is_file($f))
		include($f);
	return $MESS;
}
?>
