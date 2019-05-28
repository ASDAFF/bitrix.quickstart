<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\EntityGetter;
    use Webprofy\Bitrix\IBlock\IBlock;
    use CIBlockType;

    class IBlockTypeGetter extends EntityGetter{
        protected
            $names = array(
                'ibt',
                'iblock-type',
                'iblock-types'
            ),
            $class = 'CIBlockType',
            $args = array(
                'sort',
                'filter',
            );

        function modifyArguments(){
            $arguments = $this->arguments;
            $f = $arguments->get('f');
            $f = array_merge($f, CIBlockType::GetByIDLang($f["ID"], LANG));

            $arguments
                ->set($f, 1, 'f')
                ->remove(2);

            return $this;
        }
    }