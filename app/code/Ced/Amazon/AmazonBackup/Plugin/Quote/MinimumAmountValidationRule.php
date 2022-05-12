<?php

namespace Ced\Amazon\Plugin\Quote;

class MinimumAmountValidationRule
{
    public function afterValidateMinimumAmount(
        \Magento\Quote\Model\Quote $subject,
        $result
    )
    {
      if ($subject->getAmazonOrderId()) {
          return true;
      }
      return $result;
    }
}
