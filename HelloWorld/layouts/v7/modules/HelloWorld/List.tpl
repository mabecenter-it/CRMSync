<table id="ContactItems">
    <tr>
        <th>Contacto</th>
        <th>Relación</th>
        <th>Documento</th>
    </tr>
    {foreach from=$CONTACTS item=CONTACT}
    <tr>
        <td><input name="contactid[]" value="{$CONTACT.contactid}"/></td>
        <td><input name="relationship[]" value="{$CONTACT.relationship}"/></td>
        <td><input name="documentid[]" value="{$CONTACT.documentid}"/></td>
    </tr>
    {/foreach}
</table>
<button onclick="addContactRow()">Añadir Contacto</button>
