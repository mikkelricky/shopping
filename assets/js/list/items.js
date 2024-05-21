// @see http://aerendir.me/2018/04/06/managin-static-images-webpack-encore/
const imagesContext = require.context('../../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/)
imagesContext.keys().forEach(imagesContext)

require('../../css/list/items.scss')

const $ = require('jquery')

// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap')


$(() => {
  $('[data-toggle="tooltip"]').tooltip({ container: 'article' })

  $('form').on('submit', function() {
    $('#add-item')
      .prop('disabled', true)
      .html('Adding '+$('input', this).first().val()+' …')
  })
})
