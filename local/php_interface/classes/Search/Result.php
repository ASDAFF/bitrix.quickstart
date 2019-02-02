<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 14.03.14
 * Time: 13:47
 */

namespace Cpeople\Classes\Search;


class Result
{
    protected $data;
    protected $element = null;

    public function __construct($data = array())
    {
        $this->data = $data;
    }

    public function getTitle()
    {
        return $this->data['TITLE'];
    }

    public function getText($limit = 500)
    {
        return trim(mb_substr($this->data['BODY'], 0, $limit));
    }

    public function getUrl()
    {
        if ($this->data['MODULE_ID'] === 'iblock')
        {

        }

        return $this->data['MODULE_ID'] === 'iblock'
            ? ($this->getElement() ? $this->getElement()->getUrl() : false)
            : $this->data['URL'];
    }

    public function getPath()
    {

    }

    public function getItemID()
    {
        return $this->data['ITEM_ID'];
    }

    public function getChangeDate($format)
    {
        return FormatDate($format, strtotime($this->data['DATE_CHANGE']));
    }

    public function getBodyHighlighted($keywords = null, $wordsAround = 5, $tag = '<b>', $delimiter = ' &hellip; ', $maxMatchesNum = NULL)
    {
        if (!is_array($keywords))
        {
            $keywords = preg_replace('/<.*>/Uu', '', $keywords);
            $keywords = preg_replace('/\s+/u', ' ', $keywords);

            $keywords = preg_split('/\s+/u', $keywords, -1, PREG_SPLIT_NO_EMPTY);
        }

        $tag_close = preg_replace('/<([a-z0-9]+).*>/isx', '</$1>', $tag);

        array_walk($keywords, 'preg_quote');

        $words = preg_split('/\s+/u', strip_tags($this->data['BODY']), -1, PREG_SPLIT_NO_EMPTY);

        if (!$matched = preg_grep("/(" . join('|', $keywords) . ")/iu", $words))
        {
            return false;
        }

        foreach ($matched as $i => $word)
        {
            $words[$i] = "{$tag}{$words[$i]}{$tag_close}";
        }

        $matches = array();

        $prev = -$wordsAround * 2;

        foreach ($matched as $i => $word)
        {
            if ($i - $wordsAround > $prev)
            {
                $start = $i - $wordsAround;
                if ($start < 0) $start = 0;

                $matches[] = join(' ', array_slice($words, $start, $wordsAround * 2 + 1));
            }

            $prev = $i;
        }

        if($maxMatchesNum)
        {
            $matches = array_slice($matches, 0, $maxMatchesNum);
        }

        return join($delimiter, $matches);
    }

    /**
     * @return \Cpeople\Classes\Block\Object|\Cpeople\Classes\Section\Object
     */
    public function getElement()
    {
        if($this->element === null)
        {
            $itemId = $this->getItemID();
            $isSection = !is_numeric($itemId);

            if($isSection)
            {
                $itemId = substr($itemId, 1);
                $instance = \Cpeople\Classes\Section\Getter::instance();
            }
            else
            {
                $instance = \Cpeople\Classes\Block\Getter::instance();
            }

            $this->element = $instance->getById($itemId);
        }

        return $this->element;
    }

    public function __get($name)
    {
        if (isset($this->data[strtoupper($name)]))
        {
            return $this->data[strtoupper($name)];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property in __get(): ' . $name .
            ' in file ' . $trace[0]['file'] .
            ' line ' . $trace[0]['line'], E_USER_NOTICE
        );
    }
} 
