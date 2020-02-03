<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 17.02.2019
 * Time: 7:34
 */

define('IMAGE_EDITOR_RESIZE_STRICT',        0);
define('IMAGE_EDITOR_RESIZE_PROPORTIONAL',  1);
define('IMAGE_EDITOR_RESIZE_WIDTH',         2);
define('IMAGE_EDITOR_RESIZE_HEIGHT',        3);

define('IMAGE_EDITOR_ERROR_FILE_NOT_FOUND',         1);
define('IMAGE_EDITOR_ERROR_FILE_NOT_IMAGE',         2);
define('IMAGE_EDITOR_ERROR_CREATE_FUNC_NOT_FOUND',  3);
define('IMAGE_EDITOR_ERROR_BAD_QUALITY_VALUE',      4);
define('IMAGE_EDITOR_ERROR_IM_PATH_NOT_SET',        5);

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