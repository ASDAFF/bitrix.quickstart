<div class="bx_pagination_bottom">
    <div class="bx_pagination_section_one">
        <div class="bx_pg_section pg_pagination_num">
            <div class="bx_pagination_page">

                <ul class="pagination">
                    <?if( $this->NavPageNomer > 1 ):?>
                        <li>
                            <a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.($this->NavPageNomer-1), array('PAGEN_'.$this->NavNum))?>"><i class="fa fa-angle-left"></i></a>
                        </li>
                    <?endif;?>

                    <?if( $this->NavPageNomer-3 > 1 ):?>
                        <li><a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'=1', array('PAGEN_'.$this->NavNum))?>">1</a></li>
                        <?if( $this->NavPageNomer-4 != 1 ):?>
                            <li><span>...</span></li>
                        <?endif;?>
                    <?endif;?>

                    <?for($i=1; $i <= $this->NavPageCount; $i++):?>
                        <?if(
                            ($i+3) < $this->NavPageNomer ||
                            ($i-3) > $this->NavPageNomer
                        ) continue;?>
                        <li <?=($this->NavPageNomer == $i)?'class="active"':''?>>
                            <?if( $this->NavPageNomer == $i ):?>
                                <span><?=$i?></span>
                            <?else:?>
                                <a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.$i, array('PAGEN_'.$this->NavNum))?>"><?=$i?></a>
                            <?endif;?>
                        </li>
                    <?endfor;?>

                    <?
                    //NavPageCount
                    //NavPageNomer
                    ?>

                    <?if( $this->NavPageNomer+3 < $this->NavPageCount ):?>
                        <?if( $this->NavPageNomer+4 != $this->NavPageCount ):?>
                            <li><span>...</span></li>
                        <?endif;?>
                        <li><a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.$this->NavPageCount, array('PAGEN_'.$this->NavNum))?>"><?=$this->NavPageCount?></a></li>
                    <?endif;?>

                    <?if( $this->NavPageNomer < $this->NavPageCount ):?>
                        <li>
                            <a href="<?=$GLOBALS['APPLICATION']->GetCurPageParam('PAGEN_'.$this->NavNum.'='.($this->NavPageNomer+1), array('PAGEN_'.$this->NavNum))?>"><i class="fa fa-angle-right"></i></a>
                        </li>
                    <?endif;?>
                </ul>

            </div>
        </div>

    </div>

</div>