<?php
namespace Dovo\EmailBug\Rewrite\Sales\Model\Order\Email;
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
/**
* Configure email template
*
* @return void
*/
protected function configureEmailTemplate()
{
$this->transportBuilder->setTemplateIdentifier($this->templateContainer->getTemplateId());
$this->transportBuilder->setTemplateOptions($this->templateContainer->getTemplateOptions());
$this->transportBuilder->setTemplateVars($this->templateContainer->getTemplateVars());
/*Send From Email Issue #Dev110*/
$this->transportBuilder->setFromByStore(
$this->identityContainer->getEmailIdentity(),
$this->identityContainer->getStore()->getId()
);
/*Send From Email Issue #Dev110*/
//        $this->transportBuilderByStore->setFromByStore(
//            $this->identityContainer->getEmailIdentity(),
//            $this->identityContainer->getStore()->getId()
//        );
}
}