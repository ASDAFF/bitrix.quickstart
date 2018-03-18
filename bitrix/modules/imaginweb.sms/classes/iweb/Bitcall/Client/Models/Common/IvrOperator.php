<?php
/**
 * Created by http://bitcall.ru
 * User: Kurdikov P.S.
 * Date: 16.05.2014
 */


namespace Bitcall\Client\Models\Common;


abstract class IvrOperator extends  Enum {

    /**
     * ��������� ����
     */
    const Playback = 'Playback';
    /**
     * ������������� �����
     */
    const Saytext = 'Saytext';
    /**
     * ������������� �����
     */
    const Saydigits = 'Saydigits';
    /**
     * ��������� ���� � ����
     */
    const Background = 'Background';
    /**
     * ������������� ����� � ���������� � ����
     */
    const Backgroundtext = 'Backgroundtext';
    /**
     * ������� ������� �������
     */
    const Waitexten = 'Waitexten';
    /**
     * ���������� ������� �������
     */
    const Input = 'Input';
    /**
     * �����
     */
    const Wait = 'Wait';
    /**
     * ��������� � ������ �������
     */
    const Dial = 'Dial';
    /**
     * �����������
     */
    const Disconnect = 'Disconnect';
    /**
     * ���������, ���� ������� �� ����� �� ����� �������
     */
    const Noinput = 'Noinput';
    /**
     * ��������� ������� �������, ��� ������� �� ��� ������ Input
     */
    const Nomatch = 'Nomatch';
} 