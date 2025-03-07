from dataclasses import dataclass, field

from api import client


@dataclass
class Contact:
    id = field(default=None, init=False)  # Se asigna después de la creación
    first_name: str
    second_name: str
    last_name: str
    gender: str

    def __post_init__(self):
        contact = client.doCreate(
            'Contacts',
            {
                "firstname": self.first_name,
                # "cf_934": self.second_name,
                "lastname": self.last_name,
            },
        )
        self.id = contact.get("id")
