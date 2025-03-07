from dataclasses import dataclass, field
from typing import Optional

from api import client

from crmsync.api.models.contact import Contact


@dataclass
class Policy:
    id: Optional[int] = field(default=None, init=False)  # Se asigna después de la creación
    subject: str
    contactid: str

    def __post_init__(self):
        policy = client.doCreate(
            "SalesOrder",
            {
                "subject": self.subject,
                "status": "Created",
                "account_id": "11x3",
                "contact_id": self.contactid,
                "bill_street": "1",
                "ship_street": "1",
                "assigned_user_id": "19x1",
                "LineItems": [{"productid": "14x4", "listprice": "0", "quantity": "1"}],
            },
        )
        self.id = policy.get("id")

    def add_contact(self, contact: Contact):
        # self.contacts.append(contact)
        pass

    def __repr__(self):
        return f"Policy(contacts={self.contacts})"
