export default class Datagird {

    static init() {
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
}
