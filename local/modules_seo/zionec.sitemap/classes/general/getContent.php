<?php

/**
 * Consists methods that helps you to give static folder or iblock lists by site.
 * It also contains helper methods to obtain information that was in not be directly used in the generation of sitemaps
 */
class getContent
{
  public static $sectionsTimestamp = array();

  public static $sectionTree = array();

  public static $staticTable = 'b_sitemap_property';
  /**
   * This variable consist a files tree of file, that not included in sitemap setting.
   * If you install module, this variable will be consist all file tree of the file,
   * which approach for bitrix. (Folder, which created with help of bitrix).
   * Key of array will be a site id, and value:
   * value - is a folder
   * name - name of a section
   * path - path for a folder
   */
  public static $staticFolders = array();

  public static function setSectionTimestamp($timestamp = '', $id = '')
  {
    if (!empty(self::$sectionsTimestamp[$id]))
    {
      if (self::$sectionsTimestamp[$id] < $timestamp)
      {
        if (!empty(self::$sectionTree[$id]))
        {
          for ($i = 0; $i < $ic = count(self::$sectionTree[$id]); $i++)
            self::$sectionsTimestamp[self::$sectionTree[$id][$i]] = $timestamp;
        }
        self::$sectionsTimestamp[$id] = $timestamp;
      }
    }
    else
    {
      $rsNav = CIBlockSection::GetNavChain(false, $id);
      while ($arNav = $rsNav->Fetch())
      {
        self::$sectionTree[$id][] = $arNav['ID'];
        self::$sectionsTimestamp[$arNav['ID']] = $timestamp;
      }
    }
  }

  /**
   * Get tree of bitrix folder, that cantains a .section file.
   * This file is a needed folder for sitemap and they are is a logical part of sitemap tree
   *
   * @param             $folders string - folder, where will be search
   * @param bool|string $site    string - id of a site. Is required
   *
   * @return array|int
   */
  static public function showTree($folders, $site = false)
  {
    if (!$site)
    {
      return -6;
    }
    global $DB;
    $files = scandir($folders);

    if (in_array('index.php', $files) && in_array('.section.php', $files))
    {
      for ($u = 0; $u < $t = count($files); $u++)
      {
        if (($files[$u] == '.') || ($files[$u] == '..'))
        {
          continue;
        }
        else
        {

          if (is_dir($folders . '/' . $files[$u]))
          {

            $files__ = scandir($folders . '/' . $files[$u]);
            if (in_array('index.php', $files__) && in_array('.section.php', $files__))
            {
              $fp = fopen($folders . '/' . $files[$u] . '/.section.php', 'r');
              if ($fp)
              {
                while (($buffer = fgets($fp)) !== false)
                  if (preg_match("/\WsSectionName\s=\s\"(.*?)\"/", $buffer, $matches))
                  {
                    break;
                  }
              }
              $resSelect = $DB->Query("SELECT `ID` FROM `b_sitemap_property` WHERE `TYPE` = '0' AND `CODE` = '" . $folders . "/" . $files[$u] . "/" . "' AND `PATH` = '" . $files[$u] . "' LIMIT 1", false)->Fetch();
              if (empty($resSelect['ID']))
              {
                self::$staticFolders[$site][$folders . ';' . $files[$u]]['value'] = $files[$u];
                self::$staticFolders[$site][$folders . ';' . $files[$u]]['name'] = $matches[1];
                self::$staticFolders[$site][$folders . ';' . $files[$u]]['path'] = $folders . '/' . $files[$u] . '/';
              }
            }
            self::showTree($folders . '/' . $files[$u], $site);
          }
        }
      }
    }
  }

  /**
   * Save all static page in database
   *
   * @return array
   */
  static public function saveStatic()
  {
    global $DB;
    $post = $_POST;
    $p = count($post['path']);
    $f = count($post['folder']);
    $n = count($post['name']);
    $s = count($post['site']);
    $array = array();

    if (($p != $f) && ($p != $n))
    {
      return $array['error']['text'] = 1;
    }

    for ($p = 0; $p < $f; $p++)
    {
      if (LANG_CHARSET == 'windows-1251')
      {
        $resInsert = $DB->Query("INSERT INTO `b_sitemap_property` (`CODE`, `NAME`, `PATH`,`SIT`) VALUES ('" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $post['folder'][$p])) . "', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $post['name'][$p])) . "', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $post['path'][$p])) . "', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $post['site'][$p])) . "')", false);
      }
      else
      {
        $resInsert = $DB->Query("INSERT INTO `b_sitemap_property` (`CODE`, `NAME`, `PATH`,`SIT`) VALUES ('" . $DB->ForSql($post['folder'][$p]) . "', '" . $DB->ForSql($post['name'][$p]) . "', '" . $DB->ForSql($post['path'][$p]) . "', '" . $DB->ForSql($post['site'][$p]) . "')", false);
      }
      if (!$resInsert)
      {
        $array['error']['count']++;
      }
      else
      {
        $array['success']['count']++;
      }
    }

    return $array;
  }

  /**
   * Save params for static file (lastmod, priority, freqency)
   *
   * @return array
   */
  static public function saveParamsStaticFile()
  {
    global $DB;
    $arUpdateID = array();
    $array = array();

    for ($r = 0; $r < $cid = count($_POST['id']); $r++)
      $arUpdateID[] = $_POST['id'][$r];

    $slqSelectAllStatic = $DB->Query("SELECT `ID` FROM `b_sitemap_property` WHERE `TYPE` = '0'", false);
    while ($arSelectAllSTatic = $slqSelectAllStatic->Fetch())
    {
      if (!$keyOfArray = array_keys($arUpdateID, $arSelectAllSTatic['ID']))
      {
        $DB->Query("DELETE FROM `b_sitemap_property` WHERE `TYPE` = '0' AND `ID` = '" . $DB->ForSql($arSelectAllSTatic['ID']) . "'", false);
      }
    }

    for ($x = 0; $x < count($_POST['id']); $x++)
    {
      if (LANG_CHARSET == 'windows-1251')
      {
        $resInsert = $DB->Query("UPDATE `b_sitemap_property` SET `CODE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['folder'][$x])) . "', `NAME` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['name'][$x])) . "', `PATH` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['path'][$x])) . "', `MOD` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['mod'][$x])) . "', `PRIORITY` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['priority'][$x])) . "', `FREQ` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['freq'][$x])) . "', `SIT`='" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['site'][$x])) . "' WHERE `ID` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['id'][$x])) . "'", false);
      }
      else
      {
        $resInsert = $DB->Query("UPDATE `b_sitemap_property` SET `CODE` = '" . $DB->ForSql($_POST['folder'][$x]) . "', `NAME` = '" . $DB->ForSql($_POST['name'][$x]) . "', `PATH` = '" . $DB->ForSql($_POST['path'][$x]) . "', `MOD` = '" . $DB->ForSql($_POST['mod'][$x]) . "', `PRIORITY` = '" . $DB->ForSql($_POST['priority'][$x]) . "', `FREQ` = '" . $DB->ForSql($_POST['freq'][$x]) . "', `SIT`='" . $DB->ForSql($_POST['site'][$x]) . "' WHERE `ID` = '" . $DB->ForSql($_POST['id'][$x]) . "'", false);
      }
      if (!$resInsert)
      {
        $array['error']['count']++;
      }
      else
      {
        $array['succes']['count']++;
      }
    }

    return $array;
  }

  /**
   * Get one static file record or iblock record.
   * Type give in $_POST['type'] field.
   *
   * @return array
   */
  static public function getStaticOne()
  {
    global $DB;
    $result = $DB->Query("SELECT `ID` FROM `b_sitemap_property` WHERE `TYPE` = '" . $DB->ForSql($_POST['type']) . "' LIMIT 1", false)->Fetch();

    return $result;
  }

  /**
   * Get all static file records.
   *
   * @return bool|CDBResult
   */
  static public function getStaticAll()
  {
    global $DB;
    $result = $DB->Query("SELECT * FROM `b_sitemap_property` WHERE `TYPE` = '0' ORDER BY `SIT`", false);
    return $result;
  }

  /**
   * Get list of site and iblock by site
   *
   * @return array
   */
  static public function getIblockBySite()
  {
    CModule::IncludeModule('iblock');
    global $DB;
    $selIblock = CIBlock::GetList(
        array('LID' => 'DESC'),
        array('ACTIVE' => 'Y'),
        false
    );
    $arrayIblock = array();
    $arrayInfoIblock = array();
    while ($arIblock = $selIblock->Fetch())
    {
      $arrayIblock[] = $arIblock['ID'];
      $arrayInfoIblock[$arIblock['ID']] = $arIblock;
    }

    $rsResult = $DB->Query('SELECT * FROM `b_iblock_site` WHERE IBLOCK_ID IN (' . implode(',', $arrayIblock) . ')');
    unset($arrayIblock);
    $resultArray = array();
    while ($arResult = $rsResult->Fetch())
    {
      $arFiled = array(
          'ID' => $arResult['IBLOCK_ID'],
          'CODE' => $arrayInfoIblock[$arResult['IBLOCK_ID']]['CODE'],
          'NAME' => $arrayInfoIblock[$arResult['IBLOCK_ID']]['NAME']
      );
      $resultArray[$arResult['SITE_ID']][] = $arFiled;
    }

    return $resultArray;
  }

  /**
   * Get iblock property in sitemap settings table
   *
   * @param $iblock_id
   * @param $iblock_code
   *
   * @return array
   */
  static public function getIblockByOne($iblock_id, $iblock_code)
  {
    global $DB;
    $resSelect = $DB->Query("SELECT `ID` FROM `b_sitemap_property` WHERE `CODE` = '" . $DB->ForSql($iblock_id) . "' AND `PATH` = '" . $DB->ForSql($iblock_code) . "' LIMIT 1", false)->Fetch();

    return $resSelect;
  }

  /**
   * Save all iblock in database
   *
   * @return array
   */
  static public function saveIblock()
  {
    global $DB;
    $array = array();
    for ($p = 0; $p < $f = count($_POST['id']); $p++)
    {
      if (LANG_CHARSET == 'windows-1251')
      {
        $resInsert = $DB->Query("INSERT INTO `b_sitemap_property` (`CODE`,`NAME`,`PATH`,`TYPE`,`SIT`) VALUES ('" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['id'][$p])) . "', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['name'][$p])) . "', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['path'][$p])) . "', '1', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['site'][$p])) . "')", false);
      }
      else
      {
        $resInsert = $DB->Query("INSERT INTO `b_sitemap_property` (`CODE`,`NAME`,`PATH`,`TYPE`,`SIT`) VALUES ('" . $DB->ForSql($_POST['id'][$p]) . "', '" . $DB->ForSql($_POST['name'][$p]) . "', '" . $DB->ForSql($_POST['path'][$p]) . "', '1', '" . $DB->ForSql($_POST['site'][$p]) . "')", false);
      }

      if (!$resInsert)
      {
        $array['error']['count']++;
      }
      else
      {
        $array['succes']['count']++;
      }
    }

    return $array;
  }

  /**
   * Get all iblock records.
   *
   * @return bool|CDBResult
   */
  static public function getIblockAll()
  {
    global $DB;
    $result = $DB->Query("SELECT * FROM b_sitemap_property WHERE TYPE = 1", false);
    return $result;
  }

  /**
   * Save params for iblock (lastmod, priority, freqency)
   *
   * @return array
   */
  static public function saveParamsIblock()
  {
    global $DB;
    $arUpdateID = array();
    $array = array();

    for ($r = 0; $r < $cid = count($_POST['id']); $r++)
      $arUpdateID[] = $_POST['id'][$r];

    $slqSelectAllIblock = $DB->Query("SELECT `ID` FROM `b_sitemap_property` WHERE `TYPE`= '1'", false);
    while ($arSelectAllIblock = $slqSelectAllIblock->Fetch())
    {
      if (!$keyOfArray = array_keys($arUpdateID, $arSelectAllIblock['ID']))
      {
        $sqlDelete = $DB->Query("DELETE FROM `b_sitemap_property` WHERE `TYPE` = '1' AND `ID` = '" . $arSelectAllIblock['ID'] . "'", false);
      }
    }

    for ($x = 0; $x < count($_POST['id']); $x++)
    {
      if (LANG_CHARSET == 'windows-1251')
      {
        $resInsert = $DB->Query("UPDATE `b_sitemap_property` SET `CODE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['folder'][$x])) . "', `NAME` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['name'][$x])) . "', `PATH` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['path'][$x])) . "', `MOD` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['mod'][$x])) . "', `PRIORITY` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['priority'][$x])) . "', `FREQ` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['freq'][$x])) . "', `SIT` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['site'][$x])) . "' WHERE `ID` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['id'][$x])) . "'", false);
      }
      else
      {
        $resInsert = $DB->Query("UPDATE `b_sitemap_property` SET `CODE` = '" . $DB->ForSql($_POST['folder'][$x]) . "', `NAME` = '" . $DB->ForSql($_POST['name'][$x]) . "', `PATH` = '" . $DB->ForSql($_POST['path'][$x]) . "', `MOD` = '" . $DB->ForSql($_POST['mod'][$x]) . "', `PRIORITY` = '" . $DB->ForSql($_POST['priority'][$x]) . "', `FREQ` = '" . $DB->ForSql($_POST['freq'][$x]) . "', `SIT` = '" . $DB->ForSql($_POST['site'][$x]) . "' WHERE `ID` = '" . $DB->ForSql($_POST['id'][$x]) . "'", false);
      }
      if (!$resInsert)
      {
        $array['error']['count']++;
      }
      else
      {
        $array['succes']['count']++;
      }
    }

    return $array;
  }

  /**
   * Save protocols by site.
   *
   * @return string
   */
  static public function saveProtocol()
  {
    $by = "sort";
    $order = "desc";
    $protocol = array();
    $rsSites = CSite::GetList(
        $by,
        $order,
        Array()
    );

    while ($arSite = $rsSites->Fetch())
    {
      if (!empty($_POST['protocol_' . $arSite['LID']]))
      {
        $protocol[$arSite['LID']] = $_POST['protocol_' . $arSite['LID']];
      }
    }

    return json_encode($protocol);
  }

  /**
   * Save module property and settings
   */
  static public function saveGenericProperty()
  {
    global $DB;
    $resCheck = $DB->Query("SELECT `NAME` FROM `b_sitemap_generation`", false)->Fetch();
    if (empty($resCheck['NAME']))
    {
      if (LANG_CHARSET == 'windows-1251')
      {
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('TIME', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['time'])) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('MOD', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['mod'])) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('PRIORITY', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['priority'])) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('FREQ', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['frequency'])) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('GZIP', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['gzip'])) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('PROTOCOL', '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', self::saveProtocol())) . "')", false);
      }
      else
      {
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('TIME', '" . $DB->ForSql($_POST['time']) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('MOD', '" . $DB->ForSql($_POST['mod']) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('PRIORITY', '" . $DB->ForSql($_POST['priority']) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('FREQ', '" . $DB->ForSql($_POST['frequency']) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('GZIP', '" . $DB->ForSql($_POST['gzip']) . "')", false);
        $DB->Query("INSERT INTO `b_sitemap_generation` (`NAME`, `VALUE`) VALUES ('PROTOCOL', '" . $DB->ForSql(self::saveProtocol()) . "')", false);
      }
    }
    else
    {
      if (LANG_CHARSET == 'windows-1251')
      {
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['time'])) . "' WHERE `NAME` = 'TIME'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['mod'])) . "' WHERE `NAME` = 'MOD'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['priority'])) . "' WHERE `NAME` = 'PRIORITY'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['frequency'])) . "' WHERE `NAME` = 'FREQ'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', $_POST['gzip'])) . "' WHERE `NAME` = 'GZIP'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql(iconv('UTF-8', 'windows-1251', self::saveProtocol())) . "' WHERE `NAME` = 'PROTOCOL'", false);
      }
      else
      {
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql($_POST['time']) . "' WHERE `NAME` = 'TIME'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql($_POST['mod']) . "' WHERE `NAME` = 'MOD'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql($_POST['priority']) . "' WHERE `NAME` = 'PRIORITY'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql($_POST['frequency']) . "' WHERE `NAME` = 'FREQ'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql($_POST['gzip']) . "' WHERE `NAME` = 'GZIP'", false);
        $DB->Query("UPDATE `b_sitemap_generation` SET `VALUE` = '" . $DB->ForSql(self::saveProtocol()) . "' WHERE `NAME` = 'PROTOCOL'", false);
      }
    }
  }

  static public function getStatic4Site($site = '', $aSite = array())
  {
    $provider = new SiteMapDb();
    $rsData = $provider->query(
        self::$staticTable,
        ['*'],
        [
            [
                'TYPE',
                '=',
                '0'
            ],
            [
                'AND',
                'SIT',
                '=',
                $site
            ]
        ]
    );

    while ($arStatic = $rsData->Fetch())
    {
      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['URL'] = SiteMapGen::deleteRoot(
          $site,
          $arStatic['CODE'],
          $aSite['DIR'],
          true
      );
      if ($arStatic['MOD'] == 1)
      {
        SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['TIME'] = SiteMapGen::staticTimestamp($arStatic['CODE']);
      }

      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['CNT'] = SiteMapGen::$FC[$site];
      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['PRIORITY'] = $arStatic['PRIORITY'];
      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['FREQ'] = $arStatic['FREQ'];
      SiteMapGen::$FC[$site]++;
    }
  }

  static public function getIblocks4Site($site = '')
  {
    $provider = new SiteMapDb();
    $rsData = $provider->query(
        self::$staticTable,
        ['*'],
        [
            [
                'TYPE',
                '=',
                '1'
            ],
            [
                'AND',
                'SIT',
                '=',
                $site
            ]
        ]
    );

    while ($IBLOCKS = $rsData->Fetch())
    {
      $IBLOCK_ID = (int)$IBLOCKS['CODE'];
      $isEmpty = self::getSectionsByIblock($IBLOCK_ID, $site);
      self::getElementsByIblock($IBLOCK_ID, $site, $isEmpty);
    }
  }

  static public function getElementsByIblock($iblock = '', $site, $isEmpty)
  {
    CModule::IncludeModule('iblock');
    // собираем массив для фильтра
    $filter = array(
      'IBLOCK_ID' => $iblock,
      'ACTIVE' => 'Y',
      'ACTIVE_DATE' => 'Y',
    );
    if (false === $isEmpty)
    {
      $filter['SECTION_GLOBAL_ACTIVE'] = 'Y';
    }
    $rsElement = CIBlockElement::GetList(
        array(),
        $filter,
        false,
        false,
        array(
            'ID',
            'TIMESTAMP_X',
            'IBLOCK_ID',
            'DETAIL_PAGE_URL',
            'IBLOCK_SECTION_ID'
        )
    );

    while ($arElement = $rsElement->GetNext())
    {
      $timestamp = SiteMapGen::getTimestamp($arElement['TIMESTAMP_X']);
      self::setSectionTimestamp($timestamp, $arElement['IBLOCK_SECTION_ID']);
      if ((time() - $timestamp) < SiteMapGen::$time)
      {
        $arTime = SiteMapGen::bitrixToTimestamp($timestamp);
        SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['URL'] = SiteMapGen::isHTTPS($site) . SiteMapGen::$SITE_SERVER_NAME . $arElement['DETAIL_PAGE_URL'];
        $arAdditional = self::getIblockPropertyBySite($iblock, $site);
        if ($arAdditional['MOD'] == '1'
            || $arAdditional['MOD'] == '2'
        )
        {
          SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['TIME'] = $arTime;
        }

        SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['CNT'] = SiteMapGen::$FC[$site];
        SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['PRIORITY'] = $arAdditional['PRIORITY'];
        SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['FREQ'] = $arAdditional['FREQ'];
        SiteMapGen::$FC[$site]++;
      }
    }
  }

  static public function getIblockPropertyBySite($iblock = '', $site = '')
  {
    $provider = new SiteMapDb();
    $rsData = $provider->query(
        self::$staticTable,
        [
            'MOD',
            'PRIORITY',
            'FREQ'
        ],
        [
            [
                'TYPE',
                '=',
                '1'
            ],
            [
                'AND',
                'SIT',
                '=',
                $site
            ],
            [
                'AND',
                'CODE',
                '=',
                $iblock
            ]
        ]
    );
    if ($arProperty = $rsData->Fetch())
    {
      return $arProperty;
    }
    else
    {
      return array();
    }
  }

  static public function getSectionsByIblock($iblock = '', $site)
  {
    CModule::IncludeModule('iblock');

    $rsSections = CIBlockSection::GetList(
        array('ID' => 'ASC'),
        array(
            'IBLOCK_ID' => $iblock,
            'ACTIVE' => 'Y',
            'GLOBAL_ACTIVE' => 'Y'
        ),
        false,
        array(
            'IBLOCK_ID',
            'ID',
            'SECTION_PAGE_URL',
            'TIMESTAMP_X'
        )
    );

    $isEmpty = true;

    $arAdditional = self::getIblockPropertyBySite($iblock, $site);
    while ($arSection = $rsSections->GetNext())
    {
      if ($arAdditional['MOD'] == '1')
      {
        // yes
        $timestamp = SiteMapGen::getTimestamp($arSection['TIMESTAMP_X']);
        if ((time() - $timestamp) < SiteMapGen::$time)
        {
          SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['TIME'] = SiteMapGen::bitrixToTimestamp($timestamp);
        }
      }
      else if ($arAdditional['MOD'] == '2')
      {
        // element
        if (!empty(self::$sectionsTimestamp[$arSection['ID']]))
        {
          SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['TIME'] = SiteMapGen::bitrixToTimestamp(self::$sectionsTimestamp[$arSection['ID']]);
        }
      }

      $isEmpty = false;

      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['URL'] = SiteMapGen::isHTTPS($site) . SiteMapGen::$SITE_SERVER_NAME . $arSection["SECTION_PAGE_URL"];
      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['PRIORITY'] = $arAdditional['PRIORITY'];
      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['FREQ'] = $arAdditional['FREQ'];
      SiteMapGen::$links[$site][SiteMapGen::$FC[$site]]['CNT'] = SiteMapGen::$FC[$site];
      SiteMapGen::$FC[$site]++;
    }

    return $isEmpty;
  }

  static public function getData4Main($serverName = '', $site = '')
  {
    $main['URL'] = SiteMapGen::isHTTPS($site) . $serverName . '/';
    if (SiteMapGen::$mod > 0)
    {
      $main['TIME'] = SiteMapGen::bitrixToTimestamp(time());
    }

    if (SiteMapGen::$priority > 0)
    {
      $main['PRIORITY'] = SiteMapGen::$priority;
    }

    if (SiteMapGen::$freq != 'none')
    {
      $main['FREQ'] = SiteMapGen::$freq;
    }

    array_unshift(SiteMapGen::$links[$site], $main);
  }

  static public function showIblockView($aSite = array())
  {
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/zionec.sitemap/lang/ru/admin/sitemap_edit.php');
    if (empty($aSite))
    {
      $aSite = SiteMapGen::getSiteInfo();
    }

    $rsIblockList = getContent::getIblockAll();
    $iblockList = array();
    while ($arIblockList = $rsIblockList->Fetch())
      $iblockList[$arIblockList['SIT']][] = $arIblockList;

    if (!empty($iblockList))
    { ?>
        <form name="form_3" id="form_3" style="position: relative">
            <table>
                <thead>
                <th><?= GetMessage('PARAM_0') ?></th>
                <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 4)"
                    onmouseout="popup.hideTooltip('js-tooltip')"><?= $MESS['PARAM_1'] ?></th>
                <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 2)"
                    onmouseout="popup.hideTooltip('js-tooltip')"><?= $MESS['PARAM_2'] ?></th>
                <th style="cursor: pointer" onmouseover="popup.showTooltip(this, 3)"
                    onmouseout="popup.hideTooltip('js-tooltip')"><?= $MESS['PARAM_3'] ?></th>
                <th><?= $MESS['PARAM_4'] ?></th>
                </thead>
                <tbody>
                <?php foreach ($iblockList as $siteId => $iblockInfo)
                { ?>
                  <?php if (!empty($aSite['sites'][$siteId]))
                { ?>
                    <tr>
                        <td colspan="5">
                            <h3 class="admin-h3"><?= $aSite['sites'][$siteId]['NAME'] ?> [<?= $siteId ?>]</h3>
                        </td>
                    </tr>
                <?php } ?>
                  <? for ($i = 0; $i < $ic = count($iblockInfo); $i++)
                { ?>
                    <tr>
                        <td style="width:300px;">
                            <div class="row-property"><?= $iblockInfo[$i]['NAME'] ?></div>
                            <input type="hidden" name="folder[]" value="<?= $iblockInfo[$i]['CODE'] ?>">
                            <input type="hidden" name="path[]" value="<?= $iblockInfo[$i]['PATH'] ?>">
                            <input type="hidden" name="id[]" value="<?= $iblockInfo[$i]['ID'] ?>">
                            <input type="hidden" name="name[]" value="<?= $iblockInfo[$i]['NAME'] ?>">
                            <input type="hidden" name="site[]" value="<?= $iblockInfo[$i]['SIT'] ?>">
                        </td>
                        <td>
                            <select class="custom-input" style="height: 37px;margin-top: 10px;width:130px"
                                    name="freq[]">
                                <option value="none" <?php echo(($iblockInfo[$i]['FREQ'] == 'none') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_1'] ?></option>
                                <option value="always" <?php echo(($iblockInfo[$i]['FREQ'] == 'always') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_2'] ?></option>
                                <option value="hourly" <?php echo(($iblockInfo[$i]['FREQ'] == 'hourly') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_3'] ?></option>
                                <option value="daily" <?php echo(($iblockInfo[$i]['FREQ'] == 'daily') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_4'] ?></option>
                                <option value="weekly" <?php echo(($iblockInfo[$i]['FREQ'] == 'weekly') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_5'] ?></option>
                                <option value="monthly" <?php echo(($iblockInfo[$i]['FREQ'] == 'monthly') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_6'] ?></option>
                                <option value="yearly" <?php echo(($iblockInfo[$i]['FREQ'] == 'yearly') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_7'] ?></option>
                                <option value="never" <?php echo(($iblockInfo[$i]['FREQ'] == 'never') ? 'selected="selected"' : '') ?>><?= $MESS['FREQ_8'] ?></option>
                            </select>
                        </td>
                        <td>
                            <select class="custom-input" style="height: 37px;margin-top: 10px;width:130px" name="mod[]">
                                <option value="0"><?= $MESS['NO'] ?></option>
                                <option
                                    <?= ($iblockInfo[$i]['MOD'] == 1) ? 'selected="selected" ' : ' '; ?>value="1"><?= $MESS['YES'] ?></option>
                                <option
                                    <?= ($iblockInfo[$i]['MOD'] == 2) ? 'selected="selected" ' : ' '; ?>value="2"><?= $MESS['ELEMENT'] ?></option>
                            </select>
                        </td>
                        <td>
                            <input class="custom-input" type="text" style="margin-top: 10px;width:70px"
                                   name="priority[]"
                                   value="<?= (!empty($iblockInfo[$i]['PRIORITY'])) ? $iblockInfo[$i]['PRIORITY'] : 0 ?>">
                        </td>
                        <td style="width:50px;">
                            <div onclick="popup.delete(this, ge('form_3'))" onmouseover="popup.showTooltip(this, 0)"
                                 onmouseout="popup.hideTooltip('js-tooltip')" class="row-property"
                                 style="padding: 0;text-align: center;background-color: #597DA3;color: #fff;z-index:1">
                                <span style="position:relative;font-size: 30.6px;z-index:2;display:block"><?= $MESS['CLOSE'] ?></span>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php } ?>
                <tr>
                    <td colspan="2">
                        <div onclick="popup.send('form_3', 'bitrix/admin/sitemap_ajax', 'save_params_iblock')"
                             onmouseover="popup.showTooltip(this, 1)" onmouseout="popup.hideTooltip('js-tooltip')"
                             class="row-property-button left-margin"><?= $MESS['SAVE'] ?></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    <?php }
  }

  /**
   * Проверка, установлен ли сайт в кодировке windows-1251.
   *
   * @return bool
   */
  static function is1251()
  {
      return ('windows-1251' === LANG_CHARSET) ? true : false;
  }

  /**
   * Возвращает декодированные данные массива.
   *
   * @param array $array
   *
   * @return mixed|string
   */
  static function jsonencode($array = array())
  {
      if (self::is1251())
      {
        try
        {
          return \Bitrix\Main\Web\Json::encode($array);
        }
        catch (\Bitrix\Main\ArgumentException $e)
        {
        }
      }
      else
      {
        try
        {
          return \Bitrix\Main\Web\Json::encode($array, JSON_UNESCAPED_UNICODE);
        }
        catch (\Bitrix\Main\ArgumentException $e)
        {
        }
      }

      return '';
  }
}