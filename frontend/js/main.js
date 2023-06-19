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
    datagrid();
    // Toolbar
    toolbar();
});

function datagrid() {
    // Datagrid fix
    $(document.body).on('click', "[data-toggle='collapse']", function() {
        const target = $(this).attr('data-target');
        var $target = $(target);

        if($target.hasClass('show')) {
            $target.removeClass('show');
        } else {
            $target.addClass('show');
        }
    });
}

function toolbar() {
    $('.nav-link').on('click', function () {
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