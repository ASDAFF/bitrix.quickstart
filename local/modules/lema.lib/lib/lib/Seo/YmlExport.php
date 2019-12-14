<?php

namespace Lema\Seo;

/**
 * Class Yml
 * @package Lema\Seo
 */
class YmlExport extends \Lema\Base\XmlExport
{
    /**
     * Show YML file
     *
     * @param array $params
     * @return mixed
     *
     * @access public
     */
    public function showData(array $params = array())
    {
        if(!empty($params['sendHeader']))
        {
            header('Content-type: text/xml; charset=' . SITE_CHARSET);
        }

        ?><<?php ?>?xml version="1.0" encoding="<?=SITE_CHARSET?>"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd">
        <yml_catalog date="<?=isset($this->time) ? date('Y-m-d H:i', $this->time) : date('Y-m-d H:i')?>">
            <shop>
                <name><?=$_SERVER['HTTP_HOST']?></name>
                <company><?=$_SERVER['HTTP_HOST']?></company>
                <url><?=$this->serverUrl?></url>
                <platform>1C-Bitrix</platform>
                <currencies>
                    <currency id="RUB" rate="1" />
                </currencies>
                <categories>
                    <?foreach($this->sections as $sectionId => $section):?>
                        <category id="<?=$sectionId?>"<?if(isset($section['parentId'])){?> parentId="<?=$section['parentId']?>"<?}?>><?=$section['name']?></category>
                    <?endforeach;?>
                </categories>
                <offers>
                    <?foreach($this->products as $id => $info):
                        $alternateDescr = null;
                        ?>

                        <offer id="<?=$id?>" available="<?=$info['ACTIVE'] == 'Y' && $info['CATALOG_AVAILABLE'] == 'Y' ? 'true' : 'false'?>">
                            <url><?=$this->serverUrl?><?=$info['DETAIL_PAGE_URL']?>/</url>
                            <picture><?=$info['PREVIEW_PICTURE']?></picture>
                            <name><?=htmlspecialcharsbx($info['NAME'])?></name>

                            <?foreach($params['fields'] as $name => $key):?>
                                <<?=$name;?>><?=isset($info[$key]) ? htmlspecialcharsbx($info[$key]) : null;?></<?=$name?>>
                            <?endforeach;?>

                            <categoryId><?=$info['IBLOCK_SECTION_ID']?></categoryId>

                            <?if(!empty($params['params'])):?>
                                <?foreach($params['params'] as $data):
                                    if(empty($info[$data[1]]))
                                        continue;
                                    $alternateDescr .= $data[0] . ': ' . $info[$data[1]] . ';' . PHP_EOL;
                                    ?>
                                    <param name="<?=$data[0];?>"<?if(isset($data['unit'])){?> unit="<?=$data['unit']?>"<?}?>><?=htmlspecialcharsbx($info[$data[1]]);?></param>
                                <?endforeach;?>
                            <?endif;?>

                            <?
                            $description = trim($info[(empty($info['DETAIL_TEXT']) ? 'PREVIEW' : 'DETAIL') . '_TEXT']);
                            if(empty($description))
                                $description = trim($alternateDescr);
                            ?>
                            <description><?=htmlspecialcharsbx($description);?></description>

                        </offer>

                    <?endforeach;?>
                </offers>
            </shop>
        </yml_catalog>
        <?php
    }

}