import Autocomplete from './autocomplete';

export default (() => {
    const filters = {};
    const datePattern = 'dd/MM/yyyy';
    const datePatternRegEx = new RegExp(/(0[1-9]|1[0-9]|2[0-9]|3[01])\/(0?[1-9]|1[012])\/([0-9]{4})/);
    let filter;

    const initDateInput = (dateInput, additionalConfiguration) => {
        const configuration = {
            inputFormat: datePattern,
            outputFormat: datePattern,
            onUpdate: () => { dateInput.dispatchEvent(new Event('input')) },
        }

        $(dateInput).datepicker({
            ...configuration,
            ...additionalConfiguration,
        });
    }

    const initMessageFilter = messageInput => {
        if (!messageInput) {
            return;
        }

        messageInput.addEventListener('input', event => {
            if (event.isComposing || event.keyCode === 229 || event.keyCode === 13) {
                return;
            }

            if (!messageInput.value.trim()) {
                delete filters.message;
            } else if (messageInput.value.trim().length < 3) {
                delete filters.message;
                return;
            } else {
                filters.message = messageInput.value.trim();
            }

            filter && filter();
        });
    }

    const initDateFilter = dateInput => {
        if (!dateInput) {
            return;
        }

        dateInput.addEventListener('input', () => {
            if (!dateInput.value.trim()) {
                delete filters[dateInput.name];
            } else if (!datePatternRegEx.test(dateInput.value)) {
                delete filters[dateInput.name];
                return;
            } else {
                filters[dateInput.name] = dateInput.value;
            }

            filter && filter();
        });
    }

    const initTimeFilter = timeInput => {
        if (!timeInput) {
            return;
        }

        timeInput.addEventListener('input', () => {
            if (!timeInput.value.trim()) {
                delete filters[timeInput.name];
            } else {
                filters[timeInput.name] = timeInput.value;
            }

            filter && filter();
        });
    }

    const initIpaCodeFilter = ipaCodeInput => {
        if (ipaCodeInput.value.trim()) {
            filters.ipa_code = ipaCodeInput.value;
        }
    }

    const initIpaCodeFilterResetLink = ipaCodeInput => {
        const ipaCodeFilterResetLink = document.getElementById('reset-ipa_code-filter');
        const publicAdministrationSelectedMessage = document.getElementById('public-administration-selected');
        const publicAdministrationNotSelectedMessage = document.getElementById('public-administration-not-selected');
        const publicAdministrationSelector = document.querySelector('.pa-selector select[name="public-administration-nav"]');

        ipaCodeFilterResetLink && ipaCodeFilterResetLink.addEventListener('click', event => {
            event.preventDefault();
            ipaCodeInput.value = '';
            publicAdministrationSelector.value = '';
            publicAdministrationSelectedMessage.classList.add('d-none')
            publicAdministrationNotSelectedMessage.classList.remove('d-none');
            delete filters.ipa_code;

            filter && filter();
        });
    }

    const initWebsiteFilter = (websiteIdInput, ipaCodeInput)  => {
        if (!websiteIdInput) {
            return;
        }

        const handleSelectedWebsite = selectedWebsite => {
            filters.website_id = selectedWebsite.id;
            filter && filter();
        };

        const websiteSchema = {
            title: 'name',
            subTitle: 'pa_name',
        }

        Autocomplete.init(websiteIdInput, websiteSchema, {
            handleSelectedResult: handleSelectedWebsite,
            queryParameters: {
                public_administration: ipaCodeInput ? ipaCodeInput.value : null,
            }
        });

        websiteIdInput.addEventListener('input', () => {
            if (filters.website_id) {
                delete filters.website_id;
                filter && filter();
            }
        });
    }

    const initUserFilter = (userUuidInput, ipaCodeInput) => {
        if (!userUuidInput) {
            return;
        }

        const handleSelectedUser = selectedUser => {
            filters.user_uuid = selectedUser.uuid;
            filter && filter();
        };

        const userSchema = {
            title: ['name', 'family_name'],
        }

        Autocomplete.init(userUuidInput, userSchema, {
            handleSelectedResult: handleSelectedUser,
            queryParameters: {
                public_administration: ipaCodeInput ? ipaCodeInput.value : null,
            }
        });

        userUuidInput.addEventListener('input', () => {
            if (filters.user_uuid) {
                delete filters.user_uuid;
                filter && filter();
            }
        });
    }

    const initInputs = filtersContainer => {
        if (!filtersContainer) {
            return;
        }

        const messageInput = filtersContainer.querySelector('input[name="message"]');
        const startDateInput = filtersContainer.querySelector('input[name="start_date"]');
        const startTimeInput = filtersContainer.querySelector('input[name="start_time"]');
        const endDateInput = filtersContainer.querySelector('input[name="end_date"]');
        const endTimeInput = filtersContainer.querySelector('input[name="end_time"]');
        const ipaCodeInput = filtersContainer.querySelector('input[name="ipa_code"]');
        const websiteIdInput = filtersContainer.querySelector('input[name="website_id"]');
        const userUuidInput = filtersContainer.querySelector('input[name="user_uuid"]');

        initMessageFilter(messageInput);
        initDateInput(startDateInput);
        initDateInput(endDateInput);
        initDateFilter(startDateInput);
        initDateFilter(endDateInput);
        initTimeFilter(startTimeInput);
        initTimeFilter(endTimeInput);
        ipaCodeInput && initIpaCodeFilter(ipaCodeInput);
        ipaCodeInput && initIpaCodeFilterResetLink(ipaCodeInput);
        websiteIdInput && initWebsiteFilter(websiteIdInput, ipaCodeInput);
        userUuidInput && initUserFilter(userUuidInput, ipaCodeInput);
    }

    const initSelects = filtersContainer => {
        if (!filtersContainer) {
            return;
        }

        const eventSelect = filtersContainer.querySelector('select[name="event"]');
        const exceptionTypeSelect = filtersContainer.querySelector('select[name="exception_type"]');
        const jobSelect = filtersContainer.querySelector('select[name="job"]');
        const severitySelect = filtersContainer.querySelector('select[name="severity"]');

        // Enable/disable jobs and events selects
        // since they are mutually exclusive
        if (eventSelect && jobSelect) {
            eventSelect.addEventListener('change', () => {
                if (eventSelect.value) {
                    disableSelect(jobSelect);
                    delete filters.job;
                } else {
                    enableSelect(jobSelect);
                }
            });

            jobSelect.addEventListener('change', () => {
                if (jobSelect.value) {
                    disableSelect(eventSelect);
                    delete filters.event;
                    delete filters.exception_type;
                } else {
                    enableSelect(eventSelect);
                }
            });
        }

        // Enable/disable exception select since it can
        // be selected only with event type 'Exception'
        if (eventSelect && exceptionTypeSelect) {
            eventSelect.addEventListener('change', () => {
                const option = eventSelect.options[eventSelect.selectedIndex];

                if (option.dataset.type && 'exception' === option.dataset.type) {
                    enableSelect(exceptionTypeSelect);
                    exceptionTypeSelect.value && (filters.exception_type = exceptionTypeSelect.value);
                } else {
                    disableSelect(exceptionTypeSelect);
                    delete filters.exception_type;
                }
            });
        }

        if (eventSelect) {
            eventSelect.addEventListener('change', () => {
                if (!eventSelect.value.trim()) {
                    delete filters.event;
                } else {
                    filters.event = eventSelect.value;
                }

                filter && filter();
            });
        }

        if (exceptionTypeSelect) {
            exceptionTypeSelect.addEventListener('change', () => {
                if (!exceptionTypeSelect.value.trim()) {
                    delete filters.exception_type;
                } else {
                    filters.exception_type = exceptionTypeSelect.value;
                }

                filter && filter();
            });
        }

        if (jobSelect) {
            jobSelect.addEventListener('change', () => {
                if (!jobSelect.value.trim()) {
                    delete filters.job;
                } else {
                    filters.job = jobSelect.value;
                }

                filter && filter();
            });
        }

        if (severitySelect) {
            severitySelect.addEventListener('change', () => {
                if (!severitySelect.value.trim()) {
                    delete filters.severity;
                } else {
                    filters.severity = severitySelect.value;
                }

                filter && filter();
            });
        }
    }

    const enableSelect = selectElement => {
        const bootstrapSelect = selectElement.parentNode.classList.contains('bootstrap-select') && selectElement.parentNode;
        const bootstrapSelectWrapper = bootstrapSelect.parentNode.classList.contains('bootstrap-select-wrapper') && bootstrapSelect.parentNode;
        const selectButton = bootstrapSelect.querySelector('button.dropdown-toggle');

        selectElement.disabled = false;
        selectElement.setAttribute('aria-disabled', false);

        if (bootstrapSelect && bootstrapSelectWrapper && selectButton) {
            bootstrapSelect.classList.remove('disabled');
            bootstrapSelectWrapper.classList.remove('disabled');
            selectButton.classList.remove('disabled');
            selectButton.setAttribute('aria-disabled', false);
        }
    }

    const disableSelect = selectElement => {
        const bootstrapSelect = selectElement.parentNode.classList.contains('bootstrap-select') && selectElement.parentNode;
        const bootstrapSelectWrapper = bootstrapSelect.parentNode.classList.contains('bootstrap-select-wrapper') && bootstrapSelect.parentNode;
        const selectButton = bootstrapSelect.querySelector('button.dropdown-toggle');

        selectElement.disabled = true;
        selectElement.setAttribute('aria-disabled', true);

        if (bootstrapSelect && bootstrapSelectWrapper && selectButton) {
            bootstrapSelect.classList.add('disabled');
            bootstrapSelectWrapper.classList.add('disabled');
            selectButton.classList.add('disabled');
            selectButton.setAttribute('aria-disabled', true);
        }
    }

    const initFilters = filtersContainer => {
        initInputs(filtersContainer);
        initSelects(filtersContainer);
    }

    const addFilters = data => {
        Object.keys(filters).map(filterName => {
            data[filterName] = filters[filterName];
        });
    }

    const preDatatableInit = datatableApi => {
        const filtersContainer = document.getElementById('log-filters');
        if (!filtersContainer) {
            return;
        }

        initFilters(filtersContainer);

        datatableApi.on('preXhr.dt', (event, settings, data) => {
            addFilters(data);
        }).on('xhr.dt', (event, settings, json, xhr) => {
            if (!json) {
                const jsonResponse = JSON.parse(xhr.responseText);
                if ('object' === typeof jsonResponse.errors) {
                    Object.keys(jsonResponse.errors).map(name => {
                        const element = document.querySelector(`[name="${name}"]`);
                        if (element) {
                            const invalidFeedbackElement = element.closest('.form-group').querySelector('.invalid-feedback');
                            invalidFeedbackElement.textContent = jsonResponse.errors[name][0];
                            invalidFeedbackElement.style.display = 'block';
                        }
                    });
                } else {
                    console.error(jsonResponse.errors); // eslint-disable-line no-console
                }
            }
        });

        filter = $.fn.dataTable.util.throttle(() => {
            [...filtersContainer.querySelectorAll('.invalid-feedback')].map(invalidFeedbackElement => {
                invalidFeedbackElement.style.display = 'none';
            });

            datatableApi.ajax.reload();
        }, 350);
    }

    return { preDatatableInit };
})();
