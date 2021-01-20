import SwaggerUIBundle from 'swagger-ui';
import 'swagger-ui/dist/swagger-ui.css';
import spec from '../data/api.json'

export default (() => {
    const init = () => {
        const swaggerDiv = document.getElementById('swagger-ui');
        
        const apiUrl = swaggerDiv.hasAttribute("data-url") ? swaggerDiv.getAttribute("data-url") : "localhost";

        spec.servers.push(
            {
                "url": apiUrl + "/portal",
                "description": "Kong Portal Gateway"
            }
        );

        if(swaggerDiv){
            SwaggerUIBundle({
                spec,
                dom_id: '#swagger-ui',
              })
        }
    };

    return { init };
})();