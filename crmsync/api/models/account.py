from dataclasses import dataclass, field
from typing import Optional

from api import client


@dataclass
class Account:
    id: Optional[str] = field(default=None, init=False)
    first_name: str
    last_name: str
    second_name: str
    gender: str
    dob: str
    SSN: str
    income: str
    phone1: str
    otherphone: str
    emergencyphone: str
    email1: str
    email2: str
    ship_street: str
    ship_pobox: str
    ship_city: str
    ship_state: str
    ship_code: str

    def __post_init__(self):
        # Paso 2: Producto hijo
        accountname = f'{self.first_name} {self.second_name} {self.last_name}'
        accountExits = client.doQuery(f"SELECT id FROM Accounts WHERE accountname = '{accountname}' LIMIT 1")
        if not accountExits:
            account = client.doCreate(
                'Accounts',
                {
                    "accountname": accountname,
                    "phone": self.phone1,
                    "otherphone": self.otherphone,
                    "fax": "",
                    "email1": self.email1,
                    "email2": self.email2,
                    "rating": "Active",
                    "annual_revenue": self.income,
                    "ship_street": self.ship_street,
                    "ship_pobox": self.ship_pobox,
                    "ship_city": self.ship_city,
                    "ship_state": self.ship_state,
                    "ship_code": self.ship_code,
                    "ship_country": "United States",
                },
            )
        else:
            account = [accountExits]

        self.id = account['id']
