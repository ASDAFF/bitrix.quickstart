<?php

class MultilangDummy extends Block\ObjectBlock
{
    use \Traits\MultilangFields;

    public function getLangTitle()
    {
        return HelperFunction::coalesce($this->getLangPropText('TITLE'), $this->name);
    }
}
