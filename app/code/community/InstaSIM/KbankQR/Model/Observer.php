<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    InstaSIM
 * @package     InstaSIM_KbankQR
 * @copyright  Copyright (c) 2023 Instasim Co. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment Observer
 *
 * @category    InstaSIM
 * @package     InstaSIM_KbankQR
 * @author      Instasim software team <software@instasim.com>
 */
class InstaSIM_KbankQR_Model_Observer
{
    /**
     * Sets current instructions for bank transfer account
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function beforeOrderPaymentSave(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $observer->getEvent()->getPayment();
        if ($payment->getMethod() === InstaSIM_KbankQR_Model_KbankQR::PAYMENT_METHOD_KBANKQR_CODE) {
            $payment->setAdditionalInformation('instructions',
                $payment->getMethodInstance()->getInstructions());
            $payment->setAdditionalInformation('kb_id',
                $payment->getMethodInstance()->getKbId());
        }
    }
}