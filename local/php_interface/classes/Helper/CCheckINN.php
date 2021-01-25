<?php
/**
 * Copyright (c) 26/1/2021 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper;


class CCheckINN
{

    /**
     * @param $inn
     * @param $stolbinn - номер столбца с ИНН
     * @param $file_path - Путь до csv файла
     * @param array $file_encodings
     * @param string $col_delimiter - Разделитель колонки (по умолчанию автоопределине)
     * @param string $row_delimiter - Разделитель строки (по умолчанию автоопределине)
     * @return bool
     *
     * Читает CSV файл, проверяет ИНН. Как только находит - возвращает true
     *
     */
    function checkinn($inn, $stolbinn, $file_path, $file_encodings = ['cp1251','UTF-8'], $col_delimiter = '', $row_delimiter = "" )
    {
        if( ! file_exists($file_path) )
            return false;

        $cont = trim( file_get_contents( $file_path ) );

        $encoded_cont = mb_convert_encoding( $cont, 'UTF-8', mb_detect_encoding($cont, $file_encodings) );

        unset( $cont );

        // определим разделитель
        if( ! $row_delimiter ){
            $row_delimiter = "\r\n";
            if( false === strpos($encoded_cont, "\r\n") )
                $row_delimiter = "\n";
        }

        $lines = explode( $row_delimiter, trim($encoded_cont) );
        $lines = array_filter( $lines );
        $lines = array_map( 'trim', $lines );

        // авто-определим разделитель из двух возможных: ';' или ','.
        // для расчета берем не больше 30 строк
        if( ! $col_delimiter ){
            $lines10 = array_slice( $lines, 0, 30 );

            // если в строке нет одного из разделителей, то значит другой точно он...
            foreach( $lines10 as $line ){
                if( ! strpos( $line, ',') ) $col_delimiter = ';';
                if( ! strpos( $line, ';') ) $col_delimiter = ',';

                if( $col_delimiter ) break;
            }

            // если первый способ не дал результатов, то погружаемся в задачу и считаем кол разделителей в каждой строке.
            // где больше одинаковых количеств найденного разделителя, тот и разделитель...
            if( ! $col_delimiter ){
                $delim_counts = array( ';'=>array(), ','=>array() );
                foreach( $lines10 as $line ){
                    $delim_counts[','][] = substr_count( $line, ',' );
                    $delim_counts[';'][] = substr_count( $line, ';' );
                }

                $delim_counts = array_map( 'array_filter', $delim_counts ); // уберем нули

                // кол-во одинаковых значений массива - это потенциальный разделитель
                $delim_counts = array_map( 'array_count_values', $delim_counts );

                $delim_counts = array_map( 'max', $delim_counts ); // берем только макс. значения вхождений

                if( $delim_counts[';'] === $delim_counts[','] )
                    return array('Не удалось определить разделитель колонок.');

                $col_delimiter = array_search( max($delim_counts), $delim_counts );
            }

        }

        $inn=trim($inn);
        $data = [];
        foreach( $lines as $key => $line ){
            $tmp = array();
            $tmp = str_getcsv( $line, $col_delimiter ); // linedata
            // проверяем ИНН
            if (trim($tmp[$stolbinn])==$inn)
            {
                return true;
            }
            $data[] = $tmp;
            unset( $lines[$key] );
        }

        return false;
    }
}