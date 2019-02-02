<?php
/**
 * User: graymur
 * Date: 28.02.13
 * Time: 15:19
 */

namespace Cpeople\Classes\Block;

class File
{
    private $data;
    private $src;
    private $name;

    /**
     * @static
     * @param $id
     * @return \Cpeople\Classes\Block\File
     * @throws \Exception
     */
    static function fromId($id)
    {
        if (!$file = \CFile::GetByID($id)->GetNext())
        {
            throw new \Exception('File with id ' . $id . ' does not exist');
        }

        $className = get_called_class();
        return new $className($file);
    }

    function __construct($data)
    {
        $this->data = $data;

    }

    protected function fetchSrc()
    {
        if (!isset($this->src))
        {
            if (!$this->src = \CFile::GetFileSRC($this->data))
            {
                throw new \Exception('Source for image with id ' . $this->data['ID'] . ' does not exist');
            }
        }

        return $this->src;
    }

    public function getUrl()
    {
        return $this->fetchSrc();
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getExtension()
    {
        $pathinfo = pathinfo($this->data['FILE_NAME']);
        return $pathinfo['extension'];
    }

    public function getFileSize($precision = 0)
    {
        return format_file_size($this->data['FILE_SIZE'],$precision);
    }

    public function getDescription()
    {
        return $this->data['DESCRIPTION'];
    }

    public function getFileName()
    {
        return $this->data['FILE_NAME'];
    }

    public function getId()
    {
        return $this->data['ID'];
    }
}
