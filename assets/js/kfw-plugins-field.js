function plugin_action(event) {
     
    "use strict";

    event.preventDefault();

    var kemet_plugin = event.target,
        status = kemet_plugin.getAttribute("data-status"),
        activate_url = '',
        install_url = '',
        deactivate_url = '';

    if(status == 'activate'){

        activate_url = kemet_plugin.getAttribute("data-url-activate");
        activate(activate_url);

    } else if (status == 'install'){

        activate_url = kemet_plugin.getAttribute("data-url-activate");
        install_url = kemet_plugin.getAttribute("data-url-install");
        install_and_activate(install_url);

    }else{

        deactivate_url = kemet_plugin.getAttribute("data-url-deactivate");
        deactivate(deactivate_url);

    }

    function deactivate(url) {
        var request = new XMLHttpRequest();

        // request state change event
        request.onreadystatechange = function () {

            kemet_plugin.setAttribute("style", "color:#444; background-color: #e5e5e5; border-color: #444;");
            kemet_plugin.innerHTML = '<span class="dashicons dashicons-update"></span> Deactivating..';
            // request compvared?
            if (request.readyState !== 4) return;

            if (request.status === 200) {
                window.location.reload();
            }
            else {
                // request error
                console.log('HTTP error', request.status, request.statusText);
            }
        };
        request.open("GET", url, true);
        request.send();
    } 

    function activate(url){
        var request = new XMLHttpRequest();

        // request state change event
        request.onreadystatechange = function () {

            kemet_plugin.setAttribute("style", "color:#444; background-color: #e5e5e5; border-color: #444;");
            kemet_plugin.innerHTML = '<span class="dashicons dashicons-update"></span> Activating..';
            // request compvared?
            if (request.readyState !== 4) return;

            if (request.status === 200) {
                window.location.reload();
            }
            else {
                // request error
                console.log('HTTP error', request.status, request.statusText);
            }
        };
        request.open("GET", url, true);
        request.send();
    } 
    
    function install_and_activate(url) {

        var request = new XMLHttpRequest();

        // request state change event
        request.onreadystatechange = function () {
            kemet_plugin.setAttribute("style", "color:#444; background-color: #e5e5e5; border-color: #444;");
            kemet_plugin.innerHTML = '<span class="dashicons dashicons-update"></span> Installing..';
            // request compvared?
            if (request.readyState !== 4) return;

            if (request.status === 200) {
                activate(activate_url);
            }
            else {
                // request error
                console.log('HTTP error', request.status, request.statusText);
            }
        };
        request.open("GET", url, true);
        request.send();
        
    } 
}