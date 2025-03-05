class ColorPicker {

	/**
	 * Manage Feed editor options.
	 * 
	 * @since 3.3
	 * 
	 * @param {string} id Podcast player ID. 
	 */
	constructor() {
		// Run methods.
		this.events();
	}

	// Event handling.
	events() {
		const _this  = this;
		const widget = jQuery('#widgets-right, #elementor-editor-wrapper, #widgets-editor');

		jQuery(function() { _this.colorPicker(widget.find('.widget-content')) });

		widget.on('click', '.dpt-style-toggle', function() {
			_this.colorPicker(jQuery(this).closest('.widget-content'));
		});
	}

	/**
	 * Color Picker Functionality.
	 * 
	 * @since 3.7.0
	 */
	colorPicker(widget) {
		const params = {
			change: function(e, ui) {
				// jQuery( e.target ).val( ui.color.toString() );
				setTimeout(function() {
					jQuery( e.target ).trigger('change');
				}, 60);
			},
		};
		widget.find('.dpt-color-picker').not('[id*="__i__"]').wpColorPicker( params );
	}
}

export default ColorPicker;
