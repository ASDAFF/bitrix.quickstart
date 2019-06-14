<?php
/**
 * WebForms helper
 *
 */

namespace Ml2WebForms;

use \Bitrix\Main\Application;
use \Ml2WebForms\WebForm;

/**
 * Class WebFormResult
 * @package Ml2WebForms
 */
class WebFormResult {

    /**
     * @var string web form id
     */
    protected $webFormId;

    /**
     * @var resource external db connection
     */
    protected $externalDbCon;

    /**
     * @var string database table name
     */
    protected $tableName;

    /**
     * WebFormResult object constructor
     * @param string $webFormId web form id
     * @param bool|resource $externalDbCon external database connection if need save results to external database
     */
    public function __construct($webFormId, $externalDbCon = false) {
        $this->webFormId = $webFormId;
        $this->tableName = 'ml2webforms_' . $this->webFormId;
        $this->externalDbCon = $externalDbCon;
    }

    /**
     * Add result to database
     * @param array $fields fields of result
     * @return bool|int id of inserted result row or false if fail
     */
    public function add(array $fields) {
        $values = array();
        foreach ($fields as $field => $value) {
            if (strlen(trim($field)) > 0) {
                $values[] = '`' . trim($field) . '` = \'' . str_replace('\'', '&apos;', str_replace('"', '&quot;', htmlspecialchars($value))) . '\'';
            }
        }

        if (count($values) > 0) {
            $sql = 'INSERT `' . $this->tableName . '` SET ' . implode(', ', $values);
            $this->query($sql);
            return $this->getInsertedId();
        }

        return false;
    }

    /**
     * Update result in database by id
     * @param int $id
     * @param array $fields fields of result
     * @return bool
     */
    public function update($id, array $fields) {
        $values = array();
        foreach ($fields as $field => $value) {
            if (strlen(trim($field)) > 0) {
                $values[] = '`' . trim($field) . '` = \'' . str_replace('\'', '&apos;', str_replace('"', '&quot;', htmlspecialchars($value))) . '\'';
            }
        }

        if (count($values) > 0) {
            $sql = 'UPDATE `' . $this->tableName . '` SET ' . implode(', ', $values) . ' WHERE `id` = \'' . $id . '\'';
            return $this->query($sql);
        }

        return false;
    }

    private function _buildSQLWhere($filter, $logic = 'AND', &$match_select) {
        $where = array();
        foreach ( $filter as $key => $value ) {
            if ( $key === 'LOGIC' ) {
                continue;
            } elseif( strpos( $key, 'MATCH(' ) === 0 ) {
                $where[] = '( ' . $key . ' AGAINST( \'' . ( is_array( $value ) ? implode( ' ', $value ) : $value ) . '\' ) )';
                $match_select[] = ' ' . $key . ' AGAINST( \'' . ( is_array( $value ) ? implode( ' ', $value ) : $value ) . '\' ) ';
            } elseif ( is_array( $value ) && isset( $value[ 'LOGIC' ] ) ) {
                $where[] = '(' . str_replace( 'WHERE', '', $this->_buildSQLWhere( $value, $value[ 'LOGIC' ], $match_select ) ) . ')';
            } elseif ( is_array( $value ) && count( $value ) > 0 ) {
                $sign = $this->getSign( $key );
                if ( $sign == '~' ) {
                    $where[] = ' (' . str_replace( $sign, '', $key ) . ' LIKE \'' . implode( '\' OR ' . str_replace( $sign, '', $key ) . ' LIKE \'', $value ) . '\') ';
                } elseif ($sign == '!=') {
                    $where[] = str_replace('!', '', str_replace( $sign, '', $key )) . ' ' . ( $sign == '!=' ? ' NOT IN ' : ' IN ' ) . ' (\'' . implode( '\',\'', $value ) . '\') ';
                } else {
                    $where[] = str_replace( $sign, '', $key ) . ' ' . ( $sign == '!=' ? ' NOT IN ' : ' IN ' ) . ' (\'' . implode( '\',\'', $value ) . '\') ';
                }
            } else {
                $sign =  $this->getSign( $key );
                if ( $sign == '~' ) {
                    $where[] = str_replace( $sign, '', $key ) . ' LIKE \'' . $value . '\'';
                } elseif ($sign == '!=') {
                    $where[] = str_replace('!', '', str_replace( $sign, '', $key )) . ' ' . $sign . ' ' . ( is_numeric( $value ) ? $value : '\'' . $this->prepareValue( $value ) . '\'' );
                } else {
                    $where[] = str_replace( $sign, '', $key ) . ' ' . $sign . ' ' . ( is_numeric( $value ) ? $value : '\'' . $this->prepareValue( $value ) . '\'' );
                }
            }
        }

        if ( count( $where ) > 0 ) {
            $where = "
			WHERE
				" . implode( ' ' . $logic . ' ', $where ) . "
			";
        } else {
            $where = '';
        }

        return $where;
    }

    /**
     * Returns list of results searched with params
     * @param array $params
     * params of result query = array(
     *     'filter' => array('field' => <value>, ...)
     *     'order' => array('field' => 'asc|desc'),
     *     'select' => array('*'|<array of fields names>),
     *     'offset' => LIMIT offset, default 0
     *     'limit' => Limit count, default false - no limit
     * )
     * @param array $fieldsConfig WebForm fields configuration
     * @param bool $resourceReturn if false will be returned an array, if true will be returned resource
     * @return array|\Bitrix\Main\DB\Result
     */
    public function getList(array $params = array('order' => array('datetime' => 'desc')), $fieldsConfig = array(), $resourceReturn = false) {
        $defaultParams = array(
            'select' => array('*'),
            'filter' => array(),
            'order' => array(),
            'offset' => 0,
            'limit' => 0,
        );

        $params = array_merge($defaultParams, $params);

        if (count($fieldsConfig) > 0) {
            $select = array();
            if ($params['select'] == '*' || is_array($params['select']) && $params['select'][0] == '*') {
                $params['select'] = array_merge(array('id', 'datetime'), array_keys($fieldsConfig));
            }
            foreach ($params['select'] as $field) {
                if (isset($fieldsConfig[$field])) {
                    switch ($fieldsConfig[$field]['type']) {
                        case WebForm::FIELD_TYPE_CHECKBOX:
                            $selectStr = "CASE `{$field}` ";
                            $selectStr .= " WHEN '" . str_replace("'", "\\'", 0) . "' THEN '" . (LANGUAGE_ID == 'ru' ? 'нет' : 'no') . "' ";
                            $selectStr .= " WHEN '" . str_replace("'", "\\'", 1) . "' THEN '" . (LANGUAGE_ID == 'ru' ? 'да' : 'yes') . "' ";
                            $selectStr .= " END AS {$field}";

                            $select[] = $selectStr;
                            break;
                        case WebForm::FIELD_TYPE_SELECT_MULTIPLE:
                            if (isset($fieldsConfig[$field]['list']) && is_array($fieldsConfig[$field]['list'])) {
                                $selectStr = "{$field}";
                                foreach ($fieldsConfig[$field]['list'] as $valueId => $valueParams) {
                                    $selectStr = " REPLACE(" . $selectStr . ", '|" . str_replace("'", "\\'", $valueId) . "|', '|" . str_replace("'", "\\'", $valueParams['title'][LANGUAGE_ID]) . "|') ";
                                }
                                $selectStr = " SUBSTRING(REPLACE({$selectStr}, '|', ', '), 2, CHAR_LENGTH(REPLACE({$selectStr}, '|', ', '))-3) as {$field} ";
                            } else {
                                $selectStr = $field;
                            }

                            $select[] = $selectStr;
                            break;
                        case WebForm::FIELD_TYPE_SELECT:
                        case WebForm::FIELD_TYPE_RADIO:
                            if (isset($fieldsConfig[$field]['list']) && is_array($fieldsConfig[$field]['list'])) {
                                $selectStr = "CASE `{$field}` ";
                                foreach ($fieldsConfig[$field]['list'] as $valueId => $valueParams) {
                                    $selectStr .= " WHEN '" . str_replace("'", "\\'", $valueId) . "' THEN '" . str_replace("'", "\\'", $valueParams['title'][LANGUAGE_ID]) . "' ";
                                }
                                $selectStr .= " END AS {$field}";
                            } else {
                                $selectStr = $field;
                            }

                            $select[] = $selectStr;
                            break;
                        default:
                            $select[] = $field;
                    }
                } else {
                    $select[] = $field;
                }
            }
            $params['select'] = $select;
        }

        $sql = "SELECT " . implode(', ', $params['select']) . " FROM " . $this->tableName . " ";

        $order = array();

        foreach($params['order'] as $field => $direction) {
            $order[] = $field . ' ' . (in_array($direction, array('asc', 'desc')) ? $direction : 'asc');
        }

        if (is_array($params['filter']) && count($params['filter']) > 0) {
            $where = array();
            /*foreach ($params['filter'] as $field_name => $field_value) {
                if (is_numeric($field_name)) {
                    $where[] = $this->prepareValue($field_value);
                } elseif (is_array($field_value) && count($field_value) > 0) {
                    $sign = $this->getSign($field_name);
                    $field_name = str_replace($sign[1], '', $field_name);
                    if ($sign[0] == '~') {
                        $where[] = ' (' .$field_name . ' LIKE \'' . implode('\' OR ' . $field_name . ' LIKE \'', $field_value) . '\') ';
                    } else {
                        $where[] = $field_name . ' ' . ($sign[0] == '!=' ? ' NOT IN ' : ' IN ') . ' (\'' . implode('\',\'', $field_value) . '\') ';
                    }
                } else {
                    $sign = $this->getSign($field_name);
                    $field_name = str_replace($sign[1], '', $field_name);
                    if ($sign[0] == '~') {
                        $where[] = $field_name . ' LIKE \'' . $field_value . '\'';
                    } else {
                        $where[] = $field_name . ' ' . $sign[0] . ' ' . (is_numeric($field_value) ? $field_value : "'" . $this->prepareValue($field_value) . "'");
                    }
                }*//* elseif (is_array($field_value)) {
                    $where[] = $field_name . " IN (" . implode(',', $field_value) . ")";
                } else {
                    $where[] = "{$field_name} = '" . $this->prepareValue($field_value) . "'";
                }*/
            /*}*/

            $where = $this->_buildSQLWhere($params['filter'], 'AND', $match_select);

            $sql .= " " . $where;
        }

        if (count($order) > 0) {
            $sql .= " ORDER BY " . implode(', ', $order) . " ";
        }

        if ((int)$params['limit']) {
            $sql .= "LIMIT " . (int)$params['offset'] . ", " . (int)$params['limit'];
        }

        $res = $this->query($sql);

        if ($resourceReturn) {
            return $res;
        }

        return $this->getArray($res);
    }

    /**
     * Get result by id
     * @param int $id
     * @return bool|mixed
     */
    public function getById($id) {
        $list = $this->getList(array('filter' => array('id' => $id)));
        return count($list) > 0 ? array_shift($list) : false;
    }

    /**
     * Delete result from database by id
     * @param int $id
     * @return \Bitrix\Main\DB\Result|bool
     */
    public function delete($id) {
        $sql = "
			DELETE FROM
				`" . $this->tableName . "`
			WHERE
				`" . $this->tableName . "`.`id` = '{$id}'
		";

        $result = false;
        if (!$this->externalDbCon) {
            $result = Application::getConnection()->query($sql);
        } else {
            $result = mysql_query($sql, $this->externalDbCon);
        }

        return $result;
    }

    /**
     * Prepares value before using in sql query
     * @param string $value
     * @return string
     */
    protected function prepareValue($value) {
        return str_replace("'", '\\\'', $value);
    }

    /**
     * Returns sql query result
     * @param $sql
     * @return \Bitrix\Main\DB\Result|bool
     */
    protected function query($sql) {
        $result = false;

        if (!$this->externalDbCon) {
            $result = Application::getConnection()->query($sql);
        } else {
            $result = mysql_query($sql, $this->externalDbCon);
        }

        return $result;
    }

    /**
     * Returns last insert id
     * @return int
     */
    protected function getInsertedId() {
        $id = 0;
        if (!$this->externalDbCon) {
            $id = Application::getConnection()->getInsertedId();
        } else {
            $id = mysql_insert_id($this->externalDbCon);
        }
        return $id;
    }

    /**
     * Returns associative array of sql query result
     * @param \Bitrix\Main\DB\Result|resource $result
     * @return array
     */
    protected function getArray($result) {
        $data = array();

        if (!$this->externalDbCon) {
            $data = $result->fetchAll();
        } else {
            while ($row = mysql_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Returns sign of filter expression
     * @param $key
     * @return string sign
     */
    protected function getSign($key) {
        $sign = substr( $key, 0, 2 );
        switch ( $sign ) {
            case '!=':
            case '>=':
            case '<=':
                break;

            default:
                $sign = substr( $key, 0, 1 );
                switch ( $sign ) {
                    case '=':
                    case '>':
                    case '<':
                    case '~':
                        break;

                    case '!':
                        $sign = '!=';
                        break;

                    default:
                        $sign = '=';
                        break;
                }
                break;
        }

        return $sign;
    }
}
