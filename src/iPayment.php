<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 16.03.15
 * Time: 2:32
 */

/**
 * Interface iPayment
 */
interface iPayment {
    public function doPayment($amount, $currency);
}
