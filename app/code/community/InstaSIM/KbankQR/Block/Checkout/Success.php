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
 * @category    Mage
 * @package     InstaSIM_KbankQR
 * @copyright  Copyright (c) 2022 Instasim Co. Ltd.
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * KbankQR checkout success page
 *
 * @category   Mage
 * @package    InstaSIM_KbankQR
 * @author     Instasim Co. Ltd. <support@instasim.com>
 */
class InstaSIM_KbankQR_Block_Checkout_Success extends Mage_Checkout_Block_Onepage_Success
{

    /**
     * Return true if the order uses KbankQR payment method
     *
     * @return bool
     */
    public function getOrderUsesKbankQR()
    {
        // Yes, Mage_Checkout_Block_Onepage_Success::getOrderId() returns the increment id, not the entity id
        $incrementId = $this->getOrderId();
        if ($incrementId) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
            if ($order->getPayment()->getMethod() == 'kbankqr') {
                return true;
            }
        }
        return false;
    }
}
