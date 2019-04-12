// Legacy script to be removed
require('./auto-complete.min');

import * as PendingWebsiteCheck from './checkWebsiteTracking';

window.datatablesPostInit = function (json) {
  json.data.forEach(function (website) {
    website.actions.forEach(function (action) {
      if (action.type === 'check_tracking') {
        PendingWebsiteCheck.initWebsiteCheckButton(document.querySelector('a[href="' + action.link + '"]'));
      }
    });
  });
}
