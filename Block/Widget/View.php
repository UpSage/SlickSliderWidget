<?php

 namespace UpSage\SlickSliderWidget\Block\Widget;
 
 class View extends \Grasch\AdminUi\Block\Widget\AbstractWidget {
    
  protected $_template = 'widget/view.phtml';
  protected $_timezoneInterface;
  protected $_categoryRepository;
  protected $_productRepository;
  protected $_pageRepository;
  protected $_storeManager;
  
  public function __construct(
   \Magento\Store\Model\StoreManagerInterface $_storeManager,
   \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
   \Magento\Catalog\Model\CategoryRepository $_categoryRepository,
   \Magento\Catalog\Model\ProductRepository $_productRepository,
   \Magento\Cms\Model\PageRepository $_pageRepository,
   \Magento\Framework\View\Element\Template\Context $context,
   \Grasch\AdminUi\Model\DecodeComponentValue $decodeComponentValue,
   array $data = []
  ) {
   parent::__construct($context, $decodeComponentValue, $data);
   $this->_timezoneInterface = $timezoneInterface;
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

  private function _unitValue($prop) {
   $length = $prop['value']['length'];
   $unit = $prop['value']['unit'];
   $value = $length . $unit;
   if($length == '0') {
    $value = 0;
   } elseif(!$length) {
    $value = 'auto';
   }
   return $value;
  }

  private function _colorValue($prop) {
   $color = $prop['value'];
   if(!$color) {
    $color = 'transparent';
   }
   return $color;
  }

  private function _borderShorthandValue($width, $style, $color) {
   $size = $this->_unitValue($width);
   $value = $size.' '.$style['value'].' '.$color.'';
   if($size == 0 || $style == 'none') {
    $value = 'none';
   }
   return $value;
  }

  private function _boxShorthandValue($top, $right, $bottom, $left) {
   return
    $this->_unitValue($top).' '.
    $this->_unitValue($right).' '.
    $this->_unitValue($bottom).' '.
    $this->_unitValue($left);
  }

  private function _getContainerCss($margin, $padding, $background, $border) {
   return
    'margin:'.
     $this->_boxShorthandValue(
      $margin['top'],
      $margin['right'],
      $margin['bottom'],
      $margin['left']
     ).
    ';
    padding:'.
     $this->_boxShorthandValue(
      $padding['top'],
      $padding['right'],
      $padding['bottom'],
      $padding['left']
     ).
    ';
    background-color:'.$this->_colorValue($background['color']).';
    border:'.
     $this->_borderShorthandValue(
      $border['width'],
      $border['style'],
      $this->_colorValue($border['color'])
     ).
    ';
    border-radius:'. $this->_unitValue($border['radius']) .';'
   ;
  }

  private function _slickSetup($instance) {
   $settings = array(
    'arrows' => $instance['arrows'],
    'dots' => $instance['dots'],
    'infinite' => $instance['infinite'],
    'speed' => intval($instance['speed']),
    'autoplay' => $instance['autoplay'],
    'autoplaySpeed' => intval($instance['autoplaySpeed']),
    'centerMode' => $instance['centerMode'],
    'centerPadding' => $this->_unitValue($instance['centerPadding']),
    'adaptiveHeight' => $instance['adaptiveHeight'],
    'slidesToShow' => intval($instance['slidesToShow']),
    'slidesToScroll' => intval($instance['slidesToScroll'])
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

  public function getDateTimeZone($date) {
   return $this->_timezoneInterface->date(new \DateTime($date))->format('m/d/y H:i:s');
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

  public function getInstanceCss($data) {
   $node = $this->getStyles($data)['container'];
   $container =
    $this->_getContainerCss(
     $node['margin'],
     $node['padding'],
     $node['background'],
     $node['border']
    )
   ;
   $display =
    $this->isUnslick($data) ?
     'display:flex;flex-wrap:wrap;' :
     'display:block;'
   ;
   return
    '#' . $this->getId() . '{'
     .$display.'
     width:'.$this->_unitValue($node['width']).';'
     .$container.
    '}'
   ;
  }

  public function getColumnsCss($data) {
   $node = $this->getStyles($data)['columns'];
   $count = $this->getColumns($data)['count'];
   $margin = $node['margin'];
   $padding = $node['padding'];
   $css =
    '#' . $this->getId() . ' > div {
     width: 100%;
     padding: 0;
     margin: 0;
    }'
   ;
   if($this->isUnslick($data)) {
    $css =
     '#' . $this->getId() . ' > div {
      flex:0 0 auto;
      width:calc(100%/' . $count . ');
      margin:'.
       $this->_boxShorthandValue(
        $margin['top'],
        $margin['right'],
        $margin['bottom'],
        $margin['left']
       ).
      ';
      padding:'.
       $this->_boxShorthandValue(
        $padding['top'],
        $padding['right'],
        $padding['bottom'],
        $padding['left']
       ).
      ';
     }'
    ;
   }
   return $css;
  }

  public function getStylesheets() {
   $parent = $this->getParentData();
   $breakpoints = array();
   if($this->isResponsive()) {
    $mediafeature = ($this->isMobileFirst()) ? 'min-width' : 'max-width';
    foreach($this->getResponsiveData() as $child){
     $breakpoints[] = '
      @media('.$mediafeature.':'.$child['breakpoint'].'px){'
       .$this->getInstanceCss($child)
       .$this->getColumnsCss($child).
      '}';
    }
   }
   $style =
    '<style>'
     .$this->getInstanceCss($parent)
     .$this->getColumnsCss($parent)
     .implode('', $breakpoints).
    '</style>'
   ; 
   return trim(preg_replace('/\s+/', ' ', $style));
  }



  // public function getSlideContainerCss($key, $index) {
  //  $container = $key['container'];
  //  return '
  //   #' . $this->getId() . ' .usslick__slide' . $index . '{
  //    height:' . $this->_getCssPropValue($container['height']) . ';
  //   }
  //  ';
  // }

  // public function getSlideImageCss($key, $index) {
  //  $image = $key['image'];
  //  $x = $image['object-position']['x']['value'];
  //  $y = $image['object-position']['y']['value'];
  //  return '
  //   #' . $this->getId() . ' .usslick__slide' . $index . ' .usslick__slide--image {
  //    width:' . $this->_getCssPropValue($image['width']) . ';
  //    height:' . $this->_getCssPropValue($image['height']) . ';
  //    object-fit:' . $image['object-fit']['value'] . ';
  //    object-position:'. $x['length'] . $x['unit'] .' '. $y['length'] . $y['unit'] .';
  //   }
  //  ';
  // }

  // public function getSlideCss() {
  //  $container = array();
  //  $images = array();
  //  $breakpoints = array();
  //  foreach($this->getSlidesStyles() as $index=>$key){
  //   $container[] = $this->getSlideContainerCss($key,$index);
  //   $images[] = $this->getSlideImageCss($key,$index);
  //   $isResponsive = $key['is_responsive'];
  //   $isMobileFirst = $key['is_mobile_first'];
  //   $responsive = $this->_sortPosition($key['responsive']);
  //   if($isResponsive) {
  //    $mediafeature = ($isMobileFirst) ? 'min-width' : 'max-width';
  //    foreach($responsive as $i=>$res){
  //     $breakpoints[] = ' 
  //      @media('.$mediafeature.':' . $this->_getCssUnitValue($res['breakpoint']) . '){'
  //       . $this->getSlideContainerCss($res['styles'],$index)
  //       . $this->getSlideImageCss($res['styles'],$index)
  //       . '
  //      }';
  //    }
  //   }
  //  }
  //  return implode('', $container) . implode('', $images) . implode('', $breakpoints);
  // }

  // public function getStylesheets() {
  //  $data = $this->getParentData();
  //  $breakpoints = array();
  //  if($this->isResponsive()) {
  //   $mediafeature = ($this->isMobileFirst()) ? 'min-width' : 'max-width';
  //   foreach($this->getResponsiveData() as $responsive){
  //    $breakpoints[] = '
  //     @media('.$mediafeature.':' . $responsive['breakpoint'] . 'px){'
  //      . $this->getContainerCss($responsive, $this->getStyles($responsive))
  //      . $this->getColumnCss($responsive, $this->getColumns($responsive)) . '
  //     }';
  //   }
  //  }
  //  $style =
  //   '<style>'
  //    . $this->getContainerCss($data, $this->getStyles($data))
  //    . $this->getColumnCss($data, $this->getColumns($data))
  //    . implode('', $breakpoints)
  //    . $this->getSlideCss() .
  //   '</style>'
  //  ;
  //  return trim(preg_replace('/\s+/', ' ', $style));
  // }

  /* Scripts */
  
  public function getParentSlickSettings() {
   $source = $this->getParentData();
   $setup = $this->getSettings($source);
   $settings = array();
   $settings = $this->_slickSetup($setup);
   $settings['mobileFirst'] = $this->isMobileFirst();
   return $settings;
  }

  public function getResponsiveSlickSettings() {
   $source = $this->getResponsiveData();
   $breakpoints = array();
   foreach($source as $data) {
    $settings = 'unslick';
    if(!$this->isUnslick($data)) {
     $settings = $this->_slickSetup($this->getSettings($data));
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
   return json_encode($settings);
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