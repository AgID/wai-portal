export default (() => {
    let datatable = {};
    let datatableApi, onDrawFunctions;
    const $datatable = $('.Datatable');

    const initData = $datatable => {
        datatable.processing = $datatable.data('dt-processing') || false;
        datatable.serverSide = $datatable.data('dt-server-side') || false;
        datatable.searching = undefined === $datatable.data('dt-searching') ?  true : $datatable.data('dt-searching');
        datatable.source = $datatable.data('dt-source');
        datatable.columns = $datatable.data('dt-columns');
        datatable.columnsOrder = $datatable.data('dt-columns-order');
        datatable.columns.map(column => {
            column.name = column.data;
            column.render = (data, type) => {
                if (data[type]) {
                    return data[type];
                }
                return data.raw || data;
            }

        });
    }

    const initButtons = () => {
        datatable.columns.map(column => {
            if (column.data === 'buttons') {
                column.render = buttons => {
                    return [
                        '<span class="buttons">',
                        buttons.map(button => {
                            return [
                                `<a href="${button.link}"`,
                                'role="button"',
                                `class="btn btn-${button.color || 'primary'} btn-xs mr-1 py-1${button.dataAttributes ? ' disabled' : ''}"`,
                                button.dataAttributes && Object.keys(button.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${button.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                button.dataAttributes && 'aria-disabled="true"',
                                `title="${button.label}">`,
                                `<span>${button.label}</span>`,
                                '</a>',
                            ].join(' ');
                        }).join(''),
                        '</span>',
                    ].join('');
                }
            }
        });
    };

    const initIcons = () => {
        datatable.columns.map(column => {
            if (column.data === 'icons') {
                column.render = icons => {
                    return [
                        '<span class="icons">',
                        icons.map(icon => {
                            return [
                                icon.link ? `<a href="${icon.link}"` : '<span',
                                icon.title && 'data-toggle="tooltip"',
                                icon.link && 'role="button"',
                                `class="mr-${icon.label ? '4' : '1'} py-1 d-inline-block${icon.dataAttributes ? ' disabled' : ''}"`,
                                icon.dataAttributes && Object.keys(icon.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${icon.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                icon.title && `title="${icon.title}"`,
                                icon.dataAttributes && 'aria-disabled="true"',
                                '>',
                                `<svg class="icon`,
                                icon.color ? `icon-${icon.color}">` : '">',
                                `<use xlink:href="/svg/sprite.svg#${icon.icon}"></use></svg>`,
                                icon.label,
                                icon.link ? '</a>' : '</span>',
                            ].join(' ');
                        }).join(''),
                        '</span>',
                    ].join('');
                }
            }
        });
    };

    const initCheckboxes = () => {
        datatable.columns.map(column => {
            if (column.data === 'checkboxes') {
                column.render = checkboxes => {
                    return [
                        '<span class="checkboxes">',
                        checkboxes.map(checkbox => {
                            return [
                                checkbox.label && `<label for="${checkbox.name}-${checkbox.value}">`,
                                '<input',
                                'type="checkbox"',
                                `name="${checkbox.name}"`,
                                `id="${checkbox.name}-${checkbox.value}"`,
                                `value="${checkbox.value}"`,
                                checkbox.dataAttributes && Object.keys(checkbox.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${checkbox.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                checkbox.disabled ? 'disabled' : '',
                                checkbox.checked ? 'checked' : '',
                                '>',
                                checkbox.label && `${checkbox.label}</label>`,
                            ].join(' ');
                        }).join(''),
                        '</span>',
                    ].join('');
                }
            }
        });
    };

    const initToggles = () => {
        datatable.columns.map(column => {
            if (column.data === 'toggles') {
                column.render = toggles => {
                    return [
                        '<div class="toggles-container ml-2">',
                        toggles.map(toggle => {
                            return [
                                '<div class="toggles mr-4">',
                                `<label class="d-inline-flex" for="${toggle.name}-${toggle.value}">`,
                                toggle.label,
                                '<input',
                                'type="checkbox"',
                                `name="${toggle.name}"`,
                                `id="${toggle.name}-${toggle.value}"`,
                                `value="${toggle.value}"`,
                                toggle.dataAttributes && Object.keys(toggle.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${toggle.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                toggle.disabled ? 'disabled' : '',
                                toggle.checked ? 'checked' : '',
                                '>',
                                '<span class="lever"></span>',
                                '</label>',
                                '</div>',
                            ].join(' ');
                        }).join(''),
                        '</div>',
                    ].join('');
                }
            }
        });
    };

    const initRadios = () => {
        datatable.columns.map(column => {
            if (column.data === 'radios') {
                column.render = radios => {
                    return [
                        '<span class="radios">',
                        radios.map(radio => {
                            return [
                                radio.label && `<label for="${radio.name}-${radio.value}">`,
                                '<input',
                                'type="radio"',
                                `name="${radio.name}"`,
                                `id="${radio.name}-${radio.value}"`,
                                `value="${radio.value}"`,
                                radio.dataAttributes && Object.keys(radio.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${radio.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                radio.disabled ? 'disabled' : '',
                                radio.checked ? 'checked' : '',
                                '>',
                                radio.label && `${radio.label}</label>`,
                            ].join(' ');
                        }).join(''),
                        '</span>',
                    ].join('');
                }
            }
        });
    };

    const initOrder = () => {
        datatable.columnsOrder.map(ord => {
            ord[0] = datatable.columns.findIndex(column => {
                return column.data === ord[0];
            });
        });
    };

    const initSearch = () => {
        const searchField = document.getElementById('datatables-search');

        searchField && searchField.addEventListener('keyup', () => {
            datatableApi.search(searchField.value).draw();
        });
    };

    const initColumnFilters = () => {
        [...document.querySelectorAll('.datatable-filters .filter')].map(columnFilter => {
            const columnName = columnFilter.dataset.columnName;
            const resetFilterButton = columnFilter.querySelector('.reset-filters');
            let columnData = datatableApi.column(`${columnName}:name`).data();

            if (typeof columnData[0] === 'object' && columnData[0]['raw']) {
                columnData = columnData.pluck('raw');
            }

            columnData.unique().sort().map((value, index) => {
                return {
                    'filterList': columnFilter.querySelector('.filter-values'),
                    'renderedFilter': document.createRange().createContextualFragment([
                        '<div class="form-check filter-control">',
                        `<input type="checkbox" class="form-check-input" id="filter-${columnName}-${index}" data-filter-value="${value}">`,
                        `<label class="form-check-label text-nowrap" for="filter-${columnName}-${index}">`,
                        value,
                        '</label>',
                        '</div>',
                    ].join(''))
                }
            }).map(filterObject => { filterObject.filterList.appendChild(filterObject.renderedFilter) });

            columnFilter.addEventListener('change', () => {
                if (columnFilter.querySelectorAll('.filter-control :checked').length) {
                    resetFilterButton.classList.remove('disabled');
                    columnFilter.classList.add('active');
                } else {
                    resetFilterButton.classList.add('disabled');
                    columnFilter.classList.remove('active');
                }
                performColumnSearch();
            });

            resetFilterButton.addEventListener('click', () => {
                [...columnFilter.querySelectorAll('.filter-control :checked')].map(filterItem => filterItem.checked = false);
                resetFilterButton.classList.add('disabled');
                columnFilter.classList.remove('active');
                performColumnSearch();
            });
        });
    };

    const performColumnSearch = () => {
        [...document.querySelectorAll('.datatable-filters .filter')].map(columnFilter => {
            const searchRegex = [...columnFilter.querySelectorAll('.filter-control :checked')].map(filterItem => {
                return `^${$.fn.dataTable.util.escapeRegex(filterItem.dataset.filterValue)}$`;
            }).join('|');
            datatableApi.column(`${columnFilter.dataset.columnName}:name`).search(searchRegex, true, false);
        });
        datatableApi.draw();
    };

    const initPaginatedInputs = () => {
        if (datatableApi.page.len() < 2) {
            return;
        }

        const $form = $datatable.closest('form');

        if ($form.length) {
            const form = $form[0];
            const datatableElement = $datatable[0];
            $form.on('submit', () => {
                const inputs = datatableApi.$('input, select, textarea').serializeArray();

                inputs.map(input => {
                    if (!$.contains(datatableElement, form[input.name])) {
                        $form.append($('<input>')
                            .attr('type', 'hidden')
                            .attr('name', input.name)
                            .val(input.value));
                    }
                });
            });
        }
    };

    const reload = () => {
        if ($datatable.length === 0) {
            return;
        }

        $datatable.addClass('loading');
        datatableApi && datatableApi.ajax.reload(() => {
            onDrawFunctions && onDrawFunctions.map(onDrawFunction => onDrawFunction());
            $datatable.removeClass('loading');
        });
    };

    const responsiveRenderer = (api, rowIdx, columns) => {
        const columnsData = columns.reduce((renderedData, column) => {
            const title = column.title || 'actions';
            column.hidden && (renderedData[title] = (renderedData[title] || '') + [
                column.title && `<td class="text-wrap">${column.title}</td>`,
                column.title && '<td class="text-wrap column-data">',
                column.data,
                column.title && '</td>',
            ].join(''));
            
            return renderedData;
        }, {});

        const renderedColumns = Object.keys(columnsData).reduce((columnsMarkup, title, index) => {
            return columnsMarkup + [
                '<tr>',
                'actions' === title ? '<td colspan="2" class="border-0 text-wrap">' : '',
                columnsData[title],
                'actions' === title ? '</td>' : '',
                '</tr>',
            ].join('') + (columnsData.length -1 === index ? '</table>' : '');
        }, '<table class="w-100">');

        return renderedColumns || false;
    };

    const init = async (preInit, onDraw) => {
        if ($datatable.length === 0) {
            return;
        }

        onDrawFunctions = onDraw;

        await import(/* webpackChunkName: "datatables.net" */ './datatablesImports');

        initData($datatable);
        initButtons();
        initIcons();
        initCheckboxes();
        initToggles();
        initRadios();
        initOrder();
        initSearch();

        $.fn.dataTable.ext.errMode = 'none';
        datatableApi = $datatable.DataTable({
            processing: datatable.processing,
            serverSide: datatable.serverSide,
            searching: datatable.searching,
            ajax: datatable.source,
            responsive: {
                details: {
                    renderer: responsiveRenderer,
                }
            },
            autoWidth: false,
            dom: 'rtlip',
            columns: datatable.columns,
            order: datatable.columnsOrder,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.18/i18n/Italian.json'
            },
            initComplete: () => {
                initColumnFilters();
                initPaginatedInputs();
                // datatableApi.responsive.recalc();
                $('.dataTables_wrapper select').selectpicker();
                $('.dataTables_wrapper [data-toggle="tooltip"]').tooltip();
            },
            createdRow: (row, data) => {
                data['trashed'] && row.classList.add('trashed');
            }
        }).on('preInit.dt', () => {
            preInit && preInit.map(preInitFunction => preInitFunction(datatableApi));
        }).on('responsive-display', () => {
            onDraw && onDraw.map(onDrawFunction => onDrawFunction());
            $('.dataTables_wrapper [data-toggle="tooltip"]').tooltip();
        }).on('draw', () => {
            onDraw && onDraw.map(onDrawFunction => onDrawFunction());
            $('.dataTables_wrapper [data-toggle="tooltip"]').tooltip();
        });
    };

    return { init, reload };
})();
