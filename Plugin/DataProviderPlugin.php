<?php
/**
 * @author Thijs Adriaansens BURO210
 * @copyright Copyright © 2019 BURO210. All rights reserved.
 * @package Buro210/CategoryList
 */

namespace Buro210\CategoryList\Plugin;

use Magento\Catalog\Model\Category\DataProvider as Subject;

class DataProviderPlugin
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Buro210\CategoryList\Helper\Data
     */
    protected $_helper;

    /**
     * DataProviderPlugin constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Buro210\Categorylist\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Buro210\CategoryList\Helper\Data $helper
    ) {
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
    }

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetData(
        Subject $subject,
        \Closure $proceed
    ) {
        $result = $proceed();

        $category = $subject->getCurrentCategory();
        if ($category) {
            $categoryData = $category->getData();

            if (isset($categoryData[\Buro210\CategoryList\Helper\Data::ATTRIBUTE_NAME])) {
                unset($categoryData[\Buro210\CategoryList\Helper\Data::ATTRIBUTE_NAME]);

                $result[$category->getId()][\Buro210\CategoryList\Helper\Data::ATTRIBUTE_NAME] = array(
                    array(
                        'name' => $category->getData(\Buro210\CategoryList\Helper\Data::ATTRIBUTE_NAME),
                        'url' => $this->_helper->getImageUrl($category),
                    )
                );
            }
        }

        return $result;
    }
}
