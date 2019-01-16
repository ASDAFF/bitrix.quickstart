<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 16.01.2019
 * Time: 15:42
 */

class Thumbnail
{
    private $w;
    private $h;
    private $filename;
    private $crop;

    private $_mime_settings;
    private $_fsave_allowed;
    private $_tname_tpl = '%s_%sx%s';
    private $_default_width = 250;
    private $_default_height = 250;
    private $_jpeg_quality = 75;
    private $_sess_varname = 'THUMB';

    public function __construct()
    {
        session_start();

        $this->w = abs((int)@$_GET['w']);
        $this->h = abs((int)@$_GET['h']);
        if (!$this->w && !$this->h) {
            # вписать в рамку по умолчанию
            $this->w = $this->_default_width;
            $this->h = $this->_default_height;
        }
        $this->filename = @$_GET['name'];
        $this->crop = isset($_GET['c']) || isset($_GET['tc']);

        $this->_mime_settings = array(
            'image/gif' => array(
                'ext' => '.gif',
                'create' => 'imagecreatefromgif',
                'save' => array(&$this, '_gif_save'),
            ),
            'image/jpeg' => array(
                'ext' => '.jpg',
                'create' => 'imagecreatefromjpeg',
                'save' => array(&$this, '_jpeg_save'),
            ),
            'image/pjpeg' => array(
                'ext' => '.jpg',
                'create' => 'imagecreatefromjpeg',
                'save' => array(&$this, '_jpeg_save'),
            ),
            'image/png' => array(
                'ext' => '.png',
                'create' => 'imagecreatefrompng',
                'save' => array(&$this, '_png_save'),
            ),
        );

        $this->_fsave_allowed = isset($_SESSION[$this->_sess_varname]);
        $this->_run();
    }

    private function _run()
    {
        if (!file_exists($this->filename) || !is_file($this->filename)) exit;
        $info = getimagesize($this->filename);
        if (!$info || !isset($this->_mime_settings[$info['mime']])) {
            # можно возвращать дефолтную картинку
            # .. и удалять лишние картинки
            #$files = glob("{$name}_*{$ext}");
            #glob("*.txt")
            exit;
        }
        $settings =& $this->_mime_settings[$info['mime']];

        $orig_width = $info[0];
        $orig_height = $info[1];
        $dst_x = $dst_y = 0;

        if (!$this->w) {
            # вписываем по высоте
            $new_width = $this->w = floor($orig_width * $this->h / $orig_height);
            $new_height = $this->h;
        } elseif (!$this->h) {
            # вписываем по ширине
            $new_width = $this->w;
            $new_height = $this->h = floor($orig_height * $this->w / $orig_width);
        } elseif ($this->crop) {
            # вписываем с обрезкой
            $scaleW = $this->w / $orig_width;
            $scaleH = $this->h / $orig_height;
            $scale = max($scaleW, $scaleH);
            $new_width = floor($orig_width * $scale);
            $new_height = floor($orig_height * $scale);
            $dst_x = floor(($this->w - $new_width) / 2);
            $dst_y = floor(($this->h - $new_height) / 2);
        } else {
            # вписываем без обрезки
            $scaleW = $this->w / $orig_width;
            $scaleH = $this->h / $orig_height;
            $scale = min($scaleW, $scaleH);
            $new_width = $this->w = floor($orig_width * $scale);
            $new_height = $this->h = floor($orig_height * $scale);
        }

        if ($this->w > $orig_width || $this->h > $orig_height) {
            header('Content-type: ' . $info['mime']);
            readfile($this->filename);
            exit;
        }

        $thumbFilename = dirname($this->filename) . '/'
            . sprintf($this->_tname_tpl, basename($this->filename, $settings['ext']), $this->w, $this->h)
            . $settings['ext'];

        if (file_exists($thumbFilename) && filemtime($thumbFilename) >= filemtime($this->filename)) {
            header('Content-type: ' . $info['mime']);
            readfile($thumbFilename);
            exit;
        }

        $orig_img = call_user_func($settings['create'], $this->filename);
        $tmp_img = imagecreatetruecolor($this->w, $this->h);
        # Copy and resize old image into new image
        imagecopyresampled(
            $tmp_img, $orig_img,
            $dst_x, $dst_y,
            0, 0,
            $new_width, $new_height,
            $orig_width, $orig_height
        );
        imagedestroy($orig_img);
        header('Content-type: ' . $info['mime']);
        call_user_func($settings['save'], $tmp_img, $thumbFilename);
        imagedestroy($tmp_img);
        exit;
    }

    private function _gif_save($img, $filename = false)
    {
        if ($filename !== false && $this->_fsave_allowed) imagegif($img, $filename);
        imagegif($img);
    }

    private function _jpeg_save($img, $filename = false)
    {
        if ($filename !== false && $this->_fsave_allowed) imagejpeg($img, $filename, $this->_jpeg_quality);
        imagejpeg($img, null, $this->_jpeg_quality);
    }

    private function _png_save($img, $filename = false)
    {
        if ($filename !== false && $this->_fsave_allowed) imagepng($img, $filename);
        imagepng($img);
    }

}

new Thumbnail;