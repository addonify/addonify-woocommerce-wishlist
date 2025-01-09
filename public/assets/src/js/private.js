(function ($) {

	'use strict';

	$(document).ready(function () {

		const body = $('body'),
			wishlistSidebarEle = $('#addonify-wishlist-sticky-sidebar-container'),
			wishlistTableEle = $('#addonify-wishlist-table');

		const {
			enabledMultiWishlist,
			ajaxURL,
			nonce,
			addToWishlistAction,
			removeFromWishlistAction,
			emptyWishlistAction,
			removeFromWishlistAfterAddedToCart,
			removeAlreadyAddedProductFromWishlist,

			successModalClasses,
			alertModalClasses,
			errorModalClasses,

			addedToWishlistModalIcon,
			removedFromWishlistModalIcon,
			successModalIcon,
			alertModalIcon,
			errorModalIcon,

			addedToWishlistModalMessage,
			alreadyInWishlistModalMessage,
			productRemovedFormWishlistModalMessage,
			wishlistEmptyingConfirmationModalMessage,

			wishlistLinkModalButton,
			emptyWishlistConfirmModalButton
		} = addonifyWishlistJSObject;

		let productID = 0,
			productName = '',
			currentAddToWishlistButton = '',
			currentRemoveFromWishlistButton = '',
			currentModalIcon = '',
			currentModalMessage = '',
			currentModalButton = '',
			currentModalContainerClasses = '';

		const addonifyWishlistInitArgs = {
			loader: addonifyWishlistJSObject.loader,
			wishlistPageURL: addonifyWishlistJSObject.wishlistPageURL,
			saveForLaterButtonLabel: addonifyWishlistJSObject.saveForLaterButtonLabel,
			savedForLaterButtonLabel: addonifyWishlistJSObject.savedForLaterButtonLabel,
			initialAddToWishlistButtonLabel: addonifyWishlistJSObject.initialAddToWishlistButtonLabel,
			addedToWishlistButtonLabel: addonifyWishlistJSObject.addedToWishlistButtonLabel,
			alreadyInWishlistButtonLabel: addonifyWishlistJSObject.alreadyInWishlistButtonLabel,
			addToWishlistButtonIcon: addonifyWishlistJSObject.addToWishlistButtonIcon,
			addedToWishlistButtonIcon: addonifyWishlistJSObject.addedToWishlistButtonIcon,
			loadingWishlistButtonIcon: addonifyWishlistJSObject.loadingWishlistButtonIcon,
			undoNoticeTimeout: addonifyWishlistJSObject.undoNoticeTimeout,
			addedToWishlistModal: addonifyWishlistJSObject.addedToWishlistModal,
			removedFromWishlistModal: addonifyWishlistJSObject.removedFromWishlistModal,
			afterAddToWishlistAction: addonifyWishlistJSObject.afterAddToWishlistAction,
			productRemovalUndoNotice: addonifyWishlistJSObject.productRemovalUndoNotice,
			modalTemplate: addonifyWishlistJSObject.modalTemplate,
		};

		const addonifyWishlistInit = body.addonifyWishlistInit(addonifyWishlistInitArgs);

		addonifyWishlistInit.init();

		/**
		 * Handles DOM events.
		 *
		 * @since 2.0.6
		 */
		const addonifyWishlist = {
			init: function () {
				this.wishlistButtonEventHandler();
				this.undoEventsHandler();
				this.addedToCartEventHandler();
			},
			// Handles DOM events related to adding product into the wishlist and removing product from the wishlist.
			wishlistButtonEventHandler: function () {
				if (enabledMultiWishlist !== '1') {

					/**
					 * Check if product is already in the cart.
					 * If not call AJAX function to add the product into the cart.
					 */
					body.on('click', '.addonify-wishlist-ajax-add-to-wishlist, .addonify-add-to-wishlist-btn', function (e) {
						e.preventDefault();

						currentAddToWishlistButton = jQuery(this);

						// Set `loading` CSS class.
						currentAddToWishlistButton.addClass('loading');

						addonifyWishlistSetProductIDName(currentAddToWishlistButton);

						if (currentAddToWishlistButton.hasClass("added-to-wishlist")) {

							// Remove product from wishlist when clicked on the added to wishlist button.
							if (removeAlreadyAddedProductFromWishlist === '1') {
								currentRemoveFromWishlistButton = currentAddToWishlistButton;
								addonifyRemoveFromWishlist();
							} else {

								addonifyWishlistDispalyModal(
									successModalClasses,
									addedToWishlistModalIcon,
									alreadyInWishlistModalMessage,
									wishlistLinkModalButton
								);
							}
						} else {
							// Call function to add product into wishlist.
							addonifyAddToWishlist();
						}
						// Remove `loading` CSS class.
						currentAddToWishlistButton.removeClass('loading');
					});

					// Click event handler for removing product from wishlist.
					body.on('click', '.addonify-wishlist-ajax-remove-from-wishlist', function (event) {
						event.preventDefault();

						currentRemoveFromWishlistButton = $(this);

						addonifyWishlistSetProductIDName(currentRemoveFromWishlistButton);

						// Call function to remove product from the wishlist.
						addonifyRemoveFromWishlist();
					});

					// Click event handler for emptying wishlist.
					$(document).on('click', '#addonify-wishlist__clear-all', function (event) {
						event.preventDefault();
						// Displays confirmation modal to clear the wishlist.
						addonifyWishlistDispalyModal(
							alertModalClasses,
							alertModalIcon,
							wishlistEmptyingConfirmationModalMessage,
							emptyWishlistConfirmModalButton
						);
					});

					$(document).on('click', '#adfy-empty-wishlist-confirm-button', function (event) {
						event.preventDefault();

						// Display loader.
						addonifyWishlistInit.displayLoader();

						// Initiate AJAX request for emptying the wishlist.
						$.post(
							ajaxURL,
							{
								action: emptyWishlistAction,
								nonce: nonce
							},
						).done(function (response) {
							if (response.success) {

								// Triggering custom event when wishlist is emptied.
								// 'addonify_wishlist_emptied' custom event can be used to perform desired actions.
								$(document).trigger('addonify_wishlist_emptied');

								addonifyWishlistDispalyModal(
									successModalClasses,
									successModalIcon,
									response.message,
								);
							} else {
								addonifyWishlistDispalyModal(
									errorModalClasses,
									errorModalIcon,
									response.message,
								);
							}
						}).always(function () {
							addonifyWishlistInit.hideLoader();
							addonifyWishlistResetModalContentUpdateData();
						});
					});
				}
			},
			// Handles DOM event related to undoing product removal from the wishlist.
			undoEventsHandler: function () {
				if (enabledMultiWishlist !== '1') {
					// Click event handler for undoing the product removal from the wishlist.
					body.on('click', '#addonify-wishlist-undo-deleted-product-link', function (event) {
						event.preventDefault();
						currentAddToWishlistButton = $(this);
						// Call function to add product into wishlist.
						addonifyAddToWishlist();
					});
				}
			},
			// Handles DOM event related to removing product from the wishlist.
			addedToCartEventHandler: function () {

				if (enabledMultiWishlist !== '1') {
					// Updates sidebar and page content, and triggers custom event when product is added into the cart.
					$(document).on('added_to_cart', function (event, fragments, cart_hash, addToCartButton) {
						// Updates wishlist sidebar and page content.

						if (removeFromWishlistAfterAddedToCart === '1') {

							addonifyWishlistSetProductIDName(addToCartButton);

							currentModalContainerClasses = successModalClasses;
							currentModalMessage = productRemovedFormWishlistModalMessage;
							currentModalIcon = removedFromWishlistModalIcon;

							// Triggering custom event when product is added to wishlist.
							// 'addonify_removed_from_wishlist' custom event can be used to perform desired actions.
							$(document).trigger('addonify_removed_from_wishlist', [
								{
									productID: addToCartButton.data('product_id'),
									productName: addToCartButton.data('product_name'),
									itemsCount: fragments.itemsCount,
									thisButton: addToCartButton,
									modalContentUpdateData: addonifyWishlistPrepareModalContentUpdateData(),
								}
							]);

							addonifyWishlistResetModalContentUpdateData();
						}
					});
				}
			}
		}

		/**
		 * Function to add product into wishlist.
		 */
		function addonifyAddToWishlist() {

			// Triggering custom event when product is being added into wishlist.
			// 'addonify_adding_to_wishlist' custom event can be used to perform desired actions.
			$(document).trigger('addonify_adding_to_wishlist', [{ thisButton: currentAddToWishlistButton }]);

			let postRequestData = {
				action: addToWishlistAction,
				product_id: productID,
				nonce: nonce
			};

			if (wishlistSidebarEle.length > 0) {
				postRequestData.has_wishlist_sidebar = true;
			}

			if (wishlistTableEle.length > 0) {
				postRequestData.has_wishlist_table = true;
			}

			$.post(
				ajaxURL,
				postRequestData,
			).done(function (response) {
				if (response.success == true) {

					currentModalContainerClasses = successModalClasses;
					currentModalMessage = addedToWishlistModalMessage;
					currentModalIcon = addedToWishlistModalIcon;
					currentModalButton = wishlistLinkModalButton;

					// Triggering custom event when product is added to wishlist.
					// 'addonify_added_to_wishlist' custom event can be used to perform desired actions.
					$(document).trigger('addonify_added_to_wishlist', [
						{
							productID: productID,
							itemsCount: response.itemsCount,
							thisButton: currentAddToWishlistButton,
							modalContentUpdateData: addonifyWishlistPrepareModalContentUpdateData(),
						}
					]);

					if (response.hasOwnProperty('sidebarProductRowContent')) {
						addonifyWishlistInit.addWishlistSidebarProductRow(response.sidebarProductRowContent);
					}

					if (response.hasOwnProperty('tableProductRowContent')) {
						addonifyWishlistInit.addWishlistTableProductRow(response.tableProductRowContent);
					}
				} else {
					addonifyWishlistDispalyModal(
						errorModalClasses,
						errorModalIcon,
						response.message,
					);
				}
			}).always(function () {
				addonifyWishlistInit.hideLoader();
				addonifyWishlistResetModalContentUpdateData();
			});
		}

		/**
		 * Function to remove product from wishlist.
		 *
		 * @param {Object} data Request data.
		 */
		function addonifyRemoveFromWishlist() {

			// Triggering custom event when product is being removed from wishlist.
			// 'addonify_removing_from_wishlist' custom event can be used to perform desired actions.
			$(document).trigger('addonify_removing_from_wishlist', [{ thisButton: currentRemoveFromWishlistButton }]);

			$.post(
				ajaxURL,
				{
					action: removeFromWishlistAction,
					product_id: productID,
					nonce: nonce
				},
			).done(function (response) {
				if (response.success) {

					currentModalContainerClasses = successModalClasses;
					currentModalMessage = productRemovedFormWishlistModalMessage;
					currentModalIcon = removedFromWishlistModalIcon;

					// Triggering custom event when product is added to wishlist.
					// 'addonify_removed_from_wishlist' custom event can be used to perform desired actions.
					$(document).trigger('addonify_removed_from_wishlist', [
						{
							productID: productID,
							productName: productName,
							itemsCount: response.itemsCount,
							thisButton: currentRemoveFromWishlistButton,
							modalContentUpdateData: addonifyWishlistPrepareModalContentUpdateData(),
						}
					]);
				} else {
					addonifyWishlistDispalyModal(
						errorModalClasses,
						errorModalIcon,
						response.message,
					);
				}
			}).always(function () {
				addonifyWishlistInit.hideLoader();
				addonifyWishlistResetModalContentUpdateData();
			});
		}

		/**
		 * Assign the values to productID and productName variables.
		 *
		 * @param {object} buttonEle
		 */
		function addonifyWishlistSetProductIDName(buttonEle) {
			productID = buttonEle.data('product_id');
			productName = buttonEle.data('product_name');
		}


		/**
		 * Prepare the data required for displaying modal.
		 *
		 * @returns object
		 */
		function addonifyWishlistPrepareModalContentUpdateData() {
			return {
				modal_icon: currentModalIcon,
				modal_message: currentModalMessage,
				modal_button: currentModalButton,
				modal_container_classes: currentModalContainerClasses,
				product_name: productName,
			};
		}


		/**
		 * Resets the variables that hold the data required for modal.
		 */
		function addonifyWishlistResetModalContentUpdateData() {
			currentModalButton = '';
			currentModalIcon = '';
			currentModalContainerClasses = '';
			currentModalMessage = '';
		}


		/**
		 * Set data required for modal and display the modal.
		 */
		function addonifyWishlistDispalyModal(modalContainerClasses, modalIcon, modalMessage, modalButton = '') {

			currentModalContainerClasses = modalContainerClasses;
			currentModalIcon = modalIcon;
			currentModalMessage = modalMessage;
			currentModalButton = modalButton;
			addonifyWishlistInit.displayModal(
				addonifyWishlistPrepareModalContentUpdateData()
			);

			addonifyWishlistResetModalContentUpdateData();
		}

		addonifyWishlist.init();
	});

})(jQuery);
