<?php

 namespace UpSage\SlickSliderWidget\Model\Config\Source;
 use Magento\Framework\Option\ArrayInterface;
 
 class Units implements ArrayInterface {
    
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
    'px' => __('px'),
    '%' => __('%'),
    'pt' => __('pt'),
    'em' => __('em'),
    'rem' => __('rem'),
    'in' => __('in'),
    'cm' => __('cm'),
    'mm' => __('mm'),
    'vw' => __('vw'),
    'vh' => __('vh')
   ];
  }

}