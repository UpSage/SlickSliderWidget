define([
 'jquery',
 'moment',
 'uiRegistry',
 'Magento_Ui/js/form/element/date',
], function ($, moment, registry, Date) {
 'use strict';
 return Date.extend({

  initialize: function() {
   this.renderDateCookie(this._super().initialValue);
   return this;
  },

  onUpdate: function (value) {
   this.renderDateCookie(value);
   return this._super();
  },

  renderDateCookie: function(value) {
   console.log(value);
  }
  
//   onUpdate: function (value) {
//    var $input = $(this)[0].inputName;
//    var $target = $input.replace('end_date', 'status');
//    var $status = registry.get(`inputName = ${$target}`);
//    var $today = moment().format('MM/D/YYYY');
//    var $selected = moment(value).format('MM/D/YYYY');

//    if($today >= $selected) {
//     $status.checked(false);
//    } else {
//     $status.checked(true);
//    }
//    console.log(`Today is ${$today} and Selected is ${$selected}`);
//    return this._super();
//   }

 });
});