$(document).ready(function () {
    if(window.innerWidth < 1023){
        $(".page-top-filter__inner").removeClass("active");
        $(".aside.aside--catalog").removeClass("active");
        $(".c-g").removeClass("c-g--small");
        $(".aside__sidebar").hide();
        return;
    } else {
        var showCondition = Cookies.get("BITRIX_SM_SECTION_FILTER_CONDITION");

        if (showCondition == "SHOW") {
            $(".page-top-filter__inner").addClass("active");
            $(".aside.aside--catalog").addClass("active");
            //$(".c-g").addClass("c-g--small");
            $(".aside__sidebar").show();
        } else {
            $(".page-top-filter__inner").removeClass("active");
            $(".aside.aside--catalog").removeClass("active");
            $(".c-g").removeClass("c-g--small");
            $(".aside__sidebar").hide();
        }

        $(".page-top-filter__inner").click(function () {
            if ($(this).hasClass("active")) {
                Cookies.set("BITRIX_SM_SECTION_FILTER_CONDITION", "SHOW", {
                    expires: 1
                });
            } else {
                Cookies.set("BITRIX_SM_SECTION_FILTER_CONDITION", "HIDE", {
                    expires: 1
                });
            }
        });
    }
});