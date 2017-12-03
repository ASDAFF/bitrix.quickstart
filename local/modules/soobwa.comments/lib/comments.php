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
     * ��������� �������:
     *
     * ID       - id ���������
     * ID_CHAT  - id ������ �����������
     * ACTIVE   - ���������� ���������
     * ID_USER  - id ������������ ����������� �����������
     * DATA     - ���� �����������
     * TEXT     - ����� �����������
     * DELETE   - ������ ������ ��� ���
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