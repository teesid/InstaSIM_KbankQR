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

class InstaSIM_KbankQR_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('instasim/kbankqr/info.phtml');
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('instasim/kbankqr/pdf/info.phtml');
        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getShowQRInPdf() {
        return $this->getMethod()->getConfigData('show_qr_in_pdf');
    }


    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;
    /**
     * Get instructions text from order payment
     * (or from config, if instructions are missed in payment)
     *
     * @return string
     */
    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getInfo()->getAdditionalInformation('instructions');
            if(empty($this->_instructions)) {
                $this->_instructions = $this->getMethod()->getInstructions();
            }
        }
        return $this->_instructions;
    }
}
