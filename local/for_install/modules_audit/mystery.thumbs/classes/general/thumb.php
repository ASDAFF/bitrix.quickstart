<?
class MysteryThumbs {

    var $arParams = array ();
    var $arRightExtension = array(
        "jpg",
        "jpeg",
        "png",
        "gif",
        "JPG",
        "JPEG",
        "PNG",
        "GIF",
    );
    var $defaultExt = 'jpg';
    var $date = 0;
    var $cacheTime = 0;

    function MysteryThumbs () {
        $date = time ();
        $last_modified = gmdate ( 'D, d M Y H:i:s',
                                  $date
        ).' GMT';

        $this->date = $last_modified;

        $maxCacheTime = 60*60*24*30; // 30 days
        $this->cacheTime = $maxCacheTime;
    }

    function hex2rgb ($hex) {
        $hex = str_replace ( "#",
                             "",
                             $hex
        );

        if (strlen ( $hex ) == 3) {
            $r = hexdec ( substr ( $hex,
                                   0,
                                   1
                          ).substr ( $hex,
                                     0,
                                     1
                          )
            );
            $g = hexdec ( substr ( $hex,
                                   1,
                                   1
                          ).substr ( $hex,
                                     1,
                                     1
                          )
            );
            $b = hexdec ( substr ( $hex,
                                   2,
                                   1
                          ).substr ( $hex,
                                     2,
                                     1
                          )
            );
        } else {
            $r = hexdec ( substr ( $hex,
                                   0,
                                   2
                          )
            );
            $g = hexdec ( substr ( $hex,
                                   2,
                                   2
                          )
            );
            $b = hexdec ( substr ( $hex,
                                   4,
                                   2
                          )
            );
        }
        $rgb = array (
            $r,
            $g,
            $b
        );
        return $rgb;
    }

    function getCloudFile ($outFile, $inFile) {
        return copy ( $outFile,
                      $inFile
        );
    }

    function removeCloudTemp () {
        $cloudFile = $this->arParams['cloudTempFile'];
        if ($cloudFile) {
            if (file_exists ( $cloudFile )) {
                unlink ( $cloudFile );
            }
        }
    }

    function checkURL ($url) {
        $req = urldecode ( $url );

        $badExtension = $this->CheckExtension($req);

        $p = array (); // collect all sended URL params

        preg_match ( '/\/thumb\/([0-9]{1,4})x([0-9]{1,4})x(in|cut|cutt|trim)((?:.*)\.(gif|jpg|png|jpeg))/i',
                     $req,
                     $p
        );

        /*
             $p[1] = width
             $p[2] = height
             $p[3] = method
             $p[4] = path to changed image
             $p[5] = image extension
        */

        $tempName = md5 ( implode ( '',
                                    $p
                          )
        ).'.'.$p[5];
        // check path to original image (local or remote server)
        $arProtocols = array (
            "ftp://",
            "http://",
            "https://",
        );
        $cloud = false;
        foreach ($arProtocols as $protocol) {
            if (substr_count ( $p[4],
                               $protocol
            )
            ) {
                $cloud = true;
                break;
            }
        }
        if (substr ( $p[4],
                     0,
                     2
        ) == '//'
        ) {
            $cloud = true;
            $p[4] = 'http:'.$p[4];
        }

        $cloudDir = MYSTERY_THUMBS_CHACHE_IMG_PATH.'cloud/';

        $this->arParams = array (
            "urlFile"       => basename($req),
            "urlDir"        => $_SERVER["DOCUMENT_ROOT"].dirname($req).'/',
            "x"             => $p[1],
            "y"             => $p[2],
            "xIs"           => $p[1],
            "yIs"           => $p[2],
            "method"        => ToLower ( $p[3] ),
            "file"          => $p[4],
            "badExtension"  => $badExtension,
            "cloud"         => $cloud,
            "cloudDir"      => $_SERVER["DOCUMENT_ROOT"].$cloudDir,
            "cloudTempFile" => $_SERVER['DOCUMENT_ROOT'].$cloudDir.$tempName,
            "extension"     => ToLower ( $p[5] ),
            "tempName"      => $tempName,
            "cacheDir"      => $_SERVER["DOCUMENT_ROOT"].MYSTERY_THUMBS_CHACHE_IMG_PATH.substr ( $tempName,
                                                                                                 0,
                                                                                                 3
            ).'/'
        );

        if ($this->arParams['badExtension'])
            $this->deleteExtensionOf();

        CheckDirPath ( $this->arParams['cacheDir'] );
        if ($cloud)
        {
            CheckDirPath ( $this->arParams['cloudDir'] );
            $this->arParams['outFile'] = $this->arParams['cacheDir'].$this->arParams['tempName'];
        }
        else
        {
            CheckDirPath( $this->arParams['urlDir'] );
            $this->arParams['outFile'] = $this->arParams['urlDir'].$this->arParams['urlFile'];
        }

        return file_exists ( $this->arParams['outFile'] );
    }

    function showImage ($image = false, $needSave = false) {

        $file = $this->arParams['outFile'];

        // headers set

        header ( 'Cache-Control: public, max-age = '.$this->cacheTime );

        if ($fileDate = filectime($file))
        {
            $last_modified = gmdate ( 'D, d M Y H:i:s',
                                      $fileDate
            ).' GMT';
        }
        else
            $last_modified = $this->date;

        header ( 'Last-Modified: '.$last_modified );

        // result image publication

        if ($this->arParams['extension'] == 'gif') {
            header ( "Content-type: image/gif" );
            if (!$image) {
                $image = ImageCreateFromGif ( $file );
            }
            if ($needSave) {
                imageGif ( $image,
                           $file
                );
            }
            imageGif ( $image );
        }
        elseif ($this->arParams['extension'] == 'jpg' || $this->arParams['extension'] == 'jpeg') {
            header ( "Content-type: image/jpeg" );
            if (!$image) {
                $image = ImageCreateFromJpeg ( $file );
            }
            if ($needSave) {
                imageJpeg ( $image,
                            $file,
                            MYSTERY_THUMBS_JPG_QUALITY
                );
            }
            imageJpeg ( $image,
                        null,
                        MYSTERY_THUMBS_JPG_QUALITY
            );
        }
        elseif ($this->arParams['extension'] == 'png') {
            header ( "Content-type: image/png" );
            if (!$image) {
                $image = ImageCreateFromPng ( $file );
            }
            imageSaveAlpha ( $image,
                             true
            );
            if ($needSave) {
                imagePng ( $image,
                           $file
                );
            }
            imagePng ( $image );
        }

        imagedestroy ( $image );
    }

    function createImageFromParams () {

        // define the desired image size
        if ($this->arParams['cloud']) {
            // remote image copy to yourself, perform all the work, then remove it
            $this->getCloudFile ( $this->arParams['file'],
                                  $this->arParams['cloudTempFile']
            );
            $arFile = CFile::MakeFileArray ( $this->arParams['cloudTempFile'] );
            $file = $this->arParams['cloudTempFile'];
        } else {
            // get local image
            $arFile = CFile::MakeFileArray ( $this->arParams['file'] );
            $file = $_SERVER['DOCUMENT_ROOT'].$this->arParams['file'];
        }

        $arImageSize = getImageSize ( $arFile['tmp_name'] );
        $arFile['WIDTH'] = $arImageSize[0];
        $arFile['HEIGHT'] = $arImageSize[1];

        if ($this->arParams['x'] == 0 && $this->arParams['y'] == 0)
        {
            $this->arParams['x'] = $this->arParams['xIs'] = $arFile['WIDTH'];
            $this->arParams['y'] = $this->arParams['yIs'] = $arFile['HEIGHT'];
        }

        // if one of the parameters of the required size is not specified, it turns out of proportion.
        if ($this->arParams['x'] == 0) {
            $this->arParams['x'] = $this->arParams['xIs'] = $this->arParams['y'] / $arFile['HEIGHT'] * $arFile['WIDTH'];
        }
        if ($this->arParams['y'] == 0) {
            $this->arParams['y'] = $this->arParams['yIs'] = $this->arParams['x'] / $arFile['WIDTH'] * $arFile['HEIGHT'];
        }

        // If the image is too small to produce the required size, the picture returns to the desired size, and the source is placed at its center
        if ($this->arParams['x'] > $arFile['WIDTH'] || $this->arParams['y'] > $arFile['HEIGHT']) {
            if ($this->arParams['x'] > $arFile['WIDTH']) {
                $this->arParams['x'] = $arFile['WIDTH'];
            }
            if ($this->arParams['y'] > $arFile['HEIGHT']) {
                $this->arParams['y'] = $arFile['HEIGHT'];
            }
        }

        $outIm = ImageCreateTrueColor ( $this->arParams['xIs'],
                                        $this->arParams['yIs']
        );

        $backGroundRGB = $this->hex2rgb ( MYSTERY_THUMBS_BACKGROUND_COLOR );

        // initiate an image with all the module settings

        if ($this->arParams['extension'] == 'gif') {
            $inIm = ImageCreateFromGif ( $file );
            $icolor = imagecolorallocate ( $outIm,
                                           $backGroundRGB[0],
                                           $backGroundRGB[1],
                                           $backGroundRGB[2]
            );
            imagefill ( $outIm,
                        0,
                        0,
                        $icolor
            );
        }
        elseif ($this->arParams['extension'] == 'jpg' || $this->arParams['extension'] == 'jpeg') {
            $inIm = ImageCreateFromJpeg ( $file );
            $icolor = imagecolorallocate ( $outIm,
                                           $backGroundRGB[0],
                                           $backGroundRGB[1],
                                           $backGroundRGB[2]
            );
            imagefill ( $outIm,
                        0,
                        0,
                        $icolor
            );
        }
        elseif ($this->arParams['extension'] == 'png') {
            $inIm = ImageCreateFromPng ( $file );
            if (MYSTERY_THUMBS_PNG_TRANSPARENT == 'Y') {
                imagealphablending ( $outIm,
                                     false
                );
                $icolor = imagecolorallocatealpha ( $outIm,
                                                    255,
                                                    255,
                                                    255,
                                                    127
                );
                imagecolortransparent ( $outIm,
                                        $icolor
                );

                imagefilledrectangle ( $outIm,
                                       0,
                                       0,
                                       $this->arParams['xIs'],
                                       $this->arParams['yIs'],
                                       $icolor
                );
            } else {
                $icolor = imagecolorallocate ( $outIm,
                                               $backGroundRGB[0],
                                               $backGroundRGB[1],
                                               $backGroundRGB[2]
                );
                imagefill ( $outIm,
                            0,
                            0,
                            $icolor
                );
            }
        }
        // create the desired image

        if ($this->arParams['method'] == 'cut') {
            // get reduction coefficient
            // need to find a factor that is proportional to the smaller size for the picture exactly come to a predetermined size, and the second dimension "got out" beyond the desired container
            // then the excess will shorten

            $k_x = $arFile['WIDTH'] / $this->arParams['x'];
            $k_y = $arFile['HEIGHT'] / $this->arParams['y'];
            if ($k_x > $k_y) {
                $k = $k_y;
            } else {
                $k = $k_x;
            }

            $pn['x'] = $arFile['WIDTH'] / $k;
            $pn['y'] = $arFile['HEIGHT'] / $k;

            // cut out the desired portion of the image center
            $x = round ( ($this->arParams['xIs'] - $pn['x']) / 2 );
            $y = round ( ($this->arParams['yIs'] - $pn['y']) / 2 );

            imageCopyResampled ( $outIm,
                                 $inIm,
                                 $x,
                                 $y,
                                 0,
                                 0,
                                 $pn['x'],
                                 $pn['y'],
                                 $arFile['WIDTH'],
                                 $arFile['HEIGHT']
            );
        }
        elseif ($this->arParams['method'] == 'cutt') {
            // the same as cut, just cut out the center of the picture along X, Y on the picture taken from the top (cutt == cutTop)
            $k_x = $arFile['WIDTH'] / $this->arParams['x'];
            $k_y = $arFile['HEIGHT'] / $this->arParams['y'];
            if ($k_x > $k_y) {
                $k = $k_y;
            } else {
                $k = $k_x;
            }

            $pn[1] = $arFile['WIDTH'] / $k;
            $pn[2] = $arFile['HEIGHT'] / $k;
            $x = ($this->arParams['x'] - $pn[1]) / 2;
            $y = ($this->arParams['y'] - $pn[2]) / 2;

            imageCopyResampled ( $outIm,
                                 $inIm,
                                 $x,
                                 0,
                                 0,
                                 0,
                                 $pn[1],
                                 $pn[2],
                                 $arFile['WIDTH'],
                                 $arFile['HEIGHT']
            );
        }
        elseif ($this->arParams['method'] == 'in') {
            // the final image should be completely "inside" of the container.

            $k_x = $arFile['WIDTH'] / $this->arParams['x'];
            $k_y = $arFile['HEIGHT'] / $this->arParams['y'];
            // Conversely than cut - need a factor. to proportionally larger size came at exactly resize to the container, and the second dimension would be "less" than the container
            if ($k_x < $k_y) {
                $k = $k_y;
            } else {
                $k = $k_x;
            }
            $pn[1] = $arFile['WIDTH'] / $k;
            $pn[2] = $arFile['HEIGHT'] / $k;
            $x = ($this->arParams['xIs'] - $pn[1]) / 2;
            $y = ($this->arParams['yIs'] - $pn[2]) / 2;
            imageCopyResampled ( $outIm,
                                 $inIm,
                                 $x,
                                 $y,
                                 0,
                                 0,
                                 $pn[1],
                                 $pn[2],
                                 $arFile['WIDTH'],
                                 $arFile['HEIGHT']
            );
        }
        elseif ($this->arParams['method'] == 'trim') {

            $bg = imagecolorallocate ( $inIm,
                                       $backGroundRGB[0],
                                       $backGroundRGB[1],
                                       $backGroundRGB[2]
            );

            $outIm = $this->imagetrim ( $inIm,
                                        $bg,
                                        $this->arParams['xIs'].' '.$this->arParams['yIs']
            );

            $this->arParams['x'] = imagesx ( $outIm );
            $this->arParams['y'] = imagesy ( $outIm );
        }
        else {
            imageCopyResampled ( $outIm,
                                 $inIm,
                                 0,
                                 0,
                                 0,
                                 0,
                                 $this->arParams['x'],
                                 $this->arParams['y'],
                                 $arFile['WIDTH'],
                                 $arFile['HEIGHT']
            );
        }

        imagedestroy ( $inIm );

        $outIm = $this->addWatermark ( $outIm );

        $this->removeCloudTemp (); // if the picture was out of the cloud, its temp file should be removed

        return $this->showImage ( $outIm,
                                  true
        );
    }

    function addWatermark ($image) {

        // check exceptions section
        $arSect = explode(';',MYSTERY_THUMBS_WATERMARK_EXCEPTION);
        $referer = str_replace('http://'.$_SERVER['HTTP_HOST'],'',$_SERVER['HTTP_REFERER']);

        $checkException = true;
        foreach ($arSect as $sect)
        {
            if (substr_count($referer,$sect))
            {
                if (strpos($referer,$sect) == 0)
                {
                    $checkException = false;
                }
            }
        }

        $im = $image;

        $waterFile = $_SERVER['DOCUMENT_ROOT'].MYSTERY_THUMBS_WATERMARK_IMG;
        if (MYSTERY_THUMBS_WATERMARK_ENABLE == 'Y' && $checkException) {
            if (file_exists ( $waterFile )) {
                if ($this->arParams['x'] > MYSTERY_THUMBS_WATERMARK_MIN_WIDTH_PICTURE) {
                    // and only then should impose vatermark

                    $ii = getImageSize ( $waterFile );
                    imagealphablending ( $im,
                                         true
                    );
                    $i2 = ImageCreateFromPng ( $waterFile );
                    $im2_w = $ii[0];
                    $im2_h = $ii[1];

                    if (MYSTERY_THUMBS_WATERMARK_POSITION == 'lt') {
                        $pos_x1 = 0;
                        $pos_x2 = $im2_w;

                        $pos_y1 = 0;
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'ct') {
                        $pos_x1 = ($this->arParams['x'] / 2) - ($im2_w / 2);
                        $pos_x2 = $im2_w;

                        $pos_y1 = 0;
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'rt') {
                        $pos_x1 = $this->arParams['x'] - $im2_w;
                        $pos_x2 = $im2_w;

                        $pos_y1 = 0;
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'lm') {
                        $pos_x1 = 0;
                        $pos_x2 = $im2_w;

                        $pos_y1 = ($this->arParams['y'] / 2) - ($im2_h / 2);
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'cm') {
                        $pos_x1 = ($this->arParams['x'] / 2) - ($im2_w / 2);
                        $pos_x2 = $im2_w;

                        $pos_y1 = ($this->arParams['y'] / 2) - ($im2_h / 2);
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'rm') {
                        $pos_x1 = $this->arParams['x'] - $im2_w;
                        $pos_x2 = $im2_w;

                        $pos_y1 = ($this->arParams['y'] / 2) - ($im2_h / 2);
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'lb') {
                        $pos_x1 = 0;
                        $pos_x2 = $im2_w;

                        $pos_y1 = $this->arParams['y'] - $im2_h;
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'cb') {
                        $pos_x1 = ($this->arParams['x'] / 2) - ($im2_w / 2);
                        $pos_x2 = $im2_w;

                        $pos_y1 = $this->arParams['y'] - $im2_h;
                        $pos_y2 = $im2_h;
                    } elseif (MYSTERY_THUMBS_WATERMARK_POSITION == 'rb') {
                        $pos_x1 = $this->arParams['x'] - $im2_w;
                        $pos_x2 = $im2_w;

                        $pos_y1 = $this->arParams['y'] - $im2_h;
                        $pos_y2 = $im2_h;
                    }

                    ImageCopyResampled ( $im,
                                         $i2,
                                         $pos_x1,
                                         $pos_y1,
                                         0,
                                         0,
                                         $pos_x2,
                                         $pos_y2,
                                         $ii[0],
                                         $ii[1]
                    );
                    imagedestroy ( $i2 );
                }
            }
        }
        return $im;
    }

    function imagetrim ($im, $bg, $pad = null) {

        if (isset($pad)) {
            $pp = explode ( ' ',
                            $pad
            );
            if (isset($pp[3])) {
                $p = array (
                    (int)$pp[0],
                    (int)$pp[1],
                    (int)$pp[2],
                    (int)$pp[3]
                );
            } else if (isset($pp[2])) {
                $p = array (
                    (int)$pp[0],
                    (int)$pp[1],
                    (int)$pp[2],
                    (int)$pp[1]
                );
            } else if (isset($pp[1])) {
                $p = array (
                    (int)$pp[0],
                    (int)$pp[1],
                    (int)$pp[0],
                    (int)$pp[1]
                );
            } else {
                $p = array_fill ( 0,
                                  4,
                                  (int)$pp[0]
                );
            }
        } else {
            $p = array_fill ( 0,
                              4,
                              0
            );
        }

        $imw = imagesx ( $im );
        $imh = imagesy ( $im );
        $xmin = $imw;
        $xmax = 0;
        for ($iy = 0; $iy < $imh; $iy++) {
            $first = true;
            for ($ix = 0; $ix < $imw; $ix++) {
                $ndx = imagecolorat ( $im,
                                      $ix,
                                      $iy
                );
                if ($ndx != $bg) {
                    if ($xmin > $ix) {
                        $xmin = $ix;
                    }
                    if ($xmax < $ix) {
                        $xmax = $ix;
                    }
                    if (!isset($ymin)) {
                        $ymin = $iy;
                    }
                    $ymax = $iy;
                    if ($first) {
                        $ix = $xmax;
                        $first = false;
                    }
                }
            }
        }

        $imw = 1 + $xmax - $xmin;
        $imh = 1 + $ymax - $ymin;

        $im2 = imagecreatetruecolor ( $imw + $this->arParams['x'] + $p[3],
                                      $imh + $p[0] + $this->arParams['y']
        );

        $bg2 = imagecolorallocate ( $im2,
                                    ($bg >> 16)&0xFF,
                                    ($bg >> 8)&0xFF,
                                    $bg&0xFF
        );
        imagefill ( $im2,
                    0,
                    0,
                    $bg2
        );

        imagecopy ( $im2,
                    $im,
                    $p[3],
                    $p[0],
                    $xmin,
                    $ymin,
                    $imw,
                    $imh
        );

        return $im2;
    }

    function checkExtension(&$url)
    {
        $badExtension = true;
        foreach ($this->arRightExtension as $ext)
        {
            if (substr_count($url,'.'.$ext))
            {
                $badExtension = false;
                break;
            }
        }

        if ($badExtension)
        {
            $url .= '.'.$this->defaultExt;
        }

        return $badExtension;
    }

    function deleteExtensionOf()
    {
        $file = $this->arParams['file'];
        $file = str_replace('.jpg','',$file);
        $this->arParams['file'] = $file;
    }
}

?>