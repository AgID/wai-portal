import axios from "axios";
import SwaggerUIBundle from "swagger-ui";
import "swagger-ui/dist/swagger-ui.css";
import spec from "../data/api.json";

const getKeyInfo = async consumerId => {
    let response = {};

    try {
        response = await axios({
            method: "GET",
            url: `/api-key/${consumerId}/show/json`,
            headers: {
                accept: "*/*",
                "content-type": "application/json"
            },
            timeout: 3000
        });
    } catch (error) {
        response.data = {
            error: true,
            ...(error?.response?.data && { message: error.response.data })
        };
    }

    const data = await response.data;

    return data;
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

            const selectKey = document.getElementById("select-key");
            const getKey = document.getElementById("use-key");

            spec.servers.push({
                url: apiUrl + "/portal",
                description: "Kong Portal Gateway"
            });

            spec.components.securitySchemes.oAuthSample.flows.clientCredentials.tokenUrl =
                apiUrl + "/portal/oauth2/token";

            const disableTryItOutPlugin = () => {
                return {
                    statePlugins: {
                        spec: {
                            wrapSelectors: {
                                allowTryItOutFor: () => () => !isProduction
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

            if (selectKey && !isProduction) {
                getKey.addEventListener("click", async () => {
                    const { key } = await getKeyInfo(selectKey.value);

                    ui.initOAuth({
                        clientId: key?.client_id,
                        clientSecret: key?.client_secret,
                        appName: key?.name,
                        scopeSeparator: " ",
                        scopes: "user",
                        additionalQueryStringParams: {
                            grant_type: "client_credentials"
                        },
                        usePkceWithAuthorizationCodeGrant: false
                    });
                });
            }
        }
    };

    return { init };
})();
