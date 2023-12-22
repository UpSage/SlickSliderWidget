define([
 'Magento_Ui/js/dynamic-rows/dynamic-rows',
], function (Dynamicrows) {
 'use strict';
 return Dynamicrows.extend({
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