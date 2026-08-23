import DPT from './dpt';

/**
 * Add SVG icon definitions to the document that owns the rendered preview.
 *
 * The post editor content is rendered in an iframe in WordPress 7.1, so icon
 * definitions printed in the outer admin footer are not visible to its SVGs.
 */
function addIconDefinitions() {
	const iconData = window.dptIconData || {};
	if (
		! document.body ||
		! iconData.markup ||
		document.getElementById('display-post-types-admin-icon-definitions')
	) {
		return;
	}

	document.body.insertAdjacentHTML('beforeend', iconData.markup);
}

/**
 * Initialize post types display
 * 
 * @since 1.0.0
 */
class DisplayPostTypes {

	/**
	 * The constructor function.
	 *
	 * @since 1.0.0
	 */
	constructor() {
        const _this = this;
		addIconDefinitions();
		this.elems = Array.prototype.slice.call(document.querySelectorAll('.dpt-wrapper'));
		this.elems.forEach(elem => { this.createdpt(elem); } );

        document.addEventListener(
            'animationstart',
            function(e) {
                if ('dptAdded' !== e.animationName) { return; }

                const elem = e.target;

                // Return if element is not correct.
                if (!elem.classList.contains('dpt-wrapper')) { return; }

                // Return if element is already processed.
                if (elem.classList.contains('dpt-added')) { return; }

				addIconDefinitions();
                _this.createdpt(elem);
            },
            false
        );
    }
    
    /**
	 * Script initialize.
	 * 
	 * @since 1.0.0
     *
     * @param Object elem
	 */
	createdpt(elem) {
        elem.classList.add('dpt-added');
        new DPT(elem);
	}
}

export default DisplayPostTypes;
