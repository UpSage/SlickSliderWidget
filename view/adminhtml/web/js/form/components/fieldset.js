define([
 'Magento_Ui/js/form/components/fieldset',
], function (Fieldset) {
 'use strict';
 return Fieldset.extend({
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