<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 18.03.15
 * Time: 15:04
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) .'/src/engine.php';

/**
 * Class EngineTest
 */
class EngineTest extends PHPUnit_Framework_TestCase {

    public function testFunctions() {
        $result = paymentGateway(
            'Name Surname',
            'Name Surname',
            '4417119669820331',
            '12/20',
            '10',
            'USD',
            '874'
        );
        $this->assertStringStartsWith('<h3>Payment success!</h3>',$result);
        $result = paymentGateway(
            'Name Surname',
            'Name Surname',
            '4111111111111111',
            '12/20',
            '10',
            'THK',
            '111'
        );
        $this->assertStringStartsWith('<p>error:',$result);
        $result = paymentGateway(
            'Name Surname',
            'Name Surname',
            '4111111111111111',
            '12/20',
            '10',
            'SGD',
            '111'
        );
        $this->assertStringStartsWith('<h3>Payment success!</h3>',$result);
    }

}