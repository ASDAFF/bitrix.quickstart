<?
$MESS ['MYSTERY_THUMBS_DOC_TAB_SET'] = "������������";
$MESS ['MYSTERY_THUMBS_MAIN_TAB_SET'] = "����� ���������";
$MESS ['MYSTERY_THUMBS_HEADING_MAIN'] = "����� ���������";
$MESS ['MYSTERY_THUMBS_JPG_QUALITY'] = "�������� ������ ��� <b>*.jpg</b>";
$MESS ['MYSTERY_THUMBS_BACKGROUND_COLOR'] = "HEX-��� �������� �����";
$MESS ['MYSTERY_THUMBS_BACKGROUND_MESSAGE'] = '��� ���� ���� "�����������" ������ �����������';
$MESS ['MYSTERY_THUMBS_PNG_TRANSPARENT'] = "���������� ��� ��� <b>*.png</b>";
$MESS ['MYSTERY_THUMBS_HEADING_WATERMARK'] = '��������� ��������� ������� �����';
$MESS ['MYSTERY_THUMBS_WATERMARK_ENABLE'] = '�������� ������ ���� �� �����������';
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION'] = '��������� ������� ����� �� �����������';
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_LT'] = "����� ������� ����";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_CT'] = "�� ������ ������";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_RT'] = "������ ������� ����";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_LM'] = "����� �� ������";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_CM'] = "�� ������";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_RM'] = "������ �� ������";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_LB'] = "����� ������ ����";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_CB'] = "�� ������ �����";
$MESS ['MYSTERY_THUMBS_WATERMARK_POSITION_RB'] = "������ ������ ����";
$MESS ['MYSTERY_THUMBS_WATERMARK_MIN_WIDTH_PICTURE'] = '����������� ������ �������� ��� ��������� ������� �����';
$MESS ['MYSTERY_THUMBS_WATERMARK_EXCEPTION'] = '������ ��������-���������� ��� ��������� ������� �����';
$MESS ['MYSTERY_THUMBS_WATERMARK_EXCEPTION_DESC'] = '������� ������� ����� ����� � ������� ��� �������� ���������� �������.<br /><i>��������: /about/; /articles/</i>';
$MESS ['MYSTERY_THUMBS_WATERMARK_IMG'] = '����������� ������� ����� (������ <b>*.png</b>, �������� <b>copyright.png</b>)';
$MESS ['MYSTERY_THUMBS_WATERMARK_IMG_DESC'] = '<a href="/bitrix/admin/fileman_admin.php?path=/bitrix/images/mystery.thumbs&show_perms_for=0" target="_blank">'.MYSTERY_THUMBS_WATERMARK_IMG.'</a>';
$MESS ['MYSTERY_THUMBS_WATERMARK_ALT'] = '������ ����';
$MESS ['MYSTERY_THUMBS_COLOR_PICKER'] = '����� �����';
$MESS ['MYSTERY_THUMBS_FORM_SAVE'] = "���������";
$MESS ['MYSTERY_THUMBS_FORM_RESET'] = "��������";
$MESS ['MYSTERY_THUMBS_MAIN_RESTORE_DEFAULTS'] = "����������� �������� �� ���������";
$MESS ['MYSTERY_THUMBS_HEADING_ADDITIONAL_PARAMS'] = "�������������� ���������";
$MESS ['MYSTERY_THUMBS_DELETE_OLD_THUMBS'] = "������� ����� ��������� �����������";
$MESS ['MYSTERY_THUMBS_DELETE_OLD_THUMBS_DESC'] = "���� �� ������ ��� �������� ��� ��������� ������������� ������� �����, �������� ��� �����, ����� ��� ���������� �������� ��������� ������, � ������ ����� ��������.";
$MESS ['MYSTERY_THUMBS_DOCUMENTATION'] = '

<tr class="heading">
    <td colspan="2">�������� ������</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            ������ <b>mystery.thumbs</b> ��������� ����������� ���������� �������������, ������������ �� ����:
            <ol>
                <li>��������� ������ ����������� ��������� "�� ����".</li>
                <li>����� ������� ����� ��������� �����������.</li>
                <li>����� �������� ������ ����.</li>
            </ol>
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">��� ������������</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            ��� ��������� ���������� ������� ����������� ���������� � ���� � �������� �������� (� �������� src ���� &lt;img&gt;) /thumb/ � ������� ��������� ���������.
        </p>
        <p>
            ��������:
            <ul>
                <li>
                    &lt;img src="<b>/thumb/150x120xin</b>/upload/iblock/jd8/jd8kdk19dn2j29d8jspssv.jpg"&gt;<br />
                    <i>�������� ����������� /upload/iblock/jd8/jd8kdk19dn2j29d8jspssv.jpg</i>
                </li>
                <li>
                    &lt;img src="<b>/thumb/450x840xcut</b>//www.yousite.images.yousite.ru/upload/iblock/5bd/5db9cefbc414a902a46f1b8fae16.png?anyparam=true"&gt;<br />
                    <i>�������� ����������� //www.yousite.images.yousite.ru/upload/iblock/5bd/5db9cefbc414a902a46f1b8fae16.png ��������� � ������</i>
                </li>
            </ul>
        </p>
        <p>
            ����� ������ ����������� ���������� ��������� <b>/thumb/W</b>x<b>H</b>x<b>METHOD<span style="color:red">IMAGE</span></b>:
            <ul>
                <li><b>W</b> - ������ ���������� �����������</li>
                <li><b>H</b> - ������ ���������� �����������</li>
                <li><b>METHOD</b> - ����� ��������� ��������� �����������</li>
                <li><b>IMAGE</b> - ���� �� ��������� �����������</li>
            </ul>
            �����������:
            <ul>
                <li>
                    ��������� ����������� <b>������</b> ���������� �������.<br />
                    <i>���� �������� �������� ������ �� ����� ��������, ��� ���������, �� ��� ����� ��������� �� ������ ����������� ����������� ��� ��������� �������.</i>
                </li>
                <li>
                    <b>W</b> � <b>H</b> ����� ���� ������� ��� <b>0</b> (��� �������, ��� ������ �������� ����� ������ ����).<br />
                    <i>� ���� ������ �� ��������� ������ ����� ��������� ��������������� ������ �� �������� ��������� �����������.</i>
                </li>
                <li>
                    <b>IMAGE</b> - ����� ���� ������ ��� ��������� (���� �� ��� �� �������, ��� � ����), ��� � ���������� (�� ���������� ����� ��� �� ������)
                </li>
                <li>
                    ��� ��������� ����������� �������� � ����� <b>'.MYSTERY_THUMBS_CHACHE_IMG_PATH.'</b> �� ����� �����.<br />
                    <i>��� ��������� ������� ��������� ����� ��������� �����������.</i>
                </li>
            </ul>
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">�������� �������</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            ���������� ��������� ������ ��������� �����������:
            <ul>
                <li>
                    <b>IN</b> - �������� �������� ���������� "������" ���������� ���������� ������� � ��������������� ����������� ����.<br />
                    "������" ���� ��������� ����������� "����������" ��������� � ���������� ������ ������.<br />
                    <i>(��� <b>*.png</b> ����������� �������� �������� ���������� �����)</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/150x200xin/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <img src="/thumb/250x200xin/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <img src="/thumb/350x200xin/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                </li>
                <li>
                    <b>CUT</b> - �������� �������� ��������� ��������� ��������� ���������. ��������������� ����������� ����������� ����������� �� ������ ����������.<br />
                    <i>"������" ���� �������� ��������, ������� ������� �� ������� ���������� ���������� ����������.</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/150x200xcut/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <img src="/thumb/250x200xcut/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <img src="/thumb/350x200xcut/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                </li>
                <li>
                    <b>CUTT</b> - <i>(����� "CUT TOP")</i> ����� ���������� ������ CUT � ����� ��: ��������������� ����������� ����������� ������������� � ��������� ���������� ������� � ������� ����� �����.<br />
                    <i>�� ������ �������� ����� ��� � � CUT ������������� �� ������.</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/150x200xcutt/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <img src="/thumb/250x200xcutt/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <img src="/thumb/350x200xcutt/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <br />
                    <i>� ���� ������ ����������� ����� ������ ������� ������ ����, ������� �� � �����.</i>
                </li>
                <li>
                    <b>TRIM</b> - � �������� �������� ����������� ���� �� ���� ������: ����� � ������ - ������ W, ����� � ������ - ������ H.<br />
                    ���� ����������� ����� ����������� � ���������� ������.<br />
                    <i>(��� <b>*.png</b> ����������� �������� ���������� ���������� �����)</i>
                </li>
                <li class="noPoint">
                    <img src="/thumb/25x20xtrim/bitrix/images/mystery.thumbs/test.png" class="mysteryThumbsTest" alt="�������� �����������">
                    <br />
                    <i>����� � ������ - �� 20 ��������, ������ � ����� - �� 25.</i>
                </li>
            </ul>
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">��������� �������� �� ������</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            ��� ������������� "��������� ����� (CDN)" ��� ��������� ������� ��� �������� ����������� ���� �� �������� �������� ����� �� ������� ������. <br />
            � �������� <b>IMAGE</b> ����� ������� ��� ���������� (�������) ���� �� �����������, ��� � ������������� (���������).
        </p>
        <p>
            � ����� ������ �������� �������� ����� �������������� � ����� '.MYSTERY_THUMBS_CHACHE_IMG_PATH.'.
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">��������� ������� �����</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            ��� ������������� � ���������� ������ ����� �������� ��������� ������� ����� � ��������� ��������� ��� ������������ �� �������� �����������.<br />
            ������ ���� ������ ���� ���������� ����� �� ���������� ����: <b>'.MYSTERY_THUMBS_WATERMARK_IMG.'</b>.<br />
            ��������� ������� ��������� � ������� ������ ������ ����������.
        </p>
    </td>
</tr>

<tr class="heading">
    <td colspan="2">��� ��� ��������</td>
</tr>
<tr>
    <td colspan="2">
        <p>
            ��� ��������� ������, ��������� ������ � <a href="/bitrix/admin/urlrewrite_list.php?lang=ru" target="_blank">"��������� �������"</a>, � ������� ������� ��� ������� ������������ � /thumb/ �������������� ������� �� ���� mystery_thumbs.php � ����� �����, � ����� �� ��������� �����, ������� ��������������� ������� ��������� �����������.
        </p>
    </td>
</tr>
';
?>