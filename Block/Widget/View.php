<?php

 namespace UpSage\SlickSliderWidget\Block\Widget;

 use Magento\Widget\Block\BlockInterface;
 
 class View extends \Grasch\AdminUi\Block\Widget\AbstractWidget implements BlockInterface {
    
  protected $_template = 'widget/view.phtml';

  public function getBreakpoints($data) {
   //array_multisort(array_column($data, 'position'), SORT_ASC, $data);
   $breakpoints = array();
   foreach($data as $item) {
    $settings = 'unslick';
    unset(
     $item['record_id'],
     $item['position'],
     $item['initialize']
    );
    $item['centerPadding'] = $item['centerPadding'] . 'px';
    if(!$item['autoplay'])
     unset($item['autoplaySpeed']);
    if(!$item['centerMode'])
     unset($item['centerPadding']);
    if(!$item['unslick'])
     $settings = array_slice($item, 2);
    $breakpoints[] = array(
     'breakpoint' => $item['breakpoint'],
     'settings' => $settings
    );
   }
   return $breakpoints;
  }

  public function getSettings($data) {
   $settings = array(
    'arrows' => $data['arrows'],
    'dots' => $data['dots'],
    'infinite' => $data['infinite'],
    'fade' => $data['fade'],
    'speed' => intval($data['speed']),
    'autoplay' => $data['autoplay'],
    'autoplaySpeed' => intval($data['autoplaySpeed']),
    'centerMode' => $data['centerMode'],
    'centerPadding' => $data['centerPadding'] . 'px',
    'adaptiveHeight' => $data['adaptiveHeight'],
    'slidesToShow' => intval($data['slidesToShow']),
    'slidesToScroll' => intval($data['slidesToScroll']),
    'responsive' => $this->getBreakpoints($data['responsive']),
    'mobileFirst' => $data['mobileFirst']
   );
   if(!$settings['autoplay'])
    unset($settings['autoplaySpeed']);
   if(!$settings['centerMode'])
    unset($settings['centerPadding']);
   if(!$data['is_responsive'])
    unset($settings['responsive'], $settings['mobileFirst']);
   return json_encode($settings);
  }

 }