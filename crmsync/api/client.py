from typing import List

from api.models.product import Product
from pandas import Series

from crmsync.api.models.contact import Contact
from crmsync.api.models.policy import Policy


class PolicyAssembler:
    def __init__(self, row: Series):
        # Se asume que en cada registro existen los campos 'policy_id', 'policy_name'
        # y para el contacto: 'contactid', 'contact_name', 'contact_email'

        # Crear el objeto Contact a partir del registro.

        product = self.get_product(row)
        contacts: List[Contact] = self.get_contacts(row)

        self.get_policies(contacts, product.id)

        # policy.add_contact(contact)
        # Retornamos una lista de objetos Policy
        # return list(policy.values())

    def get_product(self, row) -> Product:
        return Product(
            planid=row.get('cf_2035'),
            benefitid=row.get('cf_2203'),
        )

    def get_contacts(self, row) -> List[Contact]:
        contacts = [
            Contact(
                first_name=row.get('cf_2293'),
                last_name=row.get('cf_2297'),
                relationship='Owner',
            ),
            Contact(
                first_name=row.get('cf_2347'),
                last_name=row.get('cf_2351'),
                relationship='Spouse',
            )
            if row.get('cf_2385')
            else None,
            Contact(
                first_name=row.get('cf_2405'),
                last_name=row.get('cf_2409'),
                relationship='dependent_1',
            )
            if row.get('cf_2401')
            else None,
            Contact(
                first_name=row.get('cf_2443'),
                last_name=row.get('cf_2447'),
                relationship='dependent_2',
            )
            if row.get('cf_2439')
            else None,
            Contact(
                first_name=row.get('cf_2479'),
                last_name=row.get('cf_2483'),
                relationship='dependent_3',
            )
            if row.get('cf_2415')
            else None,
            Contact(
                first_name=row.get('cf_2515'),
                last_name=row.get('cf_2519'),
                relationship='dependent_4',
            )
            if row.get('cf_2511')
            else None,
            Contact(
                first_name=row.get('cf_2645'),
                last_name=row.get('cf_2649'),
                relationship='dependent_5',
            )
            if row.get('cf_2615')
            else None,
        ]
        return [contact for contact in contacts if contact is not None]

    def get_policies(self, contacts, productid) -> Policy:
        return Policy(
            contacts=contacts,
            productid=productid,
        )
