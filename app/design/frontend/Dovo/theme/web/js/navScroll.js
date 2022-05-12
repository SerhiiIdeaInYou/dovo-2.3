require([
    'jquery',
    'owlcarousel'
], function ($) {

    // var docEl = $(document);
    // console.log(docEl);
    // var stylesSiteHeaderDefault = {
    //     maxHeight: '110px',
    // };
    // var stylesMiniCartDefault = {
    //     top: '40px',
    // };
    // var stylesInsideHeaderDefault = {
    //     padding: '10px',
    // };
    // var stylesSiteHeader = {
    //     maxHeight: '90px',
    // };
    // var stylesMiniCart = {
    //     top: '30px',
    // };
    // var stylesInsideHeader = {
    //     padding: '0 10px',
    // };
    // docEl.on('scroll', function () {
    //     let currentTop = docEl.scrollTop();
    //     console.log($('body').hasClass('checkout-index-index'));
    //     if (!$('body').hasClass('checkout-index-index')) {
    //         if (currentTop >= 400) {
    //             $('.header-primary-container').css(stylesSiteHeader);
    //             $('.page-header').css(stylesSiteHeader);
    //             $('.header-primary').css(stylesInsideHeader);
    //             $('.ds-minicart').css(stylesMiniCart);
    //         } else if (currentTop <= 120) {
    //             $('.page-header').css(stylesSiteHeaderDefault);
    //             $('.header-primary-container').css(stylesSiteHeaderDefault);
    //             $('.header-primary').css(stylesInsideHeaderDefault);
    //             $('.ds-minicart').css(stylesMiniCartDefault);
    //         }
    //     }
    // });
    $('#menu-item-351 a').click(function() {
        $(this).attr('href','javascript:$zopim.livechat.window.show();')
    });
    $('#menu-item-1901 a').click(function() {
        $(this).attr('href','javascript:$zopim.livechat.window.show();')
    });
   
    $('.tochat').click(function() {
        console.log($('iframe#launcher'));
    })
    $('iframe#launcher').load(function () {
        $(this).contents().on("mousedown, mouseup, click", function () {
            alert("Click detected inside iframe.");
        });
    });
});
