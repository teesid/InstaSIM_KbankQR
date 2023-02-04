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
 * @copyright  Copyright (c) 2022 Instasim Co. Ltd.
 * @copyright  Copyright (c) 2008 Andrej Sinicyn
 * @copyright  Copyright (c) 2010-2018 Phoenix Media GmbH & Co. KG (http://www.phoenix-media.eu)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Endroid\QrCode\QrCode;

class InstaSIM_KbankQR_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * @param Varien_Object $account
     * @return bool
     */
    public function displayFullAccountData($account) {
        return ($this->displaySepaAccountData($account) && $this->displayNonSepaAccountData($account));
    }

    /**
     * @param Varien_Object $account
     * @return bool
     */
    public function displayNonSepaAccountData($account) {
        return ($account->getAccountNumber() && $account->getSortCode());
    }

    /**
     * @param Varien_Object $account
     * @return bool
     */
    public function displaySepaAccountData($account) {
        return ($account->getIban());
    }

    /**
     * @param string $data
     * @return string
     * 
     * Given the data, return the 2 digit length and the data itself
     */
    private function lldata($data) {
        return sprintf('%02d%s', strlen($data), $data);
    }

    /**
     * @param string $data
     * @return string
     * 
     * This is the CCITT_FALSE() function in https://github.com/zgldh/crc16-php/blob/master/src/Crc16.php
     */
    public function getCrc16($data) {
        $crc = 0xFFFF;
        $polynomial = 0x1021;
        $xorValue = 0;

        for ($i = 0; $i < strlen($data); $i++) {
            $c = ord($data[$i]);
            $crc ^= ($c << 8);
            for ($j = 0; $j < 8; ++$j) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) & 0xffff) ^ $polynomial;
                } else {
                    $crc = ($crc << 1) & 0xffff;
                }
            }
        }

        $crc ^= $xorValue;
        return sprintf('%04X', $crc);
    }

    /**
     * @param string $s
     * @return string
     * 
     * Given a string, convert anther string with every non-alphanumeric character replaced by 'x',
     * then prefix it by 'KPS' and return the result.
     */
    public function getKbankRef(string $s) {
        $s = preg_replace('/[^a-zA-Z0-9]/', 'x', $s);
        return "KPS{$s}";
    }

    /**
     * @param Varien_Object $account
     * @param Mage_Sales_Model_Order $order
     * @return Endroid\QrCode\QrCode
     */
    public function getQrCode($orderIncrementId) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        if (! $order->getId()) {
            return null;
        }

        $ref = $this->getKbankRef($orderIncrementId);
        $amount = sprintf('%.2f', $order->getGrandTotal());
        // This field was set by the observer when the order was created.
        // It will stay the same even after we have change the KB ID in the payment method config.
        $KbId = $order->getPayment()
                      ->getMethodInstance()
                      ->getInfoInstance()
                      ->getAdditionalInformation('kb_id');
        $data = (
            '000201' .                              // 00 == payload format indicator
            '010211' .                              // 01 == point of initiation (11 == static qr)
            '30' . $this->lldata(                          // 30 == PromptPay Bill Payment
                '00' . $this->lldata('A000000677010112') . // 00 == PromptPay domestic merchant
                '01' . $this->lldata('010753600031508') .  // 01 == Kbank's tax ID with suffix
                '02' . $this->lldata($KbId) .              // 02 == payment network specific
                '03' . $this->lldata($ref)                 // 03 == payment network specific
            ) .
            '31' . $this->lldata(                          // 31 == merchant account info
                '00' . $this->lldata('A000000677010113') . // 00 == BOT's "Payment Innovation" ID
                '01' . $this->lldata('004') .              // 01 == payment network specific
                '02' . $this->lldata($KbId) .              // 02 == payment network specific
                '04' . $this->lldata($ref)                 // 03 == payment network specific
            ) .
            '53' . $this->lldata('764') .                  // 53 == currency (764 == THB)
            '54' . $this->lldata($amount) .                // 54 == amount, somehow it needs to be 2 decimal places only
            '58' . $this->lldata('TH') .                   // 55 == country code
            '6304'                                         // 63 == CRC16 checksum
        );
        $checksum = $this->getCrc16($data);
        
        $qrCode = new QrCode();
        $qrCode
            ->setText($data . $checksum)
            ->setSize(200)
            ->setPadding(10)
            ->setErrorCorrection('medium')
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
            ->setLabel('PromptPay')
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG)
        ;

        return $qrCode;
    }

    /**
     * @param Varien_Object $account
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    public function getQrCodeImageTag($orderIncrementId) {
        # get the url for the KbankQR ontroller
        # we always use the default frontend store ID because the QR is the same
        # for all stores and if the admin store id is auto chosen then the url won't work
        $url = Mage::getUrl('kbankqr', array('_store' => 1, 'order' => $orderIncrementId));
        return "<img src=\"{$url}\" alt=\"PromptPay QR Code\" style=\"margin: 0 auto;\" />";
    }
}