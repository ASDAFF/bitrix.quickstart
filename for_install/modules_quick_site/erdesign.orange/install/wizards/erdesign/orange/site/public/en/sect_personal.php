

<?$APPLICATION->IncludeComponent("bitrix:news.line", "personal", Array(
										"IBLOCK_TYPE" => "service",	// ��� ��������������� �����
										"IBLOCKS" => "#PERSONAL_IBLOCK_ID#",										
										"NEWS_COUNT" => "10",	// ���������� �������� �� ��������
										"FIELD_CODE" => array(	// ����
											0 => "NAME",
											1 => "PREVIEW_PICTURE",
										),
										"SORT_BY1" => "ACTIVE_FROM",	// ���� ��� ������ ���������� ��������
										"SORT_ORDER1" => "DESC",	// ����������� ��� ������ ���������� ��������
										"SORT_BY2" => "SORT",	// ���� ��� ������ ���������� ��������
										"SORT_ORDER2" => "ASC",	// ����������� ��� ������ ���������� ��������
										"DETAIL_URL" => "",	// URL, ������� �� �������� � ���������� �������� �������
										"ACTIVE_DATE_FORMAT" => "d.m.Y",	// ������ ������ ����
										"CACHE_TYPE" => "A",	// ��� �����������
										"CACHE_TIME" => "300",	// ����� ����������� (���.)
										"CACHE_GROUPS" => "Y",	// ��������� ����� �������
										),
										false
									);?>