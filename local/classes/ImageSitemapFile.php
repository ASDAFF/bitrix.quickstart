<?php

use Bitrix\Main\IO\Path;
use Bitrix\Main\IO\File;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Text\Converter;
use Bitrix\Seo\SitemapIblock;
use Bitrix\Main\Loader;

class ImageSitemapFile
    extends File
{
    const XML_HEADER = '<?xml version="1.0" encoding="UTF-8"?>';

    const FILE_HEADER = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
    const FILE_FOOTER = '</urlset>';

    const ENTRY_TPL = '<url><loc>%s</loc>%s</url>';
    const ENTRY_IMAGE_TPL = '<image:image><image:loc>%s</image:loc></image:image>';


    const ENTRY_TPL_SEARCH = '<url><loc>%s</loc>';

    const XPATH_URL = '/urlset/url';

    const MAX_SIZE = 5000000;

    const FILE_EXT = '.xml';
    const FILE_PART_SUFFIX = '.part';

    protected $documentRoot;
    protected $settings = array();
    protected $parser = false;

    protected $siteRoot = '';
    protected $partFile = '';
    protected $partList = array();
    protected $part = 0;
    protected $partChanged = false;
    protected $footerClosed = false;

    protected $urlToSearch = '';
    protected $urlFound = false;


    protected $IblockID = 0;


    /**
     * тут делаем файл со списком картинок
     */
    public function makeSitemap ()
    {

        Loader::includeModule("iblock");

        if ($this->isExists()) {
            $this->delete();
        }

        $this->addHeader();

        $partUrl = $this->settings['PROTOCOL'] . '://' . \CBXPunycode::toASCII($this->settings['DOMAIN'], $e = null);

        $arSelect = Array(
            "ID",
            "NAME",
            "DETAIL_PICTURE",
            "PROPERTY_MORE_PHOTO",
            "DETAIL_PAGE_URL",
        );
        $arFilter = Array(
            "IBLOCK_ID"       => $this->IblockID,
            "ACTIVE"          => "Y",
            "!DETAIL_PICTURE" => false,
            "ACTIVE_DATE"     => "Y",
        );
        $res      = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            //картинки для конкретного элемента
            $arImages = array();

            $arImages[] = CFile::GetPath($arFields['DETAIL_PICTURE']);

            if ( ! is_null($arFields['PROPERTY_MORE_PHOTO_VALUE'])) {
                if (is_array($arFields['PROPERTY_MORE_PHOTO_VALUE'])) {

                    foreach ($arFields['PROPERTY_MORE_PHOTO_VALUE'] as $morePhoto) {
                        $arImages[] = CFile::GetPath($morePhoto);
                    }

                } else {
                    $arImages[] = CFile::GetPath($arFields['PROPERTY_MORE_PHOTO_VALUE']);
                }
            }

            // remove or replace SERVER_NAME
            $url = SitemapIblock::prepareUrlToReplace($arFields['DETAIL_PAGE_URL'], $this->settings['SITE_ID']);
            $url = \CIBlock::ReplaceDetailUrl($url, $arFields, false, "E");


            $imageAttr = '';
            foreach ($arImages as $image) {
                $imageAttr .= sprintf(self::ENTRY_IMAGE_TPL, Converter::getXmlConverter()->encode($partUrl . $image));
            }

            $this->putContents(
                sprintf(
                    self::ENTRY_TPL,
                    Converter::getXmlConverter()->encode($partUrl . $url),
                    $imageAttr
                ), self::APPEND
            );

            unset($arImages);

        }

        $this->addFooter();

    }

    /**
     * Adds header to the current sitemap file.
     *
     * @return void
     */
    public function addHeader ()
    {
        $this->partChanged = true;
        $this->putContents(self::XML_HEADER . self::FILE_HEADER);
    }

    /**
     * Adds footer to the current sitemap part
     *
     * @return void
     */
    public function addFooter ()
    {
        $this->putContents(self::FILE_FOOTER, self::APPEND);
        $this->footerClosed = true;
    }

    /**
     * Returns list of file parts.
     *
     * @return array
     */
    public function getNameList ()
    {
        return $this->isCurrentPartNotEmpty() ? array_merge($this->partList, array($this->getName())) : $this->partList;
    }

    /**
     * Returns if current sitemap part contains something besides header.
     *
     * @return bool
     */
    public function isCurrentPartNotEmpty ()
    {
        if ($this->isExists()) {
            $c = $this->getContents();

            return strlen($c) > 0 && $c != self::XML_HEADER . self::FILE_HEADER;
        }

        return false;
    }

    /**
     * Divide path to directory and filemname
     * @return array
     */
    public function getPathDirectory ()
    {
//		normalize slashes
        $siteRoot = Path::normalize($this->siteRoot);
        $fileName = $this->getName();
        $path     = Path::normalize($this->path);

        $directory = str_replace(array($siteRoot, $fileName), array('', ''), $path);

        return ltrim($directory, '/');
    }

    /**
     * Returns if the whole sitemap is empty (not only current part).
     *
     * @return bool
     */
    public function isNotEmpty ()
    {
        return (count($this->partList) > 0) || $this->isCurrentPartNotEmpty();
    }

    /**
     * Searches and removes entry to the existing and finished sitemap file
     *
     * Entry array keys
     * XML_LOC - loc field value
     * XML_LASTMOD - lastmod field value
     *
     * @param string $url Entry URL.
     *
     * @return string
     */
    public function removeEntry ($url)
    {
        $fileName = $this->partFile;
        $url      = $this->settings['PROTOCOL'] . '://' . \CBXPunycode::toASCII($this->settings['DOMAIN'],
                $e = null) . $url;
        $pattern  = sprintf(self::ENTRY_TPL_SEARCH, $url);

        while ($this->isExists()) {
            $c = $this->getContents();
            $p = strpos($c, $pattern);
            unset($c);

            if ($p !== false) {
                $fd = $this->open('r+');

                fseek($fd, intval($p));
                fwrite($fd, str_repeat(" ", strlen(sprintf(
                    self::ENTRY_TPL,
                    Converter::getXmlConverter()->encode($url),
                    Converter::getXmlConverter()->encode(date('c'))
                ))));
                fclose($fd);
                break;
            }

            if ( ! $this->isSplitNeeded()) {
                break;
            } else {
                $this->part++;
                $fileName = substr($fileName, 0,
                        -strlen(self::FILE_EXT)) . self::FILE_PART_SUFFIX . $this->part . substr($fileName,
                        -strlen(self::FILE_EXT));
                $this->reInit($fileName);
            }
        }

        return $fileName;
    }

    /**
     * Checks is it needed to create new part of sitemap file
     *
     * @return bool
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    protected function isSplitNeeded ()
    {
        return $this->isExists() && $this->getSize() >= self::MAX_SIZE;
    }

    /**
     * Reinitializes current object with new file name.
     *
     * @param string $fileName New file name.
     */
    protected function reInit ($fileName)
    {
        $this->__construct($fileName, $this->settings);
    }

    public function __construct ($fileName, $settings)
    {

        $fileName = str_replace(self::FILE_EXT, '_images' . self::FILE_EXT, $fileName);

        $this->IblockID = $settings['ITEM_ID'];

        $this->settings = array(
            'SITE_ID'  => $settings['SITE_ID'],
            'PROTOCOL' => $settings['PROTOCOL'] == 'https' ? 'https' : 'http',
            'DOMAIN'   => $settings['DOMAIN'],
        );

        $site = SiteTable::getRow(array("filter" => array("LID" => $this->settings['SITE_ID'])));

        $this->documentRoot = SiteTable::getDocumentRoot($this->settings['SITE_ID']);
        $this->footerClosed = false;

        $this->siteRoot = Path::combine(
            $this->documentRoot,
            $site['DIR']
        );

//		normalize slashes
        $fileName = Path::normalize($fileName);
        if (substr($fileName, -strlen(self::FILE_EXT)) != self::FILE_EXT) {
            $fileName .= self::FILE_EXT;
        }

        if ($this->partFile == '') {
            $this->partFile = $fileName;
        }

        $this->pathPhysical = null; // hack for object reconstuct during file splitting

        parent::__construct($this->siteRoot . '/' . $fileName, $this->settings['SITE_ID']);

        $this->partChanged = $this->isExists() && ! $this->isSplitNeeded();
    }

    /**
     * Adds new file entry to the current sitemap
     *
     * @param File $f File to add.
     *
     * @return void
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    public function addFileEntry (File $f)
    {
        if ($f->isExists() && ! $f->isSystem()) {
            $this->addEntry(array(
                'XML_LOC'     => $this->settings['PROTOCOL'] . '://' . \CBXPunycode::toASCII($this->settings['DOMAIN'],
                        $e = null) . $this->getFileUrl($f),
                'XML_LASTMOD' => date('c', $f->getModificationTime()),
            ));
        }
    }

    /**
     * Adds new entry to the current sitemap file
     *
     * Entry array keys
     * XML_LOC - loc field value
     * XML_LASTMOD - lastmod field value
     *
     * @param array $entry Entry array.
     *
     * @return void
     */
    public function addEntry ($entry)
    {
        if ($this->isSplitNeeded()) {
            $this->split();
            $this->addEntry($entry);
        } else {
            if ( ! $this->partChanged) {
                $this->addHeader();
            }

            $this->putContents(
                sprintf(
                    self::ENTRY_TPL,
                    Converter::getXmlConverter()->encode($entry['XML_LOC']),
                    Converter::getXmlConverter()->encode($entry['XML_LASTMOD'])
                ), self::APPEND
            );
        }
    }

    /**
     * Creates next sitemap file part. Returns new part file name.
     *
     * @return string
     */
    public function split ()
    {
        if ($this->partChanged) {
            $this->addFooter();
        }

        $this->partList[] = $this->getName();
        $this->part++;

        $fileName = $this->partFile;
        $fileName = substr($fileName, 0,
                -strlen(self::FILE_EXT)) . self::FILE_PART_SUFFIX . $this->part . substr($fileName,
                -strlen(self::FILE_EXT));

        $this->reInit($fileName);

        $this->partChanged = $this->isExists() && ! $this->isSplitNeeded();

        return $fileName;
    }

    /**
     * Returns file relative path for URL.
     *
     * @param File $f File object.
     *
     * @return string
     */
    protected function getFileUrl (File $f)
    {
        static $indexNames;
        if ( ! is_array($indexNames)) {
            $indexNames = GetDirIndexArray();
        }

        $documentRoot = Path::normalize($this->documentRoot);
        $path         = '/';
        if (substr($this->path, 0, strlen($documentRoot)) === $documentRoot) {
            $path = '/' . substr($f->getPath(), strlen($documentRoot));
        }

        $path = Path::convertLogicalToUri($path);

        $path = in_array($f->getName(), $indexNames)
            ? str_replace('/' . $f->getName(), '/', $path)
            : $path;

        return '/' . ltrim($path, '/');
    }

    /**
     * Appends new IBlock entry to the existing finished sitemap
     *
     * @param string $url IBlock entry URL.
     * @param string $modifiedDate IBlock entry modify timestamp.
     *
     * @return void
     */
    public function appendIBlockEntry ($url, $modifiedDate)
    {
        if ($this->isExists()) {
            $this->appendEntry(array(
                'XML_LOC'     => $this->settings['PROTOCOL'] . '://' . \CBXPunycode::toASCII($this->settings['DOMAIN'],
                        $e = null) . $url,
                'XML_LASTMOD' => date('c', $modifiedDate - \CTimeZone::getOffset()),
            ));
        } else {
            $this->addHeader();
            $this->addIBlockEntry($url, $modifiedDate);
            $this->addFooter();
        }
    }

    /**
     * Appends new entry to the existing and finished sitemap file
     *
     * Entry array keys
     * XML_LOC - loc field value
     * XML_LASTMOD - lastmod field value
     *
     * @param array $entry Entry array.
     *
     * @return void
     */
    public function appendEntry ($entry)
    {
        if ($this->isSplitNeeded()) {
            $this->split();
            $this->appendEntry($entry);
        } else {
            if ( ! $this->partChanged) {
                $this->addHeader();
                $offset = $this->getSize();
            } else {
                $offset = $this->getSize() - strlen(self::FILE_FOOTER);
            }

            $fd = $this->open('r+');

            fseek($fd, $offset);
            fwrite($fd, sprintf(
                            self::ENTRY_TPL,
                            Converter::getXmlConverter()->encode($entry['XML_LOC']),
                            Converter::getXmlConverter()->encode($entry['XML_LASTMOD'])
                        ) . self::FILE_FOOTER);
            fclose($fd);

            $this->footerClosed = true;
        }
    }

    /**
     * Adds new IBlock entry to the current sitemap
     *
     * @param string $url IBlock entry URL.
     * @param string $modifiedDate IBlock entry modify timestamp.
     *
     * @return void
     */
    public function addIBlockEntry ($url, $modifiedDate)
    {
        $this->addEntry(array(
            'XML_LOC'     => $this->settings['PROTOCOL'] . '://' . \CBXPunycode::toASCII($this->settings['DOMAIN'],
                    $e = null) . $url,
            'XML_LASTMOD' => date('c', $modifiedDate - \CTimeZone::getOffset()),
        ));
    }

    /**
     * Returns sitemap site root
     *
     * @return mixed|string
     */
    public function getSiteRoot ()
    {
        return $this->siteRoot;
    }

    /**
     * Returns sitemap file URL
     *
     * @return string
     */
    public function getUrl ()
    {
        return $this->settings['PROTOCOL'] . '://' . \CBXPunycode::toASCII($this->settings['DOMAIN'],
                $e = null) . $this->getFileUrl($this);
    }

    /**
     * Parses sitemap file
     *
     * @return bool|\CDataXML
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    public function parse ()
    {
        if ( ! $this->parser) {
            if ($this->isExists()) {
                $this->parser = new \CDataXML();
                $this->parser->loadString($this->getContents());
            }
        }

        return $this->parser;
    }
}