// Legacy script to be removed
import { check_tracking } from './checkWebsiteTracking'

require('./auto-complete.min');

import './checkWebsiteTracking';

function datatablesPostInit (json) {
  json.actions.forEach(function (action) {
    if (action.type === 'check_tracking') {
      check_tracking(document.querySelector('a[href="' + action.link +']'));
    }
  });
}
