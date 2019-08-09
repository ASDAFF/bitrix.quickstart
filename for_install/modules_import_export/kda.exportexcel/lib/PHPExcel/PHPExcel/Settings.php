<?php
/**
 * KDAPHPExcel
 *
 * Copyright (c) 2006 - 2013 KDAPHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   KDAPHPExcel
 * @package    KDAPHPExcel_Settings
 * @copyright  Copyright (c) 2006 - 2013 KDAPHPExcel (http://www.codeplex.com/KDAPHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.7.9, 2013-06-02
 */

/** KDAPHPExcel root directory */
if (!defined('KDAPHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('KDAPHPEXCEL_ROOT', dirname(__FILE__) . '/../');
    require(KDAPHPEXCEL_ROOT . 'KDAPHPExcel/Autoloader.php');
}


class KDAPHPExcel_Settings
{
    /**    constants */
    /**    Available Zip library classes */
    const PCLZIP        = 'KDAPHPExcel_Shared_ZipArchive';
    const ZIPARCHIVE    = 'ZipArchive';
	const KDAIEZIPARCHIVE    = '\Bitrix\KdaImportexcel\ZipArchive';

    /**    Optional Chart Rendering libraries */
    const CHART_RENDERER_JPGRAPH    = 'jpgraph';

    /**    Optional PDF Rendering libraries */
    const PDF_RENDERER_TCPDF		= 'tcPDF';
    const PDF_RENDERER_DOMPDF		= 'DomPDF';
    const PDF_RENDERER_MPDF 		= 'mPDF';


    private static $_chartRenderers = array(
        self::CHART_RENDERER_JPGRAPH,
    );

    private static $_pdfRenderers = array(
        self::PDF_RENDERER_TCPDF,
        self::PDF_RENDERER_DOMPDF,
        self::PDF_RENDERER_MPDF,
    );


    /**
     * Name of the class used for Zip file management
     *	e.g.
     *		ZipArchive
     *
     * @var string
     */
    private static $_zipClass    = self::ZIPARCHIVE;


    /**
     * Name of the external Library used for rendering charts
     *	e.g.
     *		jpgraph
     *
     * @var string
     */
    private static $_chartRendererName = NULL;

    /**
     * Directory Path to the external Library used for rendering charts
     *
     * @var string
     */
    private static $_chartRendererPath = NULL;


    /**
     * Name of the external Library used for rendering PDF files
     *	e.g.
     * 		mPDF
     *
     * @var string
     */
    private static $_pdfRendererName = NULL;

    /**
     * Directory Path to the external Library used for rendering PDF files
     *
     * @var string
     */
    private static $_pdfRendererPath = NULL;


    /**
     * Set the Zip handler Class that KDAPHPExcel should use for Zip file management (PCLZip or ZipArchive)
     *
     * @param string $zipClass	The Zip handler class that KDAPHPExcel should use for Zip file management
     * 	 e.g. KDAPHPExcel_Settings::PCLZip or KDAPHPExcel_Settings::ZipArchive
     * @return	boolean	Success or failure
     */
    public static function setZipClass($zipClass)
    {
        if (($zipClass === self::PCLZIP) ||
            ($zipClass === self::ZIPARCHIVE) ||
			($zipClass === self::KDAIEZIPARCHIVE)
			) {
            self::$_zipClass = $zipClass;
            return TRUE;
        }
        return FALSE;
    } // function setZipClass()


    /**
     * Return the name of the Zip handler Class that KDAPHPExcel is configured to use (PCLZip or ZipArchive)
     *	or Zip file management
     *
     * @return string Name of the Zip handler Class that KDAPHPExcel is configured to use
     *	for Zip file management
     *	e.g. KDAPHPExcel_Settings::PCLZip or KDAPHPExcel_Settings::ZipArchive
     */
    public static function getZipClass()
    {
        return self::$_zipClass;
    } // function getZipClass()


    /**
     * Return the name of the method that is currently configured for cell cacheing
     *
     * @return string Name of the cacheing method
     */
    public static function getCacheStorageMethod()
    {
        return KDAPHPExcel_CachedObjectStorageFactory::getCacheStorageMethod();
    } // function getCacheStorageMethod()


    /**
     * Return the name of the class that is currently being used for cell cacheing
     *
     * @return string Name of the class currently being used for cacheing
     */
    public static function getCacheStorageClass()
    {
        return KDAPHPExcel_CachedObjectStorageFactory::getCacheStorageClass();
    } // function getCacheStorageClass()


    /**
     * Set the method that should be used for cell cacheing
     *
     * @param string $method Name of the cacheing method
     * @param array $arguments Optional configuration arguments for the cacheing method
     * @return boolean Success or failure
     */
    public static function setCacheStorageMethod(
    	$method = KDAPHPExcel_CachedObjectStorageFactory::cache_in_memory,
      $arguments = array()
    )
    {
        return KDAPHPExcel_CachedObjectStorageFactory::initialize($method, $arguments);
    } // function setCacheStorageMethod()


    /**
     * Set the locale code to use for formula translations and any special formatting
     *
     * @param string $locale The locale code to use (e.g. "fr" or "pt_br" or "en_uk")
     * @return boolean Success or failure
     */
    public static function setLocale($locale='en_us')
    {
        return KDAPHPExcel_Calculation::getInstance()->setLocale($locale);
    } // function setLocale()


    /**
     * Set details of the external library that KDAPHPExcel should use for rendering charts
     *
     * @param string $libraryName	Internal reference name of the library
     *	e.g. KDAPHPExcel_Settings::CHART_RENDERER_JPGRAPH
     * @param string $libraryBaseDir Directory path to the library's base folder
     *
     * @return	boolean	Success or failure
     */
    public static function setChartRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setChartRendererName($libraryName))
            return FALSE;
        return self::setChartRendererPath($libraryBaseDir);
    } // function setChartRenderer()


    /**
     * Identify to KDAPHPExcel the external library to use for rendering charts
     *
     * @param string $libraryName	Internal reference name of the library
     *	e.g. KDAPHPExcel_Settings::CHART_RENDERER_JPGRAPH
     *
     * @return	boolean	Success or failure
     */
    public static function setChartRendererName($libraryName)
    {
        if (!in_array($libraryName,self::$_chartRenderers)) {
            return FALSE;
        }

        self::$_chartRendererName = $libraryName;

        return TRUE;
    } // function setChartRendererName()


    /**
     * Tell KDAPHPExcel where to find the external library to use for rendering charts
     *
     * @param string $libraryBaseDir	Directory path to the library's base folder
     * @return	boolean	Success or failure
     */
    public static function setChartRendererPath($libraryBaseDir)
    {
        if ((file_exists($libraryBaseDir) === false) || (is_readable($libraryBaseDir) === false)) {
            return FALSE;
        }
        self::$_chartRendererPath = $libraryBaseDir;

        return TRUE;
    } // function setChartRendererPath()


    /**
     * Return the Chart Rendering Library that KDAPHPExcel is currently configured to use (e.g. jpgraph)
     *
     * @return string|NULL Internal reference name of the Chart Rendering Library that KDAPHPExcel is
     *	currently configured to use
     *	e.g. KDAPHPExcel_Settings::CHART_RENDERER_JPGRAPH
     */
    public static function getChartRendererName()
    {
        return self::$_chartRendererName;
    } // function getChartRendererName()


    /**
     * Return the directory path to the Chart Rendering Library that KDAPHPExcel is currently configured to use
     *
     * @return string|NULL Directory Path to the Chart Rendering Library that KDAPHPExcel is
     * 	currently configured to use
     */
    public static function getChartRendererPath()
    {
        return self::$_chartRendererPath;
    } // function getChartRendererPath()


    /**
     * Set details of the external library that KDAPHPExcel should use for rendering PDF files
     *
     * @param string $libraryName Internal reference name of the library
     * 	e.g. KDAPHPExcel_Settings::PDF_RENDERER_TCPDF,
     * 	KDAPHPExcel_Settings::PDF_RENDERER_DOMPDF
     *  or KDAPHPExcel_Settings::PDF_RENDERER_MPDF
     * @param string $libraryBaseDir Directory path to the library's base folder
     *
     * @return boolean Success or failure
     */
    public static function setPdfRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setPdfRendererName($libraryName))
            return FALSE;
        return self::setPdfRendererPath($libraryBaseDir);
    } // function setPdfRenderer()


    /**
     * Identify to KDAPHPExcel the external library to use for rendering PDF files
     *
     * @param string $libraryName Internal reference name of the library
     * 	e.g. KDAPHPExcel_Settings::PDF_RENDERER_TCPDF,
     *	KDAPHPExcel_Settings::PDF_RENDERER_DOMPDF
     * 	or KDAPHPExcel_Settings::PDF_RENDERER_MPDF
     *
     * @return boolean Success or failure
     */
    public static function setPdfRendererName($libraryName)
    {
        if (!in_array($libraryName,self::$_pdfRenderers)) {
            return FALSE;
        }

        self::$_pdfRendererName = $libraryName;

        return TRUE;
    } // function setPdfRendererName()


    /**
     * Tell KDAPHPExcel where to find the external library to use for rendering PDF files
     *
     * @param string $libraryBaseDir Directory path to the library's base folder
     * @return boolean Success or failure
     */
    public static function setPdfRendererPath($libraryBaseDir)
    {
        if ((file_exists($libraryBaseDir) === false) || (is_readable($libraryBaseDir) === false)) {
            return FALSE;
        }
        self::$_pdfRendererPath = $libraryBaseDir;

        return TRUE;
    } // function setPdfRendererPath()


    /**
     * Return the PDF Rendering Library that KDAPHPExcel is currently configured to use (e.g. dompdf)
     *
     * @return string|NULL Internal reference name of the PDF Rendering Library that KDAPHPExcel is
     * 	currently configured to use
     *  e.g. KDAPHPExcel_Settings::PDF_RENDERER_TCPDF,
     *  KDAPHPExcel_Settings::PDF_RENDERER_DOMPDF
     *  or KDAPHPExcel_Settings::PDF_RENDERER_MPDF
     */
    public static function getPdfRendererName()
    {
        return self::$_pdfRendererName;
    } // function getPdfRendererName()


    /**
     * Return the directory path to the PDF Rendering Library that KDAPHPExcel is currently configured to use
     *
     * @return string|NULL Directory Path to the PDF Rendering Library that KDAPHPExcel is
     *		currently configured to use
     */
    public static function getPdfRendererPath()
    {
        return self::$_pdfRendererPath;
    } // function getPdfRendererPath()

}