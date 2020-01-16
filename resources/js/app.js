/**
 * Main javascript file.
 */

import './bootstrap';
import Forms from './forms';
import Datatables from './datatables';
import LogsDatatables from './logsDatatables'
import WebsiteCheckTracking from './buttons/websiteCheckTracking';
import PermissionsToggles from './permissionsToggles';
import WebsiteArchiveUnarchive from './buttons/websiteArchiveUnarchive';
import UserSuspendReactivate from './buttons/userSuspendReactivate';
import UserDeleteRestore from './buttons/userDeleteRestore';
import WebsiteDeleteRestore from './buttons/websiteDeleteRestore';
import UserVerificationResend from './buttons/userVerificationResend';
import GetJavascriptSnippet from './getJavascriptSnippet';
import SearchIpa from './searchIpa';
import Notification from './notification';
import PublicAdministrationSelector from './publicAdministrationSelector';
import FaqSelector from './faqSelector';
import WidgetResizer from './widgets';
import HighlightBar from './highlightBar';

$(document).ready(() => {
    Forms.init();
    SearchIpa.init();
    Notification.init();
    GetJavascriptSnippet.init();
    WebsiteCheckTracking.init();
    WebsiteArchiveUnarchive.init();
    UserVerificationResend.init();
    PublicAdministrationSelector.init();
    FaqSelector.init();
    WidgetResizer.init();
    HighlightBar.init();
    Datatables.init([
        datatableApi => LogsDatatables.preDatatableInit(datatableApi),
    ], [
        () => WebsiteCheckTracking.init(),
        () => PermissionsToggles.init(),
        () => WebsiteArchiveUnarchive.init(),
        () => WebsiteDeleteRestore.init(),
        () => UserSuspendReactivate.init(),
        () => UserDeleteRestore.init(),
    ]);
});
