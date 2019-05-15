var brand = $('.navbar-brand');
var brandLogo = $('#brandLogo');
var sidebar = $('#sidebar');
var sidebarText = $('.sidebar-text');
var sidebarToggle = $('#sidebarToggle');
var sidebarUserItem = $('.nav-user-item');
var sidebarUser = $('#sidebarUserInfo');
var main = $('#mainContent');
var minimized = true;
var transitionDuration = 100;

const toggleSidebar = function() {
  if (sidebar.hasClass('col-md-2')) {
    closeSidebar(true);
  } else {
    openSidebar(true);
  }
};

const openSidebarIfClosed = function() {
  if (minimized) {
    openSidebar();
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
    brand.addClass('col-md-2',transitionDuration).addClass('col-sm-3');
    sidebar.addClass('col-md-2',transitionDuration);
    
    sidebarToggle.removeClass('fa-chevron-right').addClass('fa-chevron-left').show();
    sidebarUserItem.removeClass('min').addClass('max');
    sidebarUser.removeClass('sidebar-user-min').addClass('sidebar-user-max');
    sidebarText.show().delay(transitionDuration);
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
    sidebarUserItem.removeClass('max').addClass('min');
    sidebarUser.removeClass('sidebar-user-max').addClass('sidebar-user-min');
  }
  minimized = true;
}

