<?php
namespace Bitrix\EsolImportxml;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MailHeader
{
	var $arHeader = Array();
	var $arHeaderLines = Array();
	var $strHeader = "";
	var $bMultipart = false;
	var $content_type, $boundary, $charset, $filename, $MultipartType="mixed";
	public $content_id = '';

	function ConvertHeader($encoding, $type, $str, $charset)
	{
		if(strtoupper($type)=="B")
			$str = base64_decode($str);
		else
			$str = quoted_printable_decode(str_replace("_", " ", $str));

		$str = \Bitrix\EsolImportxml\MailUtil::ConvertCharset($str, $encoding, $charset);

		return $str;
	}

	function DecodeHeader($str, $charset_to, $charset_document)
	{
		while(preg_match('/(=\?[^?]+\?(Q|B)\?[^?]*\?=)(\s)+=\?/i', $str))
			$str = preg_replace('/(=\?[^?]+\?(Q|B)\?[^?]*\?=)(\s)+=\?/i', '\1=?', $str);
		if(!preg_match("'=\?(.*)\?(B|Q)\?(.*)\?='i", $str))
		{
			if(strlen($charset_document)>0 && $charset_document!=$charset_to)
				$str = \Bitrix\EsolImportxml\MailUtil::ConvertCharset($str, $charset_document, $charset_to);
		}
		else
		{
			$str = preg_replace_callback(
				"'=\?(.*?)\?(B|Q)\?(.*?)\?='i",
				create_function('$m', "return \Bitrix\EsolImportxml\MailHeader::ConvertHeader(\$m[1], \$m[2], \$m[3], '".AddSlashes($charset_to)."');"),
				$str
			);
		}

		return $str;
	}

	function Parse($message_header, $charset)
	{
		if(preg_match("'content-type:.*?charset=([^\r\n;]+)'is", $message_header, $res))
			$this->charset = strtolower(trim($res[1], ' "'));
		elseif($this->charset=='' && defined("BX_MAIL_DEFAULT_CHARSET"))
			$this->charset = BX_MAIL_DEFAULT_CHARSET;

		$ar_message_header_tmp = explode("\r\n", $message_header);

		$n = -1;
		$bConvertSubject = false;
		for($i = 0, $num = count($ar_message_header_tmp); $i < $num; $i++)
		{
			$line = $ar_message_header_tmp[$i];
			if(($line[0]==" " || $line[0]=="\t") && $n>=0)
			{
				$line = ltrim($line, " \t");
				$bAdd = true;
			}
			else
				$bAdd = false;

			$line = self::DecodeHeader($line, $charset, $this->charset);

			if($bAdd)
				$this->arHeaderLines[$n] = $this->arHeaderLines[$n].$line;
			else
			{
				$n++;
				$this->arHeaderLines[] = $line;
			}
		}

		$this->arHeader = Array();
		for($i = 0, $num = count($this->arHeaderLines); $i < $num; $i++)
		{
			$p = strpos($this->arHeaderLines[$i], ":");
			if($p>0)
			{
				$header_name = strtoupper(trim(substr($this->arHeaderLines[$i], 0, $p)));
				$header_value = trim(substr($this->arHeaderLines[$i], $p+1));
				$this->arHeader[$header_name] = $header_value;
			}
		}

		$full_content_type = $this->arHeader["CONTENT-TYPE"];
		if(strlen($full_content_type)<=0)
			$full_content_type = "text/plain";

		if(!($p = strpos($full_content_type, ";")))
			$p = strlen($full_content_type);

		$this->content_type = trim(substr($full_content_type, 0, $p));
		if(strpos(strtolower($this->content_type), "multipart/") === 0)
		{
			$this->bMultipart = true;
			if (!preg_match("'boundary\s*=(.+);'i", $full_content_type, $res))
				preg_match("'boundary\s*=(.+)'i", $full_content_type, $res);

			$this->boundary = trim($res[1], '"');
			if($p = strpos($this->content_type, "/"))
				$this->MultipartType = substr($this->content_type, $p+1);
		}

		if($p < strlen($full_content_type))
		{
			$add = substr($full_content_type, $p+1);
			if(preg_match("'name=([^;]+)'i", $full_content_type, $res))
				$this->filename = trim($res[1], '"');
		}

		$cd = $this->arHeader["CONTENT-DISPOSITION"];
		if (strlen($cd) > 0)
		{
			if (preg_match("'filename=([^;]+)'i", $cd, $res))
			{
				$this->filename = trim($res[1], '"');
			}
			else if (preg_match("'filename\*=([^;]+)'i", $cd, $res))
			{
				list($fncharset, $fnstr) = preg_split("/'[^']*'/", trim($res[1], '"'));
				$this->filename = \Bitrix\EsolImportxml\MailUtil::ConvertCharset(rawurldecode($fnstr), $fncharset, $charset);
			}
		}

		if($this->arHeader["CONTENT-ID"]!='')
			$this->content_id = trim($this->arHeader["CONTENT-ID"], '"<>');

		$this->strHeader = implode("\r\n", $this->arHeaderLines);

		return true;
	}

	function IsMultipart()
	{
		return $this->bMultipart;
	}

	function MultipartType()
	{
		return strtolower($this->MultipartType);
	}

	function GetBoundary()
	{
		return $this->boundary;
	}

	function GetHeader($type)
	{
		return $this->arHeader[strtoupper($type)];
	}
}