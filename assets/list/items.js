// @see http://aerendir.me/2018/04/06/managin-static-images-webpack-encore/
import $ from "jquery";
import "corejs-typeahead";

const imagesContext = require.context(
  "../images",
  true,
  /\.(png|jpg|jpeg|gif|ico|svg|webp)$/,
);
imagesContext.keys().forEach(imagesContext);

require("../styles/list/items.scss");

// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
const bootstrap = require("bootstrap");

$(() => {
  // https://getbootstrap.com/docs/5.1/components/tooltips/#example-enable-tooltips-everywhere
  [].slice
    .call(document.querySelectorAll('article [data-bs-toggle="tooltip"]'))
    .map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl, {
        boundary: tooltipTriggerEl.parentNode,
      });
    });

  // @see \App\Service\ShoppingListItemManager\parseName
  const parseName = (name) => {
    const tokens = name.split(/\s+/, 3);

    let quantity =
      tokens.length > 1 && /^(\d*[,.])?\d+$/.test(tokens[0])
        ? tokens.shift()
        : null;

    const units = [
      "l",
      "liter",
      "litre",
      "litres",
      "kg",
      "kilo",
      "kilos",
      "g",
      "gram",
      "grams",
    ];
    if (
      quantity !== null &&
      tokens.length > 1 &&
      units.includes(tokens[0].toLowerCase())
    ) {
      quantity += " " + tokens.shift();
    }

    name = tokens.join(" ").trim();

    return [name, quantity];
  };

  const el = document.getElementById("shopping_list_create_item_name");
  if (el) {
    try {
      const items = JSON.parse(
        document.querySelector(el.dataset.typeaheadDatasetSelector).innerHTML,
      );

      // https://typeahead.js.org/examples/
      const substringMatcher = (strs) => {
        return function findMatches(q, cb) {
          // an array that will be populated with substring matches

          const [name, quantity] = parseName(q);

          cb(
            strs
              .filter((str) => str.toLowerCase().includes(name.toLowerCase()))
              .map((name) => (quantity ? quantity + " " + name : name)),
          );
        };
      };

      $(el)
        .attr("autocomplete", "off")
        .typeahead(
          {
            hint: true,
            highlight: true,
            minLength: 1,
          },
          {
            name: "items",
            source: substringMatcher(items),
            limit: 1000,
          },
        );
    } catch (ex) {
      // console.log(ex)
    }
  }

  $('form[name="shopping_list_create_item"]').on("submit", function (event) {
    $(this)
      .find('[type="submit"]')
      .prop("disabled", true)
      .html(
        "Adding " +
          $(this).find('[name="shopping_list_create_item[name]"]').val() +
          " …",
      );
  });
});
