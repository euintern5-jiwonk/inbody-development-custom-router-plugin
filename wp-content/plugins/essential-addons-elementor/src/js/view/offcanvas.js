var EaelOffcanvas = function ($scope, $) {
   new window.EAELOffcanvasContent($scope);
};

jQuery(window).on("elementor/frontend/init", function () {
   if (eael.elementStatusCheck("offcanvasLoad")) {
      return false;
   }

   jQuery('[data-widget_type="eael-offcanvas.default"]', document).each(
      function () {
         EaelOffcanvas(jQuery(this));
      }
   );
});
