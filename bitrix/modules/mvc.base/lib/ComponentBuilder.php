<?php
/**
 * Created by PhpStorm.
 * User: ASGAlex
 * Date: 20.05.2017
 * Time: 1:27
 */

namespace DigitalWand\MVC;


use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\FileNotFoundException;

/**
 * Class ComponentBuilder
 * @package DigitalWand\MVC
 *
 * Класс для сборки "рыбы" компонента из шаблонов ивведенных пользователем параметров
 */
class ComponentBuilder
{
    protected $variables;
    protected $componentPath;

    public function __construct($variables)
    {
        $this->variables = $variables;
        $this->componentPath = $_SERVER['DOCUMENT_ROOT'] . '/local/components/' . $this->variables['namespace'] . '/' . $this->variables['name'];
    }

    public static function extractVariablesFromSEF($sef, $existingVariables)
    {
        $matches = array();
        if (preg_match_all('/\#\w.+?\#/', $sef, $matches)) {
            if (!empty($existingVariables)) {
                $existingVariables = explode(',', $existingVariables);
            } else {
                $existingVariables = array();
            }

            if(!empty($matches)){
                $matches = reset($matches);
                foreach ($matches as $match) {
                    $var = str_replace('#', '', $match);
                    if (array_search($var, $existingVariables) === false) {
                        $existingVariables[] = $var;
                    }
                }
            }


        }
        $existingVariables = implode(',', $existingVariables);
        if (is_null($existingVariables)) {
            $existingVariables = '';
        }

        return $existingVariables;
    }

    public function build()
    {
        $dir = Directory::createDirectory($this->componentPath);
        if (!$dir->isExists()) {
            return false;
        }

        try {
            $templates = array(
                '.parameters.php',
                '.description.php',
                'class.php',
            );
            foreach ($templates as $templateName) {
                $file = new File($this->componentPath . '/' . $templateName);
                $render = $this->renderTemplate($templateName);
                $file->putContents($render);
            }

            $render = $this->renderTemplate('templates/.default/template.php');
            foreach ($this->variables['routes'] as $route) {
                $routeTpl = new File($this->componentPath . '/templates/.default/' . $route['id'] . '.php');
                $routeTpl->putContents($render);
            }

        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    protected function renderTemplate($name)
    {
        $path = __DIR__ . '/../tpl/' . $name;
        if (File::isFileExists($path)) {
            extract($this->variables);
            ob_start();
            include $path;

            return '<?php' . ob_get_clean();
        } else {
            throw new FileNotFoundException($path);
        }
    }

    /**
     * @return string
     */
    public function getComponentPath()
    {
        return $this->componentPath;
    }

    /**
     * @return string
     */
    public function getComponentRelativePath()
    {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->componentPath);
    }
}