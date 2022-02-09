<?
namespace Ns\Bitrix\Helper\Files;

define("PDF", "application/pdf");
define("JPG", "image/jpeg");
define("DOC", "application/vnd.openxmlformats-officedocument.wordprocessingml.document");
define("ZIP", "application/zip");

/**
*
*/
class Checker extends \Ns\Bitrix\Helper\HelperCore
{

	/**
	 * Choose picture dependence on type
	 */
	public function chooseFileIcoClass($id)
	{
		$fileInfo = \CFile::GetFileArray($id);
		if ($fileInfo["CONTENT_TYPE"] == PDF)
		{
			return "ico_pdf";
		}
		elseif ($fileInfo["CONTENT_TYPE"] == DOC)
		{
			return "ico_doc";
		}
		elseif ($fileInfo["CONTENT_TYPE"] == JPG)
		{
			return "ico_jpg";
		}
		elseif ($fileInfo["CONTENT_TYPE"] == ZIP)
		{
			return "ico_zip";
		}
		else
		{
			return "ico_cd";
		}
		throw new \Exception("An error wa occured in " . __CLASS__, 1);

	}

}


?>