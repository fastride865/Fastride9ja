(function (global, factory) {
  if (typeof define === "function" && define.amd) {
    define("/config/tour", ["Config"], factory);
  } else if (typeof exports !== "undefined") {
    factory(require("Config"));
  } else {
    var mod = {
      exports: {}
    };
    factory(global.Config);
    global.configTour = mod.exports;
  }
})(this, function (_Config) {
  "use strict";

  (0, _Config.set)('tour', {
    steps: [{
      element: '#general-title',
      position: 'right',
      intro: 'General <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    },{
      element: '#booking-title',
      intro: 'Booking <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    },{
      element: '#driver-title',
      intro: 'Driver <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    },{
      element: '#rider-title',
      intro: 'Rider <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    },{
      element: '#other-title',
      intro: 'Others <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    },{
      element: '#settings-title',
      intro: 'Settings <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    },{
      element: '#sidebarfooter-title',
      intro: 'Site Footer <p class=\'content\'>Click this button you can view the admin template in full screen</p>'
    },{
      element: '#toggleMenubar',
      intro: 'Offcanvas Menu <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    }, {
      element: '#toggleFullscreen',
      intro: 'Full Screen <p class=\'content\'>Click this button you can view the admin template in full screen</p>'
    },{
      element: '#profile',
      position: 'left',
      intro: 'Profile Menu <p class=\'content\'>It is nice custom navigation for desktop users and a seek off-canvas menu for tablet and mobile users</p>'
    }],

    skipLabel: '<i class=\'wb-close\'></i>',
    doneLabel: '<i class=\'wb-close\'></i>',
    nextLabel: 'Next <i class=\'wb-chevron-right-mini\'></i>',
    prevLabel: '<i class=\'wb-chevron-left-mini\'></i>Prev',
    showBullets: false
  });
});