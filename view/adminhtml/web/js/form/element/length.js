define([
 'jquery',
 'uiRegistry',
 'Magento_Ui/js/form/element/abstract',
], function ($, registry, Abstract) {
 'use strict';
 return Abstract.extend({
  
  onUpdate: function (value) {
   var $length = $(this)[0].inputName;
   var $unit = $length.replace('length', 'unit');
   var $nextField = registry.get(`inputName = ${$unit}`);
   if(value) {
    $nextField.show();
   }
   return this._super();
  }

 });
});