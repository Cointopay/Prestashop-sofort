<?php

/**
 * 2010-2014 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2010-2014 PrestaShop SA

 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . '/cointopay_direct_sofort/vendor/cointopay/init.php';
require_once _PS_MODULE_DIR_ . '/cointopay_direct_sofort/vendor/version.php';

class Cointopay_Direct_Sofort extends PaymentModule
{
    public $merchant_id;
    public $security_code;
    public $crypto_currency;
    private $html = '';
    private $postErrors = array();

    public function __construct()
    {
        $this->name = 'cointopay_direct_sofort';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->author = 'Cointopay.com';
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;

        $config = Configuration::getMultiple(
            array(
                'COINTOPAY_DIRECT_SOFORT_MERCHANT_ID',
                'COINTOPAY_DIRECT_SOFORT_SECURITY_CODE',
                'COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY',
                'COINTOPAY_DIRECT_SOFORT_DISPLAY_NAME'
            )
        );

        if (!empty($config['COINTOPAY_DIRECT_SOFORT_MERCHANT_ID'])) {
            $this->merhcant_id = $config['COINTOPAY_DIRECT_SOFORT_MERCHANT_ID'];
        }
        if (!empty($config['COINTOPAY_DIRECT_SOFORT_SECURITY_CODE'])) {
            $this->security_code = $config['COINTOPAY_DIRECT_SOFORT_SECURITY_CODE'];
        }
        if (!empty($config['COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY'])) {
            $this->crypto_currency = $config['COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY'];
        }

        parent::__construct();

        $this->displayName = 'Cointopay Direct Sofort';
        $this->description = $this->l('Accept Bitcoin and other cryptocurrencies as a payment method with Cointopay');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');

        if (!isset($this->merhcant_id) || !isset($this->security_code)) {
            $this->warning = $this->l('API Access details must be configured in order to use this module correctly.');
        }

        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('No currency has been set for this module.');
        }
    }

    public function install()
    {
        if (!function_exists('curl_version')) {
            $this->_errors[] = $this->l('This module requires cURL PHP extension in order to function normally.');
            return false;
        }

        $newStates = [
            [
                'name' => 'instant bank transfer pending',
                'color' => 'RoyalBlue',
                'config' => 'COINTOPAY_CC_PENDING',
            ],
            [
                'name' => 'instant payment accepted',
                'color' => 'RoyalBlue',
                'config' => 'COINTOPAY_CC_PAID',
            ],
            [
                'name' => 'instant bank transfer',
                'color' => 'RoyalBlue',
                'config' => 'COINTOPAY_CC_PROCESSING_IN_PROGRESS',
            ],
            [
                'name' => 'Cointopay Fiat payment payment failed',
                'color' => '#FF8C00',
                'config' => 'COINTOPAY_CC_FAILED',
            ],
            [
                'name' => 'Cointopay Fiat payment payment expired',
                'color' => '#DC143C',
                'config' => 'COINTOPAY_CC_EXPIRED',
            ],
            [
                'name' => 'Cointopay Fiat payment invoice is invalid',
                'color' => '#8f0621',
                'config' => 'COINTOPAY_CC_INVALID',
            ],
            [
                'name' => 'Cointopay Fiat payment not enough',
                'color' => '#32CD32',
                'config' => 'COINTOPAY_CC_PNOTENOUGH',
            ],
        ];

        $existingStatesNames = OrderState::getOrderStates(1);
        $existingStatesNames = empty($existingStatesNames) ? [] : array_column($existingStatesNames, 'name');

        foreach ($newStates as $state) {
            // create a new state if not already exists
            if (!in_array($state['name'], $existingStatesNames)) {
                $this->createOrderState($state);
            }
        }

        if (_PS_VERSION_ >= '1.7.7') {
            if (
                !parent::install()
                || !$this->registerHook('displayPaymentEU')
                || !$this->registerHook('paymentReturn')
                || !$this->registerHook('paymentOptions')
                || !$this->registerHook('orderConfirmation')
                || !$this->registerHook('DisplayAdminOrder')
            ) {
                return false;
            }
        } else {
            if (
                !parent::install()
                || !$this->registerHook('displayPaymentEU')
                || !$this->registerHook('paymentReturn')
                || !$this->registerHook('paymentOptions')
                || !$this->registerHook('orderConfirmation')
                || !$this->registerHook('DisplayAdminOrderLeft')
            ) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        return (parent::uninstall());
    }

    protected function createOrderState($state)
    {
        $orderState = new OrderState();
        $orderState->module_name = 'cointopay_direct_sofort';
        $orderState->name = array_fill(0, 10, $state['name']);
        $orderState->send_email = 0;
        $orderState->invoice = 0;
        $orderState->color = $state['color'];
        $orderState->unremovable = false;
        $orderState->logable = 0;

        if ($orderState->add()) {
            copy(
                _PS_ROOT_DIR_ . '/modules/cointopay_direct_sofort/views/img/logo.png',
                _PS_ROOT_DIR_ . '/img/os/' . (int)$orderState->id . '.gif'
            );
        }

        Configuration::updateValue($state['config'], $orderState->id);
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->postValidation();
            if (!count($this->postErrors)) {
                $this->postProcess();
            } else {
                foreach ($this->postErrors as $err) {
                    $this->html .= $this->displayError($err);
                }
            }
        } else {
            $this->html .= '<br />';
        }

        $renderForm = $this->renderForm();
        $this->html .= $this->displayCointopayInformation($renderForm);

        return $this->html;
    }

    private function postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('COINTOPAY_DIRECT_SOFORT_MERCHANT_ID')) {
                $this->postErrors[] = $this->l('Merchant id is required.');
            }

            if (!Tools::getValue('COINTOPAY_DIRECT_SOFORT_SECURITY_CODE')) {
                $this->postErrors[] = $this->l('Security Code is required.');
            }

            if (!Tools::getValue('COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY')) {
                $this->postErrors[] = $this->l('Checkout Currency is required.');
            }

            if (empty($this->postErrors)) {
                $ctpConfig = array(
                    'merchant_id' => Tools::getValue('COINTOPAY_DIRECT_SOFORT_MERCHANT_ID'),
                    'security_code' => Tools::getValue('COINTOPAY_DIRECT_SOFORT_SECURITY_CODE'),
                    'selected_currency' => Tools::getValue('COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY'),
                    'user_agent' => 'Cointopay - Prestashop v' . _PS_VERSION_
                        . ' Extension v' . COINTOPAY_DIRECT_SOFORT_PRESTASHOP_EXTENSION_VERSION,
                );

                \Cointopay_Direct_Sofort\Cointopay_Direct_Sofort::config($ctpConfig);

                $merchant = \Cointopay_Direct_Sofort\Cointopay_Direct_Sofort::verifyMerchant();

                if ($merchant !== true) {
                    $this->postErrors[] = $this->l($merchant);
                }
            }
        }
    }

    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('COINTOPAY_DIRECT_SOFORT_DISPLAY_NAME', Tools::getValue('COINTOPAY_DIRECT_SOFORT_DISPLAY_NAME'));
            Configuration::updateValue('COINTOPAY_DIRECT_SOFORT_MERCHANT_ID', Tools::getValue('COINTOPAY_DIRECT_SOFORT_MERCHANT_ID'));
            Configuration::updateValue('COINTOPAY_DIRECT_SOFORT_SECURITY_CODE', Tools::getValue('COINTOPAY_DIRECT_SOFORT_SECURITY_CODE'));
            Configuration::updateValue('COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY', Tools::getValue('COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY'));
        }

        $this->html .= $this->displayConfirmation($this->l('Settings updated'));
    }

    public function renderForm()
    {
        $options = array(
            array(
                'id_option' => 1,
                'name' => 'Select default checkout currency'
            )
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Accept Cryptocurrencies with Cointopay.com'),
                    'icon' => 'icon-bitcoin',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Display Name'),
                        'name' => 'COINTOPAY_DIRECT_SOFORT_DISPLAY_NAME',
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Merchant ID'),
                        'name' => 'COINTOPAY_DIRECT_SOFORT_MERCHANT_ID',
                        'desc' => $this->l('Your ID (created on Cointopay.com)'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Security Code'),
                        'name' => 'COINTOPAY_DIRECT_SOFORT_SECURITY_CODE',
                        'desc' => $this->l('Your Security Code (created on Cointopay.com)'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select default checkout currency'),
                        'name' => 'COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY',
                        'id' => 'crypto_currency',
                        'default_value' => (int)Tools::getValue('COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY'),
                        'required' => true,
                        'options' => array(
                            'query' => $options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = (Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
            ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0);
        $this->fields_form = array();
        $helper->id = (int)Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module='
            . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigFormValues()
    {
        $system_name = Configuration::get('COINTOPAY_DIRECT_SOFORT_DISPLAY_NAME');
        $dislay_name = (isset($system_name) && !empty($system_name)) ? $system_name : 'Cointopay Direct Sofort';

        return array(
            'COINTOPAY_DIRECT_SOFORT_DISPLAY_NAME' => $dislay_name,
            'COINTOPAY_DIRECT_SOFORT_MERCHANT_ID' => Configuration::get('COINTOPAY_DIRECT_SOFORT_MERCHANT_ID'),
            'COINTOPAY_DIRECT_SOFORT_SECURITY_CODE' => Configuration::get('COINTOPAY_DIRECT_SOFORT_SECURITY_CODE'),
            'COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY' => Configuration::get('COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY'),
        );
    }

    private function displayCointopayInformation($renderForm)
    {
        $this->html .= $this->displayCointopay();
        $this->context->controller->addCSS($this->_path . '/views/css/tabs.css', 'all');
        $this->context->controller->addJS($this->_path . '/views/js/javascript.js', 'all');
        $this->context->controller->addJS($this->_path . '/views/js/cointopay.js', 'all');

        $this->context->smarty->assign('form', $renderForm);
        $this->context->smarty->assign("selected_currency", Configuration::get('COINTOPAY_DIRECT_SOFORT_CRYPTO_CURRENCY'));
        return $this->display(__FILE__, 'information.tpl');
    }

    private function displayCointopay()
    {
        return $this->display(__FILE__, 'infos.tpl');
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }

    public function hookPaymentReturn($params)
    {
        /**
         * Verify if this module is enabled
         */
        if (!$this->active) {
            return;
        }

        $this->context->controller->addJS($this->_path . '/views/js/cointopay_custom.js', 'all');

        array_push($params, $_REQUEST);

        if (isset($_REQUEST['CustomerReferenceNr'])) {
            //$_REQUEST['PaymentDetailCConly'] = str_replace( "<br><br>", "", $_REQUEST['PaymentDetailCConly'] );
            $this->smarty->assign('getparams', $_REQUEST);
            return $this->context->smarty->fetch('module:cointopay_direct_sofort/views/templates/hook/ctp_success_callback.tpl');
        }
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $newOption->setCallToActionText($this->displayName)
            ->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true))
            ->setAdditionalInformation(
                $this->context->smarty->fetch('module:cointopay_direct_sofort/views/templates/hook/cointopay_intro.tpl')
            )
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/order-page.png'));

        $paymentOptions = array($newOption);

        return $paymentOptions;
    }

    /**
     * @param array $hookParams
     */
    public function hookActionBuildMailLayoutVariables(array $hookParams)
    {
        if (!isset($hookParams['mailLayout'])) {
            return;
        }

        /** @var LayoutInterface $mailLayout */
        $mailLayout = $hookParams['mailLayout'];
        if ($mailLayout->getModuleName() != $this->name || $mailLayout->getName() != 'customizable_modern_layout') {
            return;
        }
    }

    public function hookDisplayAdminOrder($params)
    {
        $id_order = (int)$params['id_order'];
        $order = new Order($id_order);
        return $this->hookDisplayAdminOrderMain($order);
    }

    public function hookDisplayAdminOrderLeft($params)
    {
        $id_order = (int)$params['id_order'];
        $order = new Order($id_order);
        return $this->hookDisplayAdminOrderLeftMain($order);
    }

    public function hookDisplayAdminOrderLeftMain($order)
    {
        // $id_order = (int)$params['id_order'];
        //$order = new Order($id_order);
        $Ordtotal = (float)$order->total_paid;
        $currency = new CurrencyCore($order->id_currency);
        $OrdCurrency = $this->currencyCode($currency->iso_code);
        $link = new Link();
        $paymentUrl = Context::getContext()->shop->getBaseURL(true) . 'module/' . $this->name . '/makepayment?id_order=' . $order->reference . '&internal_order_id=' . $order->id . '&amount=' . $Ordtotal . '&isocode=' . $OrdCurrency;
        $customer = $order->getCustomer();
        if (Tools::isSubmit('send' . $this->name . 'Payment')) {
            $data = array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{paymentUrl}' => $paymentUrl,
            );

            Mail::Send(
                (int)$order->id_lang,
                'cointopay_direct_sofort',
                'Credit card payment form for your order ' . $order->getUniqReference(),
                $data,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                null,
                null,
                null,
                null,
                dirname(__FILE__) . '/mails/',
                false,
                (int)$order->id_shop
            );

            Tools::redirectAdmin('index.php?controller=AdminOrders&id_order=' . $order->id . '&vieworder&conf=10&token=' . $_GET['token']);
        }

        //return $this->display(__FILE__, 'views/templates/hook/admin_order.tpl');
        return '<div class="panel" id="cointopaydirectsofortform">
                    <div class="panel-heading">Payment Page(' . $this->displayName . ')</div>
                    <div>Payment Page For this Order:<br/><span id="ctpdirectsofortpurl">' . $paymentUrl . '</span></div>
					<form method="post" action="" style="display:inline-block;">
                    <input type="submit" class="btn btn-outline-secondary" name="send' . $this->name . 'Payment" value="Send To Customer" />
					</form><button id="ctpdirectsofortcopytext" class="btn btn-outline-secondary" onclick="ctpdirectsofortCopyFunction()" style="margin-left:10px;">Copy URL to clipboard</button>
                </div> <script>
                    function ctpdirectsofortCopyFunction() {
					  var copyText = document.getElementById("ctpdirectsofortpurl");
						navigator.clipboard.writeText(copyText.innerText);
					}
                </script>';
    }

    public function hookDisplayAdminOrderMain($order)
    {
        //$id_order = (int)$params['id_order'];
        //$order = new Order($id_order);
        $Ordtotal = (float)$order->total_paid;
        $currency = new CurrencyCore($order->id_currency);
        $OrdCurrency = $this->currencyCode($currency->iso_code);
        $link = new Link();
        $paymentUrl = $link->getModuleLink('cointopay_direct_sofort', 'makepayment', array(
            'id_order' => $order->reference,
            'internal_order_id' => $order->id,
            'amount' => $Ordtotal,
            'isocode' => $OrdCurrency
        ), true);
        $customer = $order->getCustomer();
        if (Tools::isSubmit('send' . $this->name . 'Payment')) {
            $data = array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{paymentUrl}' => $paymentUrl,
            );

            Mail::Send(
                (int)$order->id_lang,
                'cointopay_direct_sofort',
                'Credit card payment form for your order ' . $order->getUniqReference(),
                $data,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                null,
                null,
                null,
                null,
                dirname(__FILE__) . '/mails/',
                false,
                (int)$order->id_shop
            );

            Tools::redirectAdmin('index.php?controller=AdminOrders&id_order=' . $order->id . '&vieworder&conf=10&token=' . $_GET['token']);
        }

        //return $this->display(__FILE__, 'views/templates/hook/admin_order.tpl');
        return '<div class="panel" id="cointopaydirectsofortform">
                    <div class="panel-heading">Payment Page(' . $this->displayName . ')</div>
                    <div>Payment Page For this Order:<br/><span id="ctpdirectsofortpurl">' . $paymentUrl . '</span></div>
					<form method="post" action="" style="display:inline-block;">
                    <input type="submit" class="btn btn-outline-secondary" name="send' . $this->name . 'Payment" value="Send To Customer" />
					</form><button id="ctpdirectsofortcopytext" class="btn btn-outline-secondary" onclick="ctpdirectsofortCopyFunction()" style="margin-left:10px;">Copy URL to clipboard</button>
                </div> <script>
                    function ctpdirectsofortCopyFunction() {
					  var copyText = document.getElementById("ctpdirectsofortpurl");
						navigator.clipboard.writeText(copyText.innerText);
					}
                </script>';
    }

    /**
     * Currency code
     * @param $isoCode
     * @return string
     */
    public function currencyCode($isoCode)
    {
        $currencyCode = '';

        if (isset($isoCode) && ($isoCode == 'RUB')) {
            $currencyCode = 'RUR';
        } else {
            $currencyCode = $isoCode;
        }

        return $currencyCode;
    }
}
