<?php

 namespace UpSage\SlickSliderWidget\Model\Config\Source\Object;
 use Magento\Framework\Option\ArrayInterface;
 
 class Fit implements ArrayInterface {
    
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
    'fill' => __('Fill'),
    'contain' => __('Contain'),
    'cover' => __('Cover'),
    'none' => __('None'),
    'scale-down' => __('Scale Down')
   ];
  }

}