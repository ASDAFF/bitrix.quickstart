<?
namespace Ns\Parser;
set_time_limit (300);
/**
*
*/
class AlfaTaxes extends ParserCore
{
	const ALFATAXES_IBLOCK_ID = 14;

	private $dom;
	private $arFields;
    private $objElement;
    private $objSection;
    private $checker;

	function __construct()
	{
		$html = file_get_contents("http://www.alfabank.by/bank/taxes/active/");
		$this->dom = \phpQuery::newDocumentHTML($html);
		/**
		 * Устанавливаем по дефолту инфоблок новостей для альфы
		 */
		$this->arFields["IBLOCK_ID"] = self::ALFATAXES_IBLOCK_ID;
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
        $this->objSection = new \CIBlockSection();

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
        foreach ($this->dom->find("span.cat_block") as $span)
        {
            $this->arFields = array();
            $this->arFields["IBLOCK_ID"] = self::ALFATAXES_IBLOCK_ID;
            /**
             * Получение Даты создания и Имени новости
             */
            $span = \phpQuery::pq($span);
            if ($span->find("strong")->text())
            {
                $mainSection = $this->findOrCreateSection($span->find("strong")->text());
                prent($span->find("strong")->text());
                prentExpection($mainSection);
            }

            $this->arFields["IBLOCK_SECTION_ID"] = $this->findOrCreateSection(($span->find("a:eq(0)")->text()) ? $span->find("a:eq(0)")->text() : $span->find("a:eq(0)")->find("span")->text(), $mainSection);
            if ($this->arFields["IBLOCK_SECTION_ID"] === false)
            {
                continue;
            }
            foreach ($span->find('table.fileinfo') as $table)
            {
                $table = \phpQuery::pq($table);
                $this->arFields["NAME"] = $table->find("a:eq(1)")->text();
                $this->arFields["PROPERTY_VALUES"]["LINK"] = $table->find("a:eq(1)")->attr("href");
                prent($this->arFields);
                $this->Add();
            }

            /**
             * Compose name for element of infoblock
             */
        }
        /**
         * Check and add element to infoblock Terminals
         */

        return true;
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

    private function findOrCreateSection($name, $parent = 0)
    {
        if (!$name)
        {
            return false;
        }
        $rs = \CIBlockSection::GetList(
            false,
            array(
                "IBLOCK_ID" => $this->arFields["IBLOCK_ID"],
                "NAME" => $name,
                "SECTION_ID" => $parent
                ),
            false,
            array("ID")
            );
        if ($section = $rs->Fetch())
        {
            return $section["ID"];
        }
        else
        {
            $arFields = array(
                "IBLOCK_ID" => $this->arFields["IBLOCK_ID"],
                "NAME" => $name,
                "IBLOCK_SECTION_ID" => $parent,
                "ACTIVE" => "Y"
                );
            $section = $this->objSection->Add($arFields);
            if ($this->objSection->LAST_ERROR)
            {
                prent($this->objSection->LAST_ERROR);
                throw new Exception("Error of adding section", 1);
            }
            else
            {
                return $section;
            }
        }
    }
}

?>