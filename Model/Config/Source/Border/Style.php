<?php

 namespace UpSage\SlickSliderWidget\Model\Config\Source\Border;
 use Magento\Framework\Option\ArrayInterface;
 
 class Style implements ArrayInterface {
    
  public function toOptionArray() {
   $result = [];
   foreach ($this->getOptions() as $value => $label) {
    $result[] = [
     'value' => $value,
     'label' => $label,
    ];
   }
   return $result;
  }
  
  public function getOptions() {
   return [
    'none' => __('None'),
    'solid' => __('Solid'),
    'dotted' => __('Dotted'),
    'dashed' => __('Dashed'),
    'inset' => __('Inset'),
    'outset' => __('Outset'),
    'ridge' => __('Ridge'),
    'groove' => __('Groove'),
    'double' => __('Double'),
    'hidden' => __('Hidden')
   ];
  }

}