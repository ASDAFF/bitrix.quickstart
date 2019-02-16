<?php

class Paging
{
    public $self;              #   link at self
    public $pagesize;
    public $page;              #   current page;
    public $total;             #   total pages count
    public $pagingBar;         #   string to display
    public $title;             #   title before paging bar
    public $format;
    public $total_pages;
    public $next               = '';
    public $next_inactive      = '';
    public $previous           = '';
    public $previous_inactive  = '';
    public $splitter           = ' ';
    public $current_template   = '<li><a class="current" href="">%s</a></li>';
    public $item_template      = '<li><a href="%s">%s</a></li>';
    public $template           = '%s';
    public $numbers_template   = '%s';
    public $template_pages     = '%s';
    public $zerofill           = false;
    public $zero_num           = 2;
    public $display_pages      = 6;
    public $mode               = 'pages';
    public $hellip             = ' &hellip; ';

    function __construct($page, $total, $size)
    {
        $this->page     = $page;
        $this->pagesize = $size;
        $this->total    = $total;
        $this->total_pages = $size > 0 ? ceil($total / $size) : 0;
    }

    function display()
    {
        echo $this->html();
    }

    function setLabel($str)
    {
        $this->title = $str;
    }

    function insertNumber($number, $chunk)
    {
        $chunk = str_replace('{n}', $this->applyMode($number), $chunk);
        return $chunk;
    }

    function applyMode($value)
    {
        switch ($this->mode)
        {
            case 'offset':

                $value = $value * $this->pagesize;

            break;

            case 'pages':
            default:

                $value = $this->zerofill($value, $this->zero_num);

            break;
        }

        return $value;
    }

    function html()
    {
        $this->format = $this->format ? $this->format : '%d';

        $totalpages = $this->pagesize > 0 ? ceil($this->total / $this->pagesize) : 0;

        $firstpage = $this->page - floor($this->display_pages / 2);

        if ($firstpage + $this->display_pages > $this->total_pages)
        {
            $firstpage = $this->total_pages - $this->display_pages;
        }

        if ($firstpage < 1)
        {
            $firstpage = 1;
        }

        $lastpage = $firstpage + $this->display_pages;

        if ($lastpage > $this->total_pages)
        {
            $lastpage = $this->total_pages;
        }

        $this->pagingBar = '';

        if ($firstpage - 1 > 1)
        {
            $link = @sprintf($this->format, $this->applyMode(1));
            $chunk = sprintf($this->item_template, $link, $this->applyMode('1'));
            $chunk = $this->insertNumber(1, $chunk);
            $this->pagingBar .= $chunk . $this->hellip;
        }
        else if ($firstpage == 2)
        {
            $link = @sprintf($this->format, $this->applyMode(1));
            $chunk = sprintf($this->item_template, $link, $this->applyMode('1'));
            $chunk = $this->insertNumber(1, $chunk);
            $this->pagingBar .= $chunk;
        }

        for ($n = $firstpage; $n <= $lastpage; $n++)
        {
            if ($n > $firstpage ) $this->pagingBar .= $this->splitter;

            if ($this->page == $n)
            {
                $chunk = sprintf($this->current_template, $this->applyMode($n));
                $chunk = $this->insertNumber($n, $chunk);
                $this->pagingBar .= $chunk;
            }
            else
            {
                $link = sprintf($this->format, $this->applyMode($n));
                $chunk = sprintf($this->item_template, $link, $this->applyMode($n));
                $chunk = $this->insertNumber($n, $chunk);
                $this->pagingBar .= $chunk;
            }
        }

        if ($lastpage <= $totalpages - 2)
        {
            $link = sprintf($this->format, $this->applyMode($totalpages));
            $chunk = $this->hellip . sprintf($this->item_template, $link, $this->applyMode($totalpages));
            $chunk = $this->insertNumber($totalpages, $chunk);
            $this->pagingBar .= $chunk;
        }
        else if ($lastpage == $totalpages -1)
        {
            $link = sprintf($this->format, $this->applyMode($totalpages));
            $chunk = sprintf($this->item_template, $link, $this->applyMode($totalpages));
            $chunk = $this->insertNumber($totalpages, $chunk);
            $this->pagingBar .= $chunk;
        }

        if ($totalpages <= 1)
        {
            return '';
        }

        if (!empty($this->template_pages))
        {
            $this->pagingBar = sprintf($this->template_pages, $this->pagingBar);
        }

        $this->pagingBar = sprintf($this->numbers_template, $this->pagingBar);

        if (!empty($this->next) && $this->page < $totalpages)
        {
            $next_link = strstr($this->next, '<a')
                ? sprintf($this->next, sprintf($this->format, $this->applyMode($this->page + 1)))
                : '&nbsp;&nbsp;&nbsp;<a href="' . sprintf($this->format, ($this->page + 1)) . '">' . $this->next . '</a>';

            $next_link = $this->insertNumber(($this->page + 1), $next_link);

            $this->pagingBar .= $next_link;
        }
        else if (!empty($this->next_inactive) && $this->page == $totalpages)
        {
            $this->pagingBar .= $this->next_inactive;
        }

        if (!empty($this->previous) && $this->page > 1)
        {
            $previous_link = strstr($this->previous, '<a')
                ? sprintf($this->previous, sprintf($this->format, $this->applyMode($this->page - 1)))
                : '<a href="' . sprintf($this->format, ($this->page - 1)) . '">' . $this->previous . '</a>&nbsp;&nbsp;&nbsp;';

            $previous_link = $this->insertNumber(($this->page - 1), $previous_link);

            $this->pagingBar = $previous_link . $this->pagingBar;
        }
        else if (!empty($this->previous_inactive) && $this->page == 1)
        {
            $this->pagingBar = $this->previous_inactive . $this->pagingBar;
        }

        if (!empty($this->title))
        {
            $this->title .= '&nbsp;';
        }

        return sprintf($this->template, $this->title . $this->pagingBar);
    }

    function setCurrentPage($page)
    {
        $this->page = $page;
    }

    function setSelfLink($link)
    {
        $this->self = $link;
    }

    function setFormat($format)
    {
        $this->format = $format;
    }

    function getOffset()
    {
        return ($this->page - 1) * $this->pagesize;
    }

    function getFirst()
    {
        return min($this->getOffset() + 1, $this->total);
    }

    function getLast()
    {
        return min($this->getFirst() + $this->pagesize - 1, $this->total);
    }

    function getPagesize()
    {
        return $this->pagesize;
    }

    function getTotal()
    {
        return $this->total;
    }

    function getFormat($var_name = 'page', $set = true)
    {
        $url    = str_replace('%', '%%', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//            $url    = str_replace('%', '%%', NEWS_URL_BASE);
        $self   = preg_replace('/(\?|&)' . $var_name . '=\d+/isx', '', $url);
        $parsed = parse_url($self);
        $delim  = empty($parsed['query']) ? '?' : '&';

        $format = $self . $delim . $var_name . '=%d';

        if ($set)
        {
            $this->setFormat($format);
        }

        return $self . $delim . $var_name . '=%d';
    }

    function isLast()
    {
        return $this->page == $this->total_pages;
    }

    function isFirst()
    {
        return $this->page == 1;
    }

    function getPreviousHref()
    {
        return $this->isFirst() ? '' : sprintf($this->format, ($this->page - 1));
    }

    function getNextHref()
    {
        return $this->isLast() ? '' : sprintf($this->format, ($this->page + 1));
    }

    function zerofill($number, $length = 2)
    {
        return $this->zerofill ? zerofill($number, $length) : $number;
    }
}
