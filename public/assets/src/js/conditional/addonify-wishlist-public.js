(function ($) {

    'use strict';

    $(document).ready(function () {

        const body = $('body'),
            wishlistSidebarEle = $('#adfy-wl-sidebar-container'),
            wishlistTableEle = $('#addonify-wishlist-table');

        let productID = 0,
            productName = '',
            currentAddToWishlistButton = '',
            currentRemoveFromWishlistButton = '';

        const addonifyWishlistInit = body.addonifyWishlistInit(addonifyWishlistJSObject);

        addonifyWishlistInit.init();

        /**
         * Handles DOM events.
         * 
         * @since 2.0.6
         */
        const addonifyWishlist = {
            init: function() {
                this.wishlistButtonEventHandler();
                this.undoEventsHandler();
                this.addedToCartEventHandler();
            },
            // Handles DOM events related to adding product into the wishlist and removing product from the wishlist.
            wishlistButtonEventHandler: function() {
                if (addonifyWishlistJSObject.enabledMultiWishlist !== '1') {

                    /**
                     * Check if product is already in the cart.
                     * If not call AJAX function to add the product into the cart.
                     */
                    body.on('click', '.adfy-wl-ajax-add-to-wishlist, .adfy-wl-add-to-wishlist', function (e) {
                        e.preventDefault();

                        currentAddToWishlistButton = jQuery(this);
                        
                        // Set `loading` CSS class.
                        currentAddToWishlistButton.addClass('loading');

                        addonifyWishlistSetProductIDName(currentAddToWishlistButton);

                        if (currentAddToWishlistButton.hasClass("added-to-wishlist")) {

                            // Remove product from wishlist when clicked on the added to wishlist button.
                            if (addonifyWishlistJSObject.removeAlreadyAddedProductFromWishlist === '1') {
                                currentRemoveFromWishlistButton = currentAddToWishlistButton;
                                addonifyRemoveFromWishlist();
                            } else {
                                addonifyWishlistInit.displayModal( addonifyWishlistJSObject.alreadyInWishlistModal );
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
                        addonifyWishlistInit.displayModal( addonifyWishlistJSObject.confirmClearWishlistModal );      
                    });

                    $(document).on('click', '#adfy-wl-modal-confirm-clear-wl-btn', function(event){
                        event.preventDefault();

                        // Display loader.
                        addonifyWishlistInit.displayLoader();

                        // Initiate AJAX request for emptying the wishlist.
                        $.post(
                            addonifyWishlistJSObject.ajaxURL,
                            {
                                action: addonifyWishlistJSObject.emptyWishlistAction,
                                nonce: addonifyWishlistJSObject.nonce
                            },
                        ).done(function (response) {
                            if (response.success) {

                                // Triggering custom event when wishlist is emptied. 
                                // 'addonify_wishlist_emptied' custom event can be used to perform desired actions.
                                $(document).trigger('addonify_wishlist_emptied');

                                addonifyWishlistInit.displayModal(
                                    addonifyWishlistJSObject.successModal,
                                    {
                                        'success_message': response.message
                                    }
                                );
                            } else {
                                addonifyWishlistInit.displayModal(
                                    addonifyWishlistJSObject.errorModal,
                                    {
                                        'error_message': response.message
                                    }
                                );
                            }
                        }).always(function () {
                            addonifyWishlistInit.hideLoader();
                        });
                    });
                }
            },
            // Handles DOM event related to undoing product removal from the wishlist.
            undoEventsHandler: function() {
                if (addonifyWishlistJSObject.enabledMultiWishlist !== '1') {
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
            addedToCartEventHandler: function() {

                if (addonifyWishlistJSObject.enabledMultiWishlist !== '1') {
                    // Updates sidebar and page content, and triggers custom event when product is added into the cart.
                    $(document).on('added_to_cart', function (event, fragments, cart_hash, addToCartButton) {
                        // Updates wishlist sidebar and page content.

                        if (addonifyWishlistJSObject.removeFromWishlistAfterAddedToCart === '1') {

                            addonifyWishlistSetProductIDName(addToCartButton);
                            
                            // Triggering custom event when product is added to wishlist. 
                            // 'addonify_removed_from_wishlist' custom event can be used to perform desired actions.
                            $(document).trigger('addonify_removed_from_wishlist', [
                                {
                                    productID: addToCartButton.data('product_id'),
                                    productName: addToCartButton.data('product_name'),
                                    itemsCount: fragments.itemsCount,
                                    thisButton: addToCartButton,
                                    modalContentUpdateData: {
                                        'product_name': addToCartButton.data('product_name'),
                                    },
                                }
                            ]);
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
                action: addonifyWishlistJSObject.addToWishlistAction,
                product_id: productID,
                nonce: addonifyWishlistJSObject.nonce
            };

            if (wishlistSidebarEle.length > 0) {
                postRequestData.has_wishlist_sidebar = true;
            }

            if (wishlistTableEle.length > 0) {
                postRequestData.has_wishlist_table = true;
            }

            $.post(
                addonifyWishlistJSObject.ajaxURL,
                postRequestData,
            ).done(function(response){
                if (response.success == true) {

                    // Triggering custom event when product is added to wishlist. 
                    // 'addonify_added_to_wishlist' custom event can be used to perform desired actions.
                    $(document).trigger('addonify_added_to_wishlist', [
                        {
                            productID: productID,
                            itemsCount: response.itemsCount,
                            thisButton: currentAddToWishlistButton,
                            modalContentUpdateData: {
                                'product_name': currentAddToWishlistButton.data('product_name'),
                            },
                        }
                    ]);

                    if (response.hasOwnProperty('sidebarProductRowContent')) {
                        addonifyWishlistInit.addWishlistSidebarProductRow(response.sidebarProductRowContent);
                    }

                    if (response.hasOwnProperty('tableProductRowContent')) {
                        addonifyWishlistInit.addWishlistTableProductRow(response.tableProductRowContent);
                    }
                } else {
                    addonifyWishlistInit.displayModal(
                        addonifyWishlistJSObject.errorModal,
                        {
                            'error_message': response.message
                        }
                    );
                }
            }).always(function () {
                addonifyWishlistInit.hideLoader();
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
            $(document).trigger('addonify_removing_from_wishlist', [{thisButton: currentRemoveFromWishlistButton}]);

            $.post(
                addonifyWishlistJSObject.ajaxURL,
                {
                    action: addonifyWishlistJSObject.removeFromWishlistAction,
                    product_id: productID,
                    nonce: addonifyWishlistJSObject.nonce
                },
            ).done(function(response){
                if (response.success) {
                    // Triggering custom event when product is added to wishlist. 
                    // 'addonify_removed_from_wishlist' custom event can be used to perform desired actions.
                    $(document).trigger('addonify_removed_from_wishlist', [
                        {
                            productID: productID,
                            productName: productName,
                            itemsCount: response.itemsCount,
                            thisButton: currentRemoveFromWishlistButton,
                            modalContentUpdateData: {
                                'product_name': productName
                            },
                        }
                    ]);
                } else {
                    addonifyWishlistInit.displayModal(
                        addonifyWishlistJSObject.errorModal,
                        { 
                            'error_message': response.message
                        }
                    );
                }
            }).always(function () {
                addonifyWishlistInit.hideLoader();
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

        addonifyWishlist.init();
    });

})(jQuery);