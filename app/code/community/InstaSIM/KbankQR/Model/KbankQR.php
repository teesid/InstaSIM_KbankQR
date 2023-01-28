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

class InstaSIM_KbankQR_Model_KbankQR extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_METHOD_KBANKQR_CODE = 'kbankqr';
    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = self::PAYMENT_METHOD_KBANKQR_CODE;

    protected $_formBlockType = 'kbankqr/form';
    protected $_infoBlockType = 'kbankqr/info';


    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    /**
     * Get KB ID from config
     *
     * @return string
     */
    public function getKbId()
    {
        return trim($this->getConfigData('kb_id'));
    }
}
