<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Payment");
?>

<div class="p-payment">
    <div class="p-payment__warning">
        <div class="p-payment__warning-icon">
            <img src="warning.png" alt="It is Important" alt="It is Important">
        </div>
        <div class="p-payment__warning-text"><i>Method of payment of any order you choose in its design. <br> Payment in the shop only in rubles.<br>After confirming the order the operator of an Internet store payment method changed can not be.</i></div>
    </div>

    <div class="p-payment__block">
        <h2>Cash</h2>
        <p>The most common and convenient way to make purchases. You give the employee money Delivery services upon receiving the order. Please note that gift cards ELDORADO not accepted.</p>

        <h2>Online payment by credit card</h2>
        <p>We accept online payments on the next payment systems:<br>Visa, MasterCard, JCB, DCL</p>
        <figure class="p-payment__logos">
            <img src="visa_logo.png" alt="visa">
            <img src="mastercard_logo.png" alt="mastercard">
            <img src="jcb_logo.png" alt="jcb">
            <img src="dcl_logo.png" alt="dcl">
        </figure>

        <p>It does not accept credit cards Visa and MasterCard without the CVV2 / CVC2 code.</p>

        <p>Payment for orders made via the Internet immediately after its completion.</p>
        <p>The minimum payment amount is 500 rubles.</p>
        <p>If you paid the order a bank card and then refused it, the return of transferred funds is made to your bank (card) account.</p>

        <h2>Cashless payments</h2>
        <p>This is the only method of payment if the order is issued by a legal person. The minimum order amount for invoicing is 3500 rubles.</p>
        <p>Upon receipt of the order you must have a power of attorney from the contracting authority and identity. Together with the order issued through, invoice and bill of lading.</p>
    </div>
</div>

<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');?>
