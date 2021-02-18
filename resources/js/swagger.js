import axios from "axios";
import SwaggerUIBundle from "swagger-ui";
import Notification from './notification';
import I18n from './i18n';
import "swagger-ui/dist/swagger-ui.css";

const getCredentialInfo = async consumerId => {
    let response = {};

    try {
        response = await axios({
            method: "GET",
            url: `/api-credentials/${consumerId}/show/json`,
            headers: {
                accept: "*/*",
                "content-type": "application/json"
            },
            timeout: 3000
        });
    } catch (error) {
        // credential not found
        return false;
    }

    return response.data.credential;
};

export default (() => {
    const init = () => {
        const swaggerDiv = document.getElementById("swagger-ui");

        if (swaggerDiv) {
            const production = swaggerDiv.hasAttribute("data-environment")
                ? swaggerDiv.getAttribute("data-environment")
                : "false";

            const isProduction = production === "true" ? true : false;
            const selectCredential = document.getElementById(
                "select-credential"
            );

            selectCredential && selectCredential.addEventListener('change', () => {
                Notification.showNotification(I18n.t('credenziale selezionata'), I18n.t("Adesso è possibile usare la credenziale con il bottone 'Authorize'."), 'info', 'it-info-circle');
                document.querySelector('.swagger-ui .auth-wrapper').style.setProperty('display', 'flex', 'important');
            });

            const disableTryItOutPlugin = () => {
                return {
                    statePlugins: {
                        spec: {
                            wrapSelectors: {
                                allowTryItOutFor: () => () =>
                                    !isProduction && selectCredential
                            }
                        }
                    }
                };
            };

            const disableAuthorizeButtonPlugin = () => {
                return (!isProduction && selectCredential) ? {} : {
                    wrapComponents: {
                        authorizeBtn: () => () => null
                    }
                };
            };

            const ui = SwaggerUIBundle({
                url: "/api/specification",
                dom_id: "#swagger-ui",
                plugins: [disableTryItOutPlugin, disableAuthorizeButtonPlugin]
            });

            if (selectCredential && !isProduction) {
                selectCredential.addEventListener('change', async e => {
                    if (e.target.value !== "false") {
                        const credential = await getCredentialInfo(
                            selectCredential.value
                        );

                        credential && ui.initOAuth({
                            clientId: credential.client_id,
                            clientSecret: credential.client_secret,
                            appName: credential.name,
                            additionalQueryStringParams: {
                                grant_type: "client_credentials"
                            }
                        });
                     }
                });
            }
        }
    };

    return { init };
})();
