<?php
/**
 *                       ######
 *                       ######
 * ############    ####( ######  #####. ######  ############   ############
 * #############  #####( ######  #####. ######  #############  #############
 *        ######  #####( ######  #####. ######  #####  ######  #####  ######
 * ###### ######  #####( ######  #####. ######  #####  #####   #####  ######
 * ###### ######  #####( ######  #####. ######  #####          #####  ######
 * #############  #############  #############  #############  #####  ######
 *  ############   ############  #############   ############  #####  ######
 *                                      ######
 *                               #############
 *                               ############
 *
 * Adyen Payment module (https://www.adyen.com/)
 *
 * Copyright (c) 2015 Adyen BV (https://www.adyen.com/)
 * See LICENSE.txt for license details.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Block\Form;

class Boleto extends \Magento\Payment\Block\Form
{

    /**
     * @var string
     */
    protected $_template = 'Adyen_Payment::form/boleto.phtml';

    /**
     * @var \Adyen\Payment\Helper\Data
     */
    protected $_adyenHelper;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $sessionQuote;

    /**
     * Boleto constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Adyen\Payment\Helper\Data $adyenHelper
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adyen\Payment\Helper\Data $adyenHelper,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_adyenHelper = $adyenHelper;
        $this->sessionQuote = $sessionQuote;

        $this->_adyenHelper->setQuote($sessionQuote->getQuote());
    }

    /**
     * @return array
     */
    public function getBoletoTypes()
    {
        $boletoTypes = $this->_adyenHelper->getBoletoTypes();
        $types = [];
        foreach ($boletoTypes as $boletoType) {
            $types[$boletoType['value']] = $boletoType['label'];
        }
        return $types;
    }
    
}