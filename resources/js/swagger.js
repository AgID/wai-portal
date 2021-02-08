import axios from "axios";
import SwaggerUIBundle from "swagger-ui";
import "swagger-ui/dist/swagger-ui.css";
import spec from "../data/api.json";

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
            const apiUrl = swaggerDiv.hasAttribute("data-url")
                ? swaggerDiv.getAttribute("data-url")
                : "localhost";

            const production = swaggerDiv.hasAttribute("data-environment")
                ? swaggerDiv.getAttribute("data-environment")
                : "false";

            const isProduction = production === "true" ? true : false;
            const selectCredential = document.getElementById(
                "select-credential"
            );

            spec.servers.push({
                url: apiUrl,
                description: "API Gateway"
            });

            spec.components.securitySchemes.oAuth.flows.clientCredentials.tokenUrl =
                apiUrl + "/portal/oauth2/token";

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

            const ui = SwaggerUIBundle({
                spec,
                dom_id: "#swagger-ui",
                plugins: [disableTryItOutPlugin]
            });

            if (selectCredential && !isProduction) {
                selectCredential.addEventListener("change", async e => {
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
