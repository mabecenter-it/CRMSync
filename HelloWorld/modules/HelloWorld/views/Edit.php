<?php
include_once 'modules/SalesOrder/views/Edit.php';

class HelloWorld_SalesOrder_Edit_View extends SalesOrder_Edit_View {

    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);

        // Log para verificar si la vista personalizada se está ejecutando
        error_log("✅ Cargando vista personalizada de HelloWorld para SalesOrder");
        die("🚨 La vista personalizada de HelloWorld para SalesOrder se está ejecutando.");

        // Usar la plantilla personalizada
        $viewer->view('Edit.tpl', 'HelloWorld');
    }
}
