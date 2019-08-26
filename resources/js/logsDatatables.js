export default (() => {
    const filters = {};
    let filter;

    const initMessageFilter = ($message) => {
        if (!$message) {
            return;
        }

        const useAsFilter = () => {
            const length = $message.value.trim().length;
            return length >= 3;
        }

        $message.addEventListener('input', () => {
            if (!useAsFilter()) {
                delete filters.message;
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
                const regexp = new RegExp($date.getAttribute('pattern'));
                if ($date.value.trim() && !regexp.test($date.value)) {
                    delete filters[$date.name];
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
                const regexp = new RegExp($time.getAttribute('pattern'));
                if ($time.value.trim() && !regexp.test($time.value)) {
                    delete filters[$time.name];
                    return;
                }
            }
            filters[$time.name] = $time.value;
            filter && filter();
        });
    }

    const initPublicAdministrationFilter = ($publicAdministration, $ipaCode, $website, $websiteId, $user, $userUuid) => {
        if (!$publicAdministration || !$ipaCode) {
            return;
        }

        filters.pa = $publicAdministration.value;
        filters.ipa_code = $ipaCode.value;

        $publicAdministration.addEventListener('input', () => {
            if ($ipaCode) {
                if (filters.ipa_code) {
                    $ipaCode.value = '';
                    delete filters.pa;
                    delete filters.ipa_code;

                    if ($website) {
                        $website.value = '';
                        $website.cache = {};
                        $website.last_val = {};
                        delete filters.website;
                    }

                    if ($websiteId) {
                        $websiteId.value = '';
                        delete filters.website_id;
                    }

                    if ($user) {
                        $user.value = '';
                        $user.cache = {};
                        $user.last_val = {};
                        delete filters.user;
                    }

                    if ($userUuid) {
                        $userUuid.value = ''
                        delete filters.user_uuid;
                    }

                    filter && filter();
                }
            }
        });

        const publicAdministrationAutocomplete = new window.autoComplete({
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
                const re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
                return [
                    '<div class="autocomplete-suggestion pa"',
                    'data-ipa_code="' + item.ipa_code + '"',
                    'data-val="' + item.name + '">',
                    item.name.replace(re, "<b>$1</b>") + ' - ' + item.city.replace(re, "<b>$1</b>") + ' (' + item.county + ')',
                    '</div>'
                ].join('');
            },
            onSelect: (e, term, item) => {
                $ipaCode.value = item.getAttribute('data-ipa_code');
                filters.pa = $publicAdministration.value;
                filters.ipa_code = item.getAttribute('data-ipa_code');
                filter && filter();
            },
            resetInfo: () => {
                $ipaCode.value = '';
                delete filters.pa;
                delete filters.ipa_code;
            }
        });
    }

    const initWebsiteFilter = ($website, $websiteId, $publicAdministration, $ipaCode) => {
        if (!$website || !$websiteId) {
            return;
        }

        filters.website = $website.value;
        filters.website_id = $websiteId.value;

        $website.addEventListener('input', () => {
            if ($websiteId) {
                if (filters.website_id) {
                    $websiteId.value = '';
                    delete filters.website;
                    delete filters.website_id;
                    filter && filter();
                }
            }
        });

        const websiteAutocomplete = new window.autoComplete({
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
                        p: filters.ipa_code || null,
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
                const re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
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
                    if ($ipaCode) {
                        $ipaCode.value = item.getAttribute('data-pa');
                        filters.pa = $publicAdministration.value;
                        filters.ipa_code = item.getAttribute('data-pa');
                    }
                }
                filter && filter();
            },
            resetInfo: () => {
                $websiteId.value = '';
                delete filters.website;
                delete filters.website_id;
            }
        });
    }

    const initUserFilter = ($user, $userUuid) => {
        if (!$user || !$userUuid) {
            return;
        }

        filters.user = $user.value;
        filters.user_uuid = $userUuid.value;

        $user.addEventListener('input', () => {
            if ($userUuid) {
                if (filters.user_uuid) {
                    $userUuid.value = '';
                    delete filters.user;
                    delete filters.user_uuid;
                    filter && filter();
                }
            }
        });

        const userAutocomplete = new window.autoComplete({
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
                        p: filters.ipa_code || null,
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
                const re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
                return [
                    '<div class="autocomplete-suggestion user"',
                    'data-uuid="' + item.uuid + '"',
                    'data-val="' + item.family_name + ' ' + item.name + '">',
                    item.family_name.replace(re, "<b>$1</b>") + ' ' + item.name.replace(re, "<b>$1</b>"),
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
                delete filters.user;
                delete filters.user_uuid;
            }
        });
    }

    const initInputs = ($filtersContainer) => {
        if (!$filtersContainer) {
            return;
        }

        const showPA = $filtersContainer.getAttribute('data-show-pa');
        const $message = document.querySelector('input[name="message"]');
        const $startDate = document.querySelector('input[name="start_date"]');
        const $startTime = document.querySelector('input[name="start_time"]');
        const $endDate = document.querySelector('input[name="end_date"]');
        const $endTime = document.querySelector('input[name="end_time"]');
        const $publicAdministration = showPA ? document.querySelector('input[name="pa"]') : null;
        const $ipaCode = showPA ? document.querySelector('input[name="ipa_code"]') : null;
        const $website = document.querySelector('input[name="website"]');
        const $websiteId = document.querySelector('input[name="website_id"]');
        const $user = document.querySelector('input[name="user"]');
        const $userUuid = document.querySelector('input[name="user_uuid"]');

        initMessageFilter($message);
        initDateFilter($startDate);
        initDateFilter($endDate);
        initTimeFilter($startTime);
        initTimeFilter($endTime);

        if (showPA) {
            initPublicAdministrationFilter($publicAdministration, $ipaCode, $website, $websiteId, $user, $userUuid);
        }
        initWebsiteFilter($website, $websiteId, $publicAdministration, $ipaCode);
        initUserFilter($user, $userUuid);
    }

    const initSelects = () => {
        const $event = document.querySelector('select[name="event"]');
        const $exceptionType = document.querySelector('select[name="exception_type"]');
        const $job = document.querySelector('select[name="job"]');
        const $severity = document.querySelector('select[name="severity"]');

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
                    delete filters.exception_type;
                } else {
                    $event.removeAttribute('disabled');
                    $event.removeAttribute('aria-disabled');
                    filters.event = $event && $event.value;
                    filters.exception_type = $exceptionType && $exceptionType.value;
                }
            });
        }

        // Enable/disable exception select since it can
        // be selected only with event type 'Exception'
        if ($event && $exceptionType) {
            $event.addEventListener('change', () => {
                const option = $event.options[$event.selectedIndex];
                if (option.getAttribute('type') && option.getAttribute('type') === 'exception') {
                    $exceptionType.removeAttribute('disabled');
                    $exceptionType.removeAttribute('aria-disabled');
                    filters.exception_type = $exceptionType.value;
                } else {
                    $exceptionType.setAttribute('disabled', '');
                    $exceptionType.setAttribute('aria-disabled', 'true');
                    delete filters.exception_type;
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
        if ($exceptionType) {
            filters.exception_type = $exceptionType.value;
            $exceptionType.addEventListener('change', () => {
                filters.exception_type = $exceptionType.value;
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

    const initFilters = ($filtersContainer) => {
        initInputs($filtersContainer);
        initSelects();
    }

    const addFilters = (data) => {
        Object.keys(filters).forEach((key) => {
            data[key] = filters[key];
        });
        return data;
    }

    const preDatatableInit = (datatableApi) => {
        const $filtersContainer = document.getElementById('filters');
        if (!$filtersContainer) {
            return;
        }

        initFilters($filtersContainer);

        datatableApi.on('preXhr.dt', (event, settings, data) => {
            addFilters(data);
        }).on('xhr.dt', (event, settings, json, xhr) => {
            if (!json) {
                const jsonResponse = JSON.parse(xhr.responseText);
                if (typeof jsonResponse.errors === 'object') {
                    Object.keys(jsonResponse.errors).forEach((key) => {
                        const element = document.getElementsByName(key);
                        if (element && element[0]) {
                            element[0].setCustomValidity(jsonResponse.errors[key][0]);
                        }
                    });
                } else {
                    console.error(jsonResponse.errors); // eslint-disable-line
                }
            }
        });

        filter = $.fn.dataTable.util.throttle(
            () => {
                datatableApi.ajax.reload();
            },
            350
        );
    }

    return { preDatatableInit };
})();
