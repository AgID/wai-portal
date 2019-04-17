/**
 * Main javascript file.
 */

import './bootstrap';
import Datatables from './datatables';
import CheckWebsiteTracking from './checkWebsiteTracking';

$(document).ready(() => {
    Datatables.init([
        (settings, json) => CheckWebsiteTracking.initWebsiteCheckButton(json)
    ]);
});

// Legacy script to be removed
require('./auto-complete.min');
