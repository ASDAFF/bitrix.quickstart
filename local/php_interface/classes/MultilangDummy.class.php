<?php

namespace Cpeople\Classes;

class MultilangDummy extends Block\Object
{
    use \Cpeople\Traits\MultilangFields;

    public function getLangTitle()
    {
        return coalesce($this->getLangPropText('TITLE'), $this->name);
    }
}
