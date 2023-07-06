/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// import Datagird from "./components/Datagrid";
// import Toolbar from "./components/Toolbar";

document.addEventListener('DOMContentLoaded', (event) => {
    //
    bootstrapInit()
    taxDocument();
    subscription();
    flashes();
    contacts();
    validations();
});


/**
 * Bootstrap functions
 */
function bootstrapInit() {
    function datagrid() {
        // Datagrid fix
        $(document.body).on('click', "[data-toggle='collapse']", function (e) {
            var $this = $(e.target);
            const target = $this.attr('data-target');
            var $target = $(target);

            if ($target.hasClass('show')) {
                $target.removeClass('show');
            } else {
                $target.addClass('show');
            }
        });
    }

    function toolbar() {
        $(document.body).on('click', '.nav-link', function () {
            $('.nav-link').parent().find('.nav-link').removeClass('active');
            //
            $(this).addClass('active');
            //
            const target = $(this).attr('data-bs-target');
            //
            $('.tab-pane').removeClass('.show').removeClass('active');
            //
            $('.tab-content')
                .find(target)
                .addClass('show')
                .addClass('active');
        });
    }

    function modal() {
        $(document.body).on('click', ["data-toggle='toggle'"], function (e) {
            // var $this = $(e.target);
            // var $target = $($this.attr('data-target'));
            // //
            // // has id?
            // if($this.attr('data-id')) {
            //     $target.find('form .js-data-id').val($this.attr('data-id'));
            // }
            // //
            // $target.modal('show');
        })

        $(document.body).on('click', '.js-modal-close', function (e) {
            $('.modal').modal('hide');
        })
    }

    // Init
    datagrid();
    toolbar();
    modal();
    darkMode();
}

// Tax document
function taxDocument() {
    function addItem() {
        // Copy template
        let template = $('#tax-document-item-template').find('tbody').html();
        // Replace index
        let index = $('.js-tax-document-rows').find('tr').length;
        template = template.replace(/_index_/g, index);
        //
        $('.js-tax-document-rows').append(template);
    }

    function recalculateLineItemsTotals() {
        $('.js-tax-document-rows tr').each(function (item) {
            var $tr = $(this);
            //
            var quantity = parseFloat($tr.find('.js-quantity').val());
            var unitPriceTaxExcl = parseFloat($tr.find('.js-unit-price-tax-excl').val());
            var taxRate = parseFloat($tr.find('.js-tax-rate').val());
            //
            quantity = isNaN(quantity) ? 0 : quantity;
            unitPriceTaxExcl = isNaN(unitPriceTaxExcl) ? 0 : unitPriceTaxExcl;
            taxRate = isNaN(taxRate) ? 0 : taxRate;
            //
            var unitPriceTaxIncl = unitPriceTaxExcl * (1 + (taxRate / 100));
            var totalPriceTaxExcl = unitPriceTaxExcl * quantity;
            var totalPriceTaxIncl = unitPriceTaxIncl * quantity;
            //
            $tr.find('.js-item-total').val(totalPriceTaxIncl);
        });
    }

    function recalculateTotals() {
        // Line Items
        recalculateLineItemsTotals();
        // Recalculate totals
    }

    function exportPdf(url) {
        var $filter = $('#frm-taxDocumentGrid-filter');
        var checkboxes = $filter.find("[data-check='taxDocumentGrid']:checked");
        var ids = [];

        //
        checkboxes.each(function (i, item) {
            var name = $(item).attr('name');
            ids.push(parseFloat(name.replace('taxdocumentgrid_group_action_item[', '')));
        });

        //
        $('#export-pdf-form').find('.js-id').val(ids);
        $('#export-pdf-form').submit();
    }

    // ----------------------------------- Events ------------------------------------- \\

    // Add item
    $(document.body).on('click', '.js-add-item', function () {
        addItem();
        //
        recalculateTotals();
    })

    // Remove item
    $(document.body).on('click', '.js-remove-item', function (e) {
        var $btn = $(e.target);
        var $tr = $btn.parents('tr');
        //
        // Can delete?
        $tr.remove();
    });

    // Change quantity
    $(document.body).on('change', '.js-quantity', function (e) {
        recalculateTotals();
    })

    // Change tax rate
    $(document.body).on('change', '.js-tax-rate', function (e) {
        recalculateTotals();
    })

    $(document.body).on('change', '.js-unit-price-tax-excl', function (e) {
        recalculateTotals();
    })

    // Load company data
    $(document.body).on('change', '#frm-taxDocumentForm-form-userCompany', function (e) {
        var id = e.target.value;
        var url = $(this).parent('.js-load-company-data').attr('data-link');
        //
        $.ajax({
            url: url,
            data: {
                id: id
            },
            method: 'POST',
            dataType: 'json',
            complete: function (xhr) {
                var data = xhr.responseJSON;
                //
                for (var key in data) {
                    var value = data[key];
                    // Set value
                    $("input[name=" + key + "]").val(value);
                }
            }
        })
    });

    // Export PDF
    $(document.body).on('click', '.js-export-pdf', function (e) {
        var $target = $(e.target);
        var url = $target.attr('data-href');
        //
        exportPdf(url);
    })
}

function subscription() {
    // Load company data
    $(document.body).on('change', '#frm-checkoutForm-form-userCompany', function (e) {
        var id = e.target.value;
        var url = $(this).parent('.js-load-company-data').attr('data-link');
        //
        $.ajax({
            url: url,
            data: {
                id: id
            },
            method: 'POST',
            dataType: 'json',
            complete: function (xhr) {
                var data = xhr.responseJSON;
                //
                for (var key in data) {
                    var value = data[key];
                    // Set value
                    $("input[name=" + key + "]").val(value);
                }
            }
        })
    });
}

function darkMode() {
    $(document.body).on('click', '.js-dark-mode', function (e) {
        var $body = $(document.body);

        if ($body.attr('theme')) {
            // Light mode
            $body.removeAttr('theme');
            //
        } else {
            // Dark mode
            $body.attr('theme', 'dark');
            //
            $('.js-dark-mode')
        }
    });
}

function flashes() {
    // Timer
    $('.alert').delay(5000).fadeOut('slow');

    // Events
    $(document.body).on('click', '.js-close-alert', function (e) {
        var $this = $(e.target);
        //
        $('.alert').hide();
    })
}

function contacts() {
    function form() {
        $(document.body).on('change', function(e) {
            var $this = (e.target);

            if($this.checked) {
                // Show shipping
                $('.js-shipping-address').hide();
            } else {
                $('.js-shipping-address').show();
            }
        })
    }

    form();
}

function validations() {
    /**
     * Validate all fields
     */
    function validateFields($form) {
        var fields = $form.find('input, textarea, select');
        //
        fields.each(function(index, element) {
            validateField($(element));
        });
    }

    /**
     * Validate specific field
     * @param {jQuery} $input
     */
    function validateField($input) {
        var rules = $input.attr('data-nette-rules');

        if(rules) {
            rules = JSON.parse(rules);
            //
            validateFieldRules($input, rules);
        }
    }

    /**
     * Validate all field rules
     * @param $input
     * @param rules
     */
    function validateFieldRules($input, rules) {
        var value = $input.val();

        rules.every((rule) => {
            // // On condition rule?
            // if(rule.hasOwnProperty('rules')) {
            //     var conditionalControl = document.querySelector('input[name="'+rule.control+'"]');
            //     var conditionalControlValue = null;
            //
            //     // --- Filter condintional control by its type
            //     switch (conditionalControl.type) {
            //         case 'radio':
            //             conditionalControl = document.querySelector('input[name="'+rule.control+'"]:checked');
            //             break;
            //     }
            //
            //     // Get control value
            //     conditionalControlValue = this._filterControlValue(conditionalControl);
            //
            //     // Checkbox
            //     if(rule.op === ':equal') {
            //         // Checkbox has to be checked to run validation
            //         // Checkbox has to be unchecked to run validation
            //         if((rule.arg && conditionalControlValue) || (!rule.arg && !conditionalControlValue)) {
            //             return this._validateRules(rule.rules, value, input);
            //         }
            //     }
            //
            //     return false; // Break loop
            // }

            // Optional validate
            if(rule.op === 'optional' && value === '') {
                // Break loop
                return false;
            }

            // Throw error
            if (!validateByType(value, rule.op)) {
                showFieldError($input, rule.msg);
                //
                // throw new Error(rule.msg);
                return true;
            }

            // Valid
            showFieldSuccess($input);

            // Next loop
            return true;
        });
    }

    /**
     * Show field error
     * @param {jQuery} $input
     * @param msg
     */
    function showFieldError($input, msg) {
        $input.addClass('is-invalid');
        $input.parent().find('.error-msg').text(msg);
    }

    function showFieldSuccess($input) {
        hideFieldError($input);
        //
        $input.addClass('is-valid');
    }

    function hideFieldError($input) {
        $input.removeClass('is-invalid');
        $input.parent().find('.error-msg').text('');
    }


    /**
     * @param {string} value
     * @param {string} op
     * @private
     */
    function validateByType(value, op) {

        // Validate by type
        switch (op) {

            case ':filled':
                return validatorIsFilled(value);
            case ':email':
                return validatorIsEmail(value);
            // case 'App\\Validator\\PhoneNumberValidator::validate':
            //     return Validator.phone(value) !== false;
            //
            // case 'App\\Validator\\ZipValidator::validate':
            //     return Validator.zip(value) !== false;
            //
            // case 'App\\Validator\\CompanyInValidator::validate':
            //     return Validator.companyId(value) !== false;
            //
            // case 'App\\Validator\\TaxIdValidator::validate':
            //     return Validator.taxId(value) !== false;

            default:
                return true;
        }
    }

    // --- Validators

    function validatorIsFilled(value) {
        if(value.length == 0) {
            return false;
        }

        return true;
    }

    function validatorIsEmail(value) {
        return validator.isEmail(value);
    }

    // ------------------------------------------ Events -------------------------------------------- \\

    // Submit
    $('form').on('submit', function(e) {
        // Validate all fields
        e.preventDefault();
        //
        validateFields($(this));
    })

    $(document.body).on('focusout', 'input', function(e) {
        var $target = $(e.target);
        //
        validateField($target);
    })
}