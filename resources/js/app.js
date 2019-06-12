/**
 * Main javascript file.
 */

import './bootstrap';
import Datatables from './datatables';
import LogsDatatables from './logsDatatables'
import CheckWebsiteTracking from './checkWebsiteTracking';
import UserWebsitesPermissions from './userWebsitesPermissions';
import ChangeArchiveStatus from './changeArchiveStatus';

$(document).ready(() => {
    Datatables.init(
        [
            (datatableApi) => LogsDatatables.preDatatableInit(datatableApi),
        ],
        [
            () => CheckWebsiteTracking.initWebsiteCheckButton(),
            () => UserWebsitesPermissions.initPermissionInputs(),
            () => ChangeArchiveStatus.initArchiveStatusButton(),
        ]
    );
});

// Legacy script to be removed
require('./auto-complete.min');
