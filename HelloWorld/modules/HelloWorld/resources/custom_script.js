$(document).ready(function(){
    // Verifica que el módulo actual sea SalesOrder
    if (app.getModuleName && app.getModuleName() === 'SalesOrder') {
        console.log("HelloWorld script loaded in SalesOrder");
    }
});
