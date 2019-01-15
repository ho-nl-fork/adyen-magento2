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
 * Adyen Payment Module
 *
 * Copyright (c) 2017 Adyen B.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Block\Form;

class ApplePay extends \Magento\Payment\Block\Form
{
    /**
     * @var \Adyen\Payment\Helper\Data
     */
    protected $_adyenHelper;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $sessionQuote;

    /**
     * ApplePay constructor.
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
    )
    {
        parent::__construct($context, $data);
        $this->_adyenHelper = $adyenHelper;
        $this->sessionQuote = $sessionQuote;

        $this->_adyenHelper->setQuote($sessionQuote->getQuote());
    }

    /**
     * @return array
     */
    public function getApplePayShippingTypes()
    {
        $applePayShippingTypes = $this->_adyenHelper->getApplePayShippingTypes();
        $types = [];
        foreach ($applePayShippingTypes as $applePayShippingType) {
            $types[$applePayShippingType['value']] = $applePayShippingType['label'];
        }
        return $types;
    }

}