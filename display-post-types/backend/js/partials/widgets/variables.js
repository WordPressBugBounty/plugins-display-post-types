const dptWidgetData = window.dptWidgetData || {};

let dptWidgetVariables = {
	stylesSupport: dptWidgetData.styleSupport || {},
	isPremium: dptWidgetData.isPremium || false,
	isStyleSupport: function( style, feature ) {
		const currentStyleSupport = this.stylesSupport[style];
		if ( 'undefined' === typeof currentStyleSupport ) return false;
		return currentStyleSupport.includes(feature);
	},
};
export default dptWidgetVariables;