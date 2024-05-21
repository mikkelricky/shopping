// import { Modal, Tooltip } from 'bootstrap';

// // @see http://aerendir.me/2018/04/06/managin-static-images-webpack-encore/
// const imagesContext = require.context('./images', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/)
// imagesContext.keys().forEach(imagesContext)

require('./styles/app.scss')

const $ = require('jquery')
const bootstrap = require('bootstrap')

// https://getbootstrap.com/docs/5.1/components/tooltips/#example-enable-tooltips-everywhere
Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  .forEach(el => new bootstrap.Tooltip(el))

// console.debug(document.querySelectorAll('.modal'))
Array.from(document.querySelectorAll('.modal'))
  .forEach(el => new bootstrap.Modal(el))
