var config = {
 mixins: {
  'Magento_Ui/js/form/element/image-uploader': {
    'UpSage_SlickSliderWidget/js/form/element/image-uploader': true
  }
 },
 map: {
  '*': {
    'ui/template/form/element/uploader/image.html' : 'UpSage_SlickSliderWidget/templates/form/element/uploader/image.html'
   }
  }
};