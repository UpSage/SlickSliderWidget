define([
 'Magento_Ui/js/form/components/group',
], function (Group) {
 'use strict';
 return Group.extend({
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