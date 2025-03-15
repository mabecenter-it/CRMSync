{* Plantilla de edición personalizada para SalesOrder *}
<h1>{'Editando una Orden de Venta'|vtranslate:$MODULE}</h1>

<div id="ContactItems">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Contacto</th>
                <th>Relación</th>
                <th>Documento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="contactRows">
            <!-- Aquí se agregarán dinámicamente los contactos -->
        </tbody>
    </table>
    <button type="button" class="btn btn-success" id="addContactRow">+ Agregar Contacto</button>
</div>
