<?php

namespace Lema\Base;


/**
 * Class Paginator
 * @package Lema\Base
 */
class Paginator extends StaticInstance
{
    protected $pagerName = 'page';
    protected $page = 1;
    protected $perPage = 50;
    protected $elements = array();
    protected $elementsWithPageN = array();
    protected $totalCount = 0;
    protected $totalPagesCount = 1;

    /**
     * Paginator constructor.
     *
     * @param array $elements
     * @param int $perPage
     * @param int $totalCount
     *
     * @access public
     */
    public function __construct(array $elements = array(), $perPage = 50, $totalCount = 0)
    {
        empty($elements) || $this->setElements($elements);
        empty($perPage) || $this->setPerPage($perPage);
        empty($totalCount) || $this->setTotalPagesCount($totalCount);
    }

    /**
     * Set elements for pagination
     *
     * @param array $elements
     * @return $this
     *
     * @access public
     */
    public function setElements(array $elements = array())
    {
        $this->elements = $elements;
        return $this;
    }

    /**
     * Set elements count per page
     *
     * @param int $perPage
     * @return $this
     *
     * @access public
     */
    public function setPerPage($perPage = 50)
    {
        $this->perPage = (int) $perPage;
        return $this;
    }

    /**
     * Set name of pager (in GET)
     *
     * @param $pagerName
     * @return $this
     *
     * @access public
     */
    public function setPagerName($pagerName)
    {
        $this->pagerName = $pagerName;
        return $this;
    }

    /**
     * Get current page number
     *
     * @param string $globalArrType
     * @return int
     *
     * @access public
     */
    public function getPageNumber($globalArrType = 'GET')
    {
        $arr = in_array($globalArrType, array('GET', 'POST', 'REQUEST', 'COOKIES', 'SESSION')) ? '_' . $globalArrType : '_REQUEST';
        global ${$arr};
        $arr = ${$arr};
        $page = isset($arr[$this->pagerName]) ? (int) $arr[$this->pagerName] : 1;
        return $page < 1 ? 1 : $page;
    }

    /**
     * Start pagination
     *
     * @param int $totalCount
     * @return $this
     *
     * @access public
     */
    public function start($totalCount = 0)
    {
        if(empty($totalCount))
            $totalCount = count($this->elements);
        $this->totalCount = $totalCount;
        $this->setTotalPagesCount();
        return $this;
    }

    /**
     *
     * @return $this
     *
     * @access protected
     */
    protected function setTotalPagesCount()
    {
        $this->totalPagesCount = (int) ceil($this->totalCount / $this->perPage);
        $start = abs(($this->page - 1) * $this->perPage);
        return $this;
    }

    /**
     * @param int $page
     * @return array
     *
     * @access public
     */
    public function getElements($page = 0)
    {
        if(empty($page))
            $page = $this->page;
        $start = abs(($page - 1) * $this->perPage);
        return array_slice($this->elements, $start, $this->perPage);
    }

    /**
     * Include bootstrap
     *
     * @param bool $withAssets
     *
     * @access public
     */
    public static function incCss($withAssets = false)
    {
        if($withAssets)
            \Bitrix\Main\Page\Asset::getInstance()->addCss('/bitrix/css/main/bootstrap.min.css');
        else
            echo '<link href="/bitrix/css/main/bootstrap.min.css" type="text/css" rel="stylesheet">';
    }

    /**
     * Returns generated pagination links
     *
     * @param int $iNumPage
     * @param int $nPageSize
     * @param int $totalCount
     * @param int $totalPages
     * @param string $pagerName
     * @param string $title
     *
     * @access public
     */
    public function getLinks($iNumPage = 0, $nPageSize = 0, $totalCount = 0, $totalPages = 0, $pagerName = 'GET', $title = 'Товары')
    {
        $iNumPage    = empty($iNumPage)     ? $this->getPageNumber($pagerName)  : (int) $iNumPage;
        $nPageSize   = empty($nPageSize)    ? $this->perPage                    : (int) $nPageSize;
        $totalCount  = empty($totalCount)   ? $this->totalCount                 : (int) $totalCount;
        $totalPages  = empty($totalPages)   ? $this->totalPagesCount            : (int) $totalPages;

        global $APPLICATION;

        ?>
        <div class="pagination-block">
            <div class="pagination-title">
                <?=$title;?>
                <?= ($iNumPage - 1) * $nPageSize + 1;?> - <?= $iNumPage < $totalPages ? $iNumPage * $nPageSize : $totalCount;?>
                из
                <?=$totalCount?>
            </div>
            <div class="pagination-links">
                <ul class="pagination">
                    <?if($iNumPage > 2):?>
                        <li><a href="<?=$APPLICATION->GetCurPageParam('', array('page'));?>">Начало</a></li>
                    <?endif;?>
                    <?if($iNumPage > 1):?>
                        <li>
                            <a href="<?=$APPLICATION->GetCurPageParam('page=' . ($iNumPage - 1), array('page'));?>">&laquo;</a>
                        </li>
                    <?endif;?>
                    <?for($i = 1; $i <= $totalPages; ++$i):?>
                        <li<?if($i === $iNumPage){?> class="active"<?}?>>
                            <?if($i === $iNumPage):?>
                                <span><?=$i?></span>
                            <?else:?>
                                <a href="<?=$APPLICATION->GetCurPageParam('page=' . $i, array('page'));?>"><?=$i?></a>
                            <?endif;?>
                        </li>
                    <?endfor;?>
                    <?if($iNumPage < $totalPages):?>
                        <li>
                            <a href="<?=$APPLICATION->GetCurPageParam('page=' . ($iNumPage + 1), array('page'));?>">&raquo;</a>
                        </li>
                    <?endif;?>
                    <?if($iNumPage < $totalPages - 1):?>
                        <li>
                            <a href="<?=$APPLICATION->GetCurPageParam('page=' . $totalPages, array('page'));?>">Конец</a>
                        </li>
                    <?endif;?>
                </ul>
            </div>
        </div>
        <?
    }
}