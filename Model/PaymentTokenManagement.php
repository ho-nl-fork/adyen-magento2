<?php

namespace Adyen\Payment\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Api\Data\PaymentTokenSearchResultsInterfaceFactory;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;
use Magento\Vault\Model\PaymentTokenFactory;
use Magento\Vault\Model\ResourceModel\PaymentToken as PaymentTokenResourceModel;

class PaymentTokenManagement extends \Magento\Vault\Model\PaymentTokenManagement
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        PaymentTokenRepositoryInterface $repository,
        PaymentTokenResourceModel $paymentTokenResourceModel,
        PaymentTokenFactory $paymentTokenFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PaymentTokenSearchResultsInterfaceFactory $searchResultsFactory,
        EncryptorInterface $encryptor,
        DateTimeFactory $dateTimeFactory
    ) {
        parent::__construct(
            $repository,
            $paymentTokenResourceModel,
            $paymentTokenFactory,
            $filterBuilder,
            $searchCriteriaBuilder,
            $searchResultsFactory,
            $encryptor,
            $dateTimeFactory
        );
        $this->encryptor = $encryptor;
    }

    /**
     * @param PaymentTokenInterface $token
     * @param OrderPaymentInterface $payment
     * @return bool
     */
    public function saveTokenWithPaymentLink(PaymentTokenInterface $token, OrderPaymentInterface $payment)
    {
        $tokenDuplicate = $this->getByPublicHash(
            $token->getPublicHash(),
            $token->getCustomerId()
        );

        if (!empty($tokenDuplicate)) {
            if ($token->getIsVisible() || $tokenDuplicate->getIsVisible()) {
                $token->setEntityId($tokenDuplicate->getEntityId());
                $token->setIsVisible(true);
            } elseif ($token->getIsVisible() === $tokenDuplicate->getIsVisible()) {
                $token->setEntityId($tokenDuplicate->getEntityId());
            } else {
                $token->setPublicHash(
                    $this->encryptor->getHash(
                        $token->getPublicHash() . $token->getGatewayToken()
                    )
                );
            }
        }

        $this->paymentTokenRepository->save($token);

        $result = $this->addLinkToOrderPayment($token->getEntityId(), $payment->getEntityId());

        return $result;
    }
}
