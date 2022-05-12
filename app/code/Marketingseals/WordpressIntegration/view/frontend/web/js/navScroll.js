require([
    'jquery',
    'owlcarousel'
], function ($) {


    $('.ms-dropdown-menu').find('.ms-dropdown-toggle').click(function() {
        let content = $('.ms-dropdown-menu').find('.ms-dropdown-content');
        if(!content.hasClass('active')) {
            content.show();
            content.addClass('active');
            $(this).addClass('toggled');

           // $(this).find('.material-icons').html('close')
        } else {
            content.hide();
            content.removeClass('active');
            $(this).removeClass('toggled');
            //$(this).find('.material-icons').html('menu')
        }
    });

});
