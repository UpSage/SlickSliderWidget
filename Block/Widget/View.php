<?php

 namespace UpSage\SlickSliderWidget\Block\Widget;
 
 class View extends \Grasch\AdminUi\Block\Widget\AbstractWidget {
    
  protected $_template = 'widget/view.phtml';
  protected $_categoryRepository;
  protected $_productRepository;
  protected $_pageRepository;
  protected $_storeManager;
  
  public function __construct(
   \Magento\Store\Model\StoreManagerInterface $_storeManager,
   \Magento\Catalog\Model\CategoryRepository $_categoryRepository,
   \Magento\Catalog\Model\ProductRepository $_productRepository,
   \Magento\Cms\Model\PageRepository $_pageRepository,
   \Magento\Framework\View\Element\Template\Context $context,
   \Grasch\AdminUi\Model\DecodeComponentValue $decodeComponentValue,
   array $data = []
  ) {
   parent::__construct($context, $decodeComponentValue, $data);
   $this->_categoryRepository = $_categoryRepository;
   $this->_productRepository = $_productRepository;
   $this->_pageRepository = $_pageRepository;
   $this->_storeManager = $_storeManager;
  }

  /* Helper */

  private function _getCssUnitValue($prop) {
   $value = $prop['type'];
   if($prop['type'] == 'length') {
    $value = $prop['value']['length'] . $prop['value']['unit'];
   }
   return $value;
  }

  private function _basicSettings($item) {
   return array(
    'arrows' => $item['arrows'],
    'dots' => $item['dots'],
    'infinite' => $item['infinite'],
    'speed' => intval($item['speed']),
    'autoplay' => $item['autoplay'],
    'autoplaySpeed' => intval($item['autoplaySpeed']),
    'centerMode' => $item['centerMode'],
    'centerPadding' => $item['centerPadding']['value']['length'] . $item['centerPadding']['value']['unit'],
    'adaptiveHeight' => $item['adaptiveHeight'],
    'slidesToShow' => intval($item['slidesToShow']),
    'slidesToScroll' => intval($item['slidesToScroll'])
   );
  }

  private function _getCategoryUrl($id) {
   $_category = $this->_categoryRepository->get($id, $this->_storeManager->getStore()->getId());
   return $_category->getUrl();
  }

  private function _getProductUrl($id) {
   $_product = $this->_productRepository->getById($id, false, $this->_storeManager->getStore()->getId());
   return $_product->getProductUrl(); 
  }

  private function _getPageUrl($id) {
   $_page = $this->_pageRepository->getById($id);
   return $this->_storeManager->getStore()->getUrl($_page->getIdentifier()); 
  }

  public function getRawData() {
   return $this->getData('slider_data');
  }

  public function getResponsiveData() {
   return $this->getRawData()['responsive'];
  }

  public function getId() {
   return $this->getRawData()['identifier'];
  }

  public function getStyles($source) {
   return $source['styles'];
  }

  public function getColumns($source) {
   return $source['columns'];
  }

  public function isUnslick($source) {
   return $source['is_unslick'];
  }

  public function isResponsive() {
   return $this->getRawData()['is_responsive'];
  }

  public function isMobileFirst() {
   return $this->getRawData()['is_mobile_first'];
  }

  /* Styles */

  public function getContainerStyle($source) {
   return
    '#' . $this->getId() . '{
      width:' . $this->_getCssUnitValue($source['width']) . ';
      margin:'
       . $this->_getCssUnitValue($source['margin']['top']) . ' '
       . $this->_getCssUnitValue($source['margin']['right']) . ' '
       . $this->_getCssUnitValue($source['margin']['bottom']) . ' '
       . $this->_getCssUnitValue($source['margin']['left']) . ';
    }';
  }

  public function getColumnStyles($source) {
   $count = $source['count'];
   $styles = $source['styles'];
   return
   '#' . $this->getId() . ' > div {' .
    'width: calc(100%/' . $count . ');'.
    'flex: 0 0 auto;
    margin:'
     . $this->_getCssUnitValue($styles['margin']['top']) . ' '
     . $this->_getCssUnitValue($styles['margin']['right']) . ' '
     . $this->_getCssUnitValue($styles['margin']['bottom']) . ' '
     . $this->_getCssUnitValue($styles['margin']['left']) .
    ';padding:'
     . $this->_getCssUnitValue($styles['padding']['top']) . ' '
     . $this->_getCssUnitValue($styles['padding']['right']) . ' '
     . $this->_getCssUnitValue($styles['padding']['bottom']) . ' '
     . $this->_getCssUnitValue($styles['padding']['left']) .';
   }';
  }

  public function getStylesheets() {
   /* Parent */
   $parentData = $this->getRawData();
   $parent_columns = ($this->isUnslick($parentData)) ? '#'.$this->getId().'{display:flex;flex-wrap:wrap;}' . $this->getColumnStyles($this->getColumns($parentData)) : '';
   /* Breakpoints */
   $breakpoints = array();
   if($this->isResponsive()) {
    $mediafeature = ($this->isMobileFirst()) ? 'min-width' : 'max-width';
    foreach($this->getResponsiveData() as $responsiveData){
     $responsive_columns = ($this->isUnslick($responsiveData)) ? $this->getColumnStyles($this->getColumns($responsiveData)) : '';
     $breakpoints[] = '@media('.$mediafeature.':' . $responsiveData['breakpoint'] . 'px){' . $this->getContainerStyle($this->getStyles($responsiveData)) . $responsive_columns . '}';
    }
   }
   return '<style>' . $this->getContainerStyle($this->getStyles($parentData)) . $parent_columns . implode('', $breakpoints) . '</style>';
  }
  
  public function getSettings() {
   $data = $this->getRawData()['settings'];
   $settings = array();
   $settings = $this->_basicSettings($data);
   $settings['mobileFirst'] = $data['mobileFirst'];
   if(!$settings['autoplay'])
    unset($settings['autoplaySpeed']);
   if(!$settings['centerMode'])
    unset($settings['centerPadding']);
   return json_encode($settings,JSON_PRETTY_PRINT);
  }

  public function getResponsiveSettings() {
   //array_multisort(array_column($data, 'position'), SORT_ASC, $data);
   $data = $this->getResponsiveData()['settings'];
   $unslick = $this->getResponsiveData()['unslick'];
   $breakpoints = array();
   foreach($data as $item) {
    $settings = 'unslick';
    if(!$item['unslick']) {
     $settings = $this->_basicSettings($item);
     if(!$settings['autoplay'])
      unset($settings['autoplaySpeed']);
     if(!$settings['centerMode'])
      unset($settings['centerPadding']);
    }
    $breakpoints[] = array(
     'breakpoint' => intval($item['breakpoint']),
     'settings' => $settings
    );
   }
   return $breakpoints;
  }

  public function getSlides() {
   $data = array();
   foreach($this->getRawData()['slides'] as $key=>$slide){
    $data[] = $slide['slide']['data'];
   }
   return $data;
  }

  public function getSlideLink($link) {
   $url = '';
   if($link['type'] == 'page' && $link['page']) {
    $url = $this->_getPageUrl($link['page']);
   } elseif($link['type'] == 'product' && $link['product']) {
    $url = $this->_getProductUrl($link['product']);
   } elseif($link['type'] == 'category' && $link['category']) {
    $url = $this->_getCategoryUrl($link['category']);
   } else {
    $url = $link['default'];
   }
   return $url;
  }

  public function getSlideTarget($link) {
   return $link['setting'];
  }

  public function getSlideImage($image) {
   if(!$image) {
    return '';
   } else {
    foreach($image as $item) {
     return $this->_storeManager->getStore()->getBaseUrl() . ltrim($item['url'], '/');
    }
   }
  }

 }