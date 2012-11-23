(function($){
    $(document).ready(setupPage);

    function setupPage() {
        $("#top-warning input").click(function() {
            $(this).parent().hide();
        });
        $(".source pre").hide();
        $(".source input").click(function() {
            $(".source pre").toggle();
        });
    }
})(jQuery);