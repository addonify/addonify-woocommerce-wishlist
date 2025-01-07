'use strict';

const helpers = {

    /**
    * Init perfect scrollbar.
    * 
    * @returns {void} void.
    * @since 2.0.0
    */

    addonifyPerfectScrollBar: () => {

        if (typeof PerfectScrollbar === 'function') {

            const wishlistSidebarScrollableEle = document.getElementById('adfy-wl-sidebar-form');

            if (wishlistSidebarScrollableEle) {

                new PerfectScrollbar(wishlistSidebarScrollableEle, {

                    wheelSpeed: 1,
                    wheelPropagation: true,
                    minScrollbarLength: 20
                });
            }
        }
    }
}

/**
* 'DOMContentLoaded' event.
* 
* @since 2.0.0
*/
document.addEventListener("DOMContentLoaded", helpers.addonifyPerfectScrollBar());
