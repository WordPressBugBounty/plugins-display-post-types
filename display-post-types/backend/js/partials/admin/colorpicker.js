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
		jQuery(function() { _this.colorPicker() });
		jQuery(document).on( 'custom-widget-added', function() {
			_this.colorPicker();
		} );
	}

	/**
	 * Color Picker Functionality.
	 * 
	 * @since 3.7.0
	 */
	colorPicker() {
		const params = {
			change: function(e, ui) {
				setTimeout(function() {
					jQuery( e.target ).trigger('change');
				}, 60);
			},
		};
		jQuery('.dpt-color-picker').not('[id*="__i__"]').wpColorPicker( params );
	}
}

export default ColorPicker;
