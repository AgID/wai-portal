export default (() => {
    let datatable = {};

    const initData = ($datatable) => {
        datatable.processing = $datatable.data('dt-processing') || false;
        datatable.serverSide = $datatable.data('dt-server-side') || false;
        datatable.searching = undefined === $datatable.data('dt-searching') ?  true : $datatable.data('dt-searching');
        datatable.source = $datatable.data('dt-source');
        datatable.columns = $datatable.data('dt-columns');
        datatable.columnsOrder = $datatable.data('dt-columns-order');
    }

    const initButtons = () => {
        datatable.columns.map((column) => {
            if (column.data === 'buttons') {
                column.render = (buttons) => {
                    return [
                        '<span class="buttons">',
                        buttons.map((button) => {
                            return [
                                `<a href="${button.link}"`,
                                'role="button"',
                                'class="Button Button--default Button--shadow Button--round u-padding-top-xxs u-padding-bottom-xxs u-margin-right-s u-text-r-xxs"',
                                button.dataAttributes && Object.keys(button.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${button.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                `title="${button.label}">`,
                                button.label,
                                '</a>'
                            ].join(' ');
                        }).join(''),
                        '</span>'
                    ].join('');
                }
            }
            delete column.name;
        });
    }

    const initCheckboxes = () => {
        datatable.columns.map((column) => {
            if (column.data === 'checkboxes') {
                column.render = (checkboxes) => {
                    return [
                        '<span class="checkboxes">',
                        checkboxes.map((checkbox, index) => {
                            return [
                                checkbox.label && '<label>',
                                '<input',
                                'type="checkbox"',
                                // 'class="Form-input"',
                                `name="${checkbox.name}"`,
                                `id="${checkbox.name}-${index}"`,
                                `value="${checkbox.value}"`,
                                checkbox.dataAttributes && Object.keys(checkbox.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${checkbox.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                checkbox.disabled ? 'disabled' : '',
                                checkbox.checked ? 'checked' : '',
                                '>',
                                checkbox.label && `${checkbox.label}</label>`
                            ].join(' ');
                        }).join(''),
                        '</span>'
                    ].join('');
                }
            }
            delete column.name;
        });
    }

    const initRadios = () => {
        datatable.columns.map((column) => {
            if (column.data === 'radios') {
                column.render = (radios) => {
                    return [
                        '<span class="radios">',
                        radios.map((radio, index) => {
                            return [
                                radio.label && '<label>',
                                '<input',
                                'type="radio"',
                                // 'class="Form-input"',
                                `name="${radio.name}"`,
                                `id="${radio.name}-${index}"`,
                                `value="${radio.value}"`,
                                radio.dataAttributes && Object.keys(radio.dataAttributes).reduce((dataAttrs, attr) => {
                                    return [dataAttrs, `data-${attr}="${radio.dataAttributes[attr]}"`].join(' ');
                                }, ''),
                                radio.disabled ? 'disabled' : '',
                                radio.checked ? 'checked' : '',
                                '>',
                                radio.label && `${radio.label}</label>`
                            ].join(' ');
                        }).join(''),
                        '</span>'
                    ].join('');
                }
            }
            delete column.name;
        });
    }

    const initOrder = () => {
        datatable.columnsOrder.map((ord) => {
            ord[0] = datatable.columns.findIndex((column) => {
                return column.data == ord[0];
            });
        });
    }

    const init = async (preInit, afterInit) => {
        const $datatable = $('.Datatable');

        if ($datatable.length === 0) {
            return;
        }

        await import(/* webpackChunkName: "datatables.net" */ './datatablesImports');

        initData($datatable);
        initButtons();
        initCheckboxes();
        initRadios();
        initOrder();

        $.fn.dataTable.ext.errMode = 'none';
        const datatableApi = $datatable.DataTable({
            processing: datatable.processing,
            serverSide: datatable.serverSide,
            searching: datatable.searching,
            ajax: datatable.source,
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
                orderable: false
            }),
            order: datatable.columnsOrder,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.18/i18n/Italian.json"
            },
            initComplete: () => {
                afterInit && afterInit.map((afterInitFunction) => afterInitFunction());
                datatableApi.responsive.recalc();
            }
        }).on('preInit.dt', () => {
            preInit && preInit.map((preInitFunction) => preInitFunction(datatableApi));
        }).on('responsive-display', () => {
            afterInit && afterInit.map((afterInitFunction) => afterInitFunction());
        });
    }

    return { init };
})();
