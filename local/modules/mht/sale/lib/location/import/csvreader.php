<?
/**
 * This class is for internal use only, not a part of public API.
 * It can be changed at any time without notification.
 *
 * @access private
 */

namespace Bitrix\Sale\Location\Import;

use Bitrix\Main;

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/csv_data.php");

final class CSVReader extends \CCSVData
{
	const FILE_ENCODING = 'UTF-8';

	private $header = 		array();
	private $useHeader = 	false;
	private $legacy = 		false;

	private $convertCharset = true;

	public function __construct($fields_type = "R", $convertCharset = true)
	{
		parent::__construct($fields_type = "R", false);
		$this->convertCharset = $convertCharset;
	}

	public function LoadFile($filename, $firstHeader = true)
	{
		parent::LoadFile($filename);

		$this->SetFieldsType("R");
		if($firstHeader)
			$this->SetFirstHeader();
		$this->SetDelimiter(";");
	}

	public function SetFirstHeader($first_header = false)
	{
		$this->useHeader = true;
		$this->header = $this->ReadHeader();
	}

	public function ReadHeader()
	{
		if(!$this->useHeader || !$this->__file)
			return false;

		if($this->cFieldsType == 'F')
			return false; // sorry, not implemented for that

		$fPos = ftell($this->__file);
		fseek($this->__file, $this->__hasBOM ? 3 : 0);

		$h = fgets($this->__file);

		fseek($this->__file, $fPos);

		return explode($this->cDelimiter, $h);
	}

	public function FetchAssoc()
	{
		if(!($line = $this->Fetch()))
			return false;

		if(!$this->useHeader || $this->legacy)
			return $line;

		$header = $this->header;

		$result = array();
		$colCount = count($line);
		for($k = 0; $k < $colCount; $k++)
		{
			$fld = trim(array_shift($header));

			if(!$fld) // column grid appeared shorter than data field
				break;

			$resLine = array();
			$prev =& $resLine;
			$subFields = explode('.', $fld);

			foreach($subFields as $subfld)
			{
				$subfld = trim($subfld);

				$prev[$subfld] = array();
				$prev =& $prev[$subfld];
			}

			if($this->convertCharset && strpos($fld, 'NAME') !== false && self::FILE_ENCODING != SITE_CHARSET)
				$prev = trim(\CharsetConverter::ConvertCharset($line[$k], self::FILE_ENCODING, SITE_CHARSET));
			else
				$prev = $line[$k];

			$result = array_merge_recursive($result, $resLine);
		}

		return $result;
	}

	public function CheckFileIsLegacy()
	{
		return $this->legacy;
	}

	public function ReadBlockLowLevel(&$bytesRead = false, $lineLimit = false)
	{
		if(trim($this->header[0]) == 'en' && !isset($this->header[1]))
		{
			$this->legacy = true;
			$this->SetDelimiter(",");
		}

		if($bytesRead !== false)
			$this->SetPos($bytesRead);

		$result = array();
		$i = -1;
		while ($line = $this->FetchAssoc())
		{
			$i++;

			if($lineLimit !== false && $lineLimit + 1 == $i)
				break;

			if(!$i && !$bytesRead)
			{
				//_dump_r('Skip header');
				continue; // header, skip
			}

			$result[] = $line;

			if($bytesRead !== false)
				$bytesRead = $this->GetPos();
		}

		return $result;
	}

	public function ReadBlock($file, &$bytesRead = false, $lineLimit = false)
	{
		$file = $_SERVER['DOCUMENT_ROOT'].$file;

		if(!file_exists($file) || !is_readable($file))
			throw new Main\SystemException('Cannot open file '.$file.' for reading');

		$this->LoadFile($file);

		return $this->ReadBlockLowLevel($bytesRead, $lineLimit);
	}

	public function GetFileSize()
	{
		return $this->iFileLength;
	}

	public function GetHeaderAssoc()
	{
		return $this->GetAssocLineByHeader($this->header, $this->header);
	}

	private function GetAssocLineByHeader($line, $header)
	{
		$result = array();
		$lineLen = count($line);
		for($k = 0; $k < $lineLen; $k++)
		{
			$fld = array_shift($header);

			if(!$fld) // column grid appeared shorter than data field
				break;

			$resLine = array();
			$prev =& $resLine;
			$subFields = explode('.', $fld);

			foreach($subFields as $subfld)
			{
				$subfld = trim($subfld);

				$prev[$subfld] = array();
				$prev =& $prev[$subfld];
			}

			$prev = trim($line[$k]);

			$result = array_merge_recursive($result, $resLine);
		}

		return $result;
	}
}