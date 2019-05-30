<?php
namespace LastWorld\Helper;

if (!class_exists('CHelper'))
{
    class CLWLinkHelper
    {
        private $url = '';
        private $parsedUrl = array();
        private $get = array();
        private $pathArray = array();

        public function initUrl($url)
        {
            $this->url = $url;

            return $this;
        }

        public function parseUrl()
        {
            if ($this->url !== '')
            {
                $this->parsedUrl = parse_url($this->url);
                $params = explode('&', $this->parsedUrl['query']);

                foreach ($params as $param)
                {
                    $tmpParam = explode('=', $param);
                    $this->get[ $tmpParam[0] ] = $tmpParam[1];
                }

                $tmpPath = explode('/', $this->parsedUrl['path']);
                foreach ($tmpPath as $path)
                {
                    if ($path !== '')
                    {
                        $this->pathArray[] = $path;
                    }
                }
            }

            return $this;
        }

        public function getParam($paramName)
        {
            if (isset($this->get[ $paramName ]))
            {
                return $this->get[ $paramName ];
            }

            return false;
        }

        /**
         * @param int $num number of path element. If =0 then return full path. if =-1 return last element.
         * @param bool $endIfNotExists return last element if index not exists
         *
         * @return mixed
         */
        public function getPath($num = 0, $endIfNotExists = true)
        {
            $index = (int) $num;
            $path = false;

            if ($index == 0)
            {
                $path = $this->parsedUrl['path'];
            }
            elseif ($index == -1)
            {
                $path = end($this->pathArray);
                reset($this->pathArray);
            }
            else
            {
                if ((count($this->pathArray) + 1 < $index) || ($index < -1))
                {
                    if ($endIfNotExists)
                    {
                        $path = end($this->pathArray);
                        reset($this->pathArray);
                    }
                }
                else
                {
                    $path = $this->pathArray[ $index - 1 ];
                }
            }

            return $path;
        }
    }
}