<?php

    /**
     * @author Gennadiy Hatuntsev
     * @package catalog.menu
     *
     * @var array $arResult
     */

    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
        die();
    }

?>
<div class="firstLvl">
    <ul>
<?php
    $level = 1;
    foreach ($arResult[$level] as $id1 => $s1) {
?>
        <li>
            <a href="<?php echo $s1["SECTION_PAGE_URL"]; ?>"><span><?php echo $s1["NAME"]; ?></span></a>
<?php
        if (isset($arResult[$level + 1][$id1])) {
            $level++;
            echo '<ul>';
            foreach ($arResult[$level][$id1] as $id2 => $s2) {
?>
                    <li>
                        <a href="<?php echo $s2["SECTION_PAGE_URL"]; ?>"><span><?php echo $s2["NAME"]; ?></span></a>
<?php
                if (isset($arResult[$level + 1][$id2])) {
                    $level++;
                    echo '<ul>';
                    foreach ($arResult[$level][$id2] as $id3 => $s3) {
?>                              <li>
                                    <a href="<?php echo $s3["SECTION_PAGE_URL"]; ?>"><span><?php echo $s3["NAME"]; ?></span></a>
                                </li>
<?php
                    }
                    echo '</ul>';
                    $level--;
                }
?>
                    </li>
<?php
            }
            echo '</ul>';
            $level--;
        }
?>
        </li>
<?php
    }
?>
    </ul>
</div>