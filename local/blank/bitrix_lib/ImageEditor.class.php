<?php

define('IMAGE_EDITOR_RESIZE_STRICT',        0);
define('IMAGE_EDITOR_RESIZE_PROPORTIONAL',  1);
define('IMAGE_EDITOR_RESIZE_WIDTH',         2);
define('IMAGE_EDITOR_RESIZE_HEIGHT',        3);

define('IMAGE_EDITOR_ERROR_FILE_NOT_FOUND',         1);
define('IMAGE_EDITOR_ERROR_FILE_NOT_IMAGE',         2);
define('IMAGE_EDITOR_ERROR_CREATE_FUNC_NOT_FOUND',  3);
define('IMAGE_EDITOR_ERROR_BAD_QUALITY_VALUE',      4);
define('IMAGE_EDITOR_ERROR_IM_PATH_NOT_SET',        5);

class ImageEditorException extends Exception {} ;

class ImageEditorGD
{
    private $sourceFile;
    private $sourceImage;
    private $sourceInfo;
    private $format;
    private $createFunc;

    private $targetFile;
    private $targetImage;
    private $saveFunc;

    private $currentWidth;
    private $currentHeight;

    private $quality = 100;

    function __construct() {}

    /**
     * @param string $sourceFile
     * @return ImageEditorGD $instance
     */
    function setSource($sourceFile)
    {
        if (!file_exists($sourceFile))
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_FILE_NOT_FOUND);
        }

        if (!$this->sourceInfo = getimagesize($sourceFile))
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_FILE_NOT_IMAGE);
        }

        $this->sourceFile = $sourceFile;
        $this->sourceImage = null;

        $this->createSourceImage();

        return $this;
    }

    /**
     * @param int $quality
     * @return ImageEditorGD $instance
     */
    function setQuality($quality)
    {
        if ((int) $quality < 1 || (int) $quality > 100)
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_BAD_QUALITY_VALUE, $quality);
        }

        $this->quality = (int) $quality;
        return $this;
    }

    /**
     * @param string $targetFile
     * @return ImageEditorGD $instance
     */
    function setTarget($targetFile)
    {
        $this->targetFile = $targetFile;
        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $mode
     * @return ImageEditorGD $instance
     */
    public function resize($width, $height, $mode = IMAGE_EDITOR_RESIZE_PROPORTIONAL)
    {
        switch ($mode)
        {
            case IMAGE_EDITOR_RESIZE_STRICT:

                $w = $width;
                $h = $height;

                break;

            case IMAGE_EDITOR_RESIZE_WIDTH:

                $w = $width;
                $h = (int) $this->currentHeight * $width / $this->currentWidth;

                break;

            case IMAGE_EDITOR_RESIZE_HEIGHT:

                $h = $height;
                $w = (int) ($this->currentWidth * $height / $this->currentHeight);

                break;

            case IMAGE_EDITOR_RESIZE_PROPORTIONAL:
            default:
/*
                if ($this->currentHeight > $height || $this->currentWidth > $width)
                {
                    $r = max($this->currentHeight / $height, $this->currentWidth / $width);
                    $h = intval($this->currentHeight / $r);
                    $w = intval($this->currentWidth / $r);
                }
                else
                {
                    $h = $height;
                    $w = $width;
                }
*/

                if ($this->currentWidth > $this->currentHeight && $this->currentWidth > $width)
                {
                    $w = $width;
                    $h = intval($width * $this->currentHeight / $this->currentWidth);
                }
                elseif ($this->currentHeight >= $this->currentWidth && $this->currentHeight > $height)
                {
                    $h = $height;
                    $w = intval($height * $this->currentWidth / $this->currentHeight);
                }
                else
                {
                    $w = $this->currentWidth;
                    $h = $this->currentHeight;
                }

                // foolproof
                if ($h > $height)
                {
                    $h = $height;
                    $w = intval($height * $this->currentWidth / $this->currentHeight);
                }

                if ($w > $width)
                {
                    $w = $width;
                    $h = intval($width * $this->currentHeight / $this->currentWidth);
                }

            break;
        }

        if ($w > $this->currentWidth && $h > $this->currentHeight)
        {
            $w = $this->currentWidth;
            $h = $this->currentHeight;
        }

        $image = $this->createEmptyImage($w, $h);

        if ($this->format != 'png' && $this->format != 'gif')
        {
//            $bgColor = array(255, 255, 255);
//            imagefill($image, 0, 0, imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]));
        }
        else
        {
            imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        }

        imagecopyresampled(
            $image,
            $this->sourceImage,
            0,0,0,0,
            $w, $h,
            $this->currentWidth,
            $this->currentHeight
        );

        $this->setSourceImage($image);

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return ImageEditorGD $instance
     */
    public function crop($x, $y, $width, $height)
    {
        $image = $this->createEmptyImage($width, $height);
        imagecopy($image, $this->sourceImage, 0, 0, $x, $y, $width, $height);

        $this->setSourceImage($image);

        return $this;
    }

    /**
     * @param int $size
     * @return ImageEditorGD $instance
     */
    function square($size)
    {
        if ($this->currentWidth > $this->currentHeight)
        {
            $x = ceil(($this->currentWidth - $this->currentHeight) / 2);
            $this->currentWidth = $this->currentHeight;
        }
        else if ($this->currentHeight > $this->currentWidth)
        {
            $y = ceil(($this->currentHeight - $this->currentWidth) / 2);
            $this->currentHeight = $this->currentWidth;
        }

        $image = $this->createEmptyImage($size, $size);

        imagecopyresampled($image,$this->sourceImage,0,0,@$x,@$y,$size,$size,$this->currentWidth,$this->currentHeight);

        $this->setSourceImage($image);

        return $this;
    }

    /**
     * @param int $size
     * @param array $bgColor
     * @return ImageEditorGD $instance
     */
    function putIntoSquare($size, $bgColor = array(255, 255, 255))
    {
        return $this->putIntoSize($size, $size, $bgColor);
    }

    /**
     * @param int $width
     * @param int $height
     * @param array $bgColor
     * @return ImageEditorGD $instance
     */
    function putIntoSize($width, $height, $bgColor = array(255, 255, 255))
    {
        $image = $this->createEmptyImage($width, $height);

        if ($this->format != 'png' && $this->format != 'gif')
        {
            imagefill($image, 0, 0, imagecolorallocate($image, $bgColor[0], $bgColor[1], $bgColor[2]));
        }
        else
        {
            imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        }

        $ratio = $this->currentWidth / $this->currentHeight;

        $target_ratio = $width/$height;

        if ($ratio > $target_ratio)
        {
            $newWidth = $width;
            $newHeight = round($width / $ratio);
            $xOffset = 0;
            $yOffset = round(($height - $newHeight) / 2);
        }
        else
        {
            $newHeight = $height;
            $newWidth = round($height * $ratio);
            $xOffset = round(($width - $newWidth) / 2);
            $yOffset = 0;
        }

        imagecopyresampled($image, $this->sourceImage, $xOffset, $yOffset, 0, 0, $newWidth, $newHeight, $this->currentWidth, $this->currentHeight);

        $this->setSourceImage($image);

        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageEditorGD $instance
     */
    function cutIntoSize($width, $height)
    {
        $image = $this->createEmptyImage($width, $height);

        $ratio = $this->currentWidth / $this->currentHeight;

        $target_ratio = $width / $height;

        if ($ratio > $target_ratio)
        {
            $newHeight = $height;
            $newWidth = round($height * $ratio);
            $xOffset = -round(($width - $newWidth) / 2);
            $yOffset = 0;
        }
        else
        {
            $newWidth = $width;
            $newHeight = round($width / $ratio);
            $xOffset = 0;
            $yOffset= -round(($height - $newHeight) / 2);
        }

        $insert = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($insert,$this->sourceImage,0,0,0,0,$newWidth,$newHeight,$this->currentWidth,$this->currentHeight);
        imagecopymerge($image,$insert,0,0,$xOffset,$yOffset,$newWidth,$newHeight,100);

        $this->setSourceImage($image);

        return $this;
    }

    /**
     * @param float $factor
     * @return ImageEditorGD $instance
     */
    public function sharpen($factor = 1)
    {
        if (!function_exists('findSharp'))
        {
            function findSharp($orig, $final)
            {
                $final = $final * (750.0 / $orig);
                $a = 52;
                $b = -0.27810650887573124;
                $c = .00047337278106508946;

                $result = $a + $b * $final + $c * $final * $final;

                return max(round($result), 0);
            }
        }

        $sharpness = findSharp($this->currentWidth, $this->currentWidth * $factor);

        $sharpenMatrix    = array(
            array(-1, -2, -1),
            array(-2, $sharpness + 12, -2),
            array(-1, -2, -1)
        );

        $divisor = $sharpness;
        $offset = 0;

        imageconvolution($this->sourceImage, $sharpenMatrix, $divisor, $offset);

        return $this;
    }

    /**
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @param int $left
     * @return ImageEditorGD $instance
     */
    function cutEdgesByPercentage($top = 0, $right = 0, $bottom = 0, $left = 0)
    {
        $x = $y = 0;
        $width = $this->currentWidth;
        $height = $this->currentHeight;

        if ($top > 0)
        {
            $y = (int) $this->currentHeight * ($top / 100);
            $height -= $y;
        }

        if ($right > 0)
        {
            $value = (int) $this->currentWidth * ($right / 100);
            $width -= $value;
        }

        if ($bottom > 0)
        {
            $value = (int) $this->currentHeight * ($bottom / 100);
            $height -= $value;
        }

        if ($left > 0)
        {
            $x = (int) $this->currentWidth * ($left / 100);
            $width -= $x;
        }

        return $this->crop($x, $y, $width, $height);
    }

    public function commit()
    {
        $this->createTargetImage($this->currentWidth, $this->currentHeight);

        $this->saveFunc = 'image' . $this->format;

        if ($this->saveFunc == 'imagepng')
        {
            $quality = $this->quality / 10 - 1;
        }
        else
        {
            $quality = $this->quality;
        }

        call_user_func_array($this->saveFunc, array($this->sourceImage, $this->targetFile, $quality));

        imagedestroy($this->sourceImage);
        imagedestroy($this->targetImage);

        $this->sourceImage = $this->targetImage = null;
    }

    /* private functions */

    private function raiseError($code, $message = null)
    {
        throw new Exception(IMAGE_EDITOR_ERROR_CREATE_FUNC_NOT_FOUND);
    }

    private function setSourceImage($image)
    {
        $this->sourceImage = $image;
        $this->currentWidth = imagesx($image);
        $this->currentHeight = imagesy($image);
    }

    private function createSourceImage()
    {
        $this->format = strtolower(substr($this->sourceInfo['mime'], strpos($this->sourceInfo['mime'], '/') + 1));
        $this->createFunc = "imagecreatefrom{$this->format}";

        if (!function_exists($this->createFunc))
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_CREATE_FUNC_NOT_FOUND, $this->createFunc);
        }

        switch ($this->format)
        {
            case 'jpeg':
                $this->sourceImage = imagecreatefromjpeg($this->sourceFile);
            break;

            case 'gif':
            case 'png':
                $this->sourceImage = call_user_func($this->createFunc, $this->sourceFile);
                imagesavealpha($this->sourceImage, true);
            break;

            default:
                $image = false;
            break;
        }

        $this->currentWidth = $this->sourceInfo[0];
        $this->currentHeight = $this->sourceInfo[1];
    }

    private function createEmptyImage($width, $height)
    {
        $image = function_exists('imagecreatetruecolor')
            ? imagecreatetruecolor($width, $height)
            : imagecreate($width, $height)
        ;

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    private function createTargetImage($width, $height)
    {
        $this->targetImage = $this->createEmptyImage($width, $height);
    }

    private function setTargetDimensions($width, $height)
    {
        $this->currentWidth = $width;
        $this->currentHeight = $height;
    }
}

class ImageEditorIM
{
    private $sourceFile;
    private $targetFile;

    private $currentWidth;
    private $currentHeight;

    private $imageMagickPath;

    private $command;

    private $quality = 100;

    function __construct() {}

    /**
     * @param string $sourceFile
     * @return ImageEditorIM $instance
     */
    function setImageMagickPath($path)
    {
        $this->imageMagickPath = $path;
        return $this;
    }

    /**
     * @param string $sourceFile
     * @return ImageEditorIM $instance
     */
    function setSource($sourceFile)
    {
        if (!file_exists($sourceFile))
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_FILE_NOT_FOUND);
        }

        if (!$this->sourceInfo = getimagesize($sourceFile))
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_FILE_NOT_IMAGE);
        }

        $this->sourceFile = $sourceFile;

        if (empty($this->imageMagickPath))
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_IM_PATH_NOT_SET);
        }

        $this->addCommand('"' . $this->imageMagickPath . '"', true);

        return $this;
    }

    /**
     * @param int $quality
     * @return ImageEditorIM $instance
     */
    function setQuality($quality)
    {
        if ((int) $quality < 1 || (int) $quality > 100)
        {
            $this->raiseError(IMAGE_EDITOR_ERROR_BAD_QUALITY_VALUE, $quality);
        }

        $this->quality = (int) $quality;
        return $this;
    }

    /**
     * @param string $targetFile
     * @return ImageEditorIM $instance
     */
    function setTarget($targetFile)
    {
        $this->targetFile = $targetFile;
        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $mode
     * @return ImageEditorIM $instance
     */
    public function resize($width, $height, $mode = IMAGE_EDITOR_RESIZE_PROPORTIONAL)
    {
        switch ($mode)
        {
            default:
            case IMAGE_EDITOR_RESIZE_PROPORTIONAL:
                $this->addCommand("-geometry {$width}x{$height}");
                break;

            case IMAGE_EDITOR_RESIZE_STRICT:
                $this->addCommand("-geometry {$width}x{$height}!");
                break;

            case IMAGE_EDITOR_RESIZE_HEIGHT:
                $this->addCommand("-geometry x{$height}");
                break;

            case IMAGE_EDITOR_RESIZE_WIDTH:
                $this->addCommand("-geometry {$width}x");
                break;
        }

        return $this;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return ImageEditorIM $instance
     */
    public function crop($x, $y, $width, $height)
    {
        $this->addCommand("-crop {$width}x{$height}+{$x}+{$y}");

        return $this;
    }

    /**
     * @param int $size
     * @return ImageEditorIM $instance
     */
    function square($size)
    {
        $this->addCommand("-resize x{$size} -gravity center -crop {$size}x{$size}+0+0 +repage");
        return $this;
    }

    /**
     * @param int $size
     * @param array $bgColor
     * @return ImageEditorIM $instance
     */
    function putIntoSquare($size, $bgColor = array(255, 255, 255))
    {
        return $this->putIntoSize($size, $size, $bgColor);
    }

    /**
     * @param int $width
     * @param int $height
     * @param array $bgColor
     * @return ImageEditorIM $instance
     */
    function putIntoSize($width, $height, $bgColor = array(255, 255, 255))
    {
        $this->addCommand("-resize {$width}x{$height}^");
        $this->addCommand("-gravity center -extent {$width}x{$height}");

        return $this;
    }

    /**
     * @param int $width
     * @param int $height
     * @return ImageEditorIM $instance
     */
    function cutIntoSize($width, $height)
    {
        $this->addCommand("-resize {$width}x{$height}^");
        $this->addCommand("-gravity center -extent {$width}x{$height}");

        return $this;
    }

    /**
     * @param float $factor
     * @return ImageEditorIM $instance
     */
    public function sharpen($amount = 1)
    {
        $this->addCommand("-sharpen {$amount}x{$amount}");
        return $this;
    }

    /**
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @param int $left
     * @return ImageEditorIM $instance
     */
    function cutEdgesByPercentage($top = 0, $right = 0, $bottom = 0, $left = 0)
    {
        $xCrop = 100 - $top - $bottom;
        $yCrop = 100 - $left - $right;

        $this->addCommand("-gravity Center -crop {$xCrop}%x{$yCrop}%+0+0");
        return $this;
    }

    public function commit()
    {
        $this->addCommand("-quality $this->quality");
        $this->addCommand('"' . $this->sourceFile . '" "' . $this->targetFile . '"');

        $command = $this->buildCommand();

        exec($command, $output, $retval);

        return $retval === 0 ? true : false;
    }

    /* private functions */

    private function raiseError($code, $message = null)
    {
        throw new Exception($code);
    }

    private function addCommand($chunk, $clear = false)
    {
        if ($clear)
        {
            $this->command = array();
        }

        $this->command[] = $chunk;
    }

    private function buildCommand()
    {
        $retval = join(' ', $this->command);
        $retval = str_replace('"', '', $retval);

        return $retval;
    }
}
