class SalesOrder_DetailView_Custom extends Vtiger_Detail_View {
    public function getFooterScripts(Vtiger_Request $request) {
        // Obtiene los scripts originales
        $footerScriptInstances = parent::getFooterScripts($request);

        // Define la ruta de tu archivo JS personalizado
        $jsFileNames = [
            'modules/SalesOrder/resources/SalesOrder_Detail_Custom.js'
        ];

        // Convierte las rutas a objetos que vtiger entiende
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);

        // Combina los scripts originales con el nuevo script
        return array_merge($footerScriptInstances, $jsScriptInstances);
    }
}
