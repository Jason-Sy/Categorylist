<?php

namespace Buro210\Categorylist\Helper;

class Data
    extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ATTRIBUTE_NAME = "additional_image";

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve image URL by category
     *
     * @return string
     */
    public function getImageUrl(\Magento\Catalog\Model\Category $category)
    {
        $image = $category->getData(self::ATTRIBUTE_NAME);
        
        return $this->getUrl($image);
    }

    /**
     * Retrieve URL
     *
     * @return string
     */
    public function getUrl($value)
    {
        $url = false;
        if ($value) {
            if (is_string($value)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . 'catalog/category/' . $value;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        
        return $url;
    }
}