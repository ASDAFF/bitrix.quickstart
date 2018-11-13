<?php

namespace Sprint\Migration;


class Version20170213000008 extends Version
{

    protected $description = "Пример работы миграции с сохранением промежуточных данных в бд";

    public function up() {
        //сохраняем данные этой миграции
        $this->saveData('var1', '1234567');
        $this->saveData('var2', array(
            'bbb' => 'axcx',
            'bbbb' => 'axcx',
        ));

        //получаем данные этой миграции
        $var1 = $this->getSavedData('var1');
        $var2 = $this->getSavedData('var2');

        //удаляем выбранные данные этой миграции
        $this->deleteSavedData('var1');

        //удаляем все данные этой миграции
        $this->deleteSavedData();

        //получаем сохраненные данные какой-либо другой миграции
        $storage = new StorageManager();
        $var1 = $storage->getSavedData('Version20170213000007', 'var1');

    }

    public function down() {
        //
    }

}
