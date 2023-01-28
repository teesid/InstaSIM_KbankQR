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

class InstaSIM_KbankQR_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('instasim/kbankqr/form.phtml');
    }

    /**
     * @return string
     */
    public function getCustomFormBlockType()
    {
        return $this->getMethod()->getConfigData('form_block_type');
    }

    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;
    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getInstructions();
        }
        return $this->_instructions;
    }
}
