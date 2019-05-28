<?php

/**
 * Created by PhpStorm.
 * User: Fyodor V.
 * Date: 28.06.2016
 * Time: 11:28
 */
class MobileHTMLHelper
{
 
    public static function sectionsTree($bOpen = false )
    {
        ?><section class="padding"><div class="mainaccordwrap">
            <div class="accorditem accord_cats<?php if ($bOpen) echo ' opened';?>">
                <a href="javascript:void(0)" class="accorditemheading">Каталог <i class="flaticon-right"></i></a>
                <?static::sectionsTreeClean($bOpen)?>
            </div>
            </div>
        </section><?
    }

    public static function sectionsTreeClean($bOpen = false)
    {
        $aSections = getCatalogTreeHierarchy(); //local/php_interface/functions.php
        ?>
            <ul <?php echo $bOpen?'style="display:block"':'';?>><?foreach($aSections as $arSection):?>
                    <li>
                        <div class="menuicon" style="background-image:url(<?php echo $arSection['2'];?>)"></div>
                        <p><?php echo $arSection['0'];?></p><i class="flaticon-right"></i>
                        <?if ( isset($arSection['CHILDREN'])):?>
                            <ul>
                                <?foreach($arSection['CHILDREN'] as $arSubSection):?>
                                    <li><a href="<?php echo $arSubSection[1];?>"><?php echo $arSubSection[0];?></a></li>
                                <?endforeach?>
                            </ul>
                        <?endif?>
                    </li>
                <?endforeach?>
            </ul>
        <?
    }

    public static function getSubsections($iIblockId,$iSectionsId)
    {

        $arSections = getSubSections( $iIblockId, $iSectionsId );
        if (isset($arSections[0])){
        ?>

        <div class="categoryfilterblock">
            <a href="javascript:void(0)" class="togglefilter">Подразделы <i class="flaticon-bottom"></i></a>
            <div class="filterwrap subsections" <?/*if (isset($_GET['set_filter'])):?>style="display: block;"<?endif*/?> >
                <ul><?foreach($arSections as $arSection):?>
                        <li>
                            <p><a href="<?php echo $arSection['link'];?>"><?php echo $arSection['name'];?></a></p>
                        </li>
                    <?endforeach?>
                </ul>
            </div>
        </div>

       <?}
    }
}