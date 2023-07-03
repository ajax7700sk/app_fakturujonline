export default class Toolbar {

    static init() {
        //
        $('.nav-link').on('click', function() {
            $('.nav-link').parent().find('.nav-link').removeClass('active');
            //
            $(this).addClass('active');
        });
    }

}
