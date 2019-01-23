<?php
/**
 * @author Thijs Adriaansens BURO210
 * @copyright Copyright © 2019 BURO210. All rights reserved.
 * @package Buro210/CategoryList
 */
namespace Buro210\CategoryList\Block\Widget;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Catalog\Model\CategoryFactory;

class CategoryWidget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_template = 'widget/categorywidget.phtml';

	const DEFAULT_IMAGE_WIDTH = 250;
	const DEFAULT_IMAGE_HEIGHT = 250;
	
	/**
	* \Magento\Catalog\Model\CategoryFactory $categoryFactory
	*/
	protected $_categoryFactory;
	
	/**
	* \Magento\Framework\Registry
	*/
	protected $_registry;

	/**
	* \Magento\Framework\Image\AdapterFactory
	*/
	protected $_imageFactory;

	/**
	* \ Magento\Framework\Filesystem
	*/
	protected $_filesystem;

	/**
	* @param \Magento\Framework\View\Element\Template\Context $context
	* @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
	* @param array $data
	*/
	public function __construct(
	Context $context,
	CategoryFactory $categoryFactory,
	AdapterFactory $imageFactory,
	Filesystem $filesystem,
	Registry $registry,
	array $data = []
	) {
		$this->_registry = $registry;
		$this->_imageFactory = $imageFactory;
		$this->_filesystem = $filesystem;
		$this->_categoryFactory = $categoryFactory;
		parent::__construct($context, $data);
	}

	/**
	* Retrieve current store categories
	*
	* @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
	*/
	public function getCategoryCollection()
	{
		$category = $this->_categoryFactory->create();
		
		$rootCatID = NULL;
		if($this->getData('parentcat') > 0){
			$rootCatID = $this->getData('parentcat'); 
		}
		else{
			$rootCatID = $this->_storeManager->getStore()->getRootCategoryId();
		}

		$category->load($rootCatID);
		$childCategories = $category->getChildrenCategories()->clear()->addAttributeToSelect('image')->addAttributeToSelect('additional_image')->addAttributeToSelect('include_in_menu');
		return $childCategories;
	}
	
	/**
	* Get the width of product image
	* @return int
	*/
	public function getImageWidth() {
		if($this->getData('imagewidth')==''){
			return self::DEFAULT_IMAGE_WIDTH;
		}
		return (int) $this->getData('imagewidth');
	}

	/**
	* Get the height of product image
	* @return int
	*/
	public function getImageHeight() {
		if($this->getData('imageheight')==''){
			return self::DEFAULT_IMAGE_HEIGHT;
		}
		return (int) $this->getData('imageheight');
	}
	
	public function canShowImage(){
		if($this->getData('image') == 'image')
			return true;
		elseif($this->getData('image') == 'no-image')
			return false;
	}

	public function getCurrentCategory()
	{
		return $this->_registry->registry('current_category');
	}

	// pass imagename, width and height
	public function resize($image, $width = null, $height = null)
	{
		$absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::ROOT)->getAbsolutePath(parse_url($image,PHP_URL_PATH));

		$imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath(str_replace(basename($image), "", parse_url($image,PHP_URL_PATH)).'resized/'.$width.'x'.$height.'/').basename($image);
		//create image factory...
		$imageResize = $this->_imageFactory->create();
		$imageResize->open($absolutePath);
		$imageResize->constrainOnly(TRUE);
		$imageResize->keepTransparency(TRUE);
		$imageResize->keepFrame(TRUE);
		$imageResize->keepAspectRatio(TRUE);
		$imageResize->backgroundColor(array(255,255,255));
		$imageResize->resize($width,$height);
		//destination folder
		$destination = $imageResized ;
		//save image      
		$imageResize->save($destination);

		$resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).str_replace(basename($image), "", parse_url($image,PHP_URL_PATH)).'resized/'.$width.'x'.$height.'/'.basename($image);
		return $resizedURL;
	}

}
