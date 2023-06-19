export default class Toolbar {

    static init() {
        alert("Test");
        //
        $('.nav-link').on('click', function() {
            console.log($(this));
            console.log("Click");
            $('.nav-link').parent().find('.nav-link').removeClass('active');
            //
            $(this).addClass('active');
        });
    }

}
