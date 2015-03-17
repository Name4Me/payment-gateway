<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 18.03.15
 * Time: 0:22
 */

    require_once dirname(__DIR__) . '/vendor/autoload.php';
    require_once dirname(__DIR__) .'/src/PayPalPayment.php';

class PayPalPaymentTest extends PHPUnit_Framework_TestCase {
    public function testFunctions(){
        $ini_array = parse_ini_file(dirname(__DIR__) ."/config/config.ini",true);
        $Config = $ini_array['paypalConfiguration'];

        $payment = new PayPalPayment(
            $Config['mode'],
            $Config['clientID'],
            $Config['clientSecret']
        );

        $this->assertNotNull($payment);
        $this->assertNotNull($payment->getContext());
        $this->assertNull( $payment->getCreditCard());
        $payment->setCreditCard('Roman Tsapik', '4417119669820331', '11/18', '874', 'visa');
        $this->assertNotNull( $payment->getCreditCard());
        $this->assertStringStartsWith('<h3>Payment success!</h3>',$payment->doPayment('1','USD'));
        $this->assertStringStartsWith('<h3>VALIDATION_ERROR</h3>',$payment->doPayment(0,'USD'));
    }
    
}