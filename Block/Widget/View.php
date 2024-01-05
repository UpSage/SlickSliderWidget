<?php

 namespace UpSage\SlickSliderWidget\Block\Widget;

 use Magento\Widget\Block\BlockInterface;
 
 class View extends \Grasch\AdminUi\Block\Widget\AbstractWidget implements BlockInterface {
    
  protected $_template = 'widget/view.phtml';

  private function _basicSettings($item){
   return array(
    'arrows' => $item['arrows'],
    'dots' => $item['dots'],
    'infinite' => $item['infinite'],
    'speed' => intval($item['speed']),
    'autoplay' => $item['autoplay'],
    'autoplaySpeed' => intval($item['autoplaySpeed']),
    'centerMode' => $item['centerMode'],
    'centerPadding' => $item['centerPadding'] . 'px',
    'adaptiveHeight' => $item['adaptiveHeight'],
    'slidesToShow' => intval($item['slidesToShow']),
    'slidesToScroll' => intval($item['slidesToScroll'])
   );
  }

  public function getBreakpoints($data) {
   //array_multisort(array_column($data, 'position'), SORT_ASC, $data);
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

  public function getSettings($data) {
   $settings = array();
   $settings = $this->_basicSettings($data);
   $settings['responsive'] = $this->getBreakpoints($data['responsive']);
   $settings['mobileFirst'] = $data['mobileFirst'];
   if(!$settings['autoplay'])
    unset($settings['autoplaySpeed']);
   if(!$settings['centerMode'])
    unset($settings['centerPadding']);
   if(!$data['is_responsive'])
    unset($settings['responsive'], $settings['mobileFirst']);
   return json_encode($settings, JSON_PRETTY_PRINT);
  }

 }