'use strict';

const helpers = {
	/**
	* Init perfect scrollbar.
	*
	* @returns {void} void.
	* @since 2.0.0
	*/
	addonifyPerfectScrollBar: () => {

		if (PerfectScrollbar && typeof PerfectScrollbar === "function") {

			const wishlistSidebarScrollableEle = document.getElementById('addonify-wishlist-sidebar-form');

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

document.addEventListener("DOMContentLoaded", helpers.addonifyPerfectScrollBar());
