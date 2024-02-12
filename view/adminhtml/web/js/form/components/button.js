define([
 'Magento_Ui/js/form/components/button',
], function (Button) {
 'use strict';
 return Button.extend({
  show:function(){
   this.visible(true);
   return this;
  },
  hide:function(){
   this.visible(false);
   return this;
  }
 });
});