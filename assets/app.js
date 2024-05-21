// @see http://aerendir.me/2018/04/06/managin-static-images-webpack-encore/
const imagesContext = require.context('./images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/)
imagesContext.keys().forEach(imagesContext)

require('./styles/app.scss')

const $ = require('jquery')

// https://getbootstrap.com/docs/5.1/components/tooltips/#example-enable-tooltips-everywhere
const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  .map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
