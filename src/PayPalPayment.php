<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 16.03.15
 * Time: 2:34
 */

    use PayPal\Rest\ApiContext;
    use PayPal\Auth\OAuthTokenCredential;
    use PayPal\Api\CreditCard;
    use PayPal\Api\FundingInstrument;
    use PayPal\Api\Payer;
    use PayPal\Api\Payment;
    use PayPal\Api\Amount;
    use PayPal\Api\Transaction;

    require_once  __DIR__  . '/common.php';
    require_once  __DIR__  . '/iPayment.php';

/**
 * Class PayPalPayment
 */
class PayPalPayment implements iPayment {
    const DEFAULT_PAYMENT_INTENT = 'sale';
    const DEFAULT_PAYMENT_METHOD = 'credit_card';

    private $apiContext;
    private $creditCard;

    /**
     * @param $mode
     * @param $clientID
     * @param $clientSecret
     */
    public function __construct($mode, $clientID, $clientSecret) {
        $this->apiContext = new ApiContext(new OAuthTokenCredential($clientID,$clientSecret));
        $this->apiContext->setConfig(array('mode' => $mode));
    }

    /**
     * @param $cardHolderName
     * @param $cardNumber
     * @param $expirationDate
     * @param $cvv
     * @param $type
     */
    public function setCreditCard($cardHolderName, $cardNumber, $expirationDate, $cvv, $type){
        $Expire = explode('/', $expirationDate);
        $user = explode(' ', $cardHolderName);
        $this->creditCard = new CreditCard();
        if (strcasecmp('american express',$type) == 0 ) $type = 'amex';
        $this->creditCard->setType(strtolower($type)) //visa, mastercard, amex, or discover
            ->setNumber($cardNumber)
            ->setExpireMonth($Expire[0])
            ->setExpireYear('20'.((!empty($Expire[1]))?$Expire[1]:''))
            ->setCvv2($cvv)
            ->setFirstName($user[0])
            ->setLastName((!empty($user[1]))?$user[1]:'');
    }

    /**
     * @return CreditCard
     */
    public function getCreditCard(){
            return $this->creditCard;
    }



    /**
     * @return ApiContext
     */
    public function getContext(){
        return $this->apiContext;
    }

    /**
     * @param $amount
     * @param $currency
     * @return string
     */
    public function doPayment($amount, $currency) {
        $fi = new FundingInstrument();
        $fi->setCreditCard($this->getCreditCard());

        $payer = new Payer();
        $payer->setPaymentmethod(self::DEFAULT_PAYMENT_METHOD);
        $payer->setFundinginstruments(array($fi));

        $currentAmount = new Amount();
        $currentAmount->setCurrency($currency);
        $currentAmount->setTotal($amount);

        $transaction = new Transaction();
        $transaction->setAmount($currentAmount);

        $payment = new Payment();
        $payment->setIntent(self::DEFAULT_PAYMENT_INTENT);
        $payment->setPayer($payer);
        $payment->setTransactions(array($transaction));

        try {
            $payment->create($this->apiContext);
        } catch (Exception $ex) {
            $message = '<h3>Error</h3>';
            if ($ex instanceof \PayPal\Exception\PayPalConnectionException) {
                $message = $ex->getData();
                $tmpArray = json_decode($message, true);
                if (isset($tmpArray['name'])) $message = '<h3>'.$tmpArray['name'].'</h3>';
                if (isset($tmpArray['message'])) $message .= '<p>'.$tmpArray['message'].'</p>';
                if (isset($tmpArray['details'])) $message .= '<p>'.$tmpArray['details'][0]['issue']."\n".'</p>';
                if (isset($tmpArray['information_link'])) $message .= '<p>'.$tmpArray['information_link'].'</p>';
            }
            return $message;
        }
        $message = "<h3>Payment success!</h3> <p>Transaction ID:" . $payment->getId().'</p>';
        save2DB(
            $this->getCreditCard()->first_name.' '.$this->getCreditCard()->last_name,
            $payment->getId(),
            'success'
        );
        return $message;
    }
}
