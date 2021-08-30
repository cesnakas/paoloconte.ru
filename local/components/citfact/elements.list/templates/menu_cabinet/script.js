$(document).ready(function() {

    $('[data-target]').click(function(e){
        var target = $(this).attr("data-target");
        switch (target){
            case "#priceModal":
            case "#sizeModal":
            case "#cityModal":
            case "#cityModalInVacancy":
            case "#fastViewModal":
            case "#editAddressModal":
            case "#callbackModal":
            case "#reviewModal":
            case "#enterModal":
            case "#toFavoriteModal":
            case "#chooseSizeModal":
            case "#offerInbasketModal":
            case "#productReviewModal":
            case "#franchiseModal":
                return;
            default: break;
        }
        var linkUrl = $(this).parent("a").attr("href");
        e.preventDefault;
        location.replace(linkUrl);
        return false;
    });


    $(document).on('click','.aside-nav__link.active', function (e) {
        if(window.innerWidth < 768){
            $('.aside-nav__link:not(.active)').toggle();
        }
        return false;
    });
    
});