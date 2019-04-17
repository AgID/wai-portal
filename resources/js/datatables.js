export default (() => {
    let datatable = {};

    const initData = ($datatable) => {
        datatable.source = JSON.parse($datatable.data('dt-source'));
        datatable.columns = $datatable.data('dt-columns');
        datatable.columnsOrder = $datatable.data('dt-columns-order');
    }

    const initButtons = () => {
        datatable.columns.map((column) => {
            if (column.data === 'buttons') {
                column.render = (buttons) => {
                    return [
                        '<div class="buttons">',
                        buttons.map((button) => {
                            return '<a href="' + button.link + '" role="button" class="Button Button--default Button--shadow Button--round u-padding-top-xxs u-padding-bottom-xxs u-margin-right-s u-text-r-xxs" title="' + button.label + '">' + button.label + '</a>';
                        }).join(''),
                        '</div>'
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
                        '<div class="checkboxes">',
                        checkboxes.map((checkbox, index) => {
                            return [
                                checkbox.label && '<label>',
                                '<input',
                                'type="checkbox"',
                                // 'class="Form-input"',
                                `name="${checkbox.name}"`,
                                `id="${checkbox.name}-${index}"`,
                                checkbox.disabled ? 'disabled' : '',
                                checkbox.checked ? 'checked' : '',
                                '>',
                                checkbox.label && `${checkbox.label}</label>`
                            ].join(' ');
                        }).join(''),
                        '</div>'
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
                        '<div class="radios">',
                        radios.map((radio) => {
                            return [
                                radio.label && '<label>',
                                '<input',
                                'type="radio"',
                                // 'class="Form-input"',
                                `name="${radio.name}"`,
                                radio.disabled ? 'disabled' : '',
                                radio.checked ? 'checked' : '',
                                '>',
                                radio.label && `${radio.label}</label>`
                            ].join(' ');
                        }).join(''),
                        '</div>'
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

    const init = async (afterInit) => {
        let $datatable = $('.Datatable');

        if ($datatable.length === 0) {
            return;
        }

        await import(/* webpackChunkName: "datatables.net" */ './datatables-imports');

        initData($datatable);
        initButtons();
        initCheckboxes();
        initRadios();
        initOrder();

        $datatable.DataTable({
            ajax: datatable.source,
            responsive: {
                details: {
                    type: 'column',
                    target: -1
                }
            },
            columns: datatable.columns.concat({
                defaultContent: '',
                className: 'control',
                orderable: false
            }),
            order: datatable.columnsOrder,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.18/i18n/Italian.json"
            },
            initComplete: (settings, json) => {
                afterInit && afterInit.map((afterInitFunction) => afterInitFunction(settings, json));
            }
        });
    }

    return { init };
})();
