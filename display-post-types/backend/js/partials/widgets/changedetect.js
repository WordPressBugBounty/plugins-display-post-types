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

        widget.on('change', '.dpt-query-tax-toggle', function() {
            _this.toggleAdvancedTaxonomy( jQuery(this) );
        });

        widget.on('click', '.dpt-query-tax-add', function() {
            _this.addAdvancedTaxonomyRule( jQuery(this) );
        });

        widget.on('click', '.dpt-query-tax-remove', function() {
            _this.removeAdvancedTaxonomyRule( jQuery(this) );
        });

        widget.on('change', '.dpt-query-tax-rule-taxonomy', function() {
            _this.advancedTaxonomyChange( jQuery(this) );
        });

        widget.on('change', '.dpt-query-tax-rule-operator, .dpt-query-tax-rule-terms input[type="checkbox"]', function() {
            _this.updateAdvancedTaxonomyValue( jQuery(this).closest('.dpt-query-tax-control') );
        });

        widget.on('change', '.terms .terms-checklist input[type="checkbox"]', function() {
            _this.updateTermsValue( jQuery(this).closest('.terms') );
        });

		widget.on('change', 'select.dpt-orderby', function() {
			_this.toggleCustomSort( jQuery(this) );
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

	toggleCustomSort( orderbyContainer ) {
		const orderBy      = orderbyContainer.val();
		const customFields = [ '.sort_custom_field_key', '.sort_custom_field_type' ];
		const wrapper      = orderbyContainer.closest('.dpt-shortcode-form');
		if ( 'custom' !== orderBy ) {
			wrapper.find(customFields.join(',')).each(function() {
				const cField = jQuery(this);
				cField.val('').hide();
			});
		} else {
			wrapper.find(customFields.join(',')).show();
		}
	}

    postTypeChange( postTypeContainer ) {
        const postType      = postTypeContainer.val();
		const wrapper       = postTypeContainer.closest('.widget-content');
        const toggleBtn     = wrapper.find('.dpt-settings-toggle');
		const toggleContent = wrapper.find('.dpt-settings-content');
		const taxonomy      = wrapper.find( 'select.dpt-taxonomy' );
		const customFieldKey   = wrapper.find( 'select.dpt-filter-custom-field-key' );
		const customFieldOp    = wrapper.find( 'select.dpt-filter-custom-field-operator' );
		const customFieldType  = wrapper.find( 'select.dpt-filter-custom-field-type' );
		const customFieldValue = wrapper.find( 'input.dpt-filter-custom-field-value' );
		const postSupports  = [
			'.post_ids',
			'.taxonomy',
			'.query_tax_clauses',
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
				toggleContent.find(pageSupports.join(',')).show().closest('.dpt-wrapper-container').show();
				toggleContent.find(postSupports.join(',')).hide()
				.closest('.dpt-settings-content').hide()
				.closest('.dpt-wrapper-container').hide()
				.find('.dpt-settings-toggle').removeClass('toggle-active');
				this.resetAdvancedTaxonomy(wrapper);
				
			} else {
                toggleContent.find(pageSupports.join(',')).hide()
				.closest('.dpt-settings-content').hide()
				.closest('.dpt-wrapper-container').hide()
				.find('.dpt-settings-toggle').removeClass('toggle-active');
                toggleContent.find(postSupports.join(',')).show().closest('.dpt-wrapper-container').show();
				taxonomy.find( 'option' ).hide();
				taxonomy.find( '.always-visible, .' + postType ).show();
				taxonomy.val('');
				this.syncAdvancedTaxonomyOptions(wrapper, postType);
				this.resetAdvancedTaxonomy(wrapper);
			}
            toggleContent.toggleClass('not-post', 'post' !== postType);
			toggleContent.find('.terms, .relation').hide();
			customFieldKey.find( 'option' ).hide();
			customFieldKey.find( '.always-visible, .' + postType ).show();
			customFieldKey.val('');
			customFieldOp.val('');
			customFieldValue.val('');
			customFieldValue.prop('disabled', false);
			customFieldType.prop('disabled', false);
		} else {
			toggleBtn.hide();
			toggleContent.hide();
		}
    }

	toggleAdvancedTaxonomy( toggle ) {
		const control = toggle.closest('.dpt-query-tax-control');
		const wrapper = toggle.closest('.dpt-settings-content');
		const enabled = toggle.prop('checked');

		control.find('.dpt-query-tax-builder').css('display', enabled ? 'flex' : 'none');
		if (enabled) {
			wrapper.children('.taxonomy, .terms, .relation').hide();
			this.updateAdvancedTaxonomyValue(control);
			return;
		}

		control.find('.dpt-query-tax-clauses-value').val('').trigger('change');
		wrapper.children('.taxonomy').show();
		this.toggleTerms(wrapper.find('select.dpt-taxonomy'));
	}

	addAdvancedTaxonomyRule( button ) {
		const control = button.closest('.dpt-query-tax-control');
		const rules = control.find('.dpt-query-tax-rules');
		const source = rules.find('.dpt-query-tax-rule').first();
		if (!source.length) {
			return;
		}

		const rule = source.clone();
		rule.find('.dpt-query-tax-rule-taxonomy').val('');
		rule.find('.dpt-query-tax-rule-operator').val('IN');
		rule.find('.dpt-query-tax-rule-terms-row').hide();
		rule.find('.dpt-query-tax-rule-terms li').addClass('dpt-hidden').hide();
		rule.find('.dpt-query-tax-rule-terms input[type="checkbox"]').prop('checked', false);
		rules.append(rule);
		this.updateAdvancedTaxonomyValue(control);
	}

	removeAdvancedTaxonomyRule( button ) {
		const control = button.closest('.dpt-query-tax-control');
		const rules = control.find('.dpt-query-tax-rule');
		const current = button.closest('.dpt-query-tax-rule');

		if (1 < rules.length) {
			current.remove();
		} else {
			current.find('.dpt-query-tax-rule-taxonomy').val('');
			current.find('.dpt-query-tax-rule-operator').val('IN');
			current.find('.dpt-query-tax-rule-terms-row').hide();
			current.find('.dpt-query-tax-rule-terms li').addClass('dpt-hidden').hide();
			current.find('.dpt-query-tax-rule-terms input[type="checkbox"]').prop('checked', false);
		}

		this.updateAdvancedTaxonomyValue(control);
	}

	advancedTaxonomyChange( select ) {
		const rule = select.closest('.dpt-query-tax-rule');
		const taxVal = select.val();
		const termsRow = rule.find('.dpt-query-tax-rule-terms-row');
		const termItems = rule.find('.dpt-query-tax-rule-terms li');

		termItems.addClass('dpt-hidden').hide();
		termItems.find('input[type="checkbox"]').prop('checked', false);
		if (taxVal) {
			termsRow.show();
			termItems.filter('.' + taxVal).removeClass('dpt-hidden').show();
		} else {
			termsRow.hide();
		}

		this.updateAdvancedTaxonomyValue(select.closest('.dpt-query-tax-control'));
	}

	updateAdvancedTaxonomyValue( control ) {
		if (!control.length || !control.find('.dpt-query-tax-toggle').prop('checked')) {
			return;
		}

		const clauses = [];
		control.find('.dpt-query-tax-rule').each(function() {
			const rule = jQuery(this);
			const taxonomy = rule.find('.dpt-query-tax-rule-taxonomy').val();
			const operator = rule.find('.dpt-query-tax-rule-operator').val() || 'IN';
			const terms = rule.find('.dpt-query-tax-rule-terms li:not(.dpt-hidden) input[type="checkbox"]:checked').map(function() {
				return this.value;
			}).get();

			if (taxonomy && terms.length) {
				clauses.push({
					taxonomy,
					field: 'slug',
					terms,
					operator,
				});
			}
		});

		control.find('.dpt-query-tax-clauses-value').val(clauses.length ? JSON.stringify(clauses) : '').trigger('change');
	}

	updateTermsValue( termsWrapper ) {
		const values = termsWrapper.find('.terms-checklist li:not(.dpt-hidden) input[type="checkbox"]:checked').map(function() {
			return this.value;
		}).get().join(',');
		termsWrapper.find('.dpt-getval').val(values).trigger('change');
	}

	resetAdvancedTaxonomy( wrapper ) {
		wrapper.find('.dpt-query-tax-control').each((index, element) => {
			const control = jQuery(element);
			const rules = control.find('.dpt-query-tax-rule');
			const first = rules.first();

			control.find('.dpt-query-tax-toggle').prop('checked', false);
			control.find('.dpt-query-tax-builder').css('display', 'none');
			control.find('.dpt-query-tax-clauses-value').val('').trigger('change');
			rules.not(first).remove();
			first.find('.dpt-query-tax-rule-taxonomy').val('');
			first.find('.dpt-query-tax-rule-operator').val('IN');
			first.find('.dpt-query-tax-rule-terms-row').hide();
			first.find('.dpt-query-tax-rule-terms li').addClass('dpt-hidden').hide();
			first.find('.dpt-query-tax-rule-terms input[type="checkbox"]').prop('checked', false);
		});
	}

	syncAdvancedTaxonomyOptions( wrapper, postType ) {
		wrapper.find('.dpt-query-tax-rule-taxonomy').each(function() {
			const select = jQuery(this);
			select.find('option').hide();
			select.find('.always-visible, .' + postType).show();
		});
	}

	    toggleTerms( taxonomy ) {
			const wrapper = taxonomy.closest('.dpt-settings-content');
			const taxVal = taxonomy.val();
			const termsWrapper = wrapper.find('.terms');
			const termItems = termsWrapper.find('.terms-checklist li');
			if ( taxVal ) {
				wrapper.find('.terms, .relation').show();
				termItems.hide();
				termItems.filter( '.' + taxVal ).show();
				termItems.not( '.' + taxVal ).find('input[type="checkbox"]').prop('checked', false);
				const values = termItems.filter( '.' + taxVal ).find('input[type="checkbox"]:checked').map(
					function () {
						return this.value;
					}
				).get().join(',');
				termsWrapper.find('.dpt-getval').val(values).trigger('change');
			} else {
				wrapper.find('.terms, .relation').hide();
				termItems.find('input[type="checkbox"]').prop('checked', false);
				termsWrapper.find('.dpt-getval').val('').trigger('change');
			}
		}

    styleChange( styleSelect ) {
		const style = styleSelect.val();
		const wrapper = styleSelect.closest('.widget-content');

		this.saneDefaults( style, wrapper );

		if (vars.isStyleSupport(style, 'multicol')) {
			wrapper.find('.col_narr').show();
			wrapper.find('.col_narr_tab').show();
			wrapper.find('.col_narr_mob').show();
		} else {
			wrapper.find('.col_narr').hide();
			wrapper.find('.col_narr_tab').hide();
			wrapper.find('.col_narr_mob').hide();
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

	saneDefaults( style, wrapper ) {
		const styleSupDefaults = {
			'dpt-list1': ['thumbnail', 'title', 'meta', 'excerpt'],
			'dpt-list2': ['thumbnail', 'title', 'meta'],
			'dpt-pro-slider2': ['thumbnail', 'title', 'meta', 'excerpt'],
			'dpt-mag1': ['thumbnail', 'title', 'meta'],
		};
		const defaultThumbCropStyles = ['dpt-list1', 'dpt-list2', 'dpt-slider1', 'dpt-mag1'];
		const supported = wrapper.find('.spcheckbox');
		if ('undefined' !== typeof styleSupDefaults[style]) {
			supported.each(function( ) {
				const value = jQuery(this).val();
				if (styleSupDefaults[style].includes(value)) {
					jQuery(this).prop('checked', true).trigger('change');
				} else {
					jQuery(this).prop('checked', false).trigger('change');
				}
			});
		} else {
			supported.each(function( ) {
				const value = jQuery(this).val();
				if (['thumbnail', 'title'].includes(value)) {
					jQuery(this).prop('checked', true).trigger('change');
				} else {
					jQuery(this).prop('checked', false).trigger('change');
				}
			});
		}
		if (defaultThumbCropStyles.includes(style)) {
			wrapper.find('select.dpt-img-aspect').val('land1').trigger('change');
		} else if (['dpt-pro-slider1', 'dpt-pro-slider3'].includes(style)) {
			wrapper.find('select.dpt-img-aspect').val('wdscrn').trigger('change');
		} else if (['dpt-pro-slider2'].includes(style)) {
			wrapper.find('select.dpt-img-aspect').val('squr').trigger('change');
		} else {
			wrapper.find('select.dpt-img-aspect').val('').trigger('change');
		}
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
