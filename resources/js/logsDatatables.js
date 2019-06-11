export default (() => {
    let filters = {};
    
    let filter;
    
    const initMessageFilter = ($message) => {
        if (!$message) {
            return;
        }
        
        let useAsFilter = () => {
            let length = $message.value.trim().length;
            return length === 0 || length >= 3;
        }
        
        $message.addEventListener('input', (event) => {
            if (!useAsFilter()) {
                event.preventDefault();
                filters.message = '';
                return;
            }
            filters.message = $message.value.trim();
            filter && filter();
        });
    }
    
    const initDateFilter = ($date) => {
        if (!$date) {
            return;
        }
        
        filters[$date.name] = $date.value;
        
        $date.addEventListener('input', () => {
            if ($date.hasAttribute('pattern')) {
                let regexp = new RegExp($date.getAttribute('pattern'));
                if ($date.value.trim() && !regexp.test($date.value)) {
                    filters[$date.name] = '';
                    return;
                }
            }
            filters[$date.name] = $date.value;
            filter && filter();
        });
    }
    
    const initTimeFilter = ($time) => {
        if (!$time) {
            return;
        }
        
        filters[$time.name] = $time.value;
        
        $time.addEventListener('input', () => {
            if ($time.hasAttribute('pattern')) {
                let regexp = new RegExp($time.getAttribute('pattern'));
                if ($time.value.trim() && !regexp.test($time.value)) {
                    filters[$time.name] = '';
                    return;
                }
            }
            filters[$time.name] = $time.value;
            filter && filter();
        });
    }
    
    const initPublicAdministrationFilter = ($publicAdministration, $paIpaCode, $website, $websiteId, $user, $userUuid) => {
        if (!$publicAdministration || !$paIpaCode) {
            return;
        }
        
        filters.pa = $publicAdministration.value;
        filters.pa_ipa_code = $paIpaCode.value;
        
        $publicAdministration.onkeypress = (event) => {
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        };
        
        $publicAdministration.addEventListener('input', () => {
            if ($paIpaCode) {
                if (filters.pa_ipa_code) {
                    $paIpaCode.value = '';
                    filters.pa = '';
                    filters.pa_ipa_code = '';
                    
                    if ($website) {
                        $website.value = '';
                        $website.cache = {};
                        $website.last_val = {};
                        filters.website = ''
                    }
                    
                    if ($websiteId) {
                        $websiteId.value = '';
                        filters.website_id = '';
                    }
                    
                    if ($user) {
                        $user.value = '';
                        $user.cache = {};
                        $user.last_val = {};
                        filters.user = '';
                    }
                    
                    if ($userUuid) {
                        $userUuid.value = ''
                        filters.user_uuid = '';
                    }
                    
                    filter && filter();
                }
            }
        });
        
        let publicAdministrationAutocomplete = new window.autoComplete({
            selector: $publicAdministration,
            minChars: 3,
            menuClass: 'pa',
            cancelCall: '',
            source: (term, suggest) => {
                term = term.toLowerCase();
                $publicAdministration.classList.add('autocomplete-loading');
                if (publicAdministrationAutocomplete.cancelCall) {
                    publicAdministrationAutocomplete.cancelCall.cancel();
                }
                publicAdministrationAutocomplete.cancelCall = window.axios.CancelToken.source();
                window.axios({
                    method: 'GET',
                    url: $publicAdministration.getAttribute('data-source'),
                    params: { q: term },
                }).then((response) => {
                    suggest(response.data);
                    $('.autocomplete-suggestions .pa').hide();
                    publicAdministrationAutocomplete.resetInfo();
                }).catch((error) => {
                    if (!window.axios.isCancel(error)) {
                        //TODO:
                    }
                }).finally(() => {
                    publicAdministrationAutocomplete.cancelCall = '';
                    $publicAdministration.classList.remove('autocomplete-loading');
                    $('.autocomplete-suggestions .pa').show();
                });
            },
            renderItem: (item, search) => {
                search = search.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
                let re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
                return [
                    '<div class="autocomplete-suggestion pa"',
                    'data-ipa_code="' + item.ipa_code + '"',
                    'data-val="' + item.name + '">',
                    item.name.replace(re, "<b>$1</b>") + ' - ' + item.city.replace(re, "<b>$1</b>") + ' (' + item.county + ')',
                    '</div>'
                ].join('');
            },
            onSelect: (e, term, item) => {
                $paIpaCode.value = item.getAttribute('data-ipa_code');
                filters.pa = $publicAdministration.value;
                filters.pa_ipa_code = item.getAttribute('data-ipa_code');
                filter && filter();
            },
            resetInfo: () => {
                $paIpaCode.value = '';
                filters.pa = '';
                filters.pa_ipa_code = '';
            }
        });
    }
    
    const initWebsiteFilter = ($website, $websiteId, $publicAdministration, $paIpaCode) => {
        if (!$website || !$websiteId) {
            return;
        }
        
        filters.website = $website.value;
        filters.website_id = $websiteId.value;
        
        $website.onkeypress = (event) => {
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        };
        
        $website.addEventListener('input', () => {
            if ($websiteId) {
                if (filters.website_id) {
                    $websiteId.value = '';
                    filters.website = '';
                    filters.website_id = '';
                    filter && filter();
                }
            }
        });
        
        let websiteAutocomplete = new window.autoComplete({
            selector: $website,
            minChars: 3,
            menuClass: 'website',
            cancelCall: '',
            source: (term, suggest) => {
                term = term.toLowerCase();
                $website.classList.add('autocomplete-loading');
                if (websiteAutocomplete.cancelCall) {
                    websiteAutocomplete.cancelCall.cancel();
                }
                websiteAutocomplete.cancelCall = window.axios.CancelToken.source();
                window.axios({
                    cancelToken: websiteAutocomplete.cancelCall.token,
                    method: 'GET',
                    url: $website.getAttribute('data-source'),
                    params: {
                        q: term,
                        p: filters.pa_ipa_code || null,
                    },
                }).then((response) => {
                    suggest(response.data);
                    $('.autocomplete-suggestions .website').hide();
                    websiteAutocomplete.resetInfo();
                }).catch((error) => {
                    if (!window.axios.isCancel(error)) {
                        //TODO:
                    }
                }).finally(() => {
                    websiteAutocomplete.cancelCall = '';
                    $website.classList.remove('autocomplete-loading');
                    $('.autocomplete-suggestions .website').show();
                });
            },
            renderItem: (item, search) => {
                search = search.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
                let re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
                return [
                    '<div class="autocomplete-suggestion website"',
                    'data-id="' + item.id + '"',
                    'data-val="' + item.name + '"',
                    'data-pa="' + item.pa + '"',
                    'data-pa_name="' + item.pa_name + '">',
                    item.name.replace(re, "<b>$1</b>") + ' - ' + item.slug.replace(re, "<b>$1</b>"),
                    '</div>'
                ].join('');
            },
            onSelect: (e, term, item) => {
                $websiteId.value = item.getAttribute('data-id');
                filters.website = $website.value;
                filters.website_id = item.getAttribute('data-id');
                if ($publicAdministration && !$publicAdministration.value) {
                    $publicAdministration.value = item.getAttribute('data-pa_name');
                    if ($paIpaCode) {
                        $paIpaCode.value = item.getAttribute('data-pa');
                        filters.pa = $publicAdministration.value;
                        filters.pa_ipa_code = item.getAttribute('data-pa');
                    }
                }
                filter && filter();
            },
            resetInfo: () => {
                $websiteId.value = '';
                filters.website = '';
                filters.website_id = '';
            }
        });
    }
    
    const initUserFilter = ($user, $userUuid) => {
        if (!$user || !$userUuid) {
            return;
        }
        
        filters.user = $user.value;
        filters.user_uuid = $userUuid.value;
        
        $user.onkeypress = (event) => {
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        };
        
        $user.addEventListener('input', () => {
            if ($userUuid) {
                if (filters.user_uuid) {
                    $userUuid.value = '';
                    filters.user = '';
                    filters.user_uuid = '';
                    filter && filter();
                }
            }
        });
        
        let userAutocomplete = new window.autoComplete({
            selector: $user,
            minChars: 3,
            menuClass: 'user',
            cancelCall: '',
            source: (term, suggest) => {
                term = term.toLowerCase();
                $user.classList.add('autocomplete-loading');
                if (userAutocomplete.cancelCall) {
                    userAutocomplete.cancelCall.cancel();
                }
                userAutocomplete.cancelCall = window.axios.CancelToken.source();
                window.axios({
                    cancelToken: userAutocomplete.cancelCall.token,
                    method: 'GET',
                    url: $user.getAttribute('data-source'),
                    params: {
                        q: term,
                        p: filters.pa_ipa_code || null,
                    },
                }).then((response) => {
                    suggest(response.data);
                    $('.autocomplete-suggestions .user').hide();
                    userAutocomplete.resetInfo();
                }).catch((error) =>{
                    if (!window.axios.isCancel(error)) {
                        //TODO:
                    }
                }).finally(() => {
                    userAutocomplete.cancelCall = '';
                    $user.classList.remove('autocomplete-loading');
                    $('.autocomplete-suggestions .user').show();
                });
            },
            renderItem: (item, search) => {
                search = search.replace(/[-/\\^$*+?.()|[\]{}]/g, '\\$&');
                let re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
                return [
                    '<div class="autocomplete-suggestion user"',
                    'data-uuid="' + item.uuid + '"',
                    'data-val="' + item.familyName + ' ' + item.name + '">',
                    item.familyName.replace(re, "<b>$1</b>") + ' ' + item.name.replace(re, "<b>$1</b>"),
                    '</div>'
                ].join('');
            },
            onSelect: (e, term, item) => {
                $userUuid.value = item.getAttribute('data-uuid');
                filters.user = $user.value;
                filters.user_uuid = item.getAttribute('data-uuid');
                filter && filter();
            },
            resetInfo: () => {
                $userUuid.value = '';
                filters.user = '';
                filters.user_uuid = '';
            }
        });
    }
    
    const initInputs = ($form) => {
        if (!$form) {
            return;
        }
        
        let showPA = $form.getAttribute('data-show-pa');
        
        let $message = document.querySelector('input[name="message"]');
        
        let $startDate = document.querySelector('input[name="start_date"]');
        let $startTime = document.querySelector('input[name="start_time"]');
        let $endDate = document.querySelector('input[name="end_date"]');
        let $endTime = document.querySelector('input[name="end_time"]');
        
        let $publicAdministration = showPA ? document.querySelector('input[name="pa"]') : null;
        let $paIpaCode = showPA ? document.querySelector('input[name="pa_ipa_code"]') : null;
        let $website = document.querySelector('input[name="website"]');
        let $websiteId = document.querySelector('input[name="website_id"]');
        let $user = document.querySelector('input[name="user"]');
        let $userUuid = document.querySelector('input[name="user_uuid"]');
        
        initMessageFilter($message);
        initDateFilter($startDate);
        initDateFilter($endDate);
        initTimeFilter($startTime);
        initTimeFilter($endTime);
        
        if (showPA) {
            initPublicAdministrationFilter($publicAdministration, $paIpaCode, $website, $websiteId, $user, $userUuid);
        }
        initWebsiteFilter($website, $websiteId, $publicAdministration, $paIpaCode);
        initUserFilter($user, $userUuid);
    }
    
    const initSelects = () => {
        let $event = document.querySelector('select[name="event"]');
        let $exception = document.querySelector('select[name="exception"]');
        let $job = document.querySelector('select[name="job"]');
        let $severity = document.querySelector('select[name="severity"]');
        
        // Enable/disable jobs and events selects
        // since they are mutually exclusive
        if ($event && $job) {
            $event.addEventListener('change', (event) => {
                if (event.target.value) {
                    $job.setAttribute('disabled', '');
                    $job.setAttribute('aria-disabled', 'true');
                    delete filters.job;
                } else {
                    $job.removeAttribute('disabled');
                    $job.removeAttribute('aria-disabled');
                    filters.job = $job && $job.value;
                }
            });
            
            $job.addEventListener('change', (event) => {
                if (event.target.value) {
                    $event.setAttribute('disabled', '');
                    $event.setAttribute('aria-disabled', 'true');
                    delete filters.event;
                    delete filters.exception;
                } else {
                    $event.removeAttribute('disabled');
                    $event.removeAttribute('aria-disabled');
                    filters.event = $event && $event.value;
                    filters.exception = $exception && $exception.value;
                }
            });
        }
        
        // Enable/disable exception select since it can
        // be selected only with event type 'Exception'
        if ($event && $exception) {
            $event.addEventListener('change', () => {
                let option = $event.options[$event.selectedIndex];
                if (option.getAttribute('type') && option.getAttribute('type') === 'exception') {
                    $exception.removeAttribute('disabled');
                    $exception.removeAttribute('aria-disabled');
                    filters.exception = $exception.value;
                } else {
                    $exception.setAttribute('disabled', '');
                    $exception.setAttribute('aria-disabled', 'true');
                    delete filters.exception;
                }
            });
        }
        
        if ($event) {
            filters.event = $event.value;
            $event.addEventListener('change', () => {
                filters.event = $event.value;
                filter && filter();
            });
        }
        if ($exception) {
            filters.exception = $exception.value;
            $exception.addEventListener('change', () => {
                filters.exception = $exception.value;
                filter && filter();
            });
        }
        if ($job) {
            filters.job = $job.value;
            $job.addEventListener('change', () => {
                filters.job = $job.value;
                filter && filter();
            });
        }
        if ($severity) {
            filters.severity = $severity.value;
            $severity.addEventListener('change', () => {
                filters.severity = $severity.value;
                filter && filter();
            });
        }
    }
    
    const initFilters = ($form) => {
        initInputs($form);
        initSelects();
    }
    
    const addFilters = (data) => {
        Object.keys(filters).forEach((key) => {
            data[key] = filters[key];
        });
        return data;
    }
    
    const preDatatableInit = (datatableApi) => {
        let $form = document.getElementById('filters');
        if (!$form) {
            return;
        }
        
        initFilters($form);
        
        datatableApi.on('preXhr.dt', (event, settings, data) => {
            addFilters(data);
        }).on('xhr.dt', (event, settings, json, xhr) => {
            if (!json) {
                let jsonResponse = JSON.parse(xhr.responseText);
                Object.keys(jsonResponse.errors).forEach((key) => {
                    let element = document.getElementsByName(key);
                    if (element && element[0]) {
                        element[0].setCustomValidity(jsonResponse.errors[key][0]);
                    }
                });
                alert(jsonResponse.message);
            }
        });
        
        $.fn.dataTable.ext.errMode = 'none';
        filter = $.fn.dataTable.util.throttle(
            () => {
                window.dt.ajax.reload();
            },
            350
        );
    }
    
    return { preDatatableInit };
})();
