<?php

namespace cointopay_direct_sofort\Merchant;

use cointopay_direct_sofort\Cointopay_Direct_Sofort;
use cointopay_direct_sofort\Merchant;

class Order extends Merchant
{
    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public static function createOrFail($params, $options = array(), $authentication = array())
    {
        $order = Cointopay_Direct_Sofort::request('orders', 'GET', $params, $authentication);
        return new self($order);
    }
    public static function ValidateOrder($params, $options = array(), $authentication = array())
    {
        $order = Cointopay_Direct_Sofort::request('validation', 'GET', $params, $authentication);
        return new self($order);
    }

    public function toHash()
    {
        return $this->order;
    }

    public function __get($name)
    {
        return $this->order[$name];
    }
}
