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
  datagrid(); // Toolbar

  toolbar();
});

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
}
//# sourceMappingURL=main.97f5ab1c3a.js.map
