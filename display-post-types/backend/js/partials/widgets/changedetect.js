import vars from './variables';

class ChangeDetect {

	/**
	 * Manage Widget editor options.
	 * 
	 * @since 3.3
	 */
	constructor() {
		// Run methods.
		this.events();
	}

	// Event handling.
	events() {
		const _this  = this;
		const widget = jQuery('#widgets-right, #elementor-editor-wrapper, #widgets-editor');
		const doc    = jQuery(document);

        // Add event triggers to the show/hide items.
        widget.on('change', 'select.dpt-post-type', function() {
            _this.postTypeChange( jQuery(this) );
        });

        widget.on('change', 'select.dpt-taxonomy', function() {
            _this.toggleTerms( jQuery(this) );
        });

        widget.on('change', 'select.dpt-styles', function() {
            _this.styleChange( jQuery(this) );
        });

        widget.on('change', 'select.dpt-img-aspect', function() {
            _this.showCroppos( jQuery(this) );
        });

        widget.on('change', 'input[type="checkbox"].spcheckbox', function() {
            _this.showElemOptions( jQuery(this) );
        });
    
        doc.on( 'click', '.dpt-settings-toggle', function( event ) {
            const $this = jQuery( this );
            event.preventDefault();
            $this.next( '.dpt-settings-content' ).slideToggle('fast');
            $this.toggleClass( 'toggle-active' );
        });

		// Add event triggers to the show/hide widget tabs.
		doc.on( 'click', '.dpt-tab-index-item', function() {
            _this.tabFunctionality( jQuery(this) );
        });
	}

    postTypeChange( postTypeContainer ) {
        const postType      = postTypeContainer.val();
		const wrapper       = postTypeContainer.closest('.widget-content');
        const toggleBtn     = wrapper.find('.dpt-settings-toggle');
		const toggleContent = wrapper.find('.dpt-settings-content');
		const taxonomy      = wrapper.find( 'select.dpt-taxonomy' );
		const postSupports  = [
			'.post_ids',
			'.taxonomy',
			'.offset',
			'.orderby',
			'.order',
		];
        const pageSupports  = [
            '.pages',
        ];
		if (postType) {
			toggleBtn.show();
			if ('page' === postType) {
				toggleContent.find(pageSupports.join(',')).show();
                toggleContent.find(postSupports.join(',')).hide();
				
			} else {
                toggleContent.find(pageSupports.join(',')).hide();
                toggleContent.find(postSupports.join(',')).show();
				taxonomy.find( 'option' ).hide();
				taxonomy.find( '.always-visible, .' + postType ).show();
				taxonomy.val('');
			}
            toggleContent.toggleClass('not-post', 'post' !== postType);
			toggleContent.find('.terms, .relation').hide();
		} else {
			toggleBtn.hide();
			toggleContent.hide();
		}
    }

    toggleTerms( taxonomy ) {
		const wrapper = taxonomy.closest('.dpt-settings-content');
		const taxVal = taxonomy.val();
		if ( taxVal ) {
			wrapper.find('.terms, .relation').show();
			wrapper.find('.terms').find( '.terms-checklist li' ).hide();
			wrapper.find('.terms').find( '.terms-checklist .' + taxVal ).show();
		} else {
			wrapper.find('.terms, .relation').hide();
		}
	}

    styleChange( styleSelect ) {
		const style = styleSelect.val();
		const wrapper = styleSelect.closest('.widget-content');

		if (vars.isStyleSupport(style, 'multicol')) {
			wrapper.find('.col_narr').show();
		} else {
			wrapper.find('.col_narr').hide();
		}

		if (vars.isStyleSupport(style, 'ialign')) {
			wrapper.find('.img_align').show();
		} else {
			wrapper.find('.img_align').hide();
		}

		if (vars.isStyleSupport(style, 'slider')) {
			wrapper.find('.autotime').show();
		} else {
            wrapper.find('.autotime').hide();
        }

		if (vars.isStyleSupport(style, 'pagination')) {
			wrapper.find('.show_pgnation').show();
		} else {
			wrapper.find('.show_pgnation').hide();
        }

		if (vars.isStyleSupport(style, 'overlay')) {
			wrapper.find('.text_pos_hor').show();
			wrapper.find('.text_pos_ver').show();
			wrapper.find('.title_shadow').show();
			wrapper.find('.wrapper_width').show();
			const type = wrapper.find('select.dpt-wrapper-type').val();
			if ('content' === type) {
				wrapper.find('.wrapper_height').show();
			} else {
				wrapper.find('.wrapper_height').hide();
			}
		} else {
			wrapper.find('.text_pos_hor').hide();
			wrapper.find('.text_pos_ver').hide();
			wrapper.find('.title_shadow').hide();
			wrapper.find('.wrapper_width').hide();
			wrapper.find('.wrapper_height').hide();
        }

		const supported = wrapper.find('.spcheckbox');
		supported.each(function( ) {
			const value = jQuery(this).val();
			if (vars.isStyleSupport(style, value)) {
				jQuery(this).closest('.dpt-toggle-container').show();
			} else {
				jQuery(this).closest('.dpt-toggle-container').hide();
			};
		});
	}

	showCroppos( crop ) {
		var cropping  = crop.val(),
			wrapper = crop.closest('.widget-content');

		if ('' !== cropping) {
			if ('custom' === cropping) {
				wrapper.find('.custom_aspect').show();
			} else {
				wrapper.find('.custom_aspect').hide();
			}
			wrapper.find('.image_crop').show();
		} else {
			wrapper.find('.image_crop').hide();
			wrapper.find('.custom_aspect').hide();
		}
	}

	showElemOptions( option ) {
		const destElem = option.closest('.dpt-style-wrapper');
		const container = option.closest('.dpt-widget-option');
		if ( option.prop('checked') ) {
			container.parent().find('.dpt-widget-option, .dpt-tabs-container').show();
		} else {
			container.siblings('.dpt-widget-option, .dpt-tabs-container').hide();
		}
		this.updateStyleSup( destElem );
	}

	updateStyleSup( styleSup ) {
		var elemId = styleSup.find('input.spcheckbox').first().attr('data-id');
		if ( ! elemId ) {
			return;
		}
		var elems = styleSup.find('input.spcheckbox:checked');
		if ( ! elems.length ) {
			jQuery('#' + elemId).val('').trigger('change');
			return;
		}
		var supported = elems.map(
			function() {
				return this.value;
			}
		).get().join(',');
		jQuery('#' + elemId).val(supported).trigger('change');
	}

	tabFunctionality( tab ) {
		const tabId = tab.attr('data-id');
		const contentWrapper = tab.closest('.dpt-tabs').find('.dpt-tab-content');
		const content = contentWrapper.find('[data-attr="' + tabId + '"]');
		tab.siblings('.dpt-tab-index-item').removeClass('active-tab');
		tab.addClass('active-tab');
		contentWrapper.find('.dpt-tab-content-item').hide();
		content.show();
	}
}

export default ChangeDetect;
