import DPTPro from '../../dpt-pro/assets/js/partials/front/dptpro.js';
import ChangeDetect from './partials/admin/changedetect.js';
import ColorPicker from './partials/admin/colorpicker.js';

jQuery(function() {
	new ChangeDetect();
	new ColorPicker();
	new DPTPro();
});