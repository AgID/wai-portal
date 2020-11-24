import SwaggerUIBundle from 'swagger-ui';
import 'swagger-ui/dist/swagger-ui.css';
import spec from '../data/api.json'

export default (() => {
    const init = () => {
        const swaggerDiv = document.getElementById('swagger-ui');
        
        if(swaggerDiv){
            SwaggerUIBundle({
                //url: "https://petstore.swagger.io/v2/swagger.json",
                spec,
                dom_id: '#swagger-ui'
              })
        }
    };

    return { init };
})();