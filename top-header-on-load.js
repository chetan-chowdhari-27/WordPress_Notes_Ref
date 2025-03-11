jQuery(document).ready(function() {
    var headerHeight = jQuery('.header-section').outerHeight();
    console.log("Header height: " + headerHeight);
    jQuery('#before-content').css('margin-top', headerHeight + 'px');
});
