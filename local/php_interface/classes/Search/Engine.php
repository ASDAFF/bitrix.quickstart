<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 14.03.14
 * Time: 13:47
 */

namespace Cpeople\Classes\Search;


class Engine
{
    protected $defaultClassName = '\Cpeople\Classes\Search\Result';
    protected $className = '\Cpeople\Classes\Search\Result';
    protected $modulesList = array();
    protected $tryInvertedLayout = false;
    protected $tryYandexSpeller = false;
    protected $query = '';
    protected $minLenth = 0;
    protected $minWordLength = 0;
    protected $arNavStartParams = array('nPageSize' => 10, 'iNumPage' => 1);
    protected $count = 0;

    /**
     * @return Engine
     */
    static function instance()
    {
        return new self;
    }

    /**
     * @param null $query
     * @param null $offset
     * @param null $limit
     * @return \Cpeople\Classes\Search\Result[];
     */
    public function makeSearch($query = null, $offset = null, $limit = null)
    {
        $retval = $this->search($query, $offset, $limit);

        if(empty($retval) && $this->tryInvertedLayout)
        {
            $invertedQuery = $this->makeInvertedLayout($query);
            $retval = $this->search($invertedQuery, $offset, $limit);
        }

        if(empty($retval) && $this->tryYandexSpeller)
        {
            $withoutMistakeQuery = $this->makeYandexSpeller($query);
            if($query !== $withoutMistakeQuery)
            {
                $retval = $this->search($withoutMistakeQuery, $offset, $limit);
            }
        }

        if(empty($retval) && $this->tryInvertedLayout && $this->tryYandexSpeller)
        {
            $withoutMistakeInvertedQuery = $this->makeYandexSpeller($invertedQuery);
            if($query !== $withoutMistakeInvertedQuery)
            {
                $retval = $this->search($withoutMistakeInvertedQuery, $offset, $limit);
            }
        }

        return $retval;
    }

    /**
     * @param null $query
     * @param null $offset
     * @param null $limit
     * @return \Cpeople\Classes\Search\Result[];
     */
    public function search($query = null, $offset = null, $limit = null)
    {
        if($query !== null) $this->query = $query;
        $retval = array();

        $whereSQL = $this->makeSQLWhere($query);

        $limit = intval($limit === null ? $this->arNavStartParams['nPageSize'] : $limit);
        $offset = intval($offset === null ? ($this->arNavStartParams['iNumPage'] - 1) * $limit : $offset);

        $sql = "
            SELECT SQL_CALC_FOUND_ROWS *
            FROM b_search_content bsc
            LEFT JOIN b_iblock_site bis
                ON bis.IBLOCK_ID = bsc.PARAM2 AND bis.SITE_ID = '" . SITE_ID . "'
            $whereSQL
        ";

        if ($limit)
        {
            $sql .= 'LIMIT ' . ($offset ? "$offset, $limit" : $limit);
        }

        $res = $this->makeQuery($sql);

        while ($row = $res->Fetch())
        {
            $retval[] = new $this->className($row);
        }

        if (!empty($retval))
        {
            $res = $this->makeQuery('SELECT FOUND_ROWS()')->Fetch();
            $this->count = (int) reset($res);
        }

        return $retval;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function makeSQLWhere($query = null)
    {
        if($query === null) $query = $this->query;

        $sqlReadyQuery = $this->prepareQuery($query);

        $modulesSQL = $this->makeModulesSQL($this->modulesList);

        $retval =
            "WHERE
                (
                    bsc.BODY LIKE '%$sqlReadyQuery%' OR bsc.TITLE LIKE '%$sqlReadyQuery%'
                )
                $modulesSQL
            ";

        return $retval;
    }

    public function prepareQuery($query)
    {
        $query = trim($query);
        $query = str_replace(preg_split('##', '!@#$%^&*()-+=#{}[]~`,./?<>', 0, PREG_SPLIT_NO_EMPTY), '%', $query);
        $query = preg_replace('#%+#', '%', $query);

        if (!empty($this->minWordLength))
        {
            $query = preg_replace('/\b[^\s]{1,' . ($this->minWordLength - 1) . '}\b/u', '', $query);
        }

        $query = preg_replace('#\s+#', '%', $query);

        return $query;
    }

    public function setClassName($className)
    {
        $dummy = new $className;

        if (!is_subclass_of($dummy, $this->defaultClassName))
        {
            throw new SearchException('Class ' . $className . ' is not subclass of ' . $this->defaultClassName);
        }

        $this->className = $className;

        return $this;
    }

    public function addModule($module, $iblockType = null, $iblockId = null)
    {
        if (is_array($iblockId))
        {
            foreach ($iblockId as $id)
            {
                $this->modulesList[$module][$iblockType][$id] = true;
            }
        }
        else
        {
            $this->modulesList[$module][$iblockType][$iblockId] = true;
        }

        return $this;
    }

    public function getFoundRows($query = null)
    {
        return $this->getCount();
        /*
        $retval = 0;

        $whereSQL = $this->makeSQLWhere($query);

        $sql = "
            SELECT COUNT(*)
            FROM b_search_content bsc
            LEFT JOIN b_iblock_site bis
                ON bis.IBLOCK_ID = bsc.PARAM2 AND bis.SITE_ID = '" . SITE_ID . "'
            $whereSQL
        ";

        $res = $this->makeQuery($sql);

        if ($row = $res->Fetch())
        {
            $retval = $row['COUNT(*)'];
        }

        $this->total = $retval;

        return $retval;*/
    }

    protected function makeQuery($sql)
    {
        global $DB;
        return $DB->Query($sql, false, __LINE__);
    }

    protected function makeModulesSQL($modulesList)
    {
        $modulesSQL = '';

        /** если есть модули */
        if ($modulesList)
        {
            $modulesSQL .= " AND (";
        }

        foreach ($modulesList as $module => $iblockTypes)
        {
            if ($iblockTypes != reset($modulesList))
            {
                $modulesSQL .= " OR ";
            }
            /** запись модуля */
            $modulesSQL .= "(bsc.MODULE_ID = '$module'";

            /** если есть типы инфоблоков */
            if (array_filter(array_keys($iblockTypes)))
            {
                $modulesSQL .= " AND (";
            }

            /** для модуля main фильтруем по URL */
            if($module === 'main')
            {
                $urlList = array_keys($iblockTypes);
                $urlList = array_filter($urlList);
                if(!empty($urlList))
                {
                    $modulesSQL .= "bsc.URL LIKE '" . implode("' OR bsc.URL LIKE '", $urlList) . "'";
                }
            }
            else
            {
                foreach ($iblockTypes as $iblockType => $iblockIds)
                {
                    if ($iblockIds != reset($iblockTypes))
                    {
                        $modulesSQL .= " OR ";
                    }
                    if ($iblockType)
                    {
                        /** запись типа инфоблока */
                        $modulesSQL .= "(bsc.PARAM1 = '$iblockType'";

                        if ($iblocks = implode(",", array_keys($iblockIds)))
                        {
                            /** запись инфоблоков */
                            $modulesSQL .= " AND bsc.PARAM2 IN ($iblocks)";
                        }

                        /** закрываем каждый тип инфоблока */
                        $modulesSQL .= ")";
                    } elseif ($iblocks = implode(",", array_keys($iblockIds)))
                    {
                        /** запись инфоблоков */
                        if (array_filter(array_keys($iblockTypes)))
                        {
                            $modulesSQL .= "bsc.PARAM2 IN ($iblocks)";
                        /** если у нас только инфоблоки без типов */
                        } else
                        {
                            $modulesSQL .= " AND bsc.PARAM2 IN ($iblocks)";
                        }
                    }
                }
            }

            /** если есть типы инфоблоков */
            if (array_filter(array_keys($iblockTypes)))
            {
                $modulesSQL .= ")";
            }

            /** закрываем каждый модуль */
            $modulesSQL .= ")";
        }

        /** если есть модули */
        if ($modulesList)
        {
            $modulesSQL .= ")";
        }

        return $modulesSQL;
    }

    public function setInvertedLayout($bVal)
    {
        $this->tryInvertedLayout = $bVal;

        return $this;
    }

    protected function makeInvertedLayout($query)
    {
        if(!$this->tryInvertedLayout) return '';

        $invertedQuery = strtr($query, array('q'=>'й','w'=>'ц','e'=>'у','r'=>'к','t'=>'е','y'=>'н','u'=>'г','i'=>'ш','o'=>'щ','p'=>'з','['=>'х',']'=>'ъ','a'=>'ф','s'=>'ы','d'=>'в','f'=>'а','g'=>'п','h'=>'р','j'=>'о','k'=>'л','l'=>'д',';'=>'ж','\''=>'э','z'=>'я','x'=>'ч','c'=>'с','v'=>'м','b'=>'и','n'=>'т','m'=>'ь',','=>'б','.'=>'ю','/'=>'.','Q'=>'Й','W'=>'Ц','E'=>'У','R'=>'К','T'=>'Е','Y'=>'Н','U'=>'Г','I'=>'Ш','O'=>'Щ','P'=>'З','{'=>'Х','}'=>'Ъ','A'=>'Ф','S'=>'Ы','D'=>'В','F'=>'А','G'=>'П','H'=>'Р','J'=>'О','K'=>'Л','L'=>'Д',':'=>'Ж','"'=>'Э','|'=>'/','Z'=>'Я','X'=>'Ч','C'=>'С','V'=>'М','B'=>'И','N'=>'Т','M'=>'Ь','<'=>'Б','>'=>'Ю','?'=>',','й'=>'q','ц'=>'w','у'=>'e','к'=>'r','е'=>'t','н'=>'y','г'=>'u','ш'=>'i','щ'=>'o','з'=>'p','х'=>'[','ъ'=>']','ф'=>'a','ы'=>'s','в'=>'d','а'=>'f','п'=>'g','р'=>'h','о'=>'j','л'=>'k','д'=>'l','ж'=>';','э'=>'\'','я'=>'z','ч'=>'x','с'=>'c','м'=>'v','и'=>'b','т'=>'n','ь'=>'m','б'=>',','ю'=>'.','.'=>'/','Й'=>'Q','Ц'=>'W','У'=>'E','К'=>'R','Е'=>'T','Н'=>'Y','Г'=>'U','Ш'=>'I','Щ'=>'O','З'=>'P','Х'=>'{','Ъ'=>'}','Ф'=>'A','Ы'=>'S','В'=>'D','А'=>'F','П'=>'G','Р'=>'H','О'=>'J','Л'=>'K','Д'=>'L','Ж'=>':','Э'=>'"','/'=>'|','Я'=>'Z','Ч'=>'X','С'=>'C','М'=>'V','И'=>'B','Т'=>'N','Ь'=>'M','Б'=>'<','Ю'=>'>',','=>'?'));
        /*$fromString = 'qwertyuiop[]asdfghjkl;\'\zxcvbnm,./QWERTYUIOP{}ASDFGHJKL:"|ZXCVBNM<>?йцукенгшщзхъфывапролджэ\\ячсмитьбю.ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭ/ЯЧСМИТЬБЮ,';
        $toString   = 'йцукенгшщзхъфывапролджэ\\ячсмитьбю.ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭ/ЯЧСМИТЬБЮ,qwertyuiop[]asdfghjkl;\'\zxcvbnm,./QWERTYUIOP{}ASDFGHJKL:"|ZXCVBNM<>?';
        $invertedQuery = '';


        for($i = 0, $c = strlen($query); $i < $c; $i++)
        {
            $curChar = substr($query, $i, 1);
            $pos = strpos($fromString, $curChar);
            $newChar = $pos !== false ? substr($toString, $pos, 1) : $curChar;

            $invertedQuery .= $newChar;
        }*/

        return $invertedQuery;
    }

    public function setYandexSpeller($bVal)
    {
        $this->tryYandexSpeller = $bVal;

        return $this;
    }

    protected function makeYandexSpeller($query)
    {
        return \Cpeople\Classes\Services\YandexSpeller::correctText($query);
    }

    public function setPageSize($size)
    {
        $this->arNavStartParams['nPageSize'] = (int) $size;

        return $this;
    }

    public function setPageNum($pageNum)
    {
        $this->arNavStartParams['iNumPage'] = (int) $pageNum;

        return $this;
    }

    public function paginate($pagingSize, $pageNum)
    {
        $this->setPageSize($pagingSize);
        $this->setPageNum(intval($pageNum) < 1 ? 1 : intval($pageNum));

        return $this;
    }

    /**
     * @param $urlTemplate
     * @return \Cpeople\paging
     */
    public function getPagingObject($urlTemplate)
    {
        if (!isset($this->total))
        {
            $this->total = $this->getFoundRows($this->query);
        }

        $paging = new \Cpeople\paging($this->arNavStartParams['iNumPage'], $this->total, $this->arNavStartParams['nPageSize']);
        $paging->setFormat($urlTemplate);

        return $paging;
    }

    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }
} 
