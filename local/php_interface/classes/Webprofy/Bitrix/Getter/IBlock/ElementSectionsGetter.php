<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\EntityGetter;
    use CIBlockElement;

    class ElementSectionsGetter extends EntityGetter{
        protected
            $names = array(
                'es',
                'element-section',
                'element-sections',
            ),
            $nextMethod = 'Fetch';

        function getList(){
            $data = $this->data;
            $select = $data->get('select');
            $select = is_array($select) ? $select : false;
            CModule::IncludeModule('iblock');
            return CIBlockElement::GetElementGroups(
                $data->get('id'),
                false,
                $select
            );
        }
    }