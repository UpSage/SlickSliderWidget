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

  /* Helpers */

  private function _sortPosition($source) {
   $data = array();
   foreach($source as $key=>$item){
    $data[] = $item;
   }
   array_multisort(
    array_column($data, 'position'),
    SORT_ASC,
    $data
   );
   return $data;
  }

  private function _getCssUnitValue($prop) {
   return $prop['value']['length'] . $prop['value']['unit'];
  }

  private function _getCssPropValue($prop) {
   $value = $prop['type'];
   if($prop['type'] == 'length') {
    $value = $this->_getCssUnitValue($prop);
   }
   return $value;
  }

  private function _getSlickSetup($item) {
   $settings = array(
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
   if(!$settings['autoplay'])
    unset($settings['autoplaySpeed']);
   if(!$settings['centerMode'])
    unset($settings['centerPadding']);
   return $settings;
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

  /* Data */

  public function getRawData() {
   return $this->getData('slider_data');
  }

  public function getParentData() {
   $source = $this->getRawData();
   unset(
    $source['identifier'],
    $source['is_responsive'],
    $source['responsive'],
    $source['is_mobile_first'],
    $source['slides'],
    $source['']
   );
   return $source;
  }

  public function getResponsiveData() {
   return $this->_sortPosition($this->getRawData()['responsive']);
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

  public function getSettings($source) {
   return $source['settings'];
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
  
  public function getSlides() {
   return $this->_sortPosition($this->getRawData()['slides']);
  }

  /* Slides */

  public function getSlidesData() {
   $data = array();
   foreach($this->getSlides() as $key=>$slide){
    $data[] = $slide['slide']['data'];
   }
   return $data;
  }

  public function getSlidesStyles() {
   $data = array();
   foreach($this->getSlides() as $key=>$slide){
    $data[] = $this->getStyles($slide['slide']);
   }
   return $data;
  }
 
  /* Styles */

  public function getContainerCss($data, $styles) {
   $display = 'display:block;';
   if($this->isUnslick($data)) {
    $display = 'display:flex; flex-wrap:wrap;';
   }
   return
    '#' . $this->getId() . '{'
     . $display . '
     width:' . $this->_getCssPropValue($styles['width']) . ';
     margin:'
      . $this->_getCssPropValue($styles['margin']['top']) . ' '
      . $this->_getCssPropValue($styles['margin']['right']) . ' '
      . $this->_getCssPropValue($styles['margin']['bottom']) . ' '
      . $this->_getCssPropValue($styles['margin']['left']) . ';
    }';
  }

  public function getColumnCss($data, $styles) {
   $width = 'width: 100%;';
   if($this->isUnslick($data)) {
    $width = 'width: calc(100%/' . $styles['count'] . '); flex: 0 0 auto;';
   }
   $style = $styles['styles'];
   return
    '#' . $this->getId() . ' > div {' . 
      $width . '
      margin:'
       . $this->_getCssPropValue($style['margin']['top']) . ' '
       . $this->_getCssPropValue($style['margin']['right']) . ' '
       . $this->_getCssPropValue($style['margin']['bottom']) . ' '
       . $this->_getCssPropValue($style['margin']['left']) .';
      padding:'
       . $this->_getCssPropValue($style['padding']['top']) . ' '
       . $this->_getCssPropValue($style['padding']['right']) . ' '
       . $this->_getCssPropValue($style['padding']['bottom']) . ' '
       . $this->_getCssPropValue($style['padding']['left']) .';
    }';
  }

  public function getSlideContainerCss($key, $index) {
   $container = $key['container'];
   return '
    #' . $this->getId() . ' .usslick__slide' . $index . '{
     height:' . $this->_getCssPropValue($container['height']) . ';
    }
   ';
  }

  public function getSlideImageCss($key, $index) {
   $image = $key['image'];
   $x = $image['object-position']['x']['value'];
   $y = $image['object-position']['y']['value'];
   return '
    #' . $this->getId() . ' .usslick__slide' . $index . ' .usslick__slide--image {
     width:' . $this->_getCssPropValue($image['width']) . ';
     height:' . $this->_getCssPropValue($image['height']) . ';
     object-fit:' . $image['object-fit']['value'] . ';
     object-position:'. $x['length'] . $x['unit'] .' '. $y['length'] . $y['unit'] .';
    }
   ';
  }

  public function getSlideCss() {
   $container = array();
   $images = array();
   $breakpoints = array();
   foreach($this->getSlidesStyles() as $index=>$key){
    $container[] = $this->getSlideContainerCss($key,$index);
    $images[] = $this->getSlideImageCss($key,$index);
    $isResponsive = $key['is_responsive'];
    $isMobileFirst = $key['is_mobile_first'];
    $responsive = $this->_sortPosition($key['responsive']);
    if($isResponsive) {
     $mediafeature = ($isMobileFirst) ? 'min-width' : 'max-width';
     foreach($responsive as $i=>$res){
      $breakpoints[] = ' 
       @media('.$mediafeature.':' . $this->_getCssUnitValue($res['breakpoint']) . '){'
        . $this->getSlideContainerCss($res['styles'],$index)
        . $this->getSlideImageCss($res['styles'],$index)
        . '
       }';
     }
    }
   }
   return implode('', $container) . implode('', $images) . implode('', $breakpoints);
  }

  public function getStylesheets() {
   $data = $this->getParentData();
   $breakpoints = array();
   if($this->isResponsive()) {
    $mediafeature = ($this->isMobileFirst()) ? 'min-width' : 'max-width';
    foreach($this->getResponsiveData() as $responsive){
     $breakpoints[] = '
      @media('.$mediafeature.':' . $responsive['breakpoint'] . 'px){'
       . $this->getContainerCss($responsive, $this->getStyles($responsive))
       . $this->getColumnCss($responsive, $this->getColumns($responsive)) . '
      }';
    }
   }
   $style =
    '<style>'
     . $this->getContainerCss($data, $this->getStyles($data))
     . $this->getColumnCss($data, $this->getColumns($data))
     . implode('', $breakpoints)
     . $this->getSlideCss() .
    '</style>'
   ;
   return trim(preg_replace('/\s+/', ' ', $style));
  }

  /* Scripts */
  
  public function getParentSlickSettings() {
   $source = $this->getSettings($this->getParentData());
   $settings = array();
   $settings = $this->_getSlickSetup($source);
   $settings['mobileFirst'] = $this->isMobileFirst();
   return $settings;
  }

  public function getResponsiveSlickSettings() {
   $source = $this->getResponsiveData();
   $breakpoints = array();
   foreach($source as $data) {
    $settings = 'unslick';
    if(!$this->isUnslick($data)) {
     $settings = $this->_getSlickSetup($this->getSettings($data));
    }
    $breakpoints[] = array(
     'breakpoint' => intval($data['breakpoint']),
     'settings' => $settings
    );
   }
   if($this->isUnslick($this->getParentData())) {
    $settings = 'unslick';
    if($this->isMobileFirst()) {
     $breakpoints[] = array(
      'breakpoint' => 0,
      'settings' => $settings
     ); 
    } else {
     $breakpoints[] = array(
      'breakpoint' => 99999,
      'settings' => $settings
     ); 
    }
   }
   return $breakpoints;
  }

  public function getSlickSettings() {
   $settings = $this->getParentSlickSettings();
   if($this->isResponsive()) {
    $settings['responsive'] = $this->getResponsiveSlickSettings();
   }
   return $settings;
  }

  /* Slides */

  public function getLink($link) {
   $url = $link['default'];
   if($link['type'] == 'page' && $link['page']) {
    $url = $this->_getPageUrl($link['page']);
   } elseif($link['type'] == 'product' && $link['product']) {
    $url = $this->_getProductUrl($link['product']);
   } elseif($link['type'] == 'category' && $link['category']) {
    $url = $this->_getCategoryUrl($link['category']);
   }
   return $url;
  }

  public function getTarget($link) {
   return ($link['setting']) ? 'target="_blank"': '';
  }

  public function getImage($image) {
   if(!$image) {
    return '';
   } else {
    foreach($image as $item) {
     return $this->_storeManager->getStore()->getBaseUrl() . ltrim($item['url'], '/');
    }
   }
  }

 }