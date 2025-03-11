from dataclasses import dataclass, field
from typing import Optional

from api import client


@dataclass
class Contact:
    id: Optional[str] = field(default=None, init=False)  # Now with type annotation
    first_name: str
    last_name: str
    relationship: str

    def __post_init__(self):
        contact = client.doCreate(
            'Contacts',
            {
                "firstname": self.first_name,
                "lastname": self.last_name,
            },
        )
        self.id = contact.get("id")
