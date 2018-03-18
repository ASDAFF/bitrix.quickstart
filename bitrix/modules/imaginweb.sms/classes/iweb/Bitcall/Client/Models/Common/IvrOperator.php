<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 16.05.2014
 */


namespace Bitcall\Client\Models\Common;


abstract class IvrOperator extends  Enum {

    /**
     * Проиграть файл
     */
    const Playback = 'Playback';
    /**
     * Синтезировать текст
     */
    const Saytext = 'Saytext';
    /**
     * Синтезировать цифры
     */
    const Saydigits = 'Saydigits';
    /**
     * Проиграть файл в фоне
     */
    const Background = 'Background';
    /**
     * Синтезировать текст и произнести в фоне
     */
    const Backgroundtext = 'Backgroundtext';
    /**
     * Ожидать нажатия клавиши
     */
    const Waitexten = 'Waitexten';
    /**
     * Обработать нажатие клавиши
     */
    const Input = 'Input';
    /**
     * Ждать
     */
    const Wait = 'Wait';
    /**
     * Соединить с другим номером
     */
    const Dial = 'Dial';
    /**
     * Разъединить
     */
    const Disconnect = 'Disconnect';
    /**
     * Обработка, если абонент не нажал ни одной клавиши
     */
    const Noinput = 'Noinput';
    /**
     * Обработка нажатия клавиши, для которой не был указан Input
     */
    const Nomatch = 'Nomatch';
} 