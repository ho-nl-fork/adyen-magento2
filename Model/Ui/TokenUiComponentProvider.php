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

namespace Adyen\Payment\Model\Ui;

use Adyen\Payment\Helper\Data;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterface;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Framework\UrlInterface;

/**
 * Class TokenUiComponentProvider
 */
class TokenUiComponentProvider implements TokenUiComponentProviderInterface
{
    /**
     * @var TokenUiComponentInterfaceFactory
     */
    private $componentFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Data
     */
    private $adyenHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param TokenUiComponentInterfaceFactory $componentFactory
     * @param UrlInterface $urlBuilder
     * @param Data $adyenHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        TokenUiComponentInterfaceFactory $componentFactory,
        UrlInterface $urlBuilder,
        Data $adyenHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->componentFactory = $componentFactory;
        $this->urlBuilder = $urlBuilder;
        $this->adyenHelper = $adyenHelper;
        $this->checkoutSession = $checkoutSession;

        $this->adyenHelper->setQuote($checkoutSession->getQuote());
    }

    /**
     * Get UI component for token
     * @param PaymentTokenInterface $paymentToken
     * @return TokenUiComponentInterface
     */
    public function getComponentForToken(PaymentTokenInterface $paymentToken)
    {
        $details = json_decode($paymentToken->getTokenDetails() ?: '{}', true);
        $details['icon'] = $this->adyenHelper->getVariantIcon($details['type']);

        
        $component = $this->componentFactory->create(
            [
                'config' => [
                    'code' => AdyenCcConfigProvider::CC_VAULT_CODE,
                    TokenUiComponentProviderInterface::COMPONENT_DETAILS => $details,
                    TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash()
                ],
                'name' => 'Adyen_Payment/js/view/payment/method-renderer/vault'
            ]
        );

        return $component;
    }
}
