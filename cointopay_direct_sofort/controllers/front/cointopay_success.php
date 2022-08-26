<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once(_PS_MODULE_DIR_ . '/cointopay_direct_sofort/vendor/cointopay/init.php');
require_once(_PS_MODULE_DIR_ . '/cointopay_direct_sofort/vendor/version.php');

class Cointopay_Direct_SofortCointopay_SuccessModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        
        //$cart_id = Tools::getValue('CustomerReferenceNr');
		//$cart = $this->context->cart;
        
        $order_id = Tools::getValue('CustomerReferenceNr');
		//$TransactionID = Tools::getValue('TransactionID');
		
		//$ConfirmCode = Tools::getValue('ConfirmCode');
        
        $order = new Order($order_id);

        try {
            if (!$order) {
                $error_message = 'Cointopay Order #' . Tools::getValue('CustomerReferenceNr') . ' does not exists';

                $this->logError($error_message, $order_id);
                throw new Exception($error_message);
            }

           /* $this->context->smarty->assign(array(
				'text' => 'Order Status ' . $ctp_order_status . ' not implemented'
			));*/
			$this->context->smarty->assign('getparams', $_REQUEST);
			if (_PS_VERSION_ >= '1.7') {
				$this->setTemplate('module:cointopay_direct_sofort/views/templates/hook/ctp_success_callback.tpl');
			} else {
				$this->setTemplate('ctp_success_callback.tpl');
			}
			
        } catch (Exception $e) {
            $this->context->smarty->assign(array(
                'text' => get_class($e) . ': ' . $e->getMessage()
            ));
			if (_PS_VERSION_ >= '1.7') {
				$this->setTemplate('module:cointopay_direct_sofort/views/templates/front/ctp_payment_cancel.tpl');
			} else {
				$this->setTemplate('ctp_payment_cancel.tpl');
			}
        }
    }

    private function logError($message, $cart_id)
    {
        PrestaShopLogger::addLog($message, 3, null, 'Cart', $cart_id, true);
    }
}
