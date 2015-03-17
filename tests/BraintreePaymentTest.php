<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 17.03.15
 * Time: 18:04
 */

    require_once dirname(__DIR__) . '/vendor/autoload.php';
    require_once dirname(__DIR__) .'/src/BraintreePayment.php';

/**
 * Class BraintreePaymentTest
 */
class BraintreePaymentTest extends PHPUnit_Framework_TestCase {

    public function testFunctions(){
        $ini_array = parse_ini_file(dirname(__DIR__) ."/config/config.ini",true);
        $braintreeConfig = $ini_array['braintreeConfiguration'];
        $payment = new BraintreePayment(
            $braintreeConfig['environment'],
            $braintreeConfig['merchantId'],
            $braintreeConfig['publicKey'],
            $braintreeConfig['privateKey']
        );
        $this->assertNotNull($payment);
        $this->assertEquals($braintreeConfig['environment'], Braintree_Configuration::environment());
        $this->assertEquals($braintreeConfig['merchantId'], Braintree_Configuration::merchantId());
        $this->assertEquals($braintreeConfig['publicKey'], Braintree_Configuration::publicKey());
        $this->assertEquals($braintreeConfig['privateKey'], Braintree_Configuration::privateKey());
        Braintree_Configuration::environment('sandbox');
        $this->assertFalse($payment->is_Customer());
        $this->assertTrue($payment->setCustomer('Name Surname'));
        $this->assertEquals($payment->getCustomer()->firstName,'Name');
        $this->assertEquals($payment->getCustomer()->lastName,'Surname');
        $this->assertTrue($payment->is_Customer());
        $this->assertFalse($payment->setCustomer(''));
        $this->assertFalse($payment->is_CreditCard());
        $this->assertTrue($payment->setCreditCard('Name Surname', '4111111111111111', '12/20', '111'));
        $this->assertEquals($payment->getCreditCard()->maskedNumber,'411111******1111');
        $this->assertEquals($payment->getCreditCard()->cardholderName,'Name Surname');
        $this->assertEquals($payment->getCreditCard()->expirationDate,'12/2020');
        $this->assertTrue($payment->is_CreditCard());
        $this->assertNotEmpty($result = $payment->doPayment('10',$braintreeConfig['USD']));
        $this->assertStringStartsWith('<h3>Payment success!</h3>',$result);
        $this->assertNotEmpty($result = $payment->doPayment('0',$braintreeConfig['USD']));
        $this->assertNotEmpty($result =$payment->doPayment('10',$braintreeConfig['USD']));
        $this->assertNotEmpty($result =$payment->doPayment('10',$braintreeConfig['USD']));
        $this->assertStringStartsWith('<h3>Error processing transaction:</h3>',$result);
        Braintree_Configuration::reset();
    }
}