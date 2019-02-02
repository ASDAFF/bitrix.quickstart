bitrix_lib
==========

Библиотека для работы в Битриксе не как в Битриксе. Небольшой пример использования:

```
$element = \Cpeople\Classes\Block\Getter::instance()
    ->setClassName('\MyProject\Entities\EntityName')
    ->setFilter(array('ACTIVE' => 'Y', 'IBLOCK_ID' => $iblockId))
    ->getById($elementId);
    
echo $element->name . "\n";
echo $element->getDetailImageThumb(['width' => 400, 'height' => 400]) . "\n";
```
