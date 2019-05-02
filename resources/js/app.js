/**
 * Main javascript file.
 */

import './bootstrap';
import Datatables from './datatables';
import CheckWebsiteTracking from './checkWebsiteTracking';
import UserWebsitesPermissions from './userWebsitesPermissions';

$(document).ready(() => {
    Datatables.init([
        () => CheckWebsiteTracking.initWebsiteCheckButton(),
        () => UserWebsitesPermissions.initPermissionInputs()
    ]);
});

// Legacy script to be removed
require('./auto-complete.min');
