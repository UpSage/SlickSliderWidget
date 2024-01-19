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

  private function _basicSettings($item){
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

  private function _getCategoryUrl($id){
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

  public function getParentData(){
   $source = $this->getRawData();
   return array(
    'identifier' => $source['identifier'],
    'unslick' => $source['unslick'],
    'is_responsive' => $source['is_responsive'],
    'columns' => $source['columns'],
    'settings' => $source['settings'],
    'styles' => $source['styles']
   );
  }

  public function getResponsiveData(){
   $data = array();
   foreach($this->getRawData()['responsive'] as $key=>$item){
    $data[] = array(
     'id' => $item['record_id'],
     'breakpoint' => $item['breakpoint'],
     'unslick' => $item['unslick'],
     'columns' => $item['columns'],
     'settings' => $item['settings'],
     'styles' => $item['styles']
    );
   }
   return $data;
  }

  /* Helper */

  private function _getCssUnitValue($prop) {
   $value = $prop['type'];
   if($prop['type'] == 'length') {
    $value = $prop['value']['length'] . $prop['value']['unit'];
   }
   return $value;
  }

  public function isResponsive() {
   return $this->getParentData()['is_responsive'];
  }

  public function isMobileFirst() {
   return $this->getParentData()['settings']['mobileFirst'];
  }

  public function isUnslick() {
   return $this->getParentData()['unslick'];
  }

  /* Slider / Slicked */

  public function getId(){
   return $this->getParentData()['identifier'];
  }

  public function getSliderStyle($width, $margin) {
   return
   '#' . $this->getId() . '{' .
    'width: ' . $this->_getCssUnitValue($width) . '; margin: '
     . $this->_getCssUnitValue($margin['top']) . ' '
     . $this->_getCssUnitValue($margin['right']) . ' '
     . $this->_getCssUnitValue($margin['bottom']) . ' '
     . $this->_getCssUnitValue($margin['left']) .
    ';}';
  }

  public function getSliderResponsiveStyle() {
   $breakpoints = array();
   if($this->isResponsive()){
    $mediafeature = ($this->isMobileFirst()) ? 'min-width' : 'max-width';
    foreach($this->getResponsiveData() as $breakpoint){
     $breakpoints[] = '@media('.$mediafeature.':' . $breakpoint['breakpoint'] . 'px){'
      .
       $this->getSliderStyle(
        $breakpoint['styles']['width'],
        $breakpoint['styles']['margin']
       )
      .
     '}';
    }
   }
   return implode('', $breakpoints);
  }

  
  public function getSettings(){
   $data = $this->getRawData()['settings'];
   $settings = array();
   $settings = $this->_basicSettings($data);
   $settings['mobileFirst'] = $data['mobileFirst'];
   if(!$settings['autoplay'])
    unset($settings['autoplaySpeed']);
   if(!$settings['centerMode'])
    unset($settings['centerPadding']);
   // if(!$data['is_responsive'])
   // unset($settings['responsive'], $settings['mobileFirst']);
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

  /* Columns / UnSlicked */

  public function getColumnCount($source){
   return intval($source['columns']['count']);
  }

  public function getColumnStyles($margin, $padding) {
  //  $this->getColumnCount($this->getParentData());
   return
   '#' . $this->getId() . ' > div {' .
    'width: calc(100%/' . $this->getColumnCount($this->getParentData()) . ');'.
    'margin: '
     . $this->_getCssUnitValue($margin['top']) . ' '
     . $this->_getCssUnitValue($margin['right']) . ' '
     . $this->_getCssUnitValue($margin['bottom']) . ' '
     . $this->_getCssUnitValue($margin['left']) .
    ';padding:'
    . $this->_getCssUnitValue($padding['top']) . ' '
    . $this->_getCssUnitValue($padding['right']) . ' '
    . $this->_getCssUnitValue($padding['bottom']) . ' '
    . $this->_getCssUnitValue($padding['left']) .
   ';}';
  }

  public function getStyles(){
    return
     '<style>'
      .
       $this->getSliderStyle(
        $this->getParentData()['styles']['width'],
        $this->getParentData()['styles']['margin']
       )
      . $this->getSliderResponsiveStyle() .
       $this->getColumnStyles(
        $this->getParentData()['columns']['styles']['margin'],
        $this->getParentData()['columns']['styles']['padding']
       )
      .
     '</style>';
   }
 

  // public function getColumns($type) {
  //  $count = $this->getRawData()['columns']['count'];
  //  $styles = $this->getRawData()['columns']['styles'];
  //  switch($type){
  //   case 'count':
  //    return intval($count);
  //    break;
  //   case 'styles':
  //    $margin = $styles['margin'];
  //    $padding = $styles['padding'];
  //    return
  //     'margin: '
  //      . $this->_getCssUnitValue($margin['top']) . ' '
  //      . $this->_getCssUnitValue($margin['right']) . ' '
  //      . $this->_getCssUnitValue($margin['bottom']) . ' '
  //      . $this->_getCssUnitValue($margin['left']) .
  //     '; padding: ' 
  //      . $this->_getCssUnitValue($padding['top']) . ' '
  //      . $this->_getCssUnitValue($padding['right']) . ' '
  //      . $this->_getCssUnitValue($padding['bottom']) . ' '
  //      . $this->_getCssUnitValue($padding['left']) . ';'
  //    ;
  //   break;
  //  }
  // }

  public function getSlides(){
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