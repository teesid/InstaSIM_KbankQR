<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Mage
 * @package    InstaSIM_KbankQR
 * @copyright  Copyright (c) 2023 Instasim Co. Ltd.
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class InstaSIM_KbankQR_Model_KbankQR_Api extends Mage_Api_Model_Resource_Abstract
{
  public function invoiceMany($orderInfoList = array())
  {
    $ret = array();
    foreach ($orderInfoList as $oi) {
      $result = array(
        'increment_id' => $oi->increment_id,
        'invoice_id' => NULL,
        'error' => '',
      );

      try {
        # check if the order exists
        $o = Mage::getModel('sales/order')->loadByIncrementId($oi->increment_id);
        if (!$o->getId()) {
          $result['error'] = 'Order not found';
          $ret[] = $result;
          continue;
        }

        # check if it is invoiceable
        if (!$o->canInvoice()) {
          $result['error'] = 'Order cannot be invoiced';
          $ret[] = $result;
          continue;
        }

        # check if the payment method is kbankqr
        $payment = $o->getPayment();
        if ($payment->getMethod() != 'kbankqr') {
          $result['error'] = 'Payment method is not kbankqr';
          $ret[] = $result;
          continue;
        }

        # check if the amount matches
        $total = sprintf('%.2f', $o->getGrandTotal());
        if ($total != $oi->amount) {
          $result['error'] = "Amount mismatch, should be {$total} != {$oi->amount}";
          $ret[] = $result;
          continue;
        }

        # create invoice
        $invoice = Mage::getModel('sales/service_order', $o)->prepareInvoice();
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(true);
        $invoice->getOrder()->setIsInProcess(true);
        $o->addStatusHistoryComment($oi->comment, false);
        $transactionSave = Mage::getModel('core/resource_transaction')
          ->addObject($invoice)
          ->addObject($invoice->getOrder());
        $transactionSave->save();
        $result['invoice_id'] = $invoice->getIncrementId();
      } catch (Exception $e) {
        $result['error'] = $e->getMessage();
      }

      $ret[] = $result;
    }
    return $ret;
  }
}