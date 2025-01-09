(function ($) {

	'use strict';

	$(document).ready(function () {

		const body = $('body'),
			pluginName = 'addonify-wishlist',
			wishlistSidebarEle = $('#addonify-wishlist-sticky-sidebar-container'),
			wishlistTableEle = $('#addonify-wishlist-table');

		const {
			enabledMultiWishlist,
			requireLogin,
			ajaxURL,
			nonce,
			removeAlreadyAddedProductFromWishlist,
			removeFromWishlistAfterAddedToCart,
			getGuestSidebarTableProductRowAction,
			getGuestWishlistContent,
			thisSiteUrl,
			isLoginRequired,
			ifNotLoginAction,
			loginURL,

			defaultWishlistName,

			successModalClasses,
			alertModalClasses,
			errorModalClasses,
			infoModalClasses,

			addedToWishlistModalIcon,
			removedFromWishlistModalIcon,
			successModalIcon,
			alertModalIcon,
			errorModalIcon,
			loginRequiredModalIcon,

			addedToWishlistModalMessage,
			alreadyInWishlistModalMessage,
			productRemovedFormWishlistModalMessage,
			wishlistEmptyingConfirmationModalMessage,
			wishlistEmptiedModalMessage,
			errorEmptyingWishlistModalMessage,
			loginRequiredModalMessage,
			errorAddingProductToWishlistModalMessage,
			errorRemovingProductFromWishlistModalMessage,

			wishlistLinkModalButton,
			loginLinkModalButton,
			emptyWishlistConfirmModalButton
		} = addonifyWishlistJSObject;

		let productID = 0,
			productName = '',
			beforeSaving = '',
			toBeSaved = '',
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
		 * LocalStorage actions handler related to the wishlist actions.
		 *
		 * @since 2.0.6
		 */
		const localWishlistFuncs = {
			// Save modified wishlist data.
			setWishlistData: function () {
				localStorage.setItem(pluginName + '_' + thisSiteUrl + '_product_ids', JSON.stringify(toBeSaved));
			},
			// Creates default wishlist data.
			setDefaultWishlist: function () {

				toBeSaved = this.getWishlistData();
				beforeSaving = this.getRawWishlistData();

				if (toBeSaved.length === 0) {

					let currentDate = new Date();
					let defaultWishlistData = {
						'id': 0,
						'name': defaultWishlistName,
						'visibility': 'private',
						'created_at': currentDate.getTime(),
						'product_ids': []
					};

					toBeSaved.push(defaultWishlistData);

					this.setWishlistData(toBeSaved);

					if (this.isWishlistDataSaved()) {
						return defaultWishlistData;
					}

					return false;
				}

				return false;
			},
			// Gets raw wishlist data saved in JSON string.
			getRawWishlistData: function () {
				return localStorage.getItem(pluginName + '_' + thisSiteUrl + '_product_ids');
			},
			// Gets wishlist data in JS object by parsing the raw JSON wishlist data.
			getWishlistData: function () {
				let rawWishlistData = this.getRawWishlistData();

				if (rawWishlistData === null) {
					return [];
				}

				let jsonWishlistData;
				try {
					jsonWishlistData = JSON.parse(rawWishlistData);
				} catch (error) {
					console.log(error.message);
				}

				if (Array.isArray(jsonWishlistData)) {
					return jsonWishlistData;
				}

				return [];
			},
			// Checks whether the wishlist data has been modified.
			isWishlistDataSaved: function () {
				return (JSON.stringify(beforeSaving) === toBeSaved) ? false : true;
			},
			/**
			 * Adds product into the wishlist.
			 *
			 * @param {integer} productId
			 * @returns {boolean}
			 */
			addToWishlist: function (productId) {

				productId = parseInt(productId);

				toBeSaved = this.getWishlistData();
				beforeSaving = this.getRawWishlistData();

				let products = this.getProducts();
				products.push(productId);
				toBeSaved[0]['product_ids'] = products;

				this.setWishlistData();

				return (this.isWishlistDataSaved()) ? true : false;
			},
			/**
			 * Removes product from the wishlist.
			 *
			 * @param {integer} productId
			 * @returns {boolean}
			 */
			removeFromWishlist: function (productId) {

				productId = parseInt(productId);

				toBeSaved = this.getWishlistData();
				beforeSaving = this.getRawWishlistData();

				let products = this.getProducts();
				let indexOfProduct = products.indexOf(productId);
				if (indexOfProduct !== -1) {
					products.splice(indexOfProduct, 1);
				}

				toBeSaved[0]['product_ids'] = products;

				this.setWishlistData();

				return (this.isWishlistDataSaved()) ? true : false;
			},
			/**
			 * Clears wishlist.
			 *
			 * @returns {boolean}
			 */
			emptyWishlist: function () {
				toBeSaved = this.getWishlistData();
				beforeSaving = this.getRawWishlistData();

				toBeSaved[0]['product_ids'] = [];

				this.setWishlistData();

				return (this.isWishlistDataSaved()) ? true : false;
			},
			/**
			 * Checks if a product is in the wishlist.
			 *
			 * @param {integer} productId
			 * @returns {boolean}
			 */
			isProductInWishlist: function (productId) {

				productId = parseInt(productId);

				let returnWishlistID = false;

				let products = this.getProducts();

				if (products.indexOf(productId) !== -1) {
					returnWishlistID = products.indexOf(productId);
				}

				return returnWishlistID;
			},
			/**
			 * Gets the count of products in the wishlist.
			 *
			 * @returns {boolean}
			 */
			getProductsCount: function () {
				let products = this.getProducts();
				return products.length;
			},
			/**
			 * Gets the products in the wishlist.
			 *
			 * @returns {boolean}
			 */
			getProducts: function () {
				let wishlistData = this.getWishlistData();
				if (wishlistData[0] !== undefined) {
					let products = wishlistData[0]['product_ids'];
					products.reverse();
					return products;
				}

				return [];
			}
		};


		/**
		 * Handles DOM events.
		 *
		 * @since 2.0.6
		 */
		const AddonifyWishlistPublicGuest = {
			// Fire initial actions.
			init: function () {

				/**
				 * Sets default wishlist data if found not set.
				 */
				let rawWishlistData = localWishlistFuncs.getRawWishlistData();
				if (rawWishlistData === null) {
					localWishlistFuncs.setDefaultWishlist();
				}


				if (requireLogin !== '1' && enabledMultiWishlist !== '1') {
					/**
					 * Update the wishlist buttons if products associated with them are found to be in the wishlist.
					 */
					let products = localWishlistFuncs.getProducts();
					products.forEach(function (value, _) {
						let product_button = $('.adfy-wishlist-btn[data-product_id="' + value + '"]');
						addonifyWishlistInit.updateWishlistButtons(product_button, 'already-in-wishlist');
					});

					/**
					 * Load sidebar content or wishlist page content.
					 */
					if (enabledMultiWishlist !== '1') {
						addonifyLoadWishlistContent();
					}
				}

				this.onAddToWishlist();
				this.onRemoveFromWishlist();
				this.addedToCartEventHandler();
				this.undoEventsHandler();
			},
			// Handles DOM events related to adding product into the wishlist.
			onAddToWishlist: function () {
				if (enabledMultiWishlist !== '1') {
					/**
					 * Handle the event associated with add to wishlist wishlist button.
					 */
					body.on('click', '.addonify-wishlist-ajax-add-to-wishlist, .addonify-add-to-wishlist-btn', function (e) {
						e.preventDefault();
						/**
						 * If login is required, displays modal or redirects to login page.
						 */
						if (isLoginRequired === '1') {
							if (ifNotLoginAction === 'show_popup') {
								addonifyWishlistDispalyModal(
									infoModalClasses,
									loginRequiredModalIcon,
									loginRequiredModalMessage,
									loginLinkModalButton
								);
							} else {
								window.location.href = loginURL;
							}
							return;
						}

						/**
						 * If login is not required,
						 * then add product into the wishlist if not present in the wishlist,
						 * remove product if already in the wishlist or display already in the wishlist modal.
						 */
						if (!addonifyWishlistJSObject.hasOwnProperty('enabledAWP')) {
							currentAddToWishlistButton = $(this);

							// Set `loading` CSS class.
							currentAddToWishlistButton.addClass('loading');

							addonifyWishlistSetProductIDName(currentAddToWishlistButton);

							if (currentAddToWishlistButton.hasClass('added-to-wishlist')) {

								if (removeAlreadyAddedProductFromWishlist === '1') {
									// Remove product from the wishlist.
									currentRemoveFromWishlistButton = currentAddToWishlistButton;
									addonifyLocalRemoveFromWishlist();
								} else {
									// Display already in the wishlist modal.
									addonifyWishlistDispalyModal(
										successModalClasses,
										addedToWishlistModalIcon,
										alreadyInWishlistModalMessage,
										wishlistLinkModalButton
									);
								}
							} else {
								// Call funtion to add the product into the wishlist.
								addonifyLocalAddToWishlist();
							}

							// Remove `loading` CSS class.
							currentAddToWishlistButton.removeClass('loading');
						}
					});
				}
			},
			// Handles DOM event related to removing product from the wishlist.
			onRemoveFromWishlist: function () {
				if (enabledMultiWishlist !== '1') {
					/**
					 * Handle the event associated with remove from wishlist button.
					 */
					$(document).on('click', '.addonify-wishlist-ajax-remove-from-wishlist, .addonify-wishlist-remove-from-wishlist', function (event) {
						event.preventDefault();
						currentRemoveFromWishlistButton = $(this);
						addonifyWishlistSetProductIDName(currentRemoveFromWishlistButton);
						addonifyLocalRemoveFromWishlist();
					});

					/**
					 * Handle the event associated with clear wishlist button.
					 */
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

						addonifyWishlistInit.displayLoader();

						if (localWishlistFuncs.emptyWishlist()) {
							// Triggering custom event when wishlist is emptied.
							// 'addonify_wishlist_emptied' custom event can be used to perform desired actions.
							$(document).trigger('addonify_wishlist_emptied');
							// Displays wishlist cleared modal.
							addonifyWishlistDispalyModal(
								successModalClasses,
								successModalIcon,
								wishlistEmptiedModalMessage
							);
						} else {
							// Displays error modal when clearing the wishlist.
							addonifyWishlistDispalyModal(
								errorModalClasses,
								errorModalIcon,
								errorEmptyingWishlistModalMessage
							);
						}

						addonifyWishlistInit.hideLoader();
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
						addonifyLocalAddToWishlist();
					});
				}
			},
			// Handles DOM event related to removing product from the wishlist.
			addedToCartEventHandler: function () {
				if (enabledMultiWishlist !== '1') {
					// Updates sidebar and page content, and triggers custom event when product is added into the cart.
					$(document).on('added_to_cart', function (event, fragments, cart_hash, addToCartButton) {

						productID = addToCartButton.data('product_id');

						if (removeFromWishlistAfterAddedToCart === '1' && localWishlistFuncs.isProductInWishlist(productID) !== false) {
							currentRemoveFromWishlistButton = addToCartButton;
							addonifyWishlistSetProductIDName(addToCartButton);
							addonifyLocalRemoveFromWishlist();
						}
					});
				}
			},
		}

		AddonifyWishlistPublicGuest.init();

		/**
		 * Add product to local wishlist.
		 */
		function addonifyLocalAddToWishlist() {

			// Triggering custom event when product is being added into wishlist.
			// 'addonify_adding_to_wishlist' custom event can be used to perform desired actions.
			$(document).trigger('addonify_adding_to_wishlist', [{ thisButton: currentAddToWishlistButton }]);

			if (localWishlistFuncs.isProductInWishlist(productID) !== false) {

				if (removeAlreadyAddedProductFromWishlist === '1') {
					currentRemoveFromWishlistButton = currentAddToWishlistButton;
					addonifyLocalRemoveFromWishlist();
				} else {
					addonifyWishlistDispalyModal(
						successModalClasses,
						addedToWishlistModalIcon,
						alreadyInWishlistModalMessage,
						wishlistLinkModalButton
					);
					addonifyWishlistUpdateButton('already-in-wishlist', currentAddToWishlistButton);
				}
			} else {

				localWishlistFuncs.addToWishlist(productID);

				if (localWishlistFuncs.isWishlistDataSaved()) {

					let wishlistProductsCount = localWishlistFuncs.getProductsCount();

					currentModalContainerClasses = successModalClasses;
					currentModalMessage = addedToWishlistModalMessage;
					currentModalIcon = addedToWishlistModalIcon;
					currentModalButton = wishlistLinkModalButton;

					// Triggering custom event when product is added to wishlist.
					// 'addonify_added_to_wishlist' custom event can be used to perform desired actions.
					$(document).trigger('addonify_added_to_wishlist', [
						{
							productID: productID,
							itemsCount: wishlistProductsCount,
							thisButton: currentAddToWishlistButton,
							modalContentUpdateData: addonifyWishlistPrepareModalContentUpdateData(),
						}
					]);

					addonifyWishlistResetModalContentUpdateData();

					let postRequestData = {
						action: getGuestSidebarTableProductRowAction,
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
						if (response.success) {
							if (response.hasOwnProperty('sidebarProductRowContent')) {
								addonifyWishlistInit.addWishlistSidebarProductRow(response.sidebarProductRowContent);
							}
							if (response.hasOwnProperty('tableProductRowContent')) {
								addonifyWishlistInit.addWishlistTableProductRow(response.tableProductRowContent);
							}
							addonifyWishlistInit.updateWishlistSidebarElements(wishlistProductsCount);
						} else {
							addonifyWishlistDispalyModal(
								errorModalClasses,
								errorModalIcon,
								response.message
							);
						}
					});
				} else {
					addonifyWishlistDispalyModal(
						errorModalClasses,
						errorModalIcon,
						errorAddingProductToWishlistModalMessage
					);
				}
			}

			addonifyWishlistInit.hideLoader();
		}

		/**
		 * Function to remove product from wishlist locally.
		 * @param {Object} thisButton Jquery object of the button clicked
		 */
		function addonifyLocalRemoveFromWishlist() {

			// Triggering custom event when product is being removed from wishlist.
			// 'addonify_removing_from_wishlist' custom event can be used to perform desired actions.
			$(document).trigger('addonify_removing_from_wishlist', [{ thisButton: currentRemoveFromWishlistButton }]);

			if (localWishlistFuncs.isProductInWishlist(productID) !== false) {

				localWishlistFuncs.removeFromWishlist(productID);

				if (localWishlistFuncs.isWishlistDataSaved()) {

					let productsCount = localWishlistFuncs.getProductsCount();

					currentModalContainerClasses = successModalClasses;
					currentModalMessage = productRemovedFormWishlistModalMessage;
					currentModalIcon = removedFromWishlistModalIcon;

					// Triggering custom event when product is added to wishlist.
					// 'addonify_removed_from_wishlist' custom event can be used to perform desired actions.
					$(document).trigger('addonify_removed_from_wishlist', [
						{
							productID: productID,
							productName: productName,
							itemsCount: productsCount,
							thisButton: currentRemoveFromWishlistButton,
							modalContentUpdateData: addonifyWishlistPrepareModalContentUpdateData(),
						}
					]);

					addonifyWishlistResetModalContentUpdateData();
				} else {
					addonifyWishlistDispalyModal(
						errorModalClasses,
						errorModalIcon,
						errorRemovingProductFromWishlistModalMessage
					);
				}
			} else {
				addonifyWishlistDispalyModal(
					successModalClasses,
					addedToWishlistModalIcon,
					alreadyInWishlistModalMessage,
					wishlistLinkModalButton
				);
				addonifyWishlistUpdateButton('already-in-wishlist', currentRemoveFromWishlistButton);
			}

			addonifyWishlistInit.hideLoader();
		}

		/**
		 * Function that makes AJAX request and renders the wishlist content.
		 */
		function addonifyLoadWishlistContent() {

			addonifyWishlistInit.displayLoader();

			let productIds = localWishlistFuncs.getProducts();

			let productsCount = localWishlistFuncs.getProductsCount();

			if (wishlistSidebarEle.length > 0 || wishlistTableEle.length > 0) {

				let requestData = {
					action: getGuestWishlistContent,
					product_ids: JSON.stringify(productIds),
					nonce: nonce
				};

				if (wishlistSidebarEle.length > 0) {
					requestData.has_wishlist_sidebar = true;
				}

				if (wishlistTableEle.length > 0) {
					requestData.has_wishlist_table = true;
				}

				$.post(
					ajaxURL,
					requestData,
				).done(function (response) {
					if (response.success) {
						// Updates the wishlist sidebar content.
						if (response.hasOwnProperty('sidebarContent') && $('#addonify-wishlist-sidebar-items-wrapper').length > 0) {
							$('#addonify-wishlist-sidebar-items-wrapper').html(response.sidebarContent);
						}

						// Updates the wishlist page table content.
						if (response.hasOwnProperty('tableContent') && $('#addonify-wishlist-page-items-wrapper').length > 0) {
							$('#addonify-wishlist-page-items-wrapper').html(response.tableContent);
						}
					} else {
						addonifyWishlistDispalyModal(
							errorModalClasses,
							errorModalIcon,
							response.message
						);
					}
				});

				addonifyWishlistInit.updateWishlistSidebarElements(productsCount);

				addonifyWishlistInit.updateWishlistTableElements(productsCount);
			}

			addonifyWishlistInit.hideLoader();
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
	});
})(jQuery)
