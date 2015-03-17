<?php
/**
 * Created by PhpStorm.
 * User: trp
 * Date: 16.03.15
 * Time: 2:34
 */
    require_once  __DIR__  . '/common.php';
    require_once  __DIR__  . '/iPayment.php';

/**
 * Class BraintreePayment
 */
class BraintreePayment implements iPayment {

    private $creditCardData;
    private $creditCard;
    private $customerData;
    private $customer;
    private $iscustomer = false;
    /**
     * @param $cardHolderName
     * @param $cardNumber
     * @param $expirationDate
     * @param $cvv
     * @return bool
     */
    public function setCreditCard($cardHolderName, $cardNumber, $expirationDate, $cvv){
        $this->creditCardData = array(
            'cardholderName' => "$cardHolderName",
            'number' => "$cardNumber",
            'expirationDate' => "$expirationDate",
            'cvv' => "$cvv",
            'customerId' => $this->getCustomer()->id
        );
        $this->creditCard = Braintree_CreditCard::create($this->creditCardData);
        array_pop($this->creditCardData);
        return $this->is_CreditCard();
    }

    /**
     * @return Braintree_CreditCard
     */
    public function getCreditCard(){
        return $this->creditCard->creditCard;
    }

    /**
     * @return bool
     */
    public function is_CreditCard(){
        return (bool) (!empty($this->creditCard->success))?$this->creditCard->success:false;
    }

    /**
     * @return Braintree_Customer
     */
    public function getCustomer(){
        return $this->customer;
    }

    /**
     * @return bool
     */
    public function is_Customer(){
        return (bool) (!empty($this->iscustomer))?$this->iscustomer:false;
    }

    /**
     * @param $FullName
     * @return bool
     */
    public function setCustomer($FullName){
        if (empty($FullName)) return false;
        $user = explode(' ', $FullName);
        $this->customerData = array(
            'firstName' => $user[0],
            'lastName' => (!empty($user[1]))?$user[1]:''
        );

        $result = Braintree_Customer::create($this->customerData);
        if ((bool) $result->success) {
            $this->customer = $result->customer;
            $this->iscustomer = $result->success;
        }
        return (bool) $result->success;
    }

    /**
     * @param $environment
     * @param $merchantId
     * @param $publicKey
     * @param $privateKey
     */
    public function __construct($environment, $merchantId, $publicKey, $privateKey) {
        Braintree_Configuration::environment($environment);
        Braintree_Configuration::merchantId($merchantId);
        Braintree_Configuration::publicKey($publicKey);
        Braintree_Configuration::privateKey($privateKey);
    }

    /**
     * @param $amount
     * @param $currency
     * @return string
     */
    public function doPayment($amount, $currency) {
        $result = Braintree_Transaction::sale(array(
            'amount' => $amount,
            'creditCard' => $this->creditCardData,
            'customer' => $this->customerData,
            'merchantAccountId' => $currency
        ));

        if ($result->success) {
            $message = '<h3>Payment success!</h3><p>Transaction ID:' . $result->transaction->id.'</p>';
            save2DB(
                $this->getCreditCard()->cardholderName,
                $result->transaction->id,
                'success'
            );
            return $message;
        } else if ($result->transaction) {
            $message = '<h3>Error processing transaction:</h3>';
            $message .='<p>  message: ' . $result->message;
            $message .="\n  code: " . $result->transaction->processorResponseCode;
            $message .="\n  text: " . $result->transaction->processorResponseText.'</p>';
        } else {
            $message = '<h3>Error:</h3><p>' . $result->message.'</p>';
        }
        return $message;
    }
}
