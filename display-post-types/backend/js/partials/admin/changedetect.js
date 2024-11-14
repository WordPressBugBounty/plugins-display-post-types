import vars from './variables';

class ChangeDetect {

	/**
	 * Manage Widget editor options.
	 * 
	 * @since 3.3
	 */
	constructor() {
		this.muChecklistTimer = null;
		this.newFeedback = jQuery('#dpt-action-feedback');
		// Run methods.
		this.events();
	}

	// Event handling.
	events() {
		const _this  = this;
		const widget = jQuery('#dpt-options-module-shortcode');
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

		widget.on('change', '.dpt-mu-checklist input[type="checkbox"]', function() {
			_this.updateMuChecklist( jQuery(this) );
		});

		widget.on('change', '.dpt-getval', function() {
			_this.updatePreview( jQuery(this) );
		});

		widget.on('click', '#dpt-shortcode-generator-btn', function() {
			_this.blankShortcodeTemplate( jQuery(this) );
		});

		widget.on('click', '#dpt-shortcode-generator-submit-btn', function() {
			_this.createNewShortcode( jQuery(this) );
		});

		widget.on('click', '#dpt-shortcode-generator-delete-btn', function() {
			_this.deleteShortcode( jQuery(this) );
		});

		widget.on('click', '#dpt-shortcode-generator-update-btn', function() {
			_this.updateShortcode( jQuery(this) );
		});

		widget.on('change', 'select.dpt-shortcode-dropdown', function() {
			_this.loadShortcode( jQuery(this) );
		});

		widget.on('click', '.dpt-collapse-sidebar', function(e) {
			e.preventDefault();
			_this.toggleSidebar( jQuery(this) );
		});

		widget.on('click', '.dpt-copy-shortcode-text', function(e) {
			e.preventDefault();
			_this.copyShortcodeText( jQuery(this) );
		});

		this.newFeedback.on('click', '.dpt-error-close', (e) => {
			this.newFeedback.removeClass('dpt-error');
		});
    
        doc.on( 'click', '.dpt-settings-toggle', function( event ) {
            const $this = jQuery( this );
            event.preventDefault();
            $this.next( '.dpt-settings-content' ).slideToggle('fast');
            $this.toggleClass( 'toggle-active' );
        });

        doc.on( 'click', '.dpt-tab-index-item', function() {
            _this.tabFunctionality( jQuery(this) );
        });
	}

    postTypeChange( postTypeContainer ) {
        const postType      = postTypeContainer.val();
		const wrapper       = postTypeContainer.closest('.dpt-shortcode-form');
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
		const wrapper = styleSelect.closest('.dpt-shortcode-form');

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
			wrapper = crop.closest('.dpt-shortcode-form');

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

	updateMuChecklist( input ) {
		clearTimeout( this.muChecklistTimer );
		this.muChecklistTimer = setTimeout(
			() => {
				const list = input.closest('.dpt-mu-checklist');
				// Get values of all checked input boxes in the list.
				const values = list.find('input[type="checkbox"]:checked').map(
					function() {
						return this.value;
					}
				).get().join(',');
				const hiddenField = list.find('.dpt-getval').val( values ).trigger('change');
			},
			500
		);
	}

	getShortcodeFormValues() {
		const widget   = jQuery('#dpt-shortcode-form');
		const fields   = widget.find('.dpt-getval');
		const instance = widget.data('instance');
		const values = {};
		fields.each(
			function() {
				// Remove 'dpt_field_name_' from this.name.
				const name = this.name.replace( /^dpt_field_name_/, '' );
				if ( 'checkbox' === this.type ) {
					values[name] = this.checked ? this.value : '';
				} else {
					values[name] = this.value;
				}
			}
		);
		return { instance, values };
	}

	updatePreview( input ) {
		const { instance, values } = this.getShortcodeFormValues();
		// Let's get next set of episodes.
		jQuery.ajax( {
			url: vars.ajaxUrl,
			data: {
				action  : 'dpt_render_preview',
				security: vars.security,
				data    : values,
			},
			type: 'POST',
			timeout: 60000,
			success: response => {
				const details = JSON.parse( response );
				if (!jQuery.isEmptyObject(details)) {
					if ('undefined' !== typeof details.error) {
						this.newResponse(details.error, 'dpt-error');
					} else if ('undefined' !== typeof details.markup) {
						const wrapper = jQuery('#dpt-shortcode-preview');
						wrapper.html( details.markup );
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				this.newResponse(errorThrown, 'dpt-error');
			}
		} );
	}

	blankShortcodeTemplate(button) {
		button.siblings('select.dpt-shortcode-dropdown').val('');
		// Let's get next set of episodes.
		jQuery.ajax( {
			url: vars.ajaxUrl,
			data: {
				action  : 'dpt_blank_shortcode_template',
				security: vars.security,
			},
			type: 'POST',
			timeout: 60000,
			success: response => {
				const details = JSON.parse( response );
				if (!jQuery.isEmptyObject(details)) {
					if ('undefined' !== typeof details.error) {
						this.newResponse(details.error, 'dpt-error');
					} else if ('undefined' !== typeof details.form && 'undefined' !== typeof details.instance) {
						const form = `
						<div class="dpt-shortcode-form-wrapper">${details.form}</div>
						<div class="dpt-shortcode-form-submit">
							<button id="dpt-shortcode-generator-submit-btn" class="button button-secondary" style="width: 100%;">Generate Shortcode</button>
						</div>
						`;

						const preview = `
						<div style="padding: 20px; font-size: 20px; color: #aaa;">
							<span>Shortcode</span>
							<span style="color: #333;">Preview</span>
							<span> will be displayed here.</span>
						</div>
						`;
						const formWrapper = jQuery('#dpt-shortcode-form');
						const previewWrapper = jQuery('#dpt-shortcode-preview');
						jQuery('.dpt-shortcode-result').html( '' );
						formWrapper.html( form ).attr('data-instance', details.instance);
						previewWrapper.html( preview );
						jQuery(document).trigger('custom-widget-added');
						this.newResponse('Shortcode template created successfully', 'dpt-success');
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				this.newResponse(errorThrown, 'dpt-error');
			}
		} );
	}

	createNewShortcode() {
		const { instance, values } = this.getShortcodeFormValues();
		const title = values.title || 'DPT Shortcode' + ' ' + (instance + 1);
		// Let's get next set of episodes.
		jQuery.ajax( {
			url: vars.ajaxUrl,
			data: {
				action  : 'dpt_create_new_shortcode',
				security: vars.security,
				data    : values,
				instance: instance,
			},
			type: 'POST',
			timeout: 60000,
			success: response => {
				const details = JSON.parse( response );
				if (!jQuery.isEmptyObject(details)) {
					if ('undefined' !== typeof details.error) {
						this.newResponse(details.error, 'dpt-error');
					} else if ('undefined' !== typeof details.success) {
						const widget   = jQuery('#dpt-options-module-shortcode');
						const wrapper  = widget.find('.dpt-shortcode-action');
						let dropdown = widget.find('select.dpt-shortcode-dropdown');
						if (0 === dropdown.length) {
							wrapper.append(`
								<span class="dpt-separator">or</span>
								<select class="dpt-shortcode-dropdown">
									<option value="" selected="selected">Select a Shortcode to Edit</option>
								</select>
							`);
							dropdown = widget.find('select.dpt-shortcode-dropdown');
						}
						dropdown.append(`<option value="${instance}">${title}</option>`);
						dropdown.val(instance);
						dropdown.trigger('change');
						this.newResponse('New shortcode created successfully', 'dpt-success');
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				this.newResponse(errorThrown, 'dpt-error');
			}
		} );
	}

	loadShortcode(select) {
		const instance = select.val();
		if ( ! instance ) {
			jQuery('#dpt-shortcode-form').html('');
			jQuery('#dpt-shortcode-preview').html(`
				<div style="padding: 20px; font-size: 20px; color: #aaa;">
					<span>Create a </span>
					<span style="color: #333;">New Shortcode</span>
					<span> or </span>
					<span style="color: #333;">Edit an Existing</span>
					<span> Shortcode using the menu above.</span>
				</div>
			`);
			jQuery('.dpt-shortcode-result').html( '' );
			return;
		}
		// Let's get next set of episodes.
		jQuery.ajax( {
			url: vars.ajaxUrl,
			data: {
				action  : 'dpt_load_shortcode',
				security: vars.security,
				instance: instance,
			},
			type: 'POST',
			timeout: 60000,
			success: response => {
				const details = JSON.parse( response );
				if (!jQuery.isEmptyObject(details)) {
					if ('undefined' !== typeof details.error) {
						this.newResponse(details.error, 'dpt-error');
					} else if ('undefined' !== typeof details.form && 'undefined' !== typeof details.preview) {
						const form = `
						<div class="dpt-shortcode-form-wrapper">${details.form}</div>
						<div class="dpt-shortcode-form-update dpt-button-wrapper">
							<button id="dpt-shortcode-generator-update-btn" class="button button-secondary" style="width: 100%;">Update Shortcode</button>
						</div>
						<div class="dpt-shortcode-form-delete dpt-button-wrapper">
							<button id="dpt-shortcode-generator-delete-btn" class="button button-secondary" style="width: 100%;">Delete Shortcode</button>
						</div>
						`;

						const preview = `
						${details.preview}
						`;
						const resultsWrapper = jQuery('.dpt-shortcode-result');
						const formWrapper = jQuery('#dpt-shortcode-form');
						const previewWrapper = jQuery('#dpt-shortcode-preview');
						formWrapper.html( form ).attr('data-instance', details.instance);
						previewWrapper.html( preview );
						resultsWrapper.html(`
							<div class="dpt-shortcode-sidebar-collapse">
								<a href="#" class="dpt-collapse-sidebar">
									<span class="dashicons dashicons-arrow-left-alt2"></span>
									<span class="dpt-collapse-side">Collapse</span>
									<span class="dpt-expand-side" style="display: none;">Expand</span>
								</a>
							</div>
							<div class="dpt-shortcode-copy">
								<span>Your shortcode is </span>
								<pre class="dpt-shortcode-text"><code>[showdpt instance="${details.instance}"]</code></pre>
								<a href="#" class="dpt-copy-shortcode-text">(Copy shortcode)</a>
							</div>
						`);
						jQuery(document).trigger('custom-widget-added');
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				this.newResponse(errorThrown, 'dpt-error');
			}
		} );
	}

	deleteShortcode(button) {
		const widget   = jQuery('#dpt-options-module-shortcode');
		const instance = widget.find('#dpt-shortcode-form').data('instance');
		const dropdown = widget.find('select.dpt-shortcode-dropdown');
		if ( 'undefined' === typeof instance ) {
			return;
		}
		widget.find('.dpt-shortcode-result').html( '' );
		jQuery.ajax( {
			url: vars.ajaxUrl,
			data: {
				action  : 'dpt_delete_shortcode',
				security: vars.security,
				instance: instance,
			},
			type: 'POST',
			timeout: 60000,
			success: response => {
				const details = JSON.parse( response );
				if (!jQuery.isEmptyObject(details)) {
					if ('undefined' !== typeof details.error) {
						this.newResponse(details.error, 'dpt-error');
					} else if ('undefined' !== typeof details.success) {
						dropdown.val('');
						dropdown.find(`option[value="${instance}"]`).remove();
						// check if dropdown does not have any option left.
						if ( 0 === dropdown.find('option').length ) {
							dropdown.remove();
						} else {
							dropdown.trigger('change');
						}
						this.newResponse('Shortcode deleted successfully', 'dpt-success', true);
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				this.newResponse(errorThrown, 'dpt-error');
			}
		} );
	}

	updateShortcode(button) {
		const { instance, values } = this.getShortcodeFormValues();
		if ( values.title ) {
			const selectedShortcode = jQuery('.dpt-shortcode-dropdown option:selected');
			selectedShortcode.text( values.title );
		}
		// Let's get next set of episodes.
		jQuery.ajax( {
			url: vars.ajaxUrl,
			data: {
				action  : 'dpt_update_shortcode',
				security: vars.security,
				data    : values,
				instance: instance,
			},
			type: 'POST',
			timeout: 60000,
			success: response => {
				const details = JSON.parse( response );
				if (!jQuery.isEmptyObject(details)) {
					if ('undefined' !== typeof details.error) {
						this.newResponse(details.error, 'dpt-error');
					} else if ('undefined' !== typeof details.success) {
						this.newResponse('Shortcode updated successfully', 'dpt-success');
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				this.newResponse(errorThrown, 'dpt-error');
			}
		} );
	}

	/**
	 * Display action feedback.
	 * 
	 * @since 2.6.0
	 * 
	 * @param string  message
	 * @param string  type
	 * @param boolean reload
	 */
	newResponse(message = '', type = false, reload = false) {
		this.newFeedback.removeClass('dpt-error dpt-success dpt-running');
		if (false !== type) {
			this.newFeedback.addClass(type);
			this.newFeedback.find('.dpt-feedback').text(message);
		}

		// Remove classes after 2 seconds
		setTimeout(function() {
			this.newFeedback.removeClass('dpt-success dpt-running');
			if (reload) {
				window.location.reload();
			}
		}.bind(this), 1000);
	}

	/**
	 * Toggle form sidebar/
	 *
	 * @since 2.6.0
	 */
	toggleSidebar(link) {
		const sidebar = jQuery('#dpt-shortcode-form');
		sidebar.toggleClass('dpt-sidebar-close');
		link.toggleClass('dpt-sidebar-close');
		window.dispatchEvent(new Event('resize'));
	}

	/**
	 * Copy shortcode text.
	 *
	 * @since 2.6.0
	 */
	copyShortcodeText(link) {
		const wrapper = link.closest('.dpt-shortcode-copy');
		const text = wrapper.find('.dpt-shortcode-text code').text();
		// Create a temporary textarea to copy the text
		var tempTextarea = jQuery("<textarea>");
		jQuery("body").append(tempTextarea);
		tempTextarea.val(text).select();
		document.execCommand("copy");
		tempTextarea.remove();

		// Notify the user.
		this.newResponse('Shortcode copied to clipboard', 'dpt-success');
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
