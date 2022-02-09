<?
namespace Ns\Parser;
set_time_limit (300);
/**
*
*/
class SbsTerminals extends ParserCore
{
	const SBS_TERMINALS_IBLOCK_ID = 10;

	private $dom;
	private $arFields;
    private $objElement;
    private $checker;

	function __construct()
	{
		$html = file_get_contents("https://www.sbsibank.by/atms.asp?bmID=-1&dev=A");
		$this->dom = \phpQuery::newDocumentHTML($html);
		/**
		 * Устанавливаем по дефолту инфоблок новостей для альфы
		 */
		$this->arFields["IBLOCK_ID"] = self::SBS_TERMINALS_IBLOCK_ID;
        /**
         * Пытаемся подключить модуль инфоблоков
         */
        try
        {
            \Ns\Bitrix\Modules::IncludeModule('iblock');
        }
        catch (\Exception $e)
        {
            prentExpection($e->getMessage());
        }
        $this->objElement = new \CIBlockElement();

    }

    public function go()
    {
        try
        {
            $this->checker = \Ns\Bitrix\Helper::Create('iblock')->useVariant('checker');
        }
        catch (\Exception $e)
        {
            prentExpection($e->getMessage());
        }
        $table = \phpQuery::pq($this->dom->find("table.mainfnt"));
        foreach ($table->find("tr") as $tr)
        {
            /**
             * Получение Даты создания и Имени новости
             */
            $tr = \phpQuery::pq($tr);
            $this->arFields["PROPERTY_VALUES"]["CITY"] = $tr->find('td:eq(0)')->text();
            $this->arFields["PROPERTY_VALUES"]["ADDRESS"] = $tr->find('td:eq(1)')->text();
            $this->arFields["PROPERTY_VALUES"]["LOCATION"] = $tr->find('td:eq(2)')->text();
            $this->arFields["PROPERTY_VALUES"]["CURRENCY"] = $tr->find('td:eq(3)')->text();
            $this->arFields["PROPERTY_VALUES"]["OPERATION_TIME"] = $tr->find('td:eq(4)')->text();
            $this->arFields["PROPERTY_VALUES"]["STATUS"] = $tr->find('td:eq(5)')->text();
            /**
             * Compose name for element of infoblock
             */
            $this->arFields["NAME"] = $this->composeName();
            prent($this->arFields);
            $this->Add();
        }
        /**
         * Check and add element to infoblock Terminals
         */

        return true;
	}

   private function composeName()
   {
        return $this->arFields["NAME"] = $this->arFields["PROPERTY_VALUES"]["ADDRESS"] . "(" . $this->arFields["PROPERTY_VALUES"]["CITY"] . ")";
   }

    private function Add()
    {
         if ($this->checker->withParams(array("NAME" => $this->arFields["NAME"], "IBLOCK_ID" => $this->arFields["IBLOCK_ID"]))->isUnique())
            {
                $this->objElement->Add($this->arFields);
                if ($this->objElement->LAST_ERROR)
                {
                    prent($this->objElement->LAST_ERROR);
                }
            }
    }
}

?>