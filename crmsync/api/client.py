from pandas import Series

from crmsync.api.models.contact import Contact
from crmsync.api.models.policy import Policy


class PolicyAssembler:
    def __init__(self, row: Series):
        # Se asume que en cada registro existen los campos 'policy_id', 'policy_name'
        # y para el contacto: 'contactid', 'contact_name', 'contact_email'

        # Crear el objeto Contact a partir del registro.

        contact = self.get_contacts(row)
        policy = self.get_policies(f"{contact.first_name} {contact.second_name} {contact.last_name}", contact.id)

        policy.add_contact(contact)
        # Retornamos una lista de objetos Policy
        # return list(policy.values())

    def get_contacts(self, row) -> Contact:
        return Contact(
            first_name=row.get('cf_2293'),
            second_name=row.get('cf_2295'),
            last_name=row.get('cf_2297'),
            gender=row.get('cf_2303'),
        )

    def get_policies(self, full_name, contactid) -> Policy:
        return Policy(
            subject=full_name,
            contactid=contactid,
        )
