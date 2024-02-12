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

  private function _borderShorthandValue(
   $width,
   $style,
   $color
  ) {
   $size = $this->_unitValue($width);
   $value = $size.' '.$style['value'].' '.$color.'';
   if($size == 0 || $style == 'none') {
    $value = 'none';
   }
   return $value;
  }

  private function _boxShorthandValue(
   $top,
   $right,
   $bottom,
   $left
  ) {
   return
    $this->_unitValue($top).' '.
    $this->_unitValue($right).' '.
    $this->_unitValue($bottom).' '.
    $this->_unitValue($left);
  }

  private function _getContainerProperties(
   $width,
   $height,
   $margin,
   $padding,
   $background,
   $border
  ) {
   return '
    width:'.$this->_unitValue($width).';
    height:'.$this->_unitValue($height).';
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
    background-color:'.$this->_colorValue($background['color']).';
    border:'.
     $this->_borderShorthandValue(
      $border['width'],
      $border['style'],
      $this->_colorValue($border['color'])
     ).
    ';
    border-radius:'.$this->_unitValue($border['radius']).'; 
   ';
  }

  private function _getImageProperties(
   $width,
   $height,
   $object
  ) {
   return '
    width:'.$this->_unitValue($width).';
    height:'.$this->_unitValue($height).';
    object-fit:'.$object['fit']['value'].';
    object-position:'.
     $this->_unitValue($object['position']['x']).' '.
     $this->_unitValue($object['position']['y']).
    ';
   ';
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

  public function isInherit($source) {
   return $source['is_inherit'];
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

  public function getSlidesData() {
   $data = array();
   foreach($this->getSlides() as $key=>$slide){
    $data[] = array(
     'id' => $slide['record_id'],
     'data' => $slide['slide']['data']
    );
   }
   return $data;
  }

  public function getSlidesStyles() {
   $data = array();
   foreach($this->getSlides() as $key=>$slide){
    $data[] = array(
     'id' => $slide['record_id'],
     'styles' => $this->getStyles($slide['slide'])
    );
   }
   return $data;
  }
 
  /* Styles */

  public function getInstanceCss($data) {
   $node = $this->getStyles($data)['container'];
   $container =
    $this->_getContainerProperties(
     $node['width'],
     $node['height'],
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
     .$display
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
     height: 100%;
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

  public function getSlideItemCss($index, $item) {
   $container =
    $this->_getContainerProperties(
     $item['width'],
     $item['height'],
     $item['margin'],
     $item['padding'],
     $item['background'],
     $item['border']
    )
   ;
   return '
    #' . $this->getId() . ' .usslick__slide' . $index . ' .usslick__slide--inner {'
     .$container.
    '}'
   ;
  }

  public function getSlideItemImageCss($index, $item) {
   $image =
    $this->_getImageProperties(
     $item['width'],
     $item['height'],
     $item['object'],
    )
   ;
   return '
    #' . $this->getId() . ' .usslick__slide' . $index . ' .usslick__slide--image {'
     .$image.
    '}'
   ;
  }
  
  public function getSlidesCss() {
   $containers = array();
   $images = array();
   $breakpoints = array();
   foreach($this->getSlidesStyles() as $key){
    $id = $key['id'];
    $container = $this->getStyles($key)['container'];
    $image = $this->getStyles($key)['image'];
    $isResponsive = $this->getStyles($key)['is_responsive'];
    $isMobileFirst = $this->getStyles($key)['is_mobile_first'];
    $responsive = $this->_sortPosition($this->getStyles($key)['responsive']);
    $containers[] = $this->getSlideItemCss($id, $container);
    $images[] = $this->getSlideItemImageCss($id, $image);
    if($isResponsive && count($responsive)) {
     $mediafeature = ($isMobileFirst) ? 'min-width' : 'max-width';
     foreach($responsive as $res){
      $container = $this->getStyles($res)['container'];
      $image = $this->getStyles($res)['image'];
      if(!$this->isInherit($res)){
       $breakpoints[] = ' 
        @media('.$mediafeature.':' .$this->_unitValue($res['breakpoint']).'){'
         .$this->getSlideItemCss($id, $container)
         .$this->getSlideItemImageCss($id, $image).
        '}';
      }
     }
    }
   }
   return
    implode('', $containers).
    implode('', $images).
    implode('', $breakpoints)
   ;
  }

  public function getStylesheets() {
   $parent = $this->getParentData();
   $breakpoints = array();
   if($this->isResponsive()) {
    $mediafeature = ($this->isMobileFirst()) ? 'min-width' : 'max-width';
    foreach($this->getResponsiveData() as $child){
     if(!$this->isInherit($child)){
      $breakpoints[] = '
       @media('.$mediafeature.':'.$child['breakpoint'].'px){'
        .$this->getInstanceCss($child)
        .$this->getColumnsCss($child).
       '}';
     }
    }
   }
   $style =
    '<style>'
     .$this->getInstanceCss($parent)
     .$this->getColumnsCss($parent)
     .implode('', $breakpoints)
     .$this->getSlidesCss().
    '</style>'
   ; 
   return trim(preg_replace('/\s+/', ' ', $style));
  }

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

  public function getScript() {
   $js;
   if(!$this->isResponsive() && $this->isUnslick($this->getParentData())) {
    $js = '';
   } else {
    $js = '
     <script>
      require([
       "jquery",
       "slick"
      ], function($) {
       $(document).ready(function() {
        $("#'.$this->getId().'").slick('.$this->getSlickSettings().');
       });
      });
     </script>
    ';
   }
   return trim(preg_replace('/\s+/', ' ', $js));
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