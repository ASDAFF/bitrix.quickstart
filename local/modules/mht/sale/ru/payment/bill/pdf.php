<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
$arPaySysAction["ENCODING"] = "";
$ORDER_ID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
if (!is_array($arOrder))
	$arOrder = CSaleOrder::GetByID($ORDER_ID);

if (!CSalePdf::isPdfAvailable())
	die();

if ($_REQUEST['BLANK'] == 'Y')
	$blank = true;

$pdf = new CSalePdf('P', 'pt', 'A4');

if (CSalePaySystemAction::GetParamValue('BACKGROUND'))
{
	$pdf->SetBackground(
		CSalePaySystemAction::GetParamValue('BACKGROUND'),
		CSalePaySystemAction::GetParamValue('BACKGROUND_STYLE')
	);
}

$pageWidth  = $pdf->GetPageWidth();
$pageHeight = $pdf->GetPageHeight();

$pdf->AddFont('Font', '', 'pt_sans-regular.ttf', true);
$pdf->AddFont('Font', 'B', 'pt_sans-bold.ttf', true);

$fontFamily = 'Font';
$fontSize   = 10.5;

$margin = array(
	'top' => intval(CSalePaySystemAction::GetParamValue('MARGIN_TOP') ?: 15) * 72/25.4,
	'right' => intval(CSalePaySystemAction::GetParamValue('MARGIN_RIGHT') ?: 15) * 72/25.4,
	'bottom' => intval(CSalePaySystemAction::GetParamValue('MARGIN_BOTTOM') ?: 15) * 72/25.4,
	'left' => intval(CSalePaySystemAction::GetParamValue('MARGIN_LEFT') ?: 20) * 72/25.4
);

$width = $pageWidth - $margin['left'] - $margin['right'];

$pdf->SetDisplayMode(100, 'continuous');
$pdf->SetMargins($margin['left'], $margin['top'], $margin['right']);
$pdf->SetAutoPageBreak(true, $margin['bottom']);

$pdf->AddPage();


$y0 = $pdf->GetY();
$logoHeight = 0;
$logoWidth = 0;

if (CSalePaySystemAction::GetParamValue('PATH_TO_LOGO'))
{
	list($imageHeight, $imageWidth) = $pdf->GetImageSize(CSalePaySystemAction::GetParamValue('PATH_TO_LOGO'));

	$logoHeight = $imageHeight + 5;
	$logoWidth  = $imageWidth + 5;

	$pdf->Image(CSalePaySystemAction::GetParamValue('PATH_TO_LOGO'), $pdf->GetX(), $pdf->GetY());
}

$pdf->SetFont($fontFamily, 'B', $fontSize);

$pdf->SetX($pdf->GetX() + $logoWidth);
$pdf->Write(15, CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("SELLER_NAME")));
$pdf->Ln();

if (CSalePaySystemAction::GetParamValue("SELLER_ADDRESS"))
{
	$pdf->SetX($pdf->GetX() + $logoWidth);
	$pdf->MultiCell(0, 15, CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("SELLER_ADDRESS")), 0, 'L');
}

if (CSalePaySystemAction::GetParamValue("SELLER_PHONE"))
{
	$pdf->SetX($pdf->GetX() + $logoWidth);
	$pdf->Write(15, CSalePdf::prepareToPdf(sprintf("Тел.: %s", CSalePaySystemAction::GetParamValue("SELLER_PHONE"))));
	$pdf->Ln();
}

$pdf->Ln();
$pdf->SetY(max($y0 + $logoHeight, $pdf->GetY()));

if (CSalePaySystemAction::GetParamValue("SELLER_BANK"))
{
	$sellerBank = sprintf(
		"%s %s",
		CSalePaySystemAction::GetParamValue("SELLER_BANK"),
		CSalePaySystemAction::GetParamValue("SELLER_BCITY")
	);
	$sellerRs = CSalePaySystemAction::GetParamValue("SELLER_RS");
}
else
{
	$rsPattern = '/\s*\d{10,100}\s*/';

	$sellerBank = trim(preg_replace($rsPattern, ' ', CSalePaySystemAction::GetParamValue("SELLER_RS")));

	preg_match($rsPattern, CSalePaySystemAction::GetParamValue("SELLER_RS"), $matches);
	$sellerRs = trim($matches[0]);
}

$pdf->SetFont($fontFamily, '', $fontSize);

$x0 = $pdf->GetX();
$y0 = $pdf->GetY();

$pdf->Cell(
	150, 18,
	(CSalePaySystemAction::GetParamValue("SELLER_INN"))
		? CSalePdf::prepareToPdf(sprintf("ИНН %s", CSalePaySystemAction::GetParamValue("SELLER_INN")))
		: ''
);
$x1 = $pdf->GetX();
$pdf->Cell(
	150, 18,
	(CSalePaySystemAction::GetParamValue("SELLER_KPP"))
		? CSalePdf::prepareToPdf(sprintf("КПП %s", CSalePaySystemAction::GetParamValue("SELLER_KPP")))
		: ''
);
$x2 = $pdf->GetX();
$pdf->Cell(50, 18);
$x3 = $pdf->GetX();
$pdf->Cell(0, 18);
$x4 = $pdf->GetX();

$pdf->Line($x0, $y0, $x4, $y0);

$pdf->Ln();
$y1 = $pdf->GetY();

$pdf->Line($x1, $y0, $x1, $y1);

$pdf->Cell(300, 18, CSalePdf::prepareToPdf('Получатель'));
$pdf->Cell(50, 18);
$pdf->Cell(0, 18);

$pdf->Line($x0, $y1, $x2, $y1);

$pdf->Ln();
$y2 = $pdf->GetY();

$text = CSalePaySystemAction::GetParamValue("SELLER_NAME");
while ($pdf->GetStringWidth($text) > 0)
{
	list($string, $text) = $pdf->splitString($text, 300-5);

	$pdf->Cell(300, 18, CSalePdf::prepareToPdf($string));
	if ($text)
		$pdf->Ln();
}
$pdf->Cell(50, 18, CSalePdf::prepareToPdf('Сч. №'));
$pdf->Cell(0, 18, CSalePdf::prepareToPdf($sellerRs));

$pdf->Ln();
$y3 = $pdf->GetY();

$pdf->Cell(300, 18, CSalePdf::prepareToPdf('Банк получателя'));
$pdf->Cell(50, 18, CSalePdf::prepareToPdf('БИК'));
$pdf->Cell(0, 18, CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("SELLER_BIK")));

$pdf->Line($x0, $y3, $x4, $y3);

$pdf->Ln();
$y4 = $pdf->GetY();

$text = $sellerBank;
while ($pdf->GetStringWidth($text) > 0)
{
	list($string, $text) = $pdf->splitString($text, 300-5);

	$pdf->Cell(300, 18, CSalePdf::prepareToPdf($string));
	if ($text)
		$pdf->Ln();
}
$pdf->Cell(50, 18, CSalePdf::prepareToPdf('Сч. №'));
$pdf->Cell(0, 18, CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("SELLER_KS")));

$pdf->Ln();
$y5 = $pdf->GetY();

$pdf->Line($x0, $y5, $x4, $y5);

$pdf->Line($x0, $y0, $x0, $y5);
$pdf->Line($x2, $y0, $x2, $y5);
$pdf->Line($x3, $y0, $x3, $y5);
$pdf->Line($x4, $y0, $x4, $y5);

$pdf->Ln();
$pdf->Ln();


$pdf->SetFont($fontFamily, 'B', $fontSize*2);
$billNo_tmp = CSalePdf::prepareToPdf(sprintf(
	"СЧЕТ № %s от %s",
	$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ACCOUNT_NUMBER"],
	CSalePaySystemAction::GetParamValue("DATE_INSERT")
));
$billNo_width = $pdf->GetStringWidth($billNo_tmp);
$pdf->Cell(0, 20, $billNo_tmp, 0, 0, 'C');
$pdf->Ln();

$pdf->SetFont($fontFamily, '', $fontSize);

if (CSalePaySystemAction::GetParamValue("ORDER_SUBJECT"))
{
	$pdf->Cell($width/2-$billNo_width/2-2, 15, '');
	$pdf->MultiCell(0, 15, CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("ORDER_SUBJECT")), 0, 'L');
}

if (CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE"))
{
	$pdf->Cell($width/2-$billNo_width/2-2, 15, '');
	$pdf->MultiCell(0, 15, CSalePdf::prepareToPdf(sprintf(
		"Срок оплаты %s",
		ConvertDateTime(CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE"), FORMAT_DATE)
			?: CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE")
	)), 0, 'L');
}

$pdf->Ln();

if (CSalePaySystemAction::GetParamValue("BUYER_NAME"))
{
	$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(
		"Плательщик: %s",
		CSalePaySystemAction::GetParamValue("BUYER_NAME")
	)));
	if (CSalePaySystemAction::GetParamValue("BUYER_INN"))
		$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(" ИНН %s", CSalePaySystemAction::GetParamValue("BUYER_INN"))));
	if (CSalePaySystemAction::GetParamValue("BUYER_ADDRESS"))
		$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(", %s", CSalePaySystemAction::GetParamValue("BUYER_ADDRESS"))));
	if (CSalePaySystemAction::GetParamValue("BUYER_PHONE"))
		$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(", %s", CSalePaySystemAction::GetParamValue("BUYER_PHONE"))));
	if (CSalePaySystemAction::GetParamValue("BUYER_FAX"))
		$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(", %s", CSalePaySystemAction::GetParamValue("BUYER_FAX"))));
	if (CSalePaySystemAction::GetParamValue("BUYER_PAYER_NAME"))
		$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(", %s", CSalePaySystemAction::GetParamValue("BUYER_PAYER_NAME"))));
	$pdf->Ln();
}

/*
$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(
	"Грузополучатель: %s ИНН %s, %s",
	CSalePaySystemAction::GetParamValue("BUYER_NAME"),
	CSalePaySystemAction::GetParamValue("BUYER_INN"),
	CSalePaySystemAction::GetParamValue("BUYER_ADDRESS")
)));
$pdf->Ln();
*/

// Список товаров
$dbBasket = CSaleBasket::GetList(
	array("DATE_INSERT" => "ASC", "NAME" => "ASC"),
	array("ORDER_ID" => $ORDER_ID),
	false, false,
	array("ID", "PRICE", "CURRENCY", "QUANTITY", "NAME", "VAT_RATE", "MEASURE_NAME")
);
if ($arBasket = $dbBasket->Fetch())
{
	$arCurFormat = CCurrencyLang::GetCurrencyFormat($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]);
	$currency = trim(str_replace('#', '', $arCurFormat['FORMAT_STRING']));

	$arColsCaption = array(
		1 => CSalePdf::prepareToPdf('№'),
		CSalePdf::prepareToPdf('Наименование товара'),
		CSalePdf::prepareToPdf('Кол-во'),
		CSalePdf::prepareToPdf('Ед.'),
		CSalePdf::prepareToPdf('Цена, '.$currency),
		CSalePdf::prepareToPdf('Ставка НДС'),
		CSalePdf::prepareToPdf('Сумма, '.$currency)
	);
	$arCells = array();
	$arProps = array();
	$arRowsWidth = array(1 => 0, 0, 0, 0, 0, 0, 0);

	for ($i = 1; $i <= 7; $i++)
		$arRowsWidth[$i] = max($arRowsWidth[$i], $pdf->GetStringWidth($arColsCaption[$i]));

	$n = 0;
	$sum = 0.00;
	$vat = 0;
	do
	{
		// props in product basket
		$arProdProps = array();
		$dbBasketProps = CSaleBasket::GetPropsList(
			array("SORT" => "ASC", "ID" => "DESC"),
			array(
				"BASKET_ID" => $arBasket["ID"],
				"!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")
			),
			false,
			false,
			array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
		);
		while ($arBasketProps = $dbBasketProps->GetNext())
		{
			if (!empty($arBasketProps) && $arBasketProps["VALUE"] != "")
				$arProdProps[] = $arBasketProps;
		}
		$arBasket["PROPS"] = $arProdProps;

		$productName = $arBasket["NAME"];
		if ($productName == "OrderDelivery")
			$productName = "Доставка";
		else if ($productName == "OrderDiscount")
			$productName = "Скидка";

		$arCells[++$n] = array(
			1 => CSalePdf::prepareToPdf($n),
			CSalePdf::prepareToPdf($productName),
			CSalePdf::prepareToPdf(roundEx($arBasket["QUANTITY"], SALE_VALUE_PRECISION)),
			CSalePdf::prepareToPdf($arBasket["MEASURE_NAME"] ? $arBasket["MEASURE_NAME"] : 'шт.'),
			CSalePdf::prepareToPdf(SaleFormatCurrency($arBasket["PRICE"], $arBasket["CURRENCY"], true)),
			CSalePdf::prepareToPdf(roundEx($arBasket["VAT_RATE"]*100, SALE_VALUE_PRECISION)."%"),
			CSalePdf::prepareToPdf(SaleFormatCurrency(
				$arBasket["PRICE"] * $arBasket["QUANTITY"],
				$arBasket["CURRENCY"],
				true
			))
		);

		$arProps[$n] = array();
		foreach ($arBasket["PROPS"] as $vv)
			$arProps[$n][] = CSalePdf::prepareToPdf(sprintf("%s: %s", $vv["NAME"], $vv["VALUE"]));

		for ($i = 1; $i <= 7; $i++)
			$arRowsWidth[$i] = max($arRowsWidth[$i], $pdf->GetStringWidth($arCells[$n][$i]));

		$sum += doubleval($arBasket["PRICE"] * $arBasket["QUANTITY"]);
		$vat = max($vat, $arBasket["VAT_RATE"]);
	}
	while ($arBasket = $dbBasket->Fetch());

	if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"]) > 0)
	{
		$arDelivery_tmp = CSaleDelivery::GetByID($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DELIVERY_ID"]);

		$sDeliveryItem = "Доставка";
		if (strlen($arDelivery_tmp["NAME"]) > 0)
			$sDeliveryItem .= sprintf(" (%s)", $arDelivery_tmp["NAME"]);
		$arCells[++$n] = array(
			1 => CSalePdf::prepareToPdf($n),
			CSalePdf::prepareToPdf($sDeliveryItem),
			CSalePdf::prepareToPdf(1),
			CSalePdf::prepareToPdf(''),
			CSalePdf::prepareToPdf(SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			)),
			CSalePdf::prepareToPdf(roundEx($vat*100, SALE_VALUE_PRECISION)."%"),
			CSalePdf::prepareToPdf(SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			))
		);

		for ($i = 1; $i <= 7; $i++)
			$arRowsWidth[$i] = max($arRowsWidth[$i], $pdf->GetStringWidth($arCells[$n][$i]));

		$sum += doubleval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"]);
	}

	$items = $n;

	if ($sum < $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE"])
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			CSalePdf::prepareToPdf("Подытог:"),
			CSalePdf::prepareToPdf(SaleFormatCurrency($sum, $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true))
		);

		$arRowsWidth[7] = max($arRowsWidth[7], $pdf->GetStringWidth($arCells[$n][7]));
	}

	$taxes = false;
	$dbTaxList = CSaleOrderTax::GetList(
		array("APPLY_ORDER" => "ASC"),
		array("ORDER_ID" => $ORDER_ID)
	);

	while ($arTaxList = $dbTaxList->Fetch())
	{
		$taxes = true;

		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			CSalePdf::prepareToPdf(sprintf(
				"%s%s%s:",
				($arTaxList["IS_IN_PRICE"] == "Y") ? "В том числе " : "",
				$arTaxList["TAX_NAME"],
				($vat <= 0 && $arTaxList["IS_PERCENT"] == "Y")
					? sprintf(' (%s%%)', roundEx($arTaxList["VALUE"],SALE_VALUE_PRECISION))
					: ""
			)),
			CSalePdf::prepareToPdf(SaleFormatCurrency(
				$arTaxList["VALUE_MONEY"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			))
		);

		$arRowsWidth[7] = max($arRowsWidth[7], $pdf->GetStringWidth($arCells[$n][7]));
	}

	if (!$taxes)
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			CSalePdf::prepareToPdf("НДС:"),
			CSalePdf::prepareToPdf("Без НДС")
		);

		$arRowsWidth[7] = max($arRowsWidth[7], $pdf->GetStringWidth($arCells[$n][7]));
	}

	if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"]) > 0)
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			CSalePdf::prepareToPdf("Уже оплачено:"),
			CSalePdf::prepareToPdf(SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			))
		);

		$arRowsWidth[7] = max($arRowsWidth[7], $pdf->GetStringWidth($arCells[$n][7]));
	}

	if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"]) > 0)
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			CSalePdf::prepareToPdf("Скидка:"),
			CSalePdf::prepareToPdf(SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			))
		);

		$arRowsWidth[7] = max($arRowsWidth[7], $pdf->GetStringWidth($arCells[$n][7]));
	}

	$arCells[++$n] = array(
		1 => null,
		null,
		null,
		null,
		null,
		CSalePdf::prepareToPdf("Итого:"),
		CSalePdf::prepareToPdf(SaleFormatCurrency(
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
			true
		))
	);

	$arRowsWidth[7] = max($arRowsWidth[7], $pdf->GetStringWidth($arCells[$n][7]));

	for ($i = 1; $i <= 7; $i++)
		$arRowsWidth[$i] += 10;
	if ($vat <= 0)
		$arRowsWidth[6] = 0;
	$arRowsWidth[2] = $width - (array_sum($arRowsWidth)-$arRowsWidth[2]);
}
$pdf->Ln();

$x0 = $pdf->GetX();
$y0 = $pdf->GetY();

for ($i = 1; $i <= 7; $i++)
{
	if ($vat > 0 || $i != 6)
		$pdf->Cell($arRowsWidth[$i], 20, $arColsCaption[$i], 0, 0, 'C');
	${"x$i"} = $pdf->GetX();
}

$pdf->Ln();

$y5 = $pdf->GetY();

$pdf->Line($x0, $y0, $x7, $y0);
for ($i = 0; $i <= 7; $i++)
{
	if ($vat > 0 || $i != 6)
		$pdf->Line(${"x$i"}, $y0, ${"x$i"}, $y5);
}
$pdf->Line($x0, $y5, $x7, $y5);

$rowsCnt = count($arCells);
for ($n = 1; $n <= $rowsCnt; $n++)
{
	$arRowsWidth_tmp = $arRowsWidth;
	$accumulated = 0;
	for ($j = 1; $j <= 7; $j++)
	{
		if (is_null($arCells[$n][$j]))
		{
			$accumulated += $arRowsWidth_tmp[$j];
			$arRowsWidth_tmp[$j] = null;
		}
		else
		{
			$arRowsWidth_tmp[$j] += $accumulated;
			$accumulated = 0;
		}
	}

	$x0 = $pdf->GetX();
	$y0 = $pdf->GetY();

	$pdf->SetFont($fontFamily, '', $fontSize);

	if (!is_null($arCells[$n][2]))
	{
		$text = $arCells[$n][2];
		$cellWidth = $arRowsWidth_tmp[2];
	}
	else
	{
		$text = $arCells[$n][6];
		$cellWidth = $arRowsWidth_tmp[6];
	}

	for ($l = 0; $pdf->GetStringWidth($text) > 0; $l++)
	{
		list($string, $text) = $pdf->splitString($text, $cellWidth-5);

		if (!is_null($arCells[$n][1]))
			$pdf->Cell($arRowsWidth_tmp[1], 15, ($l == 0) ? $arCells[$n][1] : '', 0, 0, 'C');
		if ($l == 0)
			$x1 = $pdf->GetX();

		if (!is_null($arCells[$n][2]))
			$pdf->Cell($arRowsWidth_tmp[2], 15, $string);
		if ($l == 0)
			$x2 = $pdf->GetX();

		if (!is_null($arCells[$n][3]))
			$pdf->Cell($arRowsWidth_tmp[3], 15, ($l == 0) ? $arCells[$n][3] : '', 0, 0, 'R');
		if ($l == 0)
			$x3 = $pdf->GetX();

		if (!is_null($arCells[$n][4]))
			$pdf->Cell($arRowsWidth_tmp[4], 15, ($l == 0) ? $arCells[$n][4] : '', 0, 0, 'R');
		if ($l == 0)
			$x4 = $pdf->GetX();

		if (!is_null($arCells[$n][5]))
			$pdf->Cell($arRowsWidth_tmp[5], 15, ($l == 0) ? $arCells[$n][5] : '', 0, 0, 'R');
		if ($l == 0)
			$x5 = $pdf->GetX();

		if (!is_null($arCells[$n][6]))
		{
			if (is_null($arCells[$n][2]))
				$pdf->Cell($arRowsWidth_tmp[6], 15, $string, 0, 0, 'R');
			else if ($vat > 0)
				$pdf->Cell($arRowsWidth_tmp[6], 15, ($l == 0) ? $arCells[$n][6] : '', 0, 0, 'R');
		}
		if ($l == 0)
			$x6 = $pdf->GetX();

		if (!is_null($arCells[$n][7]))
			$pdf->Cell($arRowsWidth_tmp[7], 15, ($l == 0) ? $arCells[$n][7] : '', 0, 0, 'R');
		if ($l == 0)
			$x7 = $pdf->GetX();

		$pdf->Ln();
	}

	if (isset($arProps[$n]) && is_array($arProps[$n]))
	{
		$pdf->SetFont($fontFamily, '', $fontSize-2);
		foreach ($arProps[$n] as $property)
		{
			$pdf->Cell($arRowsWidth_tmp[1], 12, '');
			$pdf->Cell($arRowsWidth_tmp[2], 12, $property);
			$pdf->Cell($arRowsWidth_tmp[3], 12, '');
			$pdf->Cell($arRowsWidth_tmp[4], 12, '');
			$pdf->Cell($arRowsWidth_tmp[5], 12, '');
			if ($vat > 0)
				$pdf->Cell($arRowsWidth_tmp[6], 12, '');
			$pdf->Cell($arRowsWidth_tmp[7], 12, '', 0, 1);
		}
	}

	$y5 = $pdf->GetY();

	if ($y0 > $y5)
		$y0 = $margin['top'];
	for ($i = (is_null($arCells[$n][1])) ? 6 : 0; $i <= 7; $i++)
	{
		if ($vat > 0 || $i != 5)
			$pdf->Line(${"x$i"}, $y0, ${"x$i"}, $y5);
	}

	$pdf->Line((!is_null($arCells[$n][1])) ? $x0 : $x6, $y5, $x7, $y5);
}
$pdf->Ln();


$pdf->SetFont($fontFamily, '', $fontSize);
$pdf->Write(15, CSalePdf::prepareToPdf(sprintf(
	"Всего наименований %s, на сумму %s",
	$items,
	SaleFormatCurrency(
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
		false
	)
)));
$pdf->Ln();

$pdf->SetFont($fontFamily, 'B', $fontSize);
if (in_array($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], array("RUR", "RUB")))
{
	$pdf->Write(15, CSalePdf::prepareToPdf(Number2Word_Rus($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"])));
}
else
{
	$pdf->Write(15, CSalePdf::prepareToPdf(SaleFormatCurrency(
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
		false
	)));
}
$pdf->Ln();
$pdf->Ln();

if (CSalePaySystemAction::GetParamValue("COMMENT1") || CSalePaySystemAction::GetParamValue("COMMENT2"))
{
	$pdf->Write(15, CSalePdf::prepareToPdf('Условия и комментарии'));
	$pdf->Ln();

	$pdf->SetFont($fontFamily, '', $fontSize);

	if (CSalePaySystemAction::GetParamValue("COMMENT1"))
	{
		$pdf->Write(15, HTMLToTxt(preg_replace(
			array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
			CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("COMMENT1"))
		), '', array(), 0));
		$pdf->Ln();
		$pdf->Ln();
	}

	if (CSalePaySystemAction::GetParamValue("COMMENT2"))
	{
		$pdf->Write(15, HTMLToTxt(preg_replace(
			array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
			CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("COMMENT2"))
		), '', array(), 0));
		$pdf->Ln();
		$pdf->Ln();
	}
}

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();

if (!$blank && CSalePaySystemAction::GetParamValue('PATH_TO_STAMP'))
	$pdf->Image(CSalePaySystemAction::GetParamValue('PATH_TO_STAMP'), $margin['left']+40, $pdf->GetY());


$pdf->SetFont($fontFamily, 'B', $fontSize);

if (CSalePaySystemAction::GetParamValue("SELLER_DIR_POS"))
{
	$isDirSign = false;
	if (!$blank && CSalePaySystemAction::GetParamValue('SELLER_DIR_SIGN'))
	{
		list($signHeight, $signWidth) = $pdf->GetImageSize(CSalePaySystemAction::GetParamValue('SELLER_DIR_SIGN'));

		if ($signHeight && $signWidth)
		{
			$ratio = min(37.5/$signHeight, 150/$signWidth);
			$signHeight = $ratio * $signHeight;
			$signWidth  = $ratio * $signWidth;

			$isDirSign = true;
		}
	}

	$sellerDirPos = CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("SELLER_DIR_POS"));
	if ($isDirSign && $pdf->GetStringWidth($sellerDirPos) <= 160)
		$pdf->SetY($pdf->GetY() + min($signHeight, 30) - 15);
	$pdf->MultiCell(150, 15, $sellerDirPos, 0, 'L');
	$pdf->SetXY($margin['left'] + 150, $pdf->GetY() - 15);

	if ($isDirSign)
	{
		$pdf->Image(
			CSalePaySystemAction::GetParamValue('SELLER_DIR_SIGN'),
			$pdf->GetX() + 80 - $signWidth/2, $pdf->GetY() - $signHeight + 15,
			$signWidth, $signHeight
		);
	}

	$x1 = $pdf->GetX();
	$pdf->Cell(160, 15, '');
	$x2 = $pdf->GetX();

	if (CSalePaySystemAction::GetParamValue("SELLER_DIR"))
		$pdf->Write(15, CSalePdf::prepareToPdf('('.CSalePaySystemAction::GetParamValue("SELLER_DIR").')'));
	$pdf->Ln();

	$y2 = $pdf->GetY();
	$pdf->Line($x1, $y2, $x2, $y2);

	$pdf->Ln();
}

if (CSalePaySystemAction::GetParamValue("SELLER_ACC_POS"))
{
	$isAccSign = false;
	if (!$blank && CSalePaySystemAction::GetParamValue('SELLER_ACC_SIGN'))
	{
		list($signHeight, $signWidth) = $pdf->GetImageSize(CSalePaySystemAction::GetParamValue('SELLER_ACC_SIGN'));

		if ($signHeight && $signWidth)
		{
			$ratio = min(37.5/$signHeight, 150/$signWidth);
			$signHeight = $ratio * $signHeight;
			$signWidth  = $ratio * $signWidth;

			$isAccSign = true;
		}
	}

	$sellerAccPos = CSalePdf::prepareToPdf(CSalePaySystemAction::GetParamValue("SELLER_ACC_POS"));
	if ($isAccSign && $pdf->GetStringWidth($sellerAccPos) <= 160)
		$pdf->SetY($pdf->GetY() + min($signHeight, 30) - 15);
	$pdf->MultiCell(150, 15, $sellerAccPos, 0, 'L');
	$pdf->SetXY($margin['left'] + 150, $pdf->GetY() - 15);

	if ($isAccSign)
	{
		$pdf->Image(
			CSalePaySystemAction::GetParamValue('SELLER_ACC_SIGN'),
			$pdf->GetX() + 80 - $signWidth/2, $pdf->GetY() - $signHeight + 15,
			$signWidth, $signHeight
		);
	}

	$x1 = $pdf->GetX();
	$pdf->Cell((CSalePaySystemAction::GetParamValue("SELLER_DIR")) ? $x2-$x1 : 160, 15, '');
	$x2 = $pdf->GetX();

	if (CSalePaySystemAction::GetParamValue("SELLER_ACC"))
		$pdf->Write(15, CSalePdf::prepareToPdf('('.CSalePaySystemAction::GetParamValue("SELLER_ACC").')'));
	$pdf->Ln();

	$y2 = $pdf->GetY();
	$pdf->Line($x1, $y2, $x2, $y2);
}


$dest = 'I';
if ($_REQUEST['GET_CONTENT'] == 'Y')
	$dest = 'S';
else if ($_REQUEST['DOWNLOAD'] == 'Y')
	$dest = 'D';

return $pdf->Output(
	sprintf(
		'Schet No %s ot %s.pdf',
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ACCOUNT_NUMBER"],
		ConvertDateTime($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"], 'YYYY-MM-DD')
	), $dest
);
?>