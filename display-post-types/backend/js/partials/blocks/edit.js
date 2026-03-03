const { __ } = wp.i18n;
const { Component, Fragment, createRef } = wp.element;
const { InspectorControls } = wp.blockEditor;
const { apiFetch } = wp;
const { PanelColorSettings } = wp.editor;
const ServerSideRender = wp.serverSideRender;
const { TextControl, SelectControl, RangeControl, ToggleControl, Disabled } = wp.components;

import MultipleCheckboxControl from './mcc';
import DptAccordion from './components/dpt-accordion';
import { Tabs, TabList, Tab, TabPanel } from './components/dpt-tabs';
import DptFourSides from "./components/dpt-foursides";

class DisplayPostTypes extends Component {

	constructor() {
		super( ...arguments );
		this.state = {
			postTypes: [],
			pageList: [],
			taxonomies: [],
			termsList: [],
			styleList: [],
			customFields: [],
		};
		this.fetching = false;
		this.styleSupport = {};
		this.elemRef = createRef();
		this.isPro = this.props.isPro || false;
		this.displayElems = this.props.displayElems || this.displayElems.bind(this);
	}

	apiDataFetch(data, path) {
		if (this.fetching) {
			setTimeout( this.apiDataFetch.bind(this, data, path), 200 );
			return;
		}
		let obj = {};
		this.fetching = true;
		apiFetch( {
			path: '/dpt/v1/' + path,
		} )
		.then( ( items ) => {
			let itemsList = Object.keys(items);
			itemsList = itemsList.map(item => {
				return {
					label: items[item],
					value: item,
				};
			});
			obj[data] = itemsList;
			this.setState(obj);
			this.fetching = false;
		} )
		.catch( () => {
			obj[data] = [];
			this.setState(obj);
			this.fetching = false;
		} );
	}
	
	componentDidMount() {
		const {attributes} = this.props;
		const {postType} = attributes;
		this.apiDataFetch('postTypes', 'posttypes');
		if (postType) {
			if ('page' === postType) {
				this.getPagesList();
			} else {
				this.updateTaxonomy();
				this.updateTerms();
			}

			if ( this.isPro ) {
				this.apiDataFetch('customFields', 'customfields/' + postType);
			}
		}
		this.getStyleList();
	}

	componentDidUpdate( prevProps ) {
		const {
			postType: oldPostType,
			taxonomy: oldTaxonomy
		} = prevProps.attributes;
		const { postType, taxonomy } = this.props.attributes;

		if (oldPostType !== postType) {
			this.updateTaxonomy();
			if ( this.isPro ) {
				this.apiDataFetch('customFields', 'customfields/' + postType);
			}
			if ('page' === postType) { this.getPagesList() }
		}

		if (oldTaxonomy !== taxonomy) { this.updateTerms() }
	}

	updateTaxonomy() {
		const { attributes } = this.props;
		const { postType } = attributes;
		if (!postType || 'page' === postType) {
			this.setState( { taxonomies: [], termsList: [] } );
		} else {
			this.apiDataFetch('taxonomies', 'taxonomies/' + postType);
		}
	}

	updateTerms() {
		const { attributes } = this.props;
		const { taxonomy } = attributes;
		if (!taxonomy) {
			this.setState( { termsList: [] } );
		} else {
			this.apiDataFetch('termsList', 'terms/' + taxonomy);
		}
	}

	getPagesList() {
		this.apiDataFetch('pageList', 'pagelist');
	}

	getStyleList() {
		apiFetch( {
			path: '/dpt/v1/stylelist',
		} )
		.then( ( items ) => {
			const list = Object.keys(items);
			const styleList = list.map(item => {
				return {
					label: items[item]['label'],
					value: item,
				};
			});
			list.forEach(item => {
				this.styleSupport[item] = items[item]['support'];
			});
			this.setState( { styleList } );
		} )
		.catch( () => {
			this.setState( { styleList: [] } );
		} );
	}

	displayElems(context, args) {
		const {attributes, setAttributes} = this.props;
		const {styles, styleSup, showPgnation, autoTime} = attributes;
		if ('styleComponents' === context) {
			const {styleSupported, supCheckChange, getElement} = args;
			const supportedStyles = styleSupported(styles);
			return !! supportedStyles && supportedStyles.map( ( item ) => (
				<DptAccordion
					initialOpen={ false }
					title={ item.label }
					checkVal={item.value}
					checkArray={ styleSup }
					onItemChange={ supCheckChange }
					>
					{getElement(item)}
				</DptAccordion>
			) )
		}

		if ('itemWrapperOptions' === context) {
			const {ifStyleSupport} = args;
			return this.wrapperSets(ifStyleSupport);
		}

		if ('pagination' === context) {
			const {ifStyleSupport} = args;
			return (
				(styles && ifStyleSupport(styles, 'pagination')) &&
				<ToggleControl
					label={ __( 'Show Pagination.', 'display-post-types' ) }
					checked={ !! showPgnation }
					onChange={ ( showPgnation ) => setAttributes( { showPgnation } ) }
				/>
			)
		}

		if ('autotime' === context) {
			const {ifStyleSupport} = args;
			return (
				(styles && ifStyleSupport(styles, 'slider')) &&
				<RangeControl
					label={ __( 'Auto slide timer (delay in ms)', 'display-post-types' ) }
					value={ autoTime }
					onChange={ ( autoTime ) => setAttributes( { autoTime } ) }
					min={ 0 }
					max={10000}
					step={ 500 }
				/>
			)
		}
	}

	wrapperSets(ifStyleSupport) {
		const { attributes, setAttributes } = this.props;
		const {styles} = attributes;
		return (
			<div>
				<SelectControl
					label={ __( 'Wrapper Type', 'display-post-types' ) }
					value={ attributes['wrapper_type'] }
					onChange={ ( value ) => setAttributes( { ['wrapper_type']: value } ) }
					options={ [
						{ value: '', label: __( 'No Wrapper', 'display-post-types' ) },
						{ value: 'contained', label: __( 'Contained Wrap', 'display-post-types' ) },
						{ value: 'content', label: __( 'Content Wrap', 'display-post-types' ) },
					] }
				/>
				{
					'' !== attributes['wrapper_type'] &&
					<div>
						{
							(styles && ifStyleSupport(styles, 'overlay')) &&
							<RangeControl
								label={ __( 'Wrapper Width (in %)', 'display-post-types' ) }
								value={ attributes['wrapper_width'] }
								onChange={ ( value ) => setAttributes( { ['wrapper_width']: value } ) }
								min={ 0 }
								max={ 100 }
								step={ 1 }
							/>
						}
						{
							(styles && ifStyleSupport(styles, 'overlay') && 'content' === attributes['wrapper_type']) &&
							<RangeControl
								label={ __( 'Wrapper Height (in px)', 'display-post-types' ) }
								value={ attributes['wrapper_height'] }
								onChange={ ( value ) => setAttributes( { ['wrapper_height']: value } ) }
								min={ 0 }
								max={ 1000 }
								step={ 1 }
							/>
						}
						<DptFourSides
							fourSides={attributes['wrapper_padding']}
							label={__('Wrapper Padding (in px)', 'display-post-types')}
							onChange={ ( value ) => setAttributes( { ['wrapper_padding']: value } ) }
							max={200}
							hasDefault={false}
						/>
						<DptFourSides
							fourSides={attributes['wrapper_br_width']}
							label={__('Wrapper Border Width (in px)', 'display-post-types')}
							onChange={ ( value ) => setAttributes( { ['wrapper_br_width']: value } ) }
							max={10}
							hasDefault={false}
						/>
						<DptFourSides
							fourSides={attributes['wrapper_br_radius']}
							label={__('Wrapper Border Radius (in px)', 'display-post-types')}
							onChange={ ( value ) => setAttributes( { ['wrapper_br_radius']: value } ) }
							max={50}
							hasDefault={false}
						/>
						<PanelColorSettings
							title={ __( 'Color Scheme', 'display-post-types' ) }
							initialOpen={ false }
							colorSettings={ [
								{
									label: __( 'Wrapper Border Color', 'display-post-types' ),
									value: attributes['wrapper_br_color'],
									onChange: ( value ) => setAttributes( { ['wrapper_br_color']: value } ),
									enableAlpha: true,
								},
							] }
						>
						</PanelColorSettings>
					</div>
				}
			</div>
		)
	}

	render() {
		const { postTypes, taxonomies, pageList, termsList, styleList, customFields } = this.state;
		const { attributes, setAttributes } = this.props;
		const {
			title,
			postType,
			taxonomy,
			terms,
			relation,
			postIds,
			pages,
			number,
			orderBy,
			order,
			styles,
			styleSup,
			thumbFetch,
			imageCrop,
			imgAspect,
			customAspect,
			imgAlign,
			brRadius,
			colNarr,
			plHolder,
			titleShadow,
			textAlign,
			vGutter,
			hGutter,
			eLength,
			eTeaser,
			offset,
			meta1,
			meta2,
			textPosHor,
			textPosVer,
		} = attributes;
		const onChangePostType = value => {
			setAttributes({ terms: [] });
			setAttributes({ taxonomy: '' });
			setAttributes({ postType: value });
		};
		const onChangeTaxonomy = value => {
			setAttributes({ terms: [] });
			setAttributes({ taxonomy: value });
		};
		const styleSupported = (style) => {
			const all = [
				{ value: 'thumbnail', label: __( 'Thumbnail', 'display-post-types' ) },
				{ value: 'meta', label: __( 'Meta Info 1', 'display-post-types' ) },
				{ value: 'title', label: __( 'Title', 'display-post-types' ) },
				{ value: 'excerpt', label: __( 'Excerpt', 'display-post-types' ) },
				{ value: 'category', label: __( 'Meta info 2', 'display-post-types' ) },
				{ value: 'date', label: __( 'Date', 'display-post-types' ) },
				{ value: 'ago', label: __( 'Ago', 'display-post-types' ) },
				{ value: 'author', label: __( 'Author', 'display-post-types' ) },
				{ value: 'content', label: __( 'Content', 'display-post-types' ) },
			];
			const supported = this.styleSupport[style];
			if ( 'undefined' === typeof supported ) return [];
			return all.filter(sup => {
				if ('category' !== sup.value) {
					return supported.includes(sup.value);
				} else {
					return ( supported.includes(sup.value) && 'post' === postType );
				}
			});
		};
		const ifStyleSupport = (style, item) => {
			const supported = this.styleSupport[style];
			if ( 'undefined' === typeof supported ) return false;
			return supported.includes(item);
		}
		const termCheckChange = (value) => {
			const index = terms.indexOf(value);
			if (-1 === index) {
				setAttributes({ terms: [...terms, value] });
			} else {
				setAttributes({ terms: terms.filter(term => term !== value) });
			}
		};
		const onStyleChange = (value) => {
			const styleSupDefaults = {
				'dpt-list1': ['thumbnail', 'title', 'meta', 'excerpt'],
				'dpt-list2': ['thumbnail', 'title', 'meta'],
				'dpt-pro-slider2': ['thumbnail', 'title', 'meta', 'excerpt'],
				'dpt-mag1': ['thumbnail', 'title', 'meta'],
			};
			const defaultThumbCropStyles = ['dpt-list1', 'dpt-list2', 'dpt-slider1', 'dpt-mag1'];
			setAttributes({ styles: value });
			if ('undefined' !== typeof styleSupDefaults[value]) {
				setAttributes({ styleSup: styleSupDefaults[value] });
			} else {
				setAttributes({ styleSup: ['thumbnail', 'title'] });
			}
			if (defaultThumbCropStyles.includes(value)) {
				setAttributes({ imgAspect: 'land1' });
			} else if (['dpt-pro-slider1', 'dpt-pro-slider3'].includes(value)) {
				setAttributes({ imgAspect: 'wdscrn' });
			} else if (['dpt-slider2'].includes(value)) {
				setAttributes({ imgAspect: 'squr' });
			} else {
				setAttributes({ imgAspect: '' });
			}
		};
		const pageCheckChange = (value) => {
			const index = pages.indexOf(value);
			if (-1 === index) {
				setAttributes({ pages: [...pages, value] });
			} else {
				setAttributes({ pages: pages.filter(page => page !== value) });
			}
		};
		const supCheckChange = (value) => {
			const index = styleSup.indexOf(value);
			if (-1 === index) {
				setAttributes({styleSup: [...styleSup, value]});
			} else {
				setAttributes({styleSup: styleSup.filter(sup => sup !== value)});
			}
		};
		const onSortEnd = (sortedKeys) => {
			if (sortedKeys) {
				const sortedStyleSup = [...styleSup].sort((a, b) => sortedKeys.indexOf(a) - sortedKeys.indexOf(b));
				setAttributes({ styleSup: sortedStyleSup });
			}
		}
		const orderbyOptions = this.isPro ? [
			{ value: 'date', label: __( 'Publish Date', 'display-post-types' ) },
			{ value: 'modified', label: __( 'Modified Date', 'display-post-types' ) },
			{ value: 'title', label: __( 'Title', 'display-post-types' ) },
			{ value: 'author', label: __( 'Author', 'display-post-types' ) },
			{ value: 'comment_count', label: __( 'Comment Count', 'display-post-types' ) },
			{ value: 'rand', label: __( 'Random', 'display-post-types' ) },
			{ value: 'custom', label: __( 'Custom Fields', 'display-post-types' ) },
		] : [
			{ value: 'date', label: __( 'Publish Date', 'display-post-types' ) },
			{ value: 'modified', label: __( 'Modified Date', 'display-post-types' ) },
			{ value: 'title', label: __( 'Title', 'display-post-types' ) },
			{ value: 'author', label: __( 'Author', 'display-post-types' ) },
			{ value: 'comment_count', label: __( 'Comment Count', 'display-post-types' ) },
			{ value: 'rand', label: __( 'Random', 'display-post-types' ) },
		];
		const aspectOptions = [
			{ value: '', label: __( 'No Cropping', 'display-post-types' ) },
			{ value: 'land1', label: __( 'Landscape (4:3)', 'display-post-types' ) },
			{ value: 'land2', label: __( 'Landscape (3:2)', 'display-post-types' ) },
			{ value: 'port1', label: __( 'Portrait (3:4)', 'display-post-types' ) },
			{ value: 'port2', label: __( 'Portrait (2:3)', 'display-post-types' ) },
			{ value: 'wdscrn', label: __( 'Widescreen (16:9)', 'display-post-types' ) },
			{ value: 'squr', label: __( 'Square (1:1)', 'display-post-types' ) },
			{ value: 'custom', label: __( 'Custom Aspect Ratio', 'display-post-types' ) },
		];
		const cropOptions = [
			{ value: 'topleftcrop', label: __( 'Top Left Cropping', 'display-post-types' ) },
			{ value: 'topcentercrop', label: __( 'Top Center Cropping', 'display-post-types' ) },
			{ value: 'centercrop', label: __( 'Center Cropping', 'display-post-types' ) },
			{ value: 'bottomcentercrop', label: __( 'Bottom Center Cropping', 'display-post-types' ) },
			{ value: 'bottomleftcrop', label: __( 'Bottom Left Cropping', 'display-post-types' ) },
		];

		const repeatbleGeneral = (elem) => {
			const { attributes, setAttributes } = this.props;
			return (
				<div>
					<TextControl
						label={ __( 'Custom Class Name', 'display-post-types' ) }
						value={ attributes[elem + '_class'] }
						onChange={ ( value ) => setAttributes( { [elem + '_class']: value } ) }
						help={ __( 'Add custom HTML classes to this element.', 'display-post-types' ) }
					/>
				</div>
			)
		}

		const repeatbleTypography = (elem) => {
			const { attributes, setAttributes } = this.props;
			return (
				<div>
					<SelectControl
                        label={ __( 'Font Style', 'display-post-types' ) }
                    	value={ attributes[elem + '_font_style'] }
                        onChange={ ( value ) => setAttributes( { [elem + '_font_style']: value } ) }
                        options={ [
                            { value: 'normal', label: __( 'Normal', 'display-post-types' ) },
                            { value: 'italic', label: __( 'Italic', 'display-post-types' ) },
                            { value: 'oblique', label: __( 'Oblique', 'display-post-types' ) },
                        ] }
                    />
					<RangeControl
						label={ __( 'Font Weight', 'display-post-types' ) }
						value={ attributes[elem + '_font_weight'] }
						onChange={ ( value ) => setAttributes( { [elem + '_font_weight']: value } ) }
						min={ 100 }
						max={ 900 }
						step={ 100 }
					/>
					<RangeControl
						label={ __( 'Font Size (in px)', 'display-post-types' ) }
						value={ attributes[elem + '_font_size'] }
						onChange={ ( value ) => setAttributes( { [elem + '_font_size']: value } ) }
						min={ 0 }
						max={ 100 }
						step={ 1 }
					/>
					<RangeControl
						label={ __( 'Line Height', 'display-post-types' ) }
						value={ attributes[elem + '_line_height'] }
						onChange={ ( value ) => setAttributes( { [elem + '_line_height']: value } ) }
						min={ 1 }
						max={ 5 }
						step={ 0.1 }
					/>
					<RangeControl
						label={ __( 'Letter Spacing (in em)', 'display-post-types' ) }
						value={ attributes[elem + '_letter_spacing'] }
						onChange={ ( value ) => setAttributes( { [elem + '_letter_spacing']: value } ) }
						min={ -1 }
						max={ 1 }
						step={ 0.01 }
					/>
					<SelectControl
						label={ __( 'Text Transform', 'display-post-types' ) }
						value={ attributes[elem + '_text_transform'] }
						onChange={ ( value ) => setAttributes( { [elem + '_text_transform']: value } ) }
						options={ [
							{ value: 'none', label: __( 'None', 'display-post-types' ) },
							{ value: 'uppercase', label: __( 'Uppercase', 'display-post-types' ) },
							{ value: 'lowercase', label: __( 'Lowercase', 'display-post-types' ) },
							{ value: 'capitalize', label: __( 'Capitalize', 'display-post-types' ) },
						] }
					/>
					<SelectControl
						label={ __( 'Text Decoration', 'display-post-types' ) }
						value={ attributes[elem + '_text_decoration'] }
						onChange={ ( value ) => setAttributes( { [elem + '_text_decoration']: value } ) }
						options={ [
							{ value: 'none', label: __( 'None', 'display-post-types' ) },
							{ value: 'underline', label: __( 'Underline', 'display-post-types' ) },
							{ value: 'overline', label: __( 'Overline', 'display-post-types' ) },
							{ value: 'line-through', label: __( 'Line Through', 'display-post-types' ) },
						] }
					/>
					<PanelColorSettings
						title={ __( 'Color Scheme', 'display-post-types' ) }
						initialOpen={ false }
						colorSettings={ [
							{
								label: __( 'Text Color', 'display-post-types' ),
								value: attributes[elem + '_text_color'],
								onChange: ( value ) => setAttributes( { [elem + '_text_color']: value } ),
								enableAlpha: true,
							},
							{
								label: __( 'Link Color', 'display-post-types' ),
								value: attributes[elem + '_link_color'],
								onChange: ( value ) => setAttributes( { [elem + '_link_color']: value } ),
								enableAlpha: true,
							},
							{
								label: __( 'Link Hover Color', 'display-post-types' ),
								value: attributes[elem + '_link_hover_color'],
								onChange: ( value ) => setAttributes( { [elem + '_link_hover_color']: value } ),
								enableAlpha: true,
							},
						] }
					>
					</PanelColorSettings>
				</div>
			)
		}

		const getElement = (iElem) => {
			switch(iElem.value) {
				case 'thumbnail':
					return getThumbnail();
				case 'title':
					return getTitle();
				case 'excerpt':
					return getExcerpt();
				case 'content':
					return getContent();
				case 'meta':
					return getMeta1();
				case 'category':
					return getMeta2();
				default:
					return '';
			}
		};

		const getThumbnail = () => {
			return (
				<div>
					<ToggleControl
						label={ __( 'Fetch Thumbnail from Content as fallback.', 'display-post-types' ) }
						checked={ !! thumbFetch }
						onChange={ ( thumbFetch ) => setAttributes( { thumbFetch } ) }
					/>
					<ToggleControl
						label={ __( 'Thumbnail Placeholder', 'display-post-types' ) }
						checked={ !! plHolder }
						onChange={ ( plHolder ) => setAttributes( { plHolder } ) }
					/>
					<RangeControl
						label={ __( 'Thumbnail Border Radius (in px)', 'display-post-types' ) }
						value={ brRadius }
						onChange={ ( brRadius ) => setAttributes( { brRadius } ) }
						min={ 0 }
						max={ 100 }
					/>
					<SelectControl
						label={ __( 'Image Cropping', 'display-post-types' ) }
						value={ imgAspect }
						onChange={ ( imgAspect ) => setAttributes( { imgAspect } ) }
						options={ aspectOptions }
					/>
					{
						'custom' === imgAspect &&
						<RangeControl
							label={ __( 'Thumbnail custom crop aspect ratio', 'display-post-types' ) }
							value={ customAspect }
							onChange={ ( customAspect ) => setAttributes( { customAspect } ) }
							min={ 0 }
							max={ 300 }
						/>
					}
					{
						'' !== imgAspect &&
						<SelectControl
							label={ __( 'Image Cropping Position', 'display-post-types' ) }
							value={ imageCrop }
							onChange={ ( imageCrop ) => setAttributes( { imageCrop } ) }
							options={ cropOptions }
						/>
					}
					{
						(styles && ifStyleSupport(styles, 'ialign')) &&
						<SelectControl
							label={ __( 'Image Alignment', 'display-post-types' ) }
							value={ imgAlign }
							onChange={ ( imgAlign ) => setAttributes( { imgAlign } ) }
							options={ [
								{ value: '', label: __( 'Left Aligned', 'display-post-types' ) },
								{ value: 'right', label: __( 'Right Aligned', 'display-post-types' ) },
							] }
						/>
					}
					{repeatbleGeneral('thumbnail')}
				</div>
			);
		};
		
		const getTitle = () => {
			return (
				<Tabs initialTab={ 'titlegeneral' }>
					<TabList>
						<Tab
							id={ 'titlegeneral' }
							icon={<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M10.825 22q-.675 0-1.162-.45t-.588-1.1L8.85 18.8q-.325-.125-.612-.3t-.563-.375l-1.55.65q-.625.275-1.25.05t-.975-.8l-1.175-2.05q-.35-.575-.2-1.225t.675-1.075l1.325-1Q4.5 12.5 4.5 12.337v-.675q0-.162.025-.337l-1.325-1Q2.675 9.9 2.525 9.25t.2-1.225L3.9 5.975q.35-.575.975-.8t1.25.05l1.55.65q.275-.2.575-.375t.6-.3l.225-1.65q.1-.65.588-1.1T10.825 2h2.35q.675 0 1.163.45t.587 1.1l.225 1.65q.325.125.613.3t.562.375l1.55-.65q.625-.275 1.25-.05t.975.8l1.175 2.05q.35.575.2 1.225t-.675 1.075l-1.325 1q.025.175.025.338v.674q0 .163-.05.338l1.325 1q.525.425.675 1.075t-.2 1.225l-1.2 2.05q-.35.575-.975.8t-1.25-.05l-1.5-.65q-.275.2-.575.375t-.6.3l-.225 1.65q-.1.65-.587 1.1t-1.163.45zM11 20h1.975l.35-2.65q.775-.2 1.438-.587t1.212-.938l2.475 1.025l.975-1.7l-2.15-1.625q.125-.35.175-.737T17.5 12t-.05-.787t-.175-.738l2.15-1.625l-.975-1.7l-2.475 1.05q-.55-.575-1.212-.962t-1.438-.588L13 4h-1.975l-.35 2.65q-.775.2-1.437.588t-1.213.937L5.55 7.15l-.975 1.7l2.15 1.6q-.125.375-.175.75t-.05.8q0 .4.05.775t.175.75l-2.15 1.625l.975 1.7l2.475-1.05q.55.575 1.213.963t1.437.587zm1.05-4.5q1.45 0 2.475-1.025T15.55 12t-1.025-2.475T12.05 8.5q-1.475 0-2.487 1.025T8.55 12t1.013 2.475T12.05 15.5M12 12"/></svg>}
						/>
						<Tab
							id={ 'titletypography' }
							icon = {<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M11 22v-6h2v2h8v2h-8v2zm-8-2v-2h6v2zm3.425-6H8.5l1.1-3.075h4.825L15.5 14h2.075l-4.5-12h-2.15zM10.2 9.2l1.75-4.975h.1L13.8 9.2z"/></svg>}
						/>
					</TabList>
					<TabPanel id={ 'titlegeneral' }>
						{
							(styles && ifStyleSupport(styles, 'overlay')) &&
							<ToggleControl
								label={ __( 'Show Title Shadow', 'display-post-types' ) }
								checked={ !! titleShadow }
								onChange={ ( titleShadow ) => setAttributes( { titleShadow } ) }
							/>
						}
						{repeatbleGeneral('title')}
					</TabPanel>
					<TabPanel id={ 'titletypography' }>{repeatbleTypography('title')}</TabPanel>
				</Tabs>
			);
		};
		
		const getExcerpt = () => {
			return (
				<div>
					{
						<Tabs initialTab={ 'excerptgeneral' }>
							<TabList>
								<Tab
									id={ 'excerptgeneral' }
									icon={<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M10.825 22q-.675 0-1.162-.45t-.588-1.1L8.85 18.8q-.325-.125-.612-.3t-.563-.375l-1.55.65q-.625.275-1.25.05t-.975-.8l-1.175-2.05q-.35-.575-.2-1.225t.675-1.075l1.325-1Q4.5 12.5 4.5 12.337v-.675q0-.162.025-.337l-1.325-1Q2.675 9.9 2.525 9.25t.2-1.225L3.9 5.975q.35-.575.975-.8t1.25.05l1.55.65q.275-.2.575-.375t.6-.3l.225-1.65q.1-.65.588-1.1T10.825 2h2.35q.675 0 1.163.45t.587 1.1l.225 1.65q.325.125.613.3t.562.375l1.55-.65q.625-.275 1.25-.05t.975.8l1.175 2.05q.35.575.2 1.225t-.675 1.075l-1.325 1q.025.175.025.338v.674q0 .163-.05.338l1.325 1q.525.425.675 1.075t-.2 1.225l-1.2 2.05q-.35.575-.975.8t-1.25-.05l-1.5-.65q-.275.2-.575.375t-.6.3l-.225 1.65q-.1.65-.587 1.1t-1.163.45zM11 20h1.975l.35-2.65q.775-.2 1.438-.587t1.212-.938l2.475 1.025l.975-1.7l-2.15-1.625q.125-.35.175-.737T17.5 12t-.05-.787t-.175-.738l2.15-1.625l-.975-1.7l-2.475 1.05q-.55-.575-1.212-.962t-1.438-.588L13 4h-1.975l-.35 2.65q-.775.2-1.437.588t-1.213.937L5.55 7.15l-.975 1.7l2.15 1.6q-.125.375-.175.75t-.05.8q0 .4.05.775t.175.75l-2.15 1.625l.975 1.7l2.475-1.05q.55.575 1.213.963t1.437.587zm1.05-4.5q1.45 0 2.475-1.025T15.55 12t-1.025-2.475T12.05 8.5q-1.475 0-2.487 1.025T8.55 12t1.013 2.475T12.05 15.5M12 12"/></svg>}
								/>
								<Tab
									id={ 'excerpttypography' }
									icon = {<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M11 22v-6h2v2h8v2h-8v2zm-8-2v-2h6v2zm3.425-6H8.5l1.1-3.075h4.825L15.5 14h2.075l-4.5-12h-2.15zM10.2 9.2l1.75-4.975h.1L13.8 9.2z"/></svg>}
								/>
							</TabList>
							<TabPanel id={ 'excerptgeneral' }>
								<RangeControl
									label={ __( 'Excerpt Length (in words)', 'display-post-types' ) }
									value={ eLength }
									onChange={ ( eLength ) => setAttributes( { eLength } ) }
									min={ 0 }
								/>
								<TextControl
									label={ __( 'Excerpt Teaser Text', 'display-post-types' ) }
									value={ eTeaser }
									onChange={ ( eTeaser ) => setAttributes( { eTeaser } ) }
									help={ __( 'i.e., Continue Reading, Read More', 'display-post-types' ) }
								/>
								{repeatbleGeneral('excerpt')}
							</TabPanel>
							<TabPanel id={ 'excerpttypography' }>{repeatbleTypography('excerpt')}</TabPanel>
						</Tabs>
					}
				</div>
			);
		};
		
		const getContent = () => {
			return (
				<div>
					<ToggleControl
						label={ __( 'Show Full Content', 'display-post-types' ) }
						checked={ !! styleSup.includes('content') }
						onChange={ () => { supCheckChange('content') } }
					/>
				</div>
			);
		};
		
		const getMeta1 = () => {
			return (
				<div>
					<Tabs initialTab={ 'meta1general' }>
						<TabList>
							<Tab
								id={ 'meta1general' }
								icon={<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M10.825 22q-.675 0-1.162-.45t-.588-1.1L8.85 18.8q-.325-.125-.612-.3t-.563-.375l-1.55.65q-.625.275-1.25.05t-.975-.8l-1.175-2.05q-.35-.575-.2-1.225t.675-1.075l1.325-1Q4.5 12.5 4.5 12.337v-.675q0-.162.025-.337l-1.325-1Q2.675 9.9 2.525 9.25t.2-1.225L3.9 5.975q.35-.575.975-.8t1.25.05l1.55.65q.275-.2.575-.375t.6-.3l.225-1.65q.1-.65.588-1.1T10.825 2h2.35q.675 0 1.163.45t.587 1.1l.225 1.65q.325.125.613.3t.562.375l1.55-.65q.625-.275 1.25-.05t.975.8l1.175 2.05q.35.575.2 1.225t-.675 1.075l-1.325 1q.025.175.025.338v.674q0 .163-.05.338l1.325 1q.525.425.675 1.075t-.2 1.225l-1.2 2.05q-.35.575-.975.8t-1.25-.05l-1.5-.65q-.275.2-.575.375t-.6.3l-.225 1.65q-.1.65-.587 1.1t-1.163.45zM11 20h1.975l.35-2.65q.775-.2 1.438-.587t1.212-.938l2.475 1.025l.975-1.7l-2.15-1.625q.125-.35.175-.737T17.5 12t-.05-.787t-.175-.738l2.15-1.625l-.975-1.7l-2.475 1.05q-.55-.575-1.212-.962t-1.438-.588L13 4h-1.975l-.35 2.65q-.775.2-1.437.588t-1.213.937L5.55 7.15l-.975 1.7l2.15 1.6q-.125.375-.175.75t-.05.8q0 .4.05.775t.175.75l-2.15 1.625l.975 1.7l2.475-1.05q.55.575 1.213.963t1.437.587zm1.05-4.5q1.45 0 2.475-1.025T15.55 12t-1.025-2.475T12.05 8.5q-1.475 0-2.487 1.025T8.55 12t1.013 2.475T12.05 15.5M12 12"/></svg>}
							/>
							<Tab
								id={ 'meta1typography' }
								icon = {<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M11 22v-6h2v2h8v2h-8v2zm-8-2v-2h6v2zm3.425-6H8.5l1.1-3.075h4.825L15.5 14h2.075l-4.5-12h-2.15zM10.2 9.2l1.75-4.975h.1L13.8 9.2z"/></svg>}
							/>
						</TabList>
						<TabPanel id={ 'meta1general' }>
							<TextControl
								label={ __( 'Meta Info to be displayed', 'display-post-types' ) }
								value={ meta1 }
								onChange={ ( meta1 ) => setAttributes( { meta1 } ) }
							/>
							{repeatbleGeneral('meta1')}
						</TabPanel>
						<TabPanel id={ 'meta1typography' }>{repeatbleTypography('meta1')}</TabPanel>
					</Tabs>
				</div>
			);
		};
		
		const getMeta2 = () => {
			return (
				<div>
					<Tabs initialTab={ 'meta2general' }>
						<TabList>
							<Tab
								id={ 'meta2general' }
								icon={<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M10.825 22q-.675 0-1.162-.45t-.588-1.1L8.85 18.8q-.325-.125-.612-.3t-.563-.375l-1.55.65q-.625.275-1.25.05t-.975-.8l-1.175-2.05q-.35-.575-.2-1.225t.675-1.075l1.325-1Q4.5 12.5 4.5 12.337v-.675q0-.162.025-.337l-1.325-1Q2.675 9.9 2.525 9.25t.2-1.225L3.9 5.975q.35-.575.975-.8t1.25.05l1.55.65q.275-.2.575-.375t.6-.3l.225-1.65q.1-.65.588-1.1T10.825 2h2.35q.675 0 1.163.45t.587 1.1l.225 1.65q.325.125.613.3t.562.375l1.55-.65q.625-.275 1.25-.05t.975.8l1.175 2.05q.35.575.2 1.225t-.675 1.075l-1.325 1q.025.175.025.338v.674q0 .163-.05.338l1.325 1q.525.425.675 1.075t-.2 1.225l-1.2 2.05q-.35.575-.975.8t-1.25-.05l-1.5-.65q-.275.2-.575.375t-.6.3l-.225 1.65q-.1.65-.587 1.1t-1.163.45zM11 20h1.975l.35-2.65q.775-.2 1.438-.587t1.212-.938l2.475 1.025l.975-1.7l-2.15-1.625q.125-.35.175-.737T17.5 12t-.05-.787t-.175-.738l2.15-1.625l-.975-1.7l-2.475 1.05q-.55-.575-1.212-.962t-1.438-.588L13 4h-1.975l-.35 2.65q-.775.2-1.437.588t-1.213.937L5.55 7.15l-.975 1.7l2.15 1.6q-.125.375-.175.75t-.05.8q0 .4.05.775t.175.75l-2.15 1.625l.975 1.7l2.475-1.05q.55.575 1.213.963t1.437.587zm1.05-4.5q1.45 0 2.475-1.025T15.55 12t-1.025-2.475T12.05 8.5q-1.475 0-2.487 1.025T8.55 12t1.013 2.475T12.05 15.5M12 12"/></svg>}
							/>
							<Tab
								id={ 'meta2typography' }
								icon = {<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M11 22v-6h2v2h8v2h-8v2zm-8-2v-2h6v2zm3.425-6H8.5l1.1-3.075h4.825L15.5 14h2.075l-4.5-12h-2.15zM10.2 9.2l1.75-4.975h.1L13.8 9.2z"/></svg>}
							/>
						</TabList>
						<TabPanel id={ 'meta2general' }>
							<TextControl
								label={ __( 'Meta Info to be displayed', 'display-post-types' ) }
								value={ meta2 }
								onChange={ ( meta2 ) => setAttributes( { meta2 } ) }
							/>
							{repeatbleGeneral('meta2')}
						</TabPanel>
						<TabPanel id={ 'meta2typography' }>{repeatbleTypography('meta2')}</TabPanel>
					</Tabs>
				</div>
			);
		};

		return (
			<Fragment>
				<InspectorControls>
					<DptAccordion title={ __( 'Setup Display Post Types', 'display-post-types' ) } initialOpen={ true }>
						<TextControl
							label={ __( 'Title (Optional)', 'display-post-types' ) }
							value={ title }
							onChange={ ( title ) => setAttributes( { title } ) }
						/>
						{
							postTypes &&
							<SelectControl
								label={ __( 'Select a Post Type', 'display-post-types' ) }
								value={ postType }
								options={ postTypes }
								onChange={ (value) => onChangePostType(value) }
							/>
						}
						{
							('page' === postType && !! pageList.length) &&
							<MultipleCheckboxControl
								listItems={ pageList }
								selected={ pages }
								onItemChange={ pageCheckChange }
								label = { __( 'Select Pages', 'display-post-types' ) }
							/>
						}
						<RangeControl
							label={ __( 'Number of items to display', 'display-post-types' ) }
							value={ number }
							onChange={ ( number ) => setAttributes( { number } ) }
							min={ 1 }
						/>
						{
							'page' !== postType &&
							<RangeControl
								label={ __( 'Offset (Starting Item Number)', 'display-post-types' ) }
								value={ offset }
								onChange={ ( offset ) => setAttributes( { offset } ) }
								min={ 0 }
							/>
						}
						{this.displayElems('pagination', {ifStyleSupport})}
					</DptAccordion>
					<DptAccordion initialOpen={ false } title={ __( 'Sort & Filter Items', 'display-post-types' ) }>
						{
							'page' !== postType &&
							<DptAccordion initialOpen={ false } title={ __( 'Filter By Post IDs', 'display-post-types' ) }>
								<TextControl
									label={ __( 'Filter items by Post IDs (optional)', 'display-post-types' ) }
									value={ postIds }
									onChange={ ( postIds ) => setAttributes( { postIds } ) }
									help={ __( 'Comma separated ids, i.e. 230,300', 'display-post-types' ) }
								/>
							</DptAccordion>
						}
						{
							'page' !== postType &&
							<DptAccordion initialOpen={ false } title={ __( 'Filter By Taxonomy', 'display-post-types' ) }>
								{
									!! taxonomies.length &&
									<SelectControl
										label={ __( 'Get items by Taxonomy', 'display-post-types' ) }
										value={ taxonomy }
										options={ taxonomies }
										onChange={ ( value ) => onChangeTaxonomy(value) }
									/>
								}
								{
									!! termsList.length &&
									<MultipleCheckboxControl
										listItems={ termsList }
										selected={ terms }
										onItemChange={ termCheckChange }
										label = { __( 'Select Taxonomy Terms', 'display-post-types' ) }
									/>
								}
								{
									!! termsList.length &&
									<SelectControl
										label={ __( 'Terms Relationship', 'display-post-types' ) }
										value={ relation }
										onChange={ ( relation ) => setAttributes( { relation } ) }
										options={ [
											{ value: 'IN', label: __( 'OR - Show posts from any of the terms selected above.', 'display-post-types' ) },
											{ value: 'AND', label: __( 'AND - Show posts only if they belong to all of the selected terms.', 'display-post-types' ) },
										] }
									/>
								}
							</DptAccordion>
						}
						{
							!! this.isPro &&
							<DptAccordion initialOpen={ false } title={ __( 'Filter By Custom Fields', 'display-post-types' ) }>
								{this.displayElems('filterCustomFields', {ifStyleSupport, customFields})}
							</DptAccordion>
						}
						{
							'page' !== postType &&
							<DptAccordion initialOpen={ false } title={ __( 'Sort Items', 'display-post-types' ) }>
								<SelectControl
									label={ __( 'Sort By', 'display-post-types' ) }
									value={ orderBy }
									onChange={ ( orderBy ) => setAttributes( { orderBy } ) }
									options={ orderbyOptions }
								/>
								<SelectControl
									label={ __( 'Sort Order', 'display-post-types' ) }
									value={ order }
									onChange={ ( order ) => setAttributes( { order } ) }
									options={ [
										{ value: 'DESC', label: __( 'Descending', 'display-post-types' ) },
										{ value: 'ASC', label: __( 'Ascending', 'display-post-types' ) },
									] }
								/>
								{
									!! this.isPro && 'custom' === orderBy &&
									<DptAccordion initialOpen={ false } title={ __( 'Sort By Custom Fields', 'display-post-types' ) }>
										{this.displayElems('sortCustomFields', {ifStyleSupport, customFields})}
									</DptAccordion>
								}
							</DptAccordion>
						}
					</DptAccordion>
					<DptAccordion initialOpen={ false } title={ __( 'Layout & Styling', 'display-post-types' ) }>
						{
							!! styleList.length &&
							<SelectControl
								label={ __( 'Display Style', 'display-post-types' ) }
								value={ styles }
								onChange={ onStyleChange }
								options={ styleList }
							/>
						}
						{
							(styles && ifStyleSupport(styles, 'multicol')) &&
							<RangeControl
								label={ __( 'Maximum grid columns (Responsive)', 'display-post-types' ) }
								value={ colNarr }
								onChange={ ( colNarr ) => setAttributes( { colNarr } ) }
								min={ 1 }
								max={ 8 }
							/>
						}
						<RangeControl
							label={ __( 'Horizontal Gutter (in px)', 'display-post-types' ) }
							value={ hGutter }
							onChange={ ( hGutter ) => setAttributes( { hGutter } ) }
							min={ 0 }
							max={ 100 }
						/>
						<RangeControl
							label={ __( 'Vertical Gutter (in px)', 'display-post-types' ) }
							value={ vGutter }
							onChange={ ( vGutter ) => setAttributes( { vGutter } ) }
							min={ 0 }
							max={ 100 }
						/>
						{
							this.displayElems('autotime', {ifStyleSupport})
						}
					</DptAccordion>
					<DptAccordion initialOpen={ false } title={ __( 'Manage Item Components', 'display-post-types' ) }>
						{
							!! this.isPro && !! title &&
							<DptAccordion initialOpen={ false } title={ __( 'Header', 'display-post-types' ) }>
								{this.displayElems('itemHeaderOptions', {ifStyleSupport})}
							</DptAccordion>
						}
						<DptAccordion initialOpen={ false } title={ __( 'Item Wrapper', 'display-post-types' ) }>
							<SelectControl
								label={ __( 'Text Align', 'display-post-types' ) }
								value={ textAlign }
								onChange={ ( textAlign ) => setAttributes( { textAlign } ) }
								options={ [
									{ value: '', label: __( 'Left Align', 'display-post-types' ) },
									{ value: 'r-text', label: __( 'Right Align', 'display-post-types' ) },
									{ value: 'c-text', label: __( 'Center Align', 'display-post-types' ) },
								] }
							/>
							{
								(styles && ifStyleSupport(styles, 'overlay')) &&
								<SelectControl
									label={ __( 'Horizontal Text Position on Image', 'display-post-types' ) }
									value={ textPosHor }
									onChange={ ( textPosHor ) => setAttributes( { textPosHor } ) }
									options={ [
										{ value: '', label: __( 'Left', 'display-post-types' ) },
										{ value: 'right', label: __( 'Right', 'display-post-types' ) },
										{ value: 'center', label: __( 'Center', 'display-post-types' ) },
									] }
								/>
							}
							{
								(styles && ifStyleSupport(styles, 'overlay')) &&
								<SelectControl
									label={ __( 'Vertical Text Position on Image', 'display-post-types' ) }
									value={ textPosVer }
									onChange={ ( textPosVer ) => setAttributes( { textPosVer } ) }
									options={ [
										{ value: '', label: __( 'Bottom', 'display-post-types' ) },
										{ value: 'top', label: __( 'Top', 'display-post-types' ) },
										{ value: 'middle', label: __( 'Middle', 'display-post-types' ) },
									] }
								/>
							}
							{this.displayElems('itemWrapperOptions', {ifStyleSupport})}
						</DptAccordion>
						<div className="dpt-elem-container">
						{
							!! styleList.length &&
							this.displayElems('styleComponents', {styleSupported, supCheckChange, getElement, onSortEnd, ifStyleSupport})
						}
						</div>
					</DptAccordion>
				</InspectorControls>
				<Disabled>
					<div className="dpt-container" ref={this.elemRef}>
						<ServerSideRender
							block="dpt/display-post-types"
							attributes={ this.props.attributes }
						/>
					</div>
				</Disabled>
			</Fragment>
		);
	}

}

export default DisplayPostTypes;
