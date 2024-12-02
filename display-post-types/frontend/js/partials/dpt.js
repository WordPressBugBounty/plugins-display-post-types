/**
 * Initialize post types display
 * 
 * @since 1.0.0
 */
class DPT {

    /**
	 * Screen resize timeout.
	 */
    timeOut = null;
    
    /**
	 * Masonry Grid Object.
	 */
    masonGrid = false;
    
    /**
	 * Flickity Slider Object.
	 */
	flicKity = false;

	/**
	 * The constructor function.
	 *
	 * @since 1.0.0
	 */
	constructor(elem) {
        
        // Define variables.
        this.elem = elem;

		// Style update on first load.
        this.styleUpdate();

        // Apply Masonry or Slider layout.
        this.applyLayout();
        
        // Register events.
		this.events();
	}

	/**
	 * Script initialize.
	 * 
	 * @since 1.0.0
	 */
	events() {
		const _this = this;
		const dpt = this.elem.parentElement;
		window.addEventListener('resize', this.onResize.bind(this));

		// Resolve masonry element height issue due to late text wrapping.
		if (this.masonGrid) {
			window.addEventListener('load', function() { this.masonGrid.setup(); }.bind(this));
		}
	}

	/**
	 * Provision to add separate styles for narrow and wide elements.
	 * 
	 * @since 1.0.0
	 */
	styleUpdate() {
        const mobile = 450;
		const tablet = 640;
		const tabrot = 768;
		const { paddingLeft, paddingRight } = getComputedStyle(this.elem);
		const width = this.elem.clientWidth - ( this.getStyleSize(paddingLeft) + this.getStyleSize(paddingRight));

		this.elem.classList.remove('wide-wrap', 'tab-wrap', 'mob-wrap');
		if (width > tabrot) {
			this.elem.classList.add('wide-wrap');
		} else if (width > tablet) {
			this.elem.classList.add('tab-wrap');
		} else if (width > mobile) {
			this.elem.classList.add('mob-wrap');
		}
    }

    /**
	 * Parse element style values.
	 *
	 * @since 1.0.0
	 * 
	 * @param {int, str, Obj} value
	 */
	getStyleSize(value) {
		const num = parseFloat( value );
		const isValid = value.indexOf('%') == -1 && !isNaN( num );
		return isValid ? num : 0;
	}
    
    /**
	 * Update element style on screen reSize.
	 *
	 * @private
	 */
	onResize() {
		this.timeout = setTimeout(() => {
			this.styleUpdate();
			this.timeout = null;
		});
	};

	/**
	 * Conditionally apply slider or masonry layout.
	 * 
	 * @since 1.0.0
	 */
	applyLayout() {
		if ( this.elem.classList.contains('dpt-mag1') ) {
			const featuredWrap = document.createElement('div');
			const normalWrap = document.createElement('div');
			featuredWrap.classList.add('dpt-featured-wrap');
			normalWrap.classList.add('dpt-normal-wrap');
			const entries = Array.prototype.slice.call(this.elem.querySelectorAll('.dpt-entry'));
			entries.forEach((entry, index) => {
				if (index === 0) {
					featuredWrap.appendChild(entry);
				} else {
					normalWrap.appendChild(entry);
				}
			});
			this.elem.appendChild(featuredWrap);
			this.elem.appendChild(normalWrap);
		}
		this.addImageAttrs(this.elem);
		if ( this.elem.classList.contains('dpt-mason-wrap') ) {
            this.masonGrid = new brickLayer({
                container: this.elem,
                gutter: 0,
                waitForImages: false,
				dynamicContent: true,
                useTransform: false,
                callAfter: this.addLoadedClass.bind(this, this.elem),
            });
            this.masonGrid.init();
        } else if ( this.elem.classList.contains('dpt-slider') ) {
            const autotime = parseInt(this.elem.getAttribute('data-autotime'));
            const param    = {
                cellAlign: 'left',
                contain: true,
                wrapAround: true,
                prevNextButtons: true,
                imagesLoaded: true,
                cellSelector: '.dpt-entry',
            };
            if (autotime) {
                param.autoPlay = autotime;
            }
			if (this.elem.classList.contains('dpt-pro-slider1')) {
				param.adaptiveHeight = true;
			}
            this.flicKity = new Flickity(this.elem, param);
        }

		if ( this.elem.classList.contains('dpt-mag1') && ! this.elem.classList.contains('mob-wrap') ) {
			const featuredWrap = this.elem.querySelector('.dpt-featured-wrap');
			const normalWrap = this.elem.querySelector('.dpt-normal-wrap');
			const featuredWrapHeight = featuredWrap.offsetHeight;
			const normalWrapHeight = normalWrap.offsetHeight;
			if (normalWrapHeight > featuredWrapHeight) {
				normalWrap.style.maxHeight = featuredWrapHeight + 'px';
				normalWrap.style.overflowY = 'scroll';
			}
		}
	}

	addImageAttrs(elem) {
		const thumbnails = Array.prototype.slice.call(elem.querySelectorAll('.dpt-thumbnail'));
		thumbnails.forEach(thumbnail => {
			const image = thumbnail.querySelector('img');
			if ( ! image ) {
				return;
			}
			const src = image.getAttribute('data-dpt-src');
			const srcset = image.getAttribute('data-dpt-srcset');
			if ( ! src || ! srcset ) {
				return;
			}
			const containerWidth = thumbnail.offsetWidth;
			image.setAttribute('sizes', `${containerWidth + 100}px`);
			image.setAttribute('src', src);
			image.setAttribute('srcset', srcset);
		});
	}

	/**
	 * Add display class to the container.
	 * 
	 * @since 1.0.0
	 * 
	 * @param {object} elem
	 */
	addLoadedClass(elem) {
		elem.classList.add('dpt-loaded');
	}
}

export default DPT;