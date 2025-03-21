/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License.
 * The Original Code is: vtiger CRM Open Source.
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Detail_Js("SalesOrder_Detail_Js", {}, {

    registerEvents: function() {
        console.log("SalesOrder_Detail_Custom_Js: registerEvents iniciado");
        this._super();
        this.registerRelatedContactData();
    },

    registerRelatedContactData: function() {
        // Buscamos el enlace del contacto, suponiendo que está en .contact_id a
        var contactLink = jQuery('.contact_id a');
        if (contactLink.length) {
            var href = contactLink.attr('href');
            console.log("SalesOrder_Detail_Custom_Js: Contact link =", href);
            var match = href.match(/record=(\d+)/);
            if (match && match[1]) {
                var contactId = match[1];
                console.log("SalesOrder_Detail_Custom_Js: ID de Contacto extraído =", contactId);

                // Construimos los parámetros para llamar a la acción en HelloWorld
                var params = {
                    module: 'HelloWorld',
                    action: 'BasicAjax',
                    search_module: 'Contacts',
                    base_record: contactId,
                    fields: 'phone_work,phone'
                };
                var url = "index.php?" + jQuery.param(params);
                console.log("SalesOrder_Detail_Custom_Js: URL para HelloWorld BasicAjax =", url);

                // Realiza la petición AJAX
                app.request.get({
                    url: url,
                    dataType: "json"
                }).then(function(err, data) {
                    console.log("SalesOrder_Detail_Custom_Js: Respuesta recibida");
                    // Ajustamos para que si data.result no existe, use data directamente
                    var resultData = (data.result !== undefined) ? data.result : data;
                    if (!err && resultData && !jQuery.isEmptyObject(resultData)) {
                        console.log("SalesOrder_Detail_Custom_Js: Datos del Contacto =", resultData);
                        var phone = resultData.phone_work || resultData.phone || "No disponible";
                        console.log("SalesOrder_Detail_Custom_Js: Teléfono obtenido =", phone);

                        // Actualiza el HTML donde antes se mostraba "Hello World!"
                        var headerContainer = jQuery('.detailview-header');
                        var customRow = headerContainer.find('.salesorder-contact-info');
                        if(customRow.length == 0) {
                            customRow = jQuery(
                            '<div class="row salesorder-contact-info" style="padding:10px; border:0px; border-radius:4px; margin-top:10px;">' +
                                '<div class="col-sm-12" style="border-radius:8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">' +
                                    '<p style="margin:0; font-weight:bold; color:#333;">Teléfono del Contacto: ' + phone + '</p>' +
                                '</div>' +
                            '</div>');
                            /* customRow = jQuery(
                                '<div class="row" style="margin-top:10px; padding:10px;">' +
                                    '<div class="col-sm-12 text-center">' +
                                        '<div class="salesorder-contact-info" style="display:inline-block; background-color:#f5f5f5; padding:15px 30px; border:1px solid #ccc; border-radius:8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">' +
                                            '<p style="margin:0; font-weight:bold; color:#333; font-size:16px;">Teléfono del Contacto: ' + phone + '</p>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>'
                            ); */
                            headerContainer.append(customRow);
                        } else {
                            customRow.find('p').html('Teléfono del Contacto: ' + phone);
                        }
                        console.log("SalesOrder_Detail_Custom_Js: Fila personalizada actualizada");
                    } else {
                        console.log("SalesOrder_Detail_Custom_Js: No se obtuvieron datos o se produjo un error", err, data);
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.log("SalesOrder_Detail_Custom_Js: Falló la petición AJAX", textStatus, errorThrown);
                });
            } else {
                console.log("SalesOrder_Detail_Custom_Js: No se pudo extraer el ID del contacto del enlace");
            }
        } else {
            console.log("SalesOrder_Detail_Custom_Js: No se encontró el enlace del contacto");
        }
    }
});
