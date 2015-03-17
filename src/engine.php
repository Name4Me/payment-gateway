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

    require_once dirname(__DIR__) . '/vendor/autoload.php';
    require_once __DIR__ .'/BraintreePayment';
    require_once __DIR__ .'/PayPalPayment.php';

    try {
        $ini_array = parse_ini_file(dirname(__DIR__) . "/config/config.ini",true);
        $message = $ini_array['errorMessages']['emessage0'];
        if (!isset($_POST)) return;
        $configBranch = 'braintreeConfiguration';

        $payment = new BraintreePayment(
            $ini_array[$configBranch]['environment'],
            $ini_array[$configBranch]['merchantId'],
            $ini_array[$configBranch]['publicKey'],
            $ini_array[$configBranch]['privateKey']
        );

        if ($payment->setCustomer($_POST['customerFullName'])) $payment->setCreditCard(
            $_POST['creditCardHolderName'],
            $_POST['creditCardNumber'],
            $_POST['creditCardExpiration'],
            $_POST['creditCardCCV']
        );

        if ($payment->is_CreditCard()) {
            switch ($payment->getCreditCard()->cardType) {
                //if credit card type is AMEX, then use Paypal.
                case 'American Express':
                    $paymentMethod = 'Paypal';
                    //if currency is not USD and credit card is AMEX, return error message, that AMEX is possible to use only for USD
                    if ($_POST['currency'] != 'USD') {
                        $message = $ini_array['errorMessages']['emessage1'];
                        return;
                    }
                    break;
                default:
                    //if currency is USD, EUR, or AUD, then use Paypal. Otherwise use Braintree.
                    if (is_numeric(strpos('USD,EUR,AUD',$_POST['currency']))) $paymentMethod = 'Paypal';
                    else $paymentMethod = 'Braintree';
                    break;
            }
        } else $message = $payment->getCreditCard();

        switch ($paymentMethod) {
            case "Braintree":
                $message = $payment->doPayment($_POST['price'], $ini_array[$configBranch][$_POST['currency']]);
                break;

            case "Paypal":
                $configBranch = 'paypalConfiguration';
                $paypalPayment = new PayPalPayment(
                    $ini_array[$configBranch]['mode'],
                    $ini_array[$configBranch]['clientID'],
                    $ini_array[$configBranch]['clientSecret']
                );

                $paypalPayment->setCreditCard(
                    $_POST['creditCardHolderName'],
                    $_POST['creditCardNumber'],
                    $_POST['creditCardExpiration'],
                    $_POST['creditCardCCV'],
                    $payment->getCreditCard()->cardType
                );

                $message = $paypalPayment->doPayment($_POST['price'],$_POST['currency']);
                break;
        }


    } catch (\Exception $e) {
        $message .= '<p>error: '.'\n'.$e->getMessage().'</p>';
    }


    echo '      '.$message;
?>
        <br>
    </body>
</html>

<?php

