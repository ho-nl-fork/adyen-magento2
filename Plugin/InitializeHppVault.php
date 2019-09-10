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
 * Copyright (c) 2019 Adyen BV (https://www.adyen.com/)
 * See LICENSE.txt for license details.
 *
 * Author: Adyen <magento@adyen.com>
 */

namespace Adyen\Payment\Plugin;

use Adyen\Payment\Model\Ui\AdyenHppConfigProvider;
use Magento\Vault\Model\Method\Vault;

class InitializeHppVault
{
    /**
     * @param Vault $subject
     * @param callable $proceed
     * @return bool
     */
    public function aroundIsInitializeNeeded(Vault $subject, callable $proceed)
    {
        if ($subject->getCode() == AdyenHppConfigProvider::CC_VAULT_CODE) {
            return false;
        }

        return $proceed();
    }
}
