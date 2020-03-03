import i18n from './i18n';

export default (() => {
    const showConfirmation = (title, message, image, buttonConfirmText, buttonCancelText) => {
        const confirmationId = (new Date().getTime()).toString(36);
        const confirmationFragment = document.createRange().createContextualFragment(`
        <div class="modal fade" role="dialog" tabindex="-1" id="${confirmationId}">
            <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded p-5">
                <div class="modal-header d-block pt-0">
                    <svg class="icon icon-primary icon-xl"><use xlink:href="/svg/sprite.svg#it-horn"></use></svg>
                    <h3 class="modal-title">${title}</h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-7">
                            <p>${message}</p>
                        </div>
                        <div class="col-5 d-flex align-items-center justify-content-center">
                            <img src="${image}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-start">
                    <button id="${confirmationId}-confirm" class="btn btn-primary btn-sm" type="button">${buttonConfirmText || i18n.t('Conferma')}</button>
                    <button class="btn btn-outline-secondary btn-sm" type="button" data-dismiss="modal">${buttonCancelText || i18n.t('Annulla')}</button>
                </div>
              </div>
            </div>
        </div>
        `);
        document.body.appendChild(confirmationFragment);

        // bootstrap-italia modals require jquery
        const $confirmationModal = $(`#${confirmationId}`);
        $confirmationModal.modal();
        $confirmationModal.on('hidden.bs.modal', () => {
            $confirmationModal.modal('dispose');
        });

        return new Promise((resolve, reject) => {
            document.getElementById(`${confirmationId}-confirm`).addEventListener('click', () => {
                resolve();
                $confirmationModal.modal('hide');
            });
            $confirmationModal.on('hide.bs.modal', () => {
                reject();
            });
        });
    };

    return { showConfirmation };
})();
