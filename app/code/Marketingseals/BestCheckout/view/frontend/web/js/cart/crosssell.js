require([
    'jquery',
    'owlcarousel'
], function ($, owlcarousel) {
    let items = 5;
    var owl = $('.products-slider');
    owl.owlCarousel({
        items: items,
        itemsDesktop: [1199, 3],
        itemsDesktopSmall: [979, 3],
        addClassActive: true,
        /*afterAction: afterActionFunc*/
    });
    owl.parent().find('.progress-container').find('.prev').click(function () {
        owl.trigger('owl.prev');
    });
    owl.parent().find('.progress-container').find('.next').click(function () {
        owl.trigger('owl.next');
    });
    function afterActionFunc() {
        //get owl object
        var that = this;

        //get number of current slide (starting from 0)
        var current = that.currentItem;
        var amount = that.itemsAmount;
        var maxItems = that.maximumItem;
        var originalItems = that.orignalItems;
        if (current === 0) {
            let bar = originalItems / amount * 100;
            this.$elem.parent().find('.progress-container').find('.progress').css('width', bar + "%");
        } else {
            let bar = (originalItems + current) / amount * 100;
            this.$elem.parent().find('.progress-container').find('.progress').css('width', bar + "%");
        }
        //get jquery object of current slide
        //var $currentSlide = that.owlItems[current];

        //see console
        console.log(current);
    }

});