<?php

namespace Helper;


/**
 * Class SimpleHblockObject
 * Класс для работы с Highload блоками любого типа
 *
 * @example
 * Объект любого HL блока
 *
 * $operations = new \HBlock\SimpleHblockObject(2);
 * $operations->getList(....)
 */
class SimpleHblockObject extends HblockObject
{
  /**
   * @param $hblockId int ID HL блока
   * @throws \Exception
   */
  public function __construct($hblockId)
  {
    $this->hblockId = $hblockId;

    parent::__construct();
  }

} 