<?php

class InstaSIM_KbankQR_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Custom options download action
     */
    public function indexAction()
    {
        $orderIncrementId = $this->getRequest()->getParam('order');
        $qrCode = Mage::helper('kbankqr')->getQrCode($orderIncrementId);
        
        if ($qrCode) {
            $this->getResponse()->setHeader('Content-Type', $qrCode->getContentType());
            $qrCode->render();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('defaultNoRoute');
        }
    }
}