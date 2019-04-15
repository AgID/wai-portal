import PendingWebsiteCheck from './checkWebsiteTracking';

// Legacy script to be removed
require('./auto-complete.min');

window.datatablesPostInit = (json) => {
  json.data.map((website) => {
    website.actions.map((action) => {
      if (action.type === 'check_tracking') {
        PendingWebsiteCheck.initWebsiteCheckButton(document.querySelector('a[href="' + action.link + '"]'));
      }
    });
  });
}
