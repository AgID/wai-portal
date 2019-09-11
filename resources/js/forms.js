export default (() => {
    const checkSelectValidity = bootstrapSelect => {
        bootstrapSelect && (bootstrapSelect.querySelector('select').validity.valid
            ? bootstrapSelect.classList.add('is-valid') || bootstrapSelect.classList.remove('is-invalid')
            : bootstrapSelect.classList.add('is-invalid') || bootstrapSelect.classList.remove('is-valid'));

        // We need jquery here
        $('.bootstrap-select-wrapper .bootstrap-select').on('changed.bs.select', () => {
            bootstrapSelect.classList.remove('is-invalid', 'is-valid');
        });
    };

    const init = () => {
        const forms = document.getElementsByClassName('needs-validation');

        [...forms].map(form => {
            const bootstrapSelect = form.querySelector('.bootstrap-select-wrapper');
            const inputElements = [...form.querySelectorAll('input, select, textarea')];

            form.addEventListener('submit', event => {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                checkSelectValidity(bootstrapSelect);
                form.classList.add('was-validated');
            }, false);

            form.addEventListener('input', () => {
                form.classList.remove('was-validated');
                inputElements.map(inputElement => {
                    inputElement.classList.remove('is-invalid', 'is-valid');
                });
                bootstrapSelect && bootstrapSelect.classList.remove('is-invalid', 'is-valid');
            }, false);

            inputElements.map(inputElement => {
                inputElement.addEventListener('invalid', () => {
                    (inputElement === form.querySelector(':invalid')) && inputElement.scrollIntoView();
                })
            });
        });
    };

    return { init };
})();
