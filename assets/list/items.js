// @see http://aerendir.me/2018/04/06/managin-static-images-webpack-encore/
import $ from 'jquery'
import 'corejs-typeahead'

const imagesContext = require.context('../images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/)
imagesContext.keys().forEach(imagesContext)

require('../styles/list/items.scss')

// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap')

$(() => {
  $('[data-toggle="tooltip"]').tooltip({ container: 'article' })

  const el = document.getElementById('shopping_list_create_item_name')
  if (el) {
    try {
      const items = JSON.parse(document.querySelector(el.dataset.typeaheadDatasetSelector).innerHTML)

      // https://typeahead.js.org/examples/
      const substringMatcher = (strs) => {
        return function findMatches (q, cb) {
          // an array that will be populated with substring matches
          const matches = []

          // regex used to determine if a string contains the substring `q`
          const substrRegex = new RegExp(q, 'i')

          // iterate through the pool of strings and for any string that
          // contains the substring `q`, add it to the `matches` array
          $.each(strs, (i, str) => {
            if (substrRegex.test(str)) {
              matches.push(str)
            }
          })

          cb(matches)
        }
      }

      $(el)
        .attr('autocomplete', 'off')
        .typeahead({
          hint: true,
          highlight: true,
          minLength: 1
        },
        {
          name: 'items',
          source: substringMatcher(items),
          limit: 1000
        })
    } catch (ex) {
      // console.log(ex)
    }
  }

  $('form').on('submit', function () {
    $('#add-item')
      .prop('disabled', true)
      .html('Adding ' + $('input', this).first().val() + ' …')
  })
})
