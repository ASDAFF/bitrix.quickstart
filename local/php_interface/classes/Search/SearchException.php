<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 14.03.14
 * Time: 13:50
 */

namespace Cpeople\Classes\Search;


class SearchException extends \Exception
{
    protected $data;

    public function __construct($data = array())
    {
        $this->data = $data;
    }
}