define([
    'jquery',
    'owlcarousel'
], function ($, owlcarousel) {
    return function(config, element) {
        let items = config.items;
        var owl = $('#' + config.sliderId);
        owl.owlCarousel({
            lazyLoad : true,
            responsive: true,
            items: items,
            itemsDesktop: [1280, 4],
            itemsDesktopSmall: [979, 3],
            itemsMobile: [580, 2],
            addClassActive: true,
            afterAction: afterActionFunc,
            afterMove: afterMoveFunc
        });
        owl.parent().find('.progress-container').find('.prev').click(function () {
            owl.trigger('owl.prev');
        });
        owl.parent().find('.progress-container').find('.next').click(function () {
            owl.trigger('owl.next');
        });
        function afterMoveFunc() {
            let that = owl.data('owlCarousel');
            //let width = $(this).parent().find('.progress').css('width').replace(/[^-\d\.]/g, '');
            var current = that.currentItem;
            var amount = that.itemsAmount;
            let bar = 0;
            amount = amount - 4;
            if (current !== 0) {
                bar = current / amount * 100;
            } else if(current === that.maximumItem) {
                bar = 0;
            } else if(current >= that.maximumItem - 4) {
                bar = (that.maximumItem - 4) / amount * 100;
            }
            this.$elem.parent().find('.progress').css('margin-left', bar + '%');
        }
        function afterActionFunc() {
            //get owl object
            var that = this;

            //get number of current slide (starting from 0)
            var current = that.currentItem;
            var amount = that.itemsAmount;
            let bar = 0;
            amount = amount - 4;
            if (current === 0) {
                bar = 1/ amount * 100;
                this.$elem.parent().parent().find('.progress-container').find('.progress').css('width', bar + "%");
            }

            //console.log(current);
        }
    }

});

