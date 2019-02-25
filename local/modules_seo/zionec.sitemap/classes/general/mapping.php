<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/zionec.sitemap/classes/general/database_provider.php");
set_time_limit(0);
if (!CModule::IncludeModule("iblock"))
{
  return;
}

/**
 * Class for generate valid site map.
 * It consist all method, which can help do it.
 */
class SiteMapGen
{
  static $SITE_SERVER_NAME;
  static $time;
  static $mod;
  static $gzip;
  static $priority;
  static $status;
  static $count;
  static $freq;
  static $protocol;
  static $links = array();
  static $FC = array();

  /**
   * Get information about sites in the system
   *
   * @return array - sites - list of sites, count - count of sites
   */
  static public function getSiteInfo()
  {
    $by = "sort";
    $order = "desc";
    $oSites = CSite::GetList(
        $by,
        $order,
        Array()
    );
    $aSites = array();
    $siteCount = 0;
    while ($aSite = $oSites->Fetch())
    {
      $aDomain = explode("\n", $aSite['DOMAINS']);
      $aSites[$aSite['LID']] = array(
          'NAME' => $aSite['NAME'],
          'DOMAIN' => trim($aDomain[0]),
          'DIR' => $aSite['ABS_DOC_ROOT'] . ((strlen($aSite['DIR']) > 1) ? $aSite['DIR'] : '')
      );
      $siteCount++;
    }

    return array(
        'sites' => $aSites,
        'count' => $siteCount
    );
  }

  /**
   * Set default settings
   */
  static public function init()
  {
    self::$time = 100000000000000;
    self::$mod = 0;
    self::$priority = 0;
    self::$gzip = 0;
    self::$protocol = 0;
    self::$freq = 'none';

    $provider = new SiteMapDb();
    $rsSelect = $provider->query('b_sitemap_generation', array('*'));
    while ($arResult = $rsSelect->Fetch())
    {
      if ($arResult['NAME'] == 'TIME')
      {
        if ($arResult['VALUE'] == 0)
        {
          self::$time = 100000000000000;
        }
        else
        {
          self::$time = $arResult['VALUE'] * (60 * 60 * 24);
        }
      }
      if ($arResult['NAME'] == 'MOD')
      {
        self::$mod = $arResult['VALUE'];
      }

      if ($arResult['NAME'] == 'PRIORITY')
      {
        self::$priority = $arResult['VALUE'];
      }

      if ($arResult['NAME'] == 'FREQ')
      {
        self::$freq = $arResult['VALUE'];
      }

      if ($arResult['NAME'] == 'GZIP')
      {
        self::$gzip = $arResult['VALUE'];
      }

      if ($arResult['NAME'] == 'PROTOCOL')
      {
        self::$protocol = json_decode($arResult['VALUE'], true);
      }
    }
  }

  /**
   * Get a protocol, which enabled
   *
   * @param string $site
   *
   * @return string
   */
  static public function isHTTPS($site = '')
  {
    if (empty(self::$protocol[$site]))
    {
      return (CMain::IsHTTPS()) ? 'https://' : 'http://';
    }
    else
    {
      return (self::$protocol[$site] == 1) ? 'http://' : 'https://';
    }
  }

  /**
   * Get time of editing file
   *
   * @param $str - root folder
   *
   * @return string
   */
  static public function staticTimestamp($str)
  {
    return date('c', fileatime($str . '/index.php'));
  }

  /**
   * Replace current SERVER_NAME on self::$SITE_SERVER_NAME
   *
   * @param string $site
   * @param        $str         - check string
   * @param mixed  $server_name - server root
   * @param bool   $protocol    - true - is a https
   *
   * @return string
   */
  static public function deleteRoot($site = '', $str, $server_name = false, $protocol = false)
  {
    if (!$server_name)
    {
      return -5;
    }

    if (!$protocol)
    {
      return str_replace($server_name, self::$SITE_SERVER_NAME, $str);
    }
    else
    {
      return self::isHTTPS($site) . str_replace($server_name, self::$SITE_SERVER_NAME, $str);
    }
  }

  /**
   * Return a timestamp from bitrix presentation if the time
   *
   * @param string - bitrix time
   *
   * @return string
   */
  static public function getTimestamp($timestamp)
  {
    $a = date_parse_from_format('d.m.Y G:i:s', $timestamp);
    $timestamp = mktime($a['hour'], $a['minute'], $a['second'], $a['month'], $a['day'], $a['year']);
    return $timestamp;
  }

  /**
   * Return a time by "c" format
   *
   * @param string - bitrix time
   *
   * @return string
   */
  static public function bitrixToTimestamp($b_time)
  {
    return date('c', $b_time);
  }

  /**
   * Return a $FC count
   *
   * @param array $FC
   *
   * @return int
   */
  static public function getTotalCount($FC = array())
  {
    $all = 0;
    if (empty($FC))
    {
      return $all;
    }
    foreach ($FC as $site => $value)
      $all += $value;

    return $all;
  }

  static public function setTestLog()
  {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/test.log'))
    {
      file_put_contents(
          $_SERVER['DOCUMENT_ROOT'] . '/zionec_sitemap/test.log',
          print_r(self::$links, 1)
      );
      return -1;
    }
  }

  static public function write($aSites = array())
  {
    /* record */
    $record_limit = 50000;
    self::$count = self::getTotalCount(self::$FC);

    /* generic for every site own sitemap */
    foreach ($aSites['sites'] as $site => $aSite)
    {
      $row = '';
      if (empty($aSite))
      {
        continue;
      }
      if (empty(self::$links[$site]))
      {
        continue;
      }
      self::$SITE_SERVER_NAME = $aSite['DOMAIN'];
      if (self::$count < $record_limit)
      {
        /* one sitemap */
        if (self::$gzip == 1)
        {
          $doc = 'sitemap.xml.gz';
        }
        else
        {
          $doc = 'sitemap.xml';
        }

        $head_index = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
        if (self::$gzip == 1)
        {
          $fp = gzopen($aSite['DIR'] . '/' . $doc, 'w9');
          gzwrite($fp, $head_index);
        }
        else
        {
          $fp = fopen($aSite['DIR'] . '/' . $doc, 'w+');
          fwrite($fp, $head_index);
        }

        foreach (self::$links[$site] as $key => $value)
        {
          $row .= "<url><loc>" . $value['URL'] . "</loc>";
          /* lastmod */
          if (isset($value['TIME']) && $value['TIME'] != 0)
          {
            $row .= "<lastmod>" . $value['TIME'] . "</lastmod>";
          }

          /* priority */
          if (isset($value['PRIORITY']) && $value['PRIORITY'] != 0)
          {
            $row .= "<priority>" . $value['PRIORITY'] . "</priority>";
          }

          /* freq */
          if (!empty($value['FREQ']) && $value['FREQ'] != 'none' && $value['FREQ'] != '0')
          {
            $row .= "<changefreq>" . $value['FREQ'] . "</changefreq>";
          }

          if (SiteMapGen::$gzip == 1)
          {
            gzwrite($fp, $row . "</url>");
          }
          else
          {
            fwrite($fp, $row . "</url>");
          }

          $row = '';
        }
        $row = "</urlset>";
        if (self::$gzip == 1)
        {
          gzwrite($fp, $row);
        }
        else
        {
          fwrite($fp, $row);
        }

        fclose($fp);
      }
      else
      {
        /* generation any count of sitemap */
        $full = false;
        $begin = true;
        $lastEl = 0;
        $listOfXML = 'sitemap_index.xml';
        $countOfXML = 0;

        if (self::$gzip == 1)
        {
          $doc = 'sitemap_' . $countOfXML . '.xml.gz';
        }
        else
        {
          $doc = 'sitemap_' . $countOfXML . '.xml';
        }

        $head_index = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $head_file = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach (self::$links[$site] as $key => $value)
        {
          if ($begin)
          {
            if (self::$gzip == 1)
            {
              $fp = gzopen($aSite['DIR'] . '/' . $doc, 'w9');
              gzwrite($fp, $head_file);
            }
            else
            {
              $fp = fopen($aSite['DIR'] . '/' . $doc, 'w+');
              fwrite($fp, $head_file);
            }
            $index = fopen($aSite['DIR'] . '/' . $listOfXML, 'w+');
            fwrite($index, $head_index);
            $row = '<sitemap><loc>' . self::isHTTPS($key) . self::$SITE_SERVER_NAME . '/' . $doc . '</loc><lastmod>' . self::bitrixToTimestamp(time()) . '</lastmod></sitemap>';
            fwrite($index, $row);
            $begin = false;
          }
          /* file is full */
          if ($full)
          {
            $endOfFile = '</urlset>';
            if (self::$gzip == 1)
            {
              gzwrite($fp, $endOfFile);
              gzclose($fp);
              $doc = 'sitemap_' . $countOfXML . '.xml.gz';
              $fp = gzopen($aSite['DIR'] . '/' . $doc, 'w9');
              gzwrite($fp, $head_file);
            }
            else
            {
              fwrite($fp, $endOfFile);
              fclose($fp);
              $doc = 'sitemap_' . $countOfXML . '.xml';
              $fp = fopen($aSite['DIR'] . '/' . $doc, 'w+');
              fwrite($fp, $head_file);
            }

            $row = '<sitemap><loc>' . self::isHTTPS($key) . self::$SITE_SERVER_NAME . '/' . $doc . '</loc><lastmod>' . self::bitrixToTimestamp(time()) . '</lastmod></sitemap>';
            fwrite($index, $row);
            $full = false;
          }
          if ($lastEl < $record_limit)
          {
            /* lastmod */
            if (!$value['TIME'])
            {
              $lastmod = '';
            }
            else
            {
              $lastmod = '<lastmod>' . $value['TIME'] . "</lastmod>";
            }

            /* priority */
            if (!$value['PRIORITY'])
            {
              $priority = '';
            }
            else
            {
              $priority = '<priority>' . $value['PRIORITY'] . "</priority>";
            }

            /*freq */
            if (empty($value['FREQ']) && $value['FREQ'] == 'none' || $value['FREQ'] == '0')
            {
              $freq = '';
            }
            else
            {
              $freq = "<changefreq>" . $value['FREQ'] . "</changefreq>";
            }


            $wr = '<url><loc>' . $value['URL'] . '</loc>' . $lastmod . $priority . $freq . '</url>';
            if (self::$gzip == 1)
            {
              gzwrite($fp, $wr);
            }
            else
            {
              fwrite($fp, $wr);
            }

            $lastEl++;
          }
          else
          {
            $lastEl = '';
            $full = true;
            $countOfXML++;
          }
        }
        $endOfIndex = '</sitemapindex>';
        fwrite($index, $endOfIndex);
        fclose($index);
        if (!$full)
        {
          $endOfFile = '</urlset>';
          if (self::$gzip == 1)
          {
            gzwrite($fp, $endOfFile);
            gzclose($fp);
          }
          else
          {
            fwrite($fp, $endOfFile);
            fclose($fp);
          }
        }
      }
    }
  }

  /**
   * Generate a sitemap
   */
  static public function Generate()
  {
    SiteMapGen::init();
    $aSites = SiteMapGen::getSiteInfo();
    foreach ($aSites['sites'] as $site => $aSite)
    {
      if (empty($aSite['DOMAIN']))
      {
        return -2;
      }
    }

    foreach ($aSites['sites'] as $site => $aSite)
    {
      if (empty($aSite))
      {
        continue;
      }
      self::$FC[$site] = 0;
      self::$SITE_SERVER_NAME = $aSite['DOMAIN'];

      /* get static files for every site */
      getContent::getStatic4Site($site, $aSite);

      /* get iblocks for every site */
      getContent::getIblocks4Site($site);

      /* for main */
      getContent::getData4Main(self::$SITE_SERVER_NAME, $site);
    }

    /* test mode */
    if (self::setTestLog() == -1)
    {
      return -1;
    }

    /* record */
    self::write($aSites);

    return true;
  }
}