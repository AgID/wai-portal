import { upperCaseFirst } from "upper-case-first";
import Datatable from "../datatables";
import Notification from "../notification";
import I18n from "../i18n";
import AjaxButton from "../ajaxButton";
import FormButton from "../formButton";

export default (() => {
    const init = () => {
        const keyDeleteButtons = [
            ...document.querySelectorAll(
                'a[role="button"][data-type="keyDelete"]'
            )
        ];

        keyDeleteButtons.map(keyDeleteButton => {
            const isAjax = "ajax" in keyDeleteButton.dataset;
            const currentAction = I18n.t("eliminazione");
            const confirmation = {
                title: upperCaseFirst(
                    [currentAction, I18n.t("della chiave")].join(" ")
                ),
                body: [
                    "<p>",
                    I18n.t("Stai eliminando la chiave"),
                    "<strong>" + keyDeleteButton.dataset.keyname + "</strong>",
                    "</p>",
                    "<p>" + I18n.t("Sei sicuro?") + "<p>"
                ].join(" "),
                image: "/images/website-archive.svg"
            };

            const success = ()  => {
                Notification.showNotification(
                    I18n.t("Chiave eliminata"),
                    [
                        I18n.t("La chiave"),
                        "<strong>" +
                        keyDeleteButton.dataset.keyname  +
                            "</strong>",
                        I18n.t("è stata eliminata.")
                    ].join(" "),
                    "success",
                    "it-check-circle"
                );
                Datatable.reload();
            };

            isAjax &&
                AjaxButton.init(
                    keyDeleteButton,
                    "patch",
                    confirmation,
                    success
                );
            isAjax || FormButton.init(keyDeleteButton, "patch", confirmation);
        });
    };

    return { init };
})();
