var brand = $('.navbar-brand');
var brandLogo = $('#brandLogo');
var sidebar = $('#sidebar');
var sidebarText = $('.sidebar-text');
var sidebarToggle = $('#sidebarToggle');
var main = $('#mainContent');
var minimized = true;
var transitionDuration = 0;

const toggleSidebar = function() {
  if (sidebar.hasClass('col-md-2')) {
    closeSidebar(true);
    // brand.mouseenter( openSidebar );
  } else {
    openSidebar(true);
  }
};

function openSidebar (forceOpen) {
  if (minimized || forceOpen) {
    // brand.stop(true).animate({"max-width": "16.66667%", "padding-right": "6.5em"}, 100);
    // brandLogo.stop(true).animate({"margin-left": "0"}, 100);
    // sidebar.stop(true).animate({"max-width": "16.66667%"}, 100);
    if (brand.hasClass('brand-minimized')) {
      brand.removeClass('brand-minimized');
    }
    if (sidebar.hasClass('sidebar-minimized')) {
      sidebar.removeClass('sidebar-minimized');
    }
    brand.addClass('col-md-2').addClass('col-sm-3');
    sidebar.addClass('col-md-2');
    sidebarText.show();
    sidebarToggle.removeClass('fa-chevron-right').addClass('fa-chevron-left');
  }
  minimized = false;
}

function closeSidebar (forceClose) {
  if (!minimized || forceClose) {
    // brand.stop(true).animate({"max-width": "5%", "padding-right": "3.0em"}, 100);
    if (!brand.hasClass('brand-minimized')) {
      brand.addClass('brand-minimized');
    }
    if (!sidebar.hasClass('sidebar-minimized')) {
      sidebar.addClass('sidebar-minimized');
    }
    brand.removeClass('col-md-2').removeClass('col-sm-3');
    sidebar.removeClass('col-md-2');
    sidebarText.hide();
    sidebarToggle.removeClass('fa-chevron-left').addClass('fa-chevron-right');
  }
  minimized = true;
}

