from dataclasses import dataclass, field
from typing import Optional

from api import client


@dataclass
class Contact:
    id: Optional[str] = field(default=None, init=False)  # Now with type annotation
    first_name: str
    last_name: str
    relationship: str
    account_name: str

    def __post_init__(self):
        if self.first_name and self.last_name:
            query = f"""
                SELECT * FROM Contacts
                WHERE firstname = '{self.first_name}' and lastname = '{self.last_name}'
                LIMIT 1
            """
            data = next(iter(client.doQuery(query)), None)
            if not data:
                data = client.doCreate(
                    'Contacts',
                    {"firstname": self.first_name, "lastname": self.last_name, "accountname": self.account_name},
                )
            self.id = data.get("id")

    def update(self, accountid):
        self.data['accountname'] = accountid
        client.doUpdate(self.data)
