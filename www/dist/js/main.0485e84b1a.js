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
  bootstrapInit(); //

  taxDocument();
});
/**
 * Bootstrap functions
 */

function bootstrapInit() {
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
  }

  function recalculateLineItemsTotals() {
    $('.js-tax-document-rows tr').each(function (item) {
      var $tr = $(this); //

      var quantity = parseFloat($tr.find('.js-quantity').val());
      var unitPriceTaxExcl = parseFloat($tr.find('.js-unit-price-tax-excl').val());
      var taxRate = parseFloat($tr.find('.js-tax-rate').val()); //

      quantity = isNaN(quantity) ? 0 : quantity;
      unitPriceTaxExcl = isNaN(unitPriceTaxExcl) ? 0 : unitPriceTaxExcl;
      taxRate = isNaN(taxRate) ? 0 : taxRate; //

      var unitPriceTaxIncl = unitPriceTaxExcl * (1 + taxRate / 100);
      var totalPriceTaxExcl = unitPriceTaxExcl * quantity;
      var totalPriceTaxIncl = unitPriceTaxIncl * quantity; //

      $tr.find('.js-item-total').val(totalPriceTaxIncl);
    });
  }

  function recalculateTotals() {
    // Line Items
    recalculateLineItemsTotals(); // Recalculate totals
  } // ----------------------------------- Events ------------------------------------- \\
  // Add item


  $(document.body).on('click', '.js-add-item', function () {
    addItem(); //

    recalculateTotals();
  }); // Remove item

  $(document.body).on('click', '.js-remove-item', function (e) {
    var $btn = $(e.target);
    var $tr = $btn.parents('tr'); //
    // Can delete?

    $tr.remove();
  }); // Change quantity

  $(document.body).on('change', '.js-quantity', function (e) {
    recalculateTotals();
  }); // Change tax rate

  $(document.body).on('change', '.js-tax-rate', function (e) {
    recalculateTotals();
  }); // Load company data

  $(document.body).on('change', '#frm-taxDocumentForm-form-userCompany', function (e) {
    var id = e.target.value;
    var url = $(this).parent('.js-load-company-data').attr('data-link'); //

    $.ajax({
      url: url,
      data: {
        id: id
      },
      method: 'POST',
      dataType: 'json',
      complete: function complete(xhr) {
        var data = xhr.responseJSON; //

        for (var key in data) {
          var value = data[key]; // Set value

          $("input[name=" + key + "]").val(value);
        }
      }
    });
  });
}
//# sourceMappingURL=main.0485e84b1a.js.map
