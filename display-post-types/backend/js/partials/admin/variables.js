const dptShortgenData = window.dptShortgenData || {};

let dptShortgenVariables = {
	stylesSupport: dptShortgenData.styleSupport || {},
	ajaxUrl: dptShortgenData.ajaxurl || '',
	security: dptShortgenData.security || '',
	isPremium: dptShortgenData.isPremium || false,
	isStyleSupport: function( style, feature ) {
		const currentStyleSupport = this.stylesSupport[style];
		if ( 'undefined' === typeof currentStyleSupport ) return false;
		return currentStyleSupport.includes(feature);
	},
};
export default dptShortgenVariables;