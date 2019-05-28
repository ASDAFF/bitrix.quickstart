<?
    namespace Webprofy\Bitrix\Getter\IBlock;

    use Webprofy\Bitrix\Getter\IBlock\SectionGetter;
    use Webprofy\Bitrix\Getter\EntityGetter;
    use Webprofy\Bitrix\IBlock\Section;

    class SectionTreeGetter extends SectionGetter{
        protected
            $names = array(
                'st',
                'section-tree',
                'sections-tree'
            ),
            $getListMethod = 'GetTreeList',
            $args = array(
                'filter',
                'select',
            );
    }