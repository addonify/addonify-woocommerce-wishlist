(function ($) {
    $.fn.addonifyWishlistInit = function (settings) {

        const body = $('body');

        let undoTimeout;

        /**
         * Collection of functions that are common to both guest and logged in users.
         */
        const initializeAddonifyWishlist = {
            init: function () {
                initializeAddonifyWishlist.toggleModalBackgrounds();
                initializeAddonifyWishlist.wishlistEvents();
                initializeAddonifyWishlist.undoEvent();
            },
            // Update modal template content and display the modal.
            displayModal: function (elesToUpdate) {

                let modalTemplate = settings.modalTemplate;

                // Update or modify modal elements with the data received.
                for (const key in elesToUpdate) {

                    if (elesToUpdate.hasOwnProperty(key)) {
                        let value = elesToUpdate[key];

                        modalTemplate = modalTemplate.includes(`{${key}}`) ? modalTemplate.replace(`{${key}}`, value) : modalTemplate.replace(`{${key}}`, '');
                    }
                };

                // Display the modal.
                $('#addonify-wishlist-modal-wrapper').replaceWith(modalTemplate);
                if (!body.hasClass('addonify-wishlist-modal-is-open')) {
                    body.toggleClass('addonify-wishlist-modal-is-open');
                }
            },
            // Handle the event for toggling the modal overlays.
            toggleModalBackgrounds: function () {
                // Toggle modal background overlay.
                body.on('click', '#addonify-wishlist-close-modal-btn, #addonify-wishlist-modal-overlay', function () {
                    body.toggleClass('addonify-wishlist-modal-is-open');
                    initializeAddonifyWishlist.hideLoader();
                });

                // Toggle sidebar background overlay.
                body.on('click', '#addonify-wishlist-show-sidebar-btn, #close-adfy-wishlist-sidebar-button, #addonify-wishlist-sticky-sidebar-overlay', function () {
                    body.toggleClass('addonify-wishlist-sticky-sidebar-is-visible');
                });
            },
            // Handle the wishlist events.
            wishlistEvents: function () {

                // Displays loader when product is being added into the wishlist.
                $(document).on('addonify_adding_to_wishlist', function (event, data) {
                    initializeAddonifyWishlist.displayLoader();
                    if (data.thisButton.find('span.adfy-wishlist-btn-icon').length > 0) {
                        data.thisButton.find('span.adfy-wishlist-btn-icon').html(settings.loadingWishlistButtonIcon);
                    }
                });

                // Displays loader when product is being removed from the wishlist.
                $(document).on('addonify_removing_from_wishlist', function (event, data) {
                    initializeAddonifyWishlist.displayLoader();
                    if (data.thisButton.find('span.adfy-wishlist-btn-icon').length > 0) {
                        data.thisButton.find('span.adfy-wishlist-btn-icon').html(settings.loadingWishlistButtonIcon);
                    }
                });

                // Sets button label and icon for add to wishlist buttons on product added into the cart.
                $(document).on('addonify_added_to_wishlist', function (event, data) {
                    // Remove product removal undo notice.
                    initializeAddonifyWishlist.renderUndoNotice(false);

                    // Redirect to wishlist page after product is added to the wishlist.
                    if (data.hasOwnProperty('thisButton')) {
                        if (settings.afterAddToWishlistAction === 'redirect_to_wishlist_page' && data.thisButton.hasClass('addonify-add-to-wishlist-btn')) {
                            window.location.href = settings.wishlistPageURL;
                            return;
                        }
                    }

                    // Display added to wishlist modal.
                    if (data.hasOwnProperty('thisButton') && data.hasOwnProperty('modalContentUpdateData')) {
                        // Display added to wishlist modal.
                        if (settings.afterAddToWishlistAction === 'show_popup_notice' && data.thisButton.hasClass('addonify-add-to-wishlist-btn')) {
                            initializeAddonifyWishlist.displayModal(data.modalContentUpdateData);
                        }
                    }

                    // Update the wishlist buttons.
                    // Add class 'added-to-wishlist', add data attributes containing wishlist id and wishlist name.
                    if (data.hasOwnProperty('productID')) {
                        let wishlistButtons = $('button[data-product_id=' + data.productID + ']');
                        if (wishlistButtons.length > 0) {
                            wishlistButtons.each(function () {
                                let currentButton = $(this);
                                let wishlistId = data.hasOwnProperty('wishlistID') ? data.wishlistID : '';
                                let wishlistName = data.hasOwnProperty('wishlistName') ? data.wishlistName : '';
                                initializeAddonifyWishlist.updateWishlistButtons(currentButton, 'added-to-wishlist', wishlistId, wishlistName);
                            });
                        }
                    }

                    // Update wishlist sidebar and wishlist table.
                    if (data.hasOwnProperty('itemsCount')) {
                        initializeAddonifyWishlist.updateWishlistSidebarElements(data.itemsCount);
                        initializeAddonifyWishlist.updateWishlistTableElements(data.itemsCount);
                        initializeAddonifyWishlist.updateWishlistIconElements(data.itemsCount);
                    }
                });

                // Sets button label and icon for add to wishlist buttons on product removed from the cart.
                $(document).on('addonify_removed_from_wishlist', function (event, data) {

                    // Display the product removed from wishlist modal.
                    if (data.hasOwnProperty('thisButton') && data.hasOwnProperty('modalContentUpdateData')) {
                        if (data.thisButton !== undefined && data.thisButton.hasClass('addonify-add-to-wishlist-btn')) {
                            initializeAddonifyWishlist.displayModal(data.modalContentUpdateData);
                        }
                    }

                    // Update the wishlist buttons.
                    // Removes class 'added-to-wishlist', add data attributes containing wishlist id and wishlist name.
                    if (data.hasOwnProperty('productID')) {

                        let wishlistButtons = $('[data-product_id=' + data.productID + ']');
                        if (wishlistButtons.length > 0) {
                            wishlistButtons.each(function () {
                                let currentButton = $(this);
                                let wishlistId = data.hasOwnProperty('wishlistID') ? data.wishlistID : '';
                                let wishlistName = data.hasOwnProperty('wishlistName') ? data.wishlistName : '';
                                initializeAddonifyWishlist.updateWishlistButtons(currentButton, 'removed-from-wishlist', wishlistId, wishlistName);
                            });
                        }

                        // Remove the product row from the sidebar and wishlist table.
                        initializeAddonifyWishlist.removeWishlistSidebarTableProductRow(data.productID);
                    }

                    // Update wishlist sidebar and wishlist table.
                    if (data.hasOwnProperty('itemsCount')) {
                        initializeAddonifyWishlist.updateWishlistSidebarElements(data.itemsCount);
                        initializeAddonifyWishlist.updateWishlistTableElements(data.itemsCount);
                        initializeAddonifyWishlist.updateWishlistIconElements(data.itemsCount);
                    }

                    // Displays the product removal and undo notice.
                    if (data.hasOwnProperty('productName')) {
                        initializeAddonifyWishlist.renderUndoNotice(true, data.productName);
                    }
                });

                // Update the wishlist sidebar and wishlist table when wishlist is emptied.
                $(document).on('addonify_wishlist_emptied', function (event) {
                    event.preventDefault();
                    // Remove product removal undo notice.
                    initializeAddonifyWishlist.renderUndoNotice(false);
                    initializeAddonifyWishlist.updateWishlistSidebarElements(0);
                    initializeAddonifyWishlist.updateWishlistTableElements(0);
                    initializeAddonifyWishlist.updateWishlistIconElements(0);
                });
            },
            updateWishlistButtons: function (buttonElement, buttonStatus, wishlistId = '', wishlistName = '') {

                if (buttonStatus === 'added-to-wishlist' || buttonStatus === 'already-in-wishlist') {
                    if (!buttonElement.hasClass('added-to-wishlist')) {
                        buttonElement.addClass('added-to-wishlist');
                    }
                    // Update button label and icon of custom add to wishlist button.
                    if (!buttonElement.hasClass('addonify-custom-wishlist-btn') && buttonElement.hasClass('addonify-add-to-wishlist-btn')) {
                        // Update button icon.
                        buttonElement.find('span.adfy-wishlist-btn-icon').html(settings.addedToWishlistButtonIcon);

                        // Update button label.
                        if (buttonElement.hasClass('addonify-wishlist-save-for-later')) {
                            // If button is save for later button.
                            buttonElement.find('span.addonify-wishlist-btn-label').text(settings.savedForLaterButtonLabel);
                        } else {
                            if (buttonStatus === 'added-to-wishlist') {
                                let addedToWishlistButtonLabel = settings.addedToWishlistButtonLabel;
                                if (addedToWishlistButtonLabel.includes('{wishlist_name}')) {
                                    addedToWishlistButtonLabel = addedToWishlistButtonLabel.replace('{wishlist_name}', wishlistName);
                                }
                                if (buttonElement.data('added_to_wishlist_button_label')) {
                                    buttonElement.find('span.addonify-wishlist-btn-label').text(buttonElement.data('added_to_wishlist_button_label'));
                                } else {
                                    buttonElement.find('span.addonify-wishlist-btn-label').text(addedToWishlistButtonLabel);
                                }
                            } else {
                                let alreadyInWishlistButtonLabel = settings.alreadyInWishlistButtonLabel;
                                if (alreadyInWishlistButtonLabel.includes('{wishlist_name}')) {
                                    alreadyInWishlistButtonLabel = alreadyInWishlistButtonLabel.replace('{wishlist_name}', wishlistName);
                                }
                                if (buttonElement.data('already_in_wishlist_button_label')) {
                                    buttonElement.find('span.addonify-wishlist-btn-label').text(buttonElement.data('already_in_wishlist_button_label'));
                                } else {
                                    buttonElement.find('span.addonify-wishlist-btn-label').text(alreadyInWishlistButtonLabel);
                                }
                            }
                        }
                    }

                    if (wishlistId !== '') {
                        buttonElement.attr('data-wishlist_id', wishlistId);
                    }

                    if (wishlistName !== '') {
                        buttonElement.attr('data-wishlist_name', wishlistName);
                    }
                }

                if (buttonStatus === 'removed-from-wishlist') {
                    if (buttonElement.hasClass('added-to-wishlist')) {
                        buttonElement.removeClass('added-to-wishlist');
                    }
                    // Update button label and icon of custom add to wishlist button.
                    if (!buttonElement.hasClass('addonify-custom-wishlist-btn') && buttonElement.hasClass('addonify-add-to-wishlist-btn')) {
                        // Update button icon.
                        buttonElement.find('span.adfy-wishlist-btn-icon').html(settings.addToWishlistButtonIcon);

                        // Update button label.
                        if (buttonElement.hasClass('addonify-wishlist-save-for-later')) {
                            // If button is save for later button.
                            buttonElement.find('span.addonify-wishlist-btn-label').text(settings.saveForLaterButtonLabel);
                        } else if (buttonElement.hasClass('adfy-wishlist-shortcode-btn') && buttonElement.data('button_label')) {
                            // If button is a shortcode button.
                            buttonElement.find('span.addonify-wishlist-btn-label').text(buttonElement.data('button_label'));
                        } else {
                            // If button is neither save for later or shortcode button.
                            buttonElement.find('span.addonify-wishlist-btn-label').text(settings.initialAddToWishlistButtonLabel);
                        }
                    }

                    let buttonWishlistIdAttr = buttonElement.attr('data-wishlist_id');

                    if (typeof buttonWishlistIdAttr !== 'undefined' && buttonWishlistIdAttr !== false) {
                        buttonElement.removeAttr('data-wishlist_id');
                    }

                    let buttonWishlistNameAttr = buttonElement.attr('data-wishlist_name');

                    if (typeof buttonWishlistNameAttr !== 'undefined' && buttonWishlistNameAttr !== false) {
                        buttonElement.removeAttr('data-wishlist_name');
                    }
                }
            },
            addWishlistSidebarProductRow: function (newSidebarProductRow) {
                let wishlistSidebarListEle = $('#addonify-wishlist-sidebar-ul');
                if (wishlistSidebarListEle.length > 0) {
                    wishlistSidebarListEle.prepend(newSidebarProductRow);
                }
            },
            addWishlistTableProductRow: function (newTableProductRow) {
                let wishlistTableBodyEle = $('#adfy-wishlist-table-body');
                if (wishlistTableBodyEle.length > 0) {
                    wishlistTableBodyEle.prepend(newTableProductRow);
                }
            },
            removeWishlistSidebarTableProductRow: function (productId) {

                let removedProductRow = '';
                if ($('#addonify-wishlist-sticky-sidebar-container').length > 0) {
                    removedProductRow = $('#adfy-wishlist-sidebar-product-row-' + productId);
                }

                if ($('#addonify-wishlist-table').length > 0) {
                    removedProductRow = $('#adfy-wishlist-table-product-row-' + productId);
                }

                if (removedProductRow.length > 0) {
                    removedProductRow.remove();
                }
            },
            updateWishlistSidebarElements: function (itemsCount) {

                if ($('#addonify-wishlist-sticky-sidebar-container').length > 0) {

                    let sidebarToggleButton = $('#addonify-wishlist-show-sidebar-btn');
                    if (sidebarToggleButton.length > 0) {
                        // Toggles the wishlist sidebar toggle button.
                        if (itemsCount > 0) {
                            sidebarToggleButton.removeClass('hidden');
                        } else {
                            sidebarToggleButton.addClass('hidden');
                        }
                    }

                    let sidebarEmptyWishlistParagraphEle = $('#adfy-wishlist-empty-sidebar-para');
                    if (sidebarEmptyWishlistParagraphEle.length > 0) {
                        if (itemsCount > 0) {
                            sidebarEmptyWishlistParagraphEle.removeClass('adfy-wishlist-show').addClass('adfy-wishlist-hide');
                        } else {
                            sidebarEmptyWishlistParagraphEle.removeClass('adfy-wishlist-hide').addClass('adfy-wishlist-show');
                        }
                    }
                }
            },
            updateWishlistTableElements: function (itemsCount) {

                if ($('#addonify-wishlist-page-container').length > 0) {
                    let wishlistTableFormEle = $('#addonify-wishlist-page-form');
                    let wishlistEmptyParaEle = $('#addonify-empty-wishlist-para');

                    if (itemsCount > 0) {
                        wishlistTableFormEle.addClass('adfy-wishlist-show').removeClass('adfy-wishlist-hide');
                        wishlistEmptyParaEle.addClass('adfy-wishlist-hide').removeClass('adfy-wishlist-show');
                    } else {
                        wishlistTableFormEle.addClass('adfy-wishlist-hide').removeClass('adfy-wishlist-show');
                        wishlistEmptyParaEle.addClass('adfy-wishlist-show').removeClass('adfy-wishlist-hide');
                    }
                }
            },
            updateWishlistIconElements: function (itemsCount) {
                if ($('.adfy_wishlist-icon-shortcode-btn').length > 0 ) {
                    $('.adfy_wishlist-icon-shortcode-btn').find('.adfy_wishlist-icon-items-count').text(itemsCount);
                }
            },
            undoEvent: function () {
                // Event handler for setting timeout for undo notice.
                $(document).on('addonify_wishlist_undo_notice_set', function (data) {
                    clearTimeout(undoTimeout);
                    if (parseInt(settings.undoNoticeTimeout) > 0) {
                        undoTimeout = setTimeout(
                            function () {
                                $('#addonify-wishlist-notice').html('');
                            },
                            parseInt(settings.undoNoticeTimeout) * 1000
                        );
                    }
                });
            },
            displayLoader: function () {
                if ($('#addonify-wishlist-sticky-sidebar-container').length > 0) {
                    $('#addonify-wishlist-sticky-sidebar-container').append(settings.loader);
                }

                if ($('#addonify-wishlist-page-container').length > 0) {
                    $('#addonify-wishlist-page-container').append(settings.loader);
                }
            },
            hideLoader: function () {
                if ($('#addonify-wishlist_spinner')) {
                    $('#addonify-wishlist_spinner').remove();
                }
            },
            displayModalLoader: function () {
                if ($('.addonify-wishlist-modal-btns').length > 0) {
                    $('.addonify-wishlist-modal-btns').append(settings.loader);
                }
            },
            renderUndoNotice: function (setNotice = false, productName = "") {
                let undoNoticeContainerEle = $('#addonify-wishlist-notice');
                if (undoNoticeContainerEle.length > 0) {
                    if (setNotice) {
                        let undoNoticeTemplate = settings.productRemovalUndoNotice;
                        if (undoNoticeTemplate.includes('{product_name}') && productName !== '' && productName !== undefined) {
                            undoNoticeTemplate = undoNoticeTemplate.replace('{product_name}', productName);
                        }
                        undoNoticeContainerEle.html(undoNoticeTemplate);
                        $(document).trigger('addonify_wishlist_undo_notice_set');
                    } else {
                        undoNoticeContainerEle.html('');
                    }
                }
            }
        }

        return initializeAddonifyWishlist;
    };
}(jQuery));