"use strict";

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// import Datagird from "./components/Datagrid";
// import Toolbar from "./components/Toolbar";
document.addEventListener('DOMContentLoaded', function (event) {
  //
  bootstrap(); //

  taxDocument();
});
/**
 * Bootstrap functions
 */

function bootstrap() {
  function datagrid() {
    // Datagrid fix
    $(document.body).on('click', "[data-toggle='collapse']", function () {
      var target = $(this).attr('data-target');
      var $target = $(target);

      if ($target.hasClass('show')) {
        $target.removeClass('show');
      } else {
        $target.addClass('show');
      }
    });
  }

  function toolbar() {
    $('.nav-link').on('click', function () {
      $('.nav-link').parent().find('.nav-link').removeClass('active'); //

      $(this).addClass('active'); //

      var target = $(this).attr('data-bs-target'); //

      $('.tab-pane').removeClass('.show').removeClass('active'); //

      $('.tab-content').find(target).addClass('show').addClass('active');
    });
  } // Init


  datagrid();
  toolbar();
} // Tax document


function taxDocument() {
  function addItem() {
    // Copy template
    var template = $('#tax-document-item-template').find('tbody').html(); // Replace index

    var index = $('.js-tax-document-rows').find('tr').length;
    template = template.replace(/_index_/g, index); //

    $('.js-tax-document-rows').append(template);
  } // --- Events


  $(document.body).on('click', '.js-add-item', function () {
    addItem();
  });
  $(document.body).on('click', '.js-remove-item', function (e) {
    var $btn = $(e.target);
    var $tr = $btn.parents('tr'); //
    // Can delete?

    if ($('.js-tax-document-rows').find('tr').length > 1) {
      $tr.remove();
    }
  });
}
//# sourceMappingURL=main.7de27a68ad.js.map
