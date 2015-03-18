<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 11.03.15
 * Time: 15:26
 */


?>

<!DOCTYPE html>
<html>
    <head lang="en">
        <meta charset="UTF-8">
        <title>Payment result</title>
    </head>
    <body>

<?php

    if (!isset($_POST) || !isset($_POST['customerFullName'])
        || !isset($_POST['creditCardHolderName']) || !isset( $_POST['creditCardNumber'])
        || !isset($_POST['creditCardExpiration']) || !isset($_POST['amount']) || !isset($_POST['currency'])
        || !isset($_POST['creditCardCCV'])) return 'error: don`t found POST data';

    $result = paymentGateway(
        $_POST['customerFullName'],
        $_POST['creditCardHolderName'],
        $_POST['creditCardNumber'],
        $_POST['creditCardExpiration'],
        $_POST['amount'],
        $_POST['currency'],
        $_POST['creditCardCCV']
    );

    echo '      '.$result;
?>
        <br>
    </body>
</html>

<?php

function paymentGateway($customerFullName, $creditCardHolderName, $creditCardNumber, $creditCardExpiration, $amount, $currency, $creditCardCCV){
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    require_once __DIR__ .'/BraintreePayment.php';
    require_once __DIR__ .'/PayPalPayment.php';
    require_once __DIR__ .'/common.php';

    try {
        $ini_array = parse_ini_file(dirname(__DIR__) . "/config/config.ini",true);
        $configBranch = 'braintreeConfiguration';

        $payment = new BraintreePayment(
            $ini_array[$configBranch]['environment'],
            $ini_array[$configBranch]['merchantId'],
            $ini_array[$configBranch]['publicKey'],
            $ini_array[$configBranch]['privateKey']
        );

        $payment->setCustomer($customerFullName);

        if ($payment->is_Customer()) $payment->setCreditCard(
            $creditCardHolderName,
            $creditCardNumber,
            $creditCardExpiration,
            $creditCardCCV
        );

        if ($payment->is_CreditCard()) $cardType = $payment->getCreditCard()->cardType;
        elseif ($ini_array[$configBranch]['environment'] == 'sandbox')
            $cardType = cardTypeByNumber($creditCardNumber);

        if (!empty($cardType)) {
            switch ($cardType) {
                //if credit card type is AMEX, then use Paypal.
                case 'American Express':
                    $paymentMethod = 'Paypal';
                    //if currency is not USD and credit card is AMEX, return error message, that AMEX is possible to use only for USD
                    if ($currency != 'USD') {
                        return $ini_array['errorMessages']['emessage1'];
                    }
                    break;
                default:
                    //if currency is USD, EUR, or AUD, then use Paypal. Otherwise use Braintree.
                    if (is_numeric(strpos('USD,EUR,AUD',$currency))) $paymentMethod = 'Paypal';
                    else $paymentMethod = 'Braintree';
                    break;
            }
        } else {
            return $ini_array['errorMessages']['emessage0'];
        }

        switch ($paymentMethod) {
            case "Braintree":
                return $payment->doPayment($amount, $ini_array[$configBranch][$currency]);
                break;

            case "Paypal":
                $configBranch = 'paypalConfiguration';
                $paypalPayment = new PayPalPayment(
                    $ini_array[$configBranch]['mode'],
                    $ini_array[$configBranch]['clientID'],
                    $ini_array[$configBranch]['clientSecret']
                );

                $paypalPayment->setCreditCard(
                    $creditCardHolderName,
                    $creditCardNumber,
                    $creditCardExpiration,
                    $creditCardCCV,
                    $cardType
                );

                return $paypalPayment->doPayment($amount,$currency);
                break;
        }


    } catch (Exception $e) {
        return '<p>error: '.'\n'.$e->getMessage().'</p>';
    }
}

