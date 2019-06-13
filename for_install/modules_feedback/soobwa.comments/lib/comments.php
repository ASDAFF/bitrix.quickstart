<?

namespace Soobwa\Comments;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class CommentsTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'soobwa_comments';
    }

    public static function getUfId()
    {
        return 'SOOBWA_COMMENTS';
    }

    public static function getConnectionName()
    {
        return 'default';
    }

    /*
     * Стриктура таблицы:
     *
     * ID       - id сообщения
     * ID_CHAT  - id группы коментариев
     * ACTIVE   - Активность сообщения
     * ID_USER  - id пользователя оставившего комментарий
     * DATA     - дата комментария
     * TEXT     - текст комментария
     * DELETE   - статус удален или нет
     *
     * */
    public static function getMap()
    {
        return array(
            // ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            // ID_CHAT
            new Entity\StringField('ID_CHAT', array(
                'required' => true,
            )),
            // ACTIVE
            new Entity\BooleanField('ACTIVE', array(
                'required' => true,
            )),
            // ID_USER
            new Entity\IntegerField('ID_USER', array(
                'required' => true,
            )),
            // DATA
            new Entity\StringField('DATA', array(
                'required' => true,
            )),
            // TEXT
            new Entity\TextField('TEXT', array(
                'required' => true,
            )),
            // DELETE
            new Entity\BooleanField('DELETE', array(
                'required' => true,
            )),
        );
    }
}
?>