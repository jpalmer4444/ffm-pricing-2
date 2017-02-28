(function(jQuery) {
    
  jQuery.fn.animateGreenHighlight = function (highlightColor, duration) {
        var highlightBg = highlightColor || "#68e372";
        var animateMs = duration || "1000"; // edit is here
        var originalBg = this.css("background-color");

        if (!originalBg || originalBg == highlightBg)
            originalBg = "#FFFFFF"; // default to white

        jQuery(this)
            .css("backgroundColor", highlightBg)
            .animate({ backgroundColor: originalBg }, animateMs, null, function () {
                jQuery(this).css("backgroundColor", originalBg);
            });
    };

    

})(jQuery);


