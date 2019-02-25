<?php

/**
 * Main database class for Sitemap generator
 */
class SiteMapDb
{
  /**
   * @var string - contain sql query
   */
  protected $sql = '';

  /**
   * @var array - contain main errors
   */
  protected $errors = array();

  public function query($table = '', $select = array(), $where = array(), $limit = '', $sort = array(), $group = array())
  {
    $this->getSelect($select);
    $this->getTable($table);
    $this->getWhere($where);
    $this->getGroup($group);
    $this->getSort($sort);
    $this->getLimit($limit);

    if (empty($this->errors))
    {
      return $this->execute();
    }
    else
    {
      return $this->getErrorCode();
    }
  }

  protected function getErrorCode()
  {
    if (!empty($this->errors))
    {
      return $this->errors;
    }
  }

  protected function execute()
  {
    global $DB;
    return $DB->Query($this->sql);
  }

  protected function getTable($table = '')
  {
    if (empty($table))
    {
      $this->errors[] = 1;
    }

    $this->sql .= ' FROM `' . $table . '` ';
  }

  protected function getSelect($select = array())
  {
    $this->sql = 'SELECT';
    if (!empty($select[0])
        && is_array($select[0])
    )
    {
      for ($i = 0; $i < $ic = count($select); $i++)
      {
        $conduction = $this->getSelectConduction($select[$i][0]);
        if ($conduction)
        {
          $this->sql .= $conduction . '(`' . $select[$i][1] . '`), ';
        }
        else
        {
          $this->sql .= '`' . implode('`,`', $select[$i]) . '`, ';
        }
      }
    }
    else
    {
      if ($select[0] == '*')
      {
        $this->sql .= ' * , ';
      }
      else
      {
        $this->sql .= ' `' . implode('`,`', $select) . '`, ';
      }
    }

    $this->sql = substr($this->sql, 0, -2);
  }

  protected function getWhere($where = array())
  {
    global $DB;
    if (!empty($where))
    {
      $this->sql .= ' WHERE ';
      if (!empty($where[1])
          && !is_array($where[1])
      )
      {
        // single array
        $this->get3Where($where);
      }
      else if (is_array($where[1]))
      {
        for ($i = 0; $i < $ic = count($where); $i++)
        {
          // multiple array
          if (is_array($where[$i]))
          {
            $cnt = count($where[$i]);
            if ($cnt == 4)
            {
              $this->get4Where($where[$i]);
            }
            else if ($cnt == 3)
            {
              $this->get3Where($where[$i]);
            }
          }
        }
      }
    }
  }

  protected function get3Where($row = array())
  {
    global $DB;
    $field = '`' . $row[0] . '`';
    $operand = $this->checkOperand($row[1]);
    $value = '\'' . $DB->ForSQL($row[2]) . '\'';
    if (empty($operand))
    {
      $this->errors = 2;
    }
    $this->sql .= ' ' . $field . $operand . $value . ' ';
  }

  protected function get4Where($row = array())
  {
    global $DB;
    $logical = $this->checkLogicalConduction($row[0]);
    if (empty($logical))
    {
      $this->errors = 3;
    }

    $field = '`' . $row[1] . '`';
    $operand = $this->checkOperand($row[2]);
    $value = '\'' . $DB->ForSQL($row[3]) . '\'';
    if (empty($operand))
    {
      $this->errors = 2;
    }
    $this->sql .= ' ' . $logical . $field . $operand . $value . ' ';
  }

  protected function getLimit($limit = '')
  {
    if (!empty($limit)
        && is_string($limit)
    )
    {
      $this->sql .= ' LIMIT ' . $limit;
    }
  }

  protected function getSort($sort = array())
  {
    if (!empty($sort))
    {
      $this->sql .= ' ORDER BY ';
      if (!empty($sort)
          && !is_array($sort[0])
      )
      {
        // single array
        $field = '`' . $sort[0] . '`';
        $conduction = $this->checkSortConduction($sort[1]);
        if (empty($conduction))
        {
          $this->errors = 3;
        }
        $this->sql .= ' ' . $field . ' ' . $conduction . ', ';
      }
      else if (is_array($sort))
      {
        for ($i = 0; $i < $ic = count($sort); $i++)
        {
          if (is_array($sort[$i]))
          {
            $field = '`' . $sort[$i][0] . '`';
            $conduction = $this->checkSortConduction($sort[$i][1]);
            if (empty($conduction))
            {
              $this->errors = 3;
            }
            $this->sql .= ' ' . $field . ' ' . $conduction . ',';
          }
        }
      }

      $this->sql = substr($this->sql, 0, -2);
    }
  }

  protected function getGroup($group = array())
  {
    if (!empty($group))
    {
      if (!empty($group)
          && is_array($group)
      )
      {
        $this->sql .= ' GROUP BY `' . implode('`,`', $group) . '`';
      }
    }
  }

  protected function checkOperand($operand = '')
  {
    $op = '';
    switch ($operand)
    {
      case '=':
      case '>':
      case '<':
      case '>=':
      case '<=':
      case '<>':
        $op = $operand;
        break;
    }

    return $op;
  }

  protected function checkLogicalConduction($conduction = '')
  {
    $cond = '';
    switch ($conduction)
    {
      case 'AND':
      case 'OR':
        $cond = ' ' . $conduction . ' ';
        break;
    }

    return $cond;
  }

  protected function checkSortConduction($conduction = '')
  {
    $cond = '';
    switch ($conduction)
    {
      case 'ASC':
      case 'DESC':
        $cond = ' ' . $conduction . ' ';
        break;
    }

    return $cond;
  }

  protected function getSelectConduction($conduction = '')
  {
    $cond = '';
    switch ($conduction)
    {
      case 'COUNT':
      case 'DISTINCT':
        $cond = $conduction;
        break;
    }

    return $cond;
  }
}