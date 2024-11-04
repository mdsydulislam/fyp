// /*!
//     * Start Bootstrap - SB Admin v6.0.1 (https://startbootstrap.com/templates/sb-admin)
//     * Copyright 2013-2020 Start Bootstrap
//     * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
//     */
//     (function($) {
//     "use strict";
//     // Add active state to sidbar nav links
//     var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
//         $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
//             if (this.href === path) {
//                 $(this).addClass("active");
//             }
//         });
//     // Toggle the side navigation
//     $("#sidebarToggle").on("click", function(e) {
//         e.preventDefault();
//         $("body").toggleClass("sb-sidenav-toggled");
//     });
// })(jQuery);
// document.addEventListener('DOMContentLoaded', function () {
//     const sidebarToggle = document.getElementById('sidebarToggle');
//     if (sidebarToggle) {
//         sidebarToggle.addEventListener('click', function (event) {
//             event.preventDefault();
//             document.body.classList.toggle('sb-sidenav-toggled');
//         });
//     }
// });
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function (event) {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
        });
    }
    // Add active state to sidebar nav links
    const path = window.location.href;
    document.querySelectorAll("#layoutSidenav_nav .sb-sidenav a.nav-link").forEach(link => {
        if (link.href === path) {
            link.classList.add("active");
        }
    });
});