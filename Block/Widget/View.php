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

  // private function _basicSettings($item){
  //  return array(
  //   'arrows' => $item['arrows'],
  //   'dots' => $item['dots'],
  //   'infinite' => $item['infinite'],
  //   'speed' => intval($item['speed']),
  //   'autoplay' => $item['autoplay'],
  //   'autoplaySpeed' => intval($item['autoplaySpeed']),
  //   'centerMode' => $item['centerMode'],
  //   'centerPadding' => $item['centerPadding'] . 'px',
  //   'adaptiveHeight' => $item['adaptiveHeight'],
  //   'slidesToShow' => intval($item['slidesToShow']),
  //   'slidesToScroll' => intval($item['slidesToScroll'])
  //  );
  // }

  // private function _breakpoints($items) {
  //  //array_multisort(array_column($data, 'position'), SORT_ASC, $data);
  //  $breakpoints = array();
  //  foreach($items as $item) {
  //   $settings = 'unslick';
  //   if(!$item['unslick']) {
  //    $settings = $this->_basicSettings($item);
  //    if(!$settings['autoplay'])
  //     unset($settings['autoplaySpeed']);
  //    if(!$settings['centerMode'])
  //     unset($settings['centerPadding']);
  //   }
  //   $breakpoints[] = array(
  //    'breakpoint' => intval($item['breakpoint']),
  //    'settings' => $settings
  //   );
  //  }
  //  return $breakpoints;
  // }

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

  public function getSettings(){
   $data = $this->getRawData();
   $settings = array();
   $settings = $this->_basicSettings($data);
   $settings['responsive'] = $this->_breakpoints($data['responsive']);
   $settings['mobileFirst'] = $data['mobileFirst'];
   if(!$settings['autoplay'])
    unset($settings['autoplaySpeed']);
   if(!$settings['centerMode'])
    unset($settings['centerPadding']);
   if(!$data['is_responsive'])
    unset($settings['responsive'], $settings['mobileFirst']);
   return json_encode($settings);
  }

  public function getSlides() {
   return $this->getRawData()['slides'];
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