<?php

namespace Buro210\Categorylist\Controller\Adminhtml\Image;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Upload
 */
class Upload
    extends \Magento\Backend\App\Action
{
    /**
     * @var \Buro210\Categorylist\Model\ImageUploader
     */
    protected $imageUploader;

    /**
     * Upload constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Buro210\Categorylist\Model\ImageUploader $imageUploader
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Buro210\Categorylist\Model\ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::categories');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir(\Buro210\Categorylist\Helper\Data::ATTRIBUTE_NAME);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}