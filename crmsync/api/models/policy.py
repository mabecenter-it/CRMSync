from dataclasses import dataclass, field
from typing import List, Optional

from api import client

from crmsync.api.models.contact import Contact


@dataclass
class Policy:
    id: Optional[int] = field(default=None, init=False)  # Se asigna después de la creación
    contacts: List[Contact]
    accountid: str
    productid: str

    def get_contacts_by_relationship(self, relationship) -> List[Contact]:
        return [contact for contact in self.contacts if contact.relationship == relationship]

    def get_owner(self) -> Contact:
        [owner] = self.get_contacts_by_relationship('Owner')
        return owner

    def get_spouse(self) -> Contact:
        [spouse] = self.get_contacts_by_relationship('dependent_1')
        return spouse

    def __post_init__(self):
        owner = self.get_owner()
        spouse = self.get_spouse()
        subject = f"{owner.first_name} {owner.last_name}"

        policy = client.doCreate(
            "SalesOrder",
            {
                "subject": subject,
                "sostatus": "Created",
                "account_id": self.accountid,
                "contact_id": owner.id,
                "bill_street": "1",
                "ship_street": "1",
                "assigned_user_id": "19x1",
                "enable_recurring": "0",
                "invoicestatus": "Created",
                "productid": self.productid,
                "cf_dependent_1": spouse.id,
                "LineItems": [{"productid": self.productid, "listprice": "0", "quantity": "1"}],
            },
        )
        self.id = policy.get("id")

    def add_contact(self, contact: Contact):
        # self.contacts.append(contact)
        pass

    def __repr__(self):
        return f"Policy(contacts={self.contacts})"
