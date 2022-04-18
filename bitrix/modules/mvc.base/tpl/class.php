<?php
/**
 * @var $name
 * @var $routes
 */
?>

namespace DigitalWand\MVC;
use Bitrix\Main\Loader;
use DigitalWand\MVC\BaseComponent;

Loader::includeModule('digitalwand.mvc');

class <?=ucfirst($name)?>Component extends BaseComponent
{
<?foreach ($routes as $route):?><?
    $strVariables = '';
    if(!empty($route['variables'])) {
        $variables = explode(',',$route['variables']);
        array_walk($variables,function (&$varName, $index){
            $varName = '$'.$varName;
        });
        $strVariables = implode(', ',$variables);
    }?>
    public function action<?=ucfirst($route['id'])?>(<?=$strVariables?>)
    {
        $this->arResult['action_message'] = 'MESSAGE from "<?=$route['id']?>"';
    }

<?endforeach;?>
}
