export default (() => {
    let datatable = {};
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
        
        $message.oninput = (event) => {
            if (!useAsFilter()) {
                event.preventDefault();
                filters.message = '';
                return;
            }
            filters.message = $message.value.trim();
            filter && filter();
        }
    }
    
    const initDateFilter = ($date, $startTime, $endTime) => {
        if (!$date) {
            return;
        }
        
        let setValidity = () => {
            if (!$date.value.trim() && (filters.start_time || filters.end_time)) {
                $date.setCustomValidity('"Date" is required with Starting time or "Ending time"');
                return false;
            } else {
                $date.setCustomValidity('');
            }
            return true;
        }
        
        filters.date = $date.value;
        
        $date.oninput = () => {
            if ($date.hasAttribute('pattern')) {
                let regexp = new RegExp($date.getAttribute('pattern'));
                if ($date.value.trim() && !regexp.test($date.value)) {
                    return;
                }
            }
            if (!setValidity($date)) {
                return;
            }
            filters.date = $date.value;
            filter && filter();
        }
        
        $startTime.onblur = $endTime.onblur = () => {
            setValidity()
        }
    }
    
    const initStartTimeFilter = ($startTime) => {
        if (!$startTime) {
            return;
        }
        
        filters.start_time = $startTime.value;
        
        $startTime.oninput = () => {
            if ($startTime.hasAttribute('pattern')) {
                let regexp = new RegExp($startTime.getAttribute('pattern'));
                if ($startTime.value.trim() && !regexp.test($startTime.value)) {
                    return;
                }
            }
            if ($startTime.value.trim() && filters.end_time) {
                let startTime = new Date();
                let endTime = new Date();
                let startArray = $startTime.value.trim().split(':');
                startTime.setHours(startArray[0]);
                startTime.setMinutes(startArray[1]);
                let endArray = filters.end_time.split(':');
                endTime.setHours(endArray[0]);
                endTime.setMinutes(endArray[1]);
                if (startTime >= endTime) {
                    $startTime.setCustomValidity('"Starting time" must be before "Ending time"');
                    return;
                } else {
                    $startTime.setCustomValidity('');
                }
            }
            filters.start_time = $startTime.value;
            filter && filter();
        }
    }
    
    const initEndTimeFilter = ($endTime) => {
        if (!$endTime) {
            return;
        }
        
        filters.end_time = $endTime.value;
    
        $endTime.oninput = () => {
            if ($endTime.hasAttribute('pattern')) {
                let regexp = new RegExp($endTime.getAttribute('pattern'));
                if ($endTime.value.trim() && !regexp.test($endTime.value)) {
                    return;
                }
            }
            if ($endTime.value.trim() && filters.start_time) {
                let startTime = Date.now();
                let endTime = Date.now();
                let startArray = filters.start_time.split(':');
                startTime.setHours(startArray[0]);
                startTime.setMinutes(startArray[1]);
                let endArray = $endTime.value.trim().split(':');
                endTime.setHours(endArray[0]);
                endTime.setMinutes(endArray[1]);
                if (startTime >= endTime) {
                    $endTime.setCustomValidity('"Ending time" must be after "Starting time"');
                    return;
                } else {
                    $endTime.setCustomValidity('');
                }
        
            }
            filters.end_time = $endTime.value;
            filter && filter();
        }
    }
    
    const initPublicAdministrationFilter = ($publicAdministration, $ipa, $website, $slug, $user, $uuid) => {
        if (!$publicAdministration || !$ipa) {
            return;
        }
        
        filters.ipa_code = $ipa.value;
        
        $publicAdministration.onkeypress = (event) => {
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        };
    
        $publicAdministration.oninput = () => {
            if ($ipa.value) {
                $ipa.value = '';
    
                if ($website) {
                    $website.value = '';
                    $website.cache = {};
                    $website.last_val = {};
                }
    
                if ($slug) {
                    $slug.value = '';
                    filters.slug = '';
                }
    
                if ($user) {
                    $user.value = '';
                    $user.cache = {};
                    $user.last_val = {};
                }
    
                if ($uuid) {
                    $uuid.value = ''
                    filters.uuid = '';
                }
            }
        }
        
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
                    method: 'POST',
                    url: $publicAdministration.getAttribute('data-source'),
                    data: { q: term },
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
                $ipa.value = item.getAttribute('data-ipa_code');
                filters.ipa_code = item.getAttribute('data-ipa_code');
                filter && filter();
            },
            resetInfo: () => {
                $ipa.value = '';
                filters.ipa_code = '';
            }
        });
    }
    
    const initWebsiteFilter = ($website, $slug, $publicAdministration, $ipa) => {
        if (!$website || !$slug) {
            return;
        }
    
        filters.slug = $slug.value;
        
        $website.onkeypress = (event) => {
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        };
    
        $website.oninput = () => {
            if ($slug.value) {
                $slug.value = '';
                filters.slug = '';
            }
        }
    
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
                    method: 'POST',
                    url: $website.getAttribute('data-source'),
                    data: {
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
                let re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
                return [
                    '<div class="autocomplete-suggestion website"',
                    'data-slug="' + item.slug + '"',
                    'data-val="' + item.name + '"',
                    'data-pa="' + item.pa + '"',
                    'data-pa_name="' + item.pa_name + '">',
                    item.name.replace(re, "<b>$1</b>") + ' - ' + item.slug.replace(re, "<b>$1</b>"),
                    '</div>'
                ].join('');
            },
            onSelect: (e, term, item) => {
                $slug.value = item.getAttribute('data-slug');
                filters.slug = item.getAttribute('data-slug');
                if ($publicAdministration && !$publicAdministration.value) {
                    $publicAdministration.value = item.getAttribute('data-pa_name');
                    if ($ipa) {
                        $ipa.value = item.getAttribute('data-pa');
                        filters.ipa_code = item.getAttribute('data-pa');
                    }
                }
                filter && filter();
            },
            resetInfo: () => {
                $slug.value = '';
                filters.slug = '';
            }
        });
    }
    
    const initUserFilter = ($user, $uuid) => {
        if (!$user || !$uuid) {
            return;
        }
        
        filters.uuid = $uuid.value;
    
        $user.onkeypress = (event) => {
            if (13 === event.keyCode) {
                event.preventDefault();
            }
        };
    
        $user.oninput = () => {
            if ($uuid) {
                $uuid.value = '';
                filters.uuid = '';
            }
        }
        
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
                    method: 'POST',
                    url: $user.getAttribute('data-source'),
                    data: {
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
                $uuid.value = item.getAttribute('data-uuid');
                filters.uuid = item.getAttribute('data-uuid');
                filter && filter();
            },
            resetInfo: () => {
                $uuid.value = '';
                filters.uuid = '';
            }
        });
    }
    
    const initInputs = ($form) => {
        if (!$form) {
            return;
        }
        
        let showPA = $form.getAttribute('data-show-pa');
        
        let $message = document.querySelector('input[name="message"]');
        
        let $date = document.querySelector('input[name="date"]');
        let $startTime = document.querySelector('input[name="start_time"]');
        let $endTime = document.querySelector('input[name="end_time"]');
        
        let $publicAdministration = showPA ? document.querySelector('input[name="pa"]') : null;
        let $ipa = showPA ? document.querySelector('input[name="ipa_code"]') : null;
        let $website = document.querySelector('input[name="website"]');
        let $slug = document.querySelector('input[name="slug"]');
        let $user = document.querySelector('input[name="user"]');
        let $uuid = document.querySelector('input[name="uuid"]');
    
        initMessageFilter($message);
        initDateFilter($date, $startTime, $endTime);
        initStartTimeFilter($startTime)
        initEndTimeFilter($endTime)
        
        if (showPA) {
            initPublicAdministrationFilter($publicAdministration, $ipa, $website, $slug, $user, $uuid);
        }
        initWebsiteFilter($website, $slug, $publicAdministration, $ipa);
        initUserFilter($user, $uuid);
    }
    
    const initSelects = () => {
        let $event = document.querySelector('select[name="event"]');
        let $exception = document.querySelector('select[name="exception"]');
        let $job = document.querySelector('select[name="job"]');
        let $severity = document.querySelector('select[name="severity"]');
    
        // Enable/disable jobs and events selects
        // since they are mutually exclusive
        if ($event && $job) {
            $event.onchange = (event) => {
                if (event.target.value) {
                    $job.setAttribute('disabled', '');
                    $job.setAttribute('aria-disabled', 'true');
                } else {
                    $job.removeAttribute('disabled');
                    $job.removeAttribute('aria-disabled');
                }
            }
    
            $job.onchange = (event) => {
                if (event.target.value) {
                    $event.setAttribute('disabled', '');
                    $event.setAttribute('aria-disabled', 'true');
                } else {
                    $event.removeAttribute('disabled');
                    $event.removeAttribute('aria-disabled');
                }
            }
        }
        
        // Enable/disable exception select since it can
        // be selected only with event type 'Exception'
        if ($event && $exception) {
            $event.onchange = (event) => {
                if (event.target.type && event.target.type === 'exception') {
                    $exception.removeAttribute('disabled');
                    $exception.removeAttribute('aria-disabled');
                } else {
                    $exception.setAttribute('disabled', '');
                    $exception.setAttribute('aria-disabled', 'true');
                }
            }
        }
        
        if ($event) {
            filters.event = $event.value;
            $event.onchange = () => {
                filters.event = $event.value;
                filter && filter();
            }
        }
        if ($exception) {
            filters.exception = $exception.value;
            $exception.onchange = () => {
                filters.exception = $event.value;
                filter && filter();
            };
        }
        if ($job) {
            filters.job = $job.value;
            $job.onchange = () => {
                filters.job = $job.value;
                filter && filter();
            };
        }
        if ($severity) {
            filters.severity = $severity.value;
            $severity.onchange = () => {
                filters.severity = $severity.value;
                filter && filter();
            };
        }
    }
    
    const initFilters = () => {
        let $form = document.getElementById('filters');
        if (!$form) {
            return;
        }
        
        initInputs($form);
        initSelects();
    }
    
    const addFilters = (data) => {
        Object.keys(filters).forEach((key) => {
            data[key] = filters[key];
        });
        return data;
    }
    
    const initData = ($datatableElement) => {
        datatable.source = JSON.parse($datatableElement.data('dt-source'));
        datatable.columns = $datatableElement.data('dt-columns');
        datatable.columnsOrder = $datatableElement.data('dt-columns-order');
    }
    
    const initOrder = () => {
        datatable.columnsOrder.map((ord) => {
            ord[0] = datatable.columns.findIndex((column) => {
                return column.data == ord[0];
            });
        });
    }
    
    const init = async () => {
        let $datatable = $('.LogsDatatable');
        if ($datatable.length === 0) {
            return;
        }
    
        await import(/* webpackChunkName: "datatables.net" */ './datatablesImports');
        
        initFilters();
        initData($datatable);
        initOrder();
        
        let api = window.dt = $datatable.DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: datatable.source,
                dataType: 'json',
            },
            responsive: {
                details: {
                    type: 'column',
                    target: -1
                }
            },
            autoWidth: false,
            columns: datatable.columns.concat({
                defaultContent: '',
                className: 'control',
                orderable: false,
                targets: -1,
            }),
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.18/i18n/Italian.json"
            },
            order: datatable.columnsOrder,
            initComplete: () => {
                api.responsive.recalc();
            }
        }).on('preXhr.dt', (event, settings, data) => {
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
    
    return { init };
})();