<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 18.03.15
 * Time: 14:50
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) .'/src/common.php';

/**
 * Class CommonTest
 */
class CommonTest extends PHPUnit_Framework_TestCase {

    public function testFunctions() {
        $this->assertEquals(cardTypeByNumber('3411111111111111'),AMEX);
        $this->assertEquals(cardTypeByNumber('3011111111111111'),CARTE_BLANCHE);
        $this->assertEquals(cardTypeByNumber('8811111111111111'),CHINA_UNION_PAY);
        $this->assertEquals(cardTypeByNumber('3611111111111111'),DINERS_CLUB_INTERNATIONAL);
        $this->assertEquals(cardTypeByNumber('6511111111111111'),DISCOVER);
        $this->assertEquals(cardTypeByNumber('3511111111111111'),JCB);
        $this->assertEquals(cardTypeByNumber('6304111111111111'),LASER);
        $this->assertEquals(cardTypeByNumber('5011111111111111'),MAESTRO);
        $this->assertEquals(cardTypeByNumber('5111111111111111'),MASTER_CARD);
        $this->assertEquals(cardTypeByNumber('6334111111111111'),SOLO);
        $this->assertEquals(cardTypeByNumber('4903111111111111'),SWITCH_TYPE);
        $this->assertEquals(cardTypeByNumber('4111111111111111'),VISA);
        $this->assertEquals(cardTypeByNumber('4026111111111111'),VISA_ELECTRON);
        $this->assertEquals(cardTypeByNumber('1111111111111111'),UATP);
        $this->assertEquals(cardTypeByNumber('0111111111111111'),UNKNOWN);
    }
}