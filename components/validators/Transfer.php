<?php
/**
 * Created by PhpStorm.
 * User: abdujabbor
 * Date: 6/22/18
 * Time: 4:59 PM
 */
namespace app\components\validators;
use app\models\User;
class Transfer
{
    CONST MIN_BALANCE = -1000;
    private $user;
    private $amount;
    private $username;
    private $errors;
    public function __construct(User $user, int $amount, $username)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->username = $username;
    }


    public function validate() {

        if($this->username === $this->user->username) {
            $this->errors['username'] = 'You cannot transfer amount to yourself';
            return false;
        }

        if(!$this->getReceiver($this->username)) {
            $this->errors['username'] = 'Receiver found with username: ' . $this->username . ' not found';
            return false;
        }
        if($this->canUserTransferAmount()) {
            $this->errors['amount'] = 'Max value for transfer is:' . $this->availableTransferAmount();
        }
        return count($this->errors) === 0;
    }

    public function canUserTransferAmount() {
        if($this->user->balance - $this->amount < self::MIN_BALANCE) {
            return false;
        }
        return true;
    }

    public function availableTransferAmount() {
        if($this->user->balance >= 0) {
            return $this->user->balance + abs(self::MIN_BALANCE);
        }
        return abs(self::MIN_BALANCE) - abs($this->user->balance);
    }

    public function getReceiver($username = '') {
        return User::findByUsername($username);
    }

    public function getErrors() {
        return $this->errors;
    }
}