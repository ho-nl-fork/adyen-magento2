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


namespace Adyen\Payment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class InstallmentValidator extends AbstractValidator
{
    /**
     * @var \Adyen\Payment\Logger\AdyenLogger
     */
    private $adyenLogger;

    /**
     * @var \Adyen\Payment\Helper\Data
     */
    private $adyenHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    private $backendSession;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * InstallmentValidator constructor.
     *
     * @param \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory
     * @param \Adyen\Payment\Logger\AdyenLogger $adyenLogger
     * @param \Adyen\Payment\Helper\Data $adyenHelper
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Backend\Model\Session\Quote $backendSession
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Magento\Payment\Gateway\Validator\ResultInterfaceFactory $resultFactory,
        \Adyen\Payment\Logger\AdyenLogger $adyenLogger,
        \Adyen\Payment\Helper\Data $adyenHelper,
        \Magento\Checkout\Model\Session $session,
        \Magento\Backend\Model\Session\Quote $backendSession,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->adyenLogger = $adyenLogger;
        $this->adyenHelper = $adyenHelper;
        $this->session = $session;
        $this->backendSession = $backendSession;
        $this->serializer = $serializer;
        $this->quoteRepository = $quoteRepository;
        parent::__construct($resultFactory);

        $quote = $this->session->getQuote();
        if ($this->backendSession->getQuoteId()) {
            $quote = $this->backendSession->getQuote();
        }
        $this->adyenHelper->setQuote($quote);
    }

    public function validate(array $validationSubject)
    {
        $isValid = true;
        $fails = [];
        $payment = $validationSubject['payment'];

        $quoteId = $payment->getQuoteId();
        //This validator also runs for other payments that don't necesarily have a quoteId
        if ($quoteId) {
            $quote = $this->quoteRepository->get($quoteId);
        } else {
            $quote = false;
        }

        $installmentsEnabled = $this->adyenHelper->getAdyenCcConfigData('enable_installments');
        if ($quote && $installmentsEnabled) {
            $grandTotal = $quote->getGrandTotal();
            $installmentsAvailable = $this->adyenHelper->getAdyenCcConfigData('installments');
            $installmentSelected = $payment->getAdditionalInformation('number_of_installments');
            $ccType = $payment->getAdditionalInformation('cc_type');
            if ($installmentsAvailable) {
                $installments = $this->serializer->unserialize($installmentsAvailable);
            }
            if ($installmentSelected && $installmentsAvailable) {
                $isValid = false;
                $fails[] = __('Installments not valid.');
                if ($installments) {
                    foreach ($installments as $ccTypeInstallment => $installment) {
                        if ($ccTypeInstallment == $ccType) {
                            foreach ($installment as $amount => $installmentsData) {
                                if ($installmentSelected == $installmentsData) {
                                    if ($grandTotal >= $amount) {
                                        $isValid = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this->createResult($isValid, $fails);
    }
}
