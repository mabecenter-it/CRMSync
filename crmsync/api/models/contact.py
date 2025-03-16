from dataclasses import dataclass, field
from typing import Optional

from api import client


@dataclass
class Contact:
    id: Optional[str] = field(default=None, init=False)  # Now with type annotation
    data: Optional[list] = field(default=None, init=False)
    country: Optional[str] = field(default="United States", init=False) 
    work: Optional[str] = field(default=None, init=False)
    income: Optional[str] = field(default=None, init=False)
    language: Optional[str] = field(default=None, init=False)
    smoke: Optional[str] = field(default=None, init=False)
    jail: Optional[str] = field(default=None, init=False)
    account_name: str
    apply: str
    relationship: str
    first_name: str
    last_name: str
    second_name: str
    gender: str
    dob: str
    ssn: str
    document: str
    memberid: str
    username: str
    password: str
    
    def __post_init__(self):
        if self.first_name and self.last_name:
            query = f"""
                SELECT * FROM Contacts
                WHERE firstname = '{self.first_name}' and lastname = '{self.last_name}'
                LIMIT 1
            """
            data = next(iter(client.doQuery(query)), None)
            new_data = {
                #"apply": self.apply,
                #"relationship": self.relationship,
                "firstname": self.first_name,
                "secondname": self.second_name,
                "lastname": self.last_name,
                "account_id": self.account_name,
                "birthday": self.dob.strftime("%Y-%m-%d")
            }
            if not data:
                data = client.doCreate('Contacts', new_data)
            else:
                def normalize_value(value):
                    try:
                        return float(value)  # Convierte números a float para comparación
                    except (ValueError, TypeError):
                        return str(value).strip() 
        
                changes = {key: value for key, value in new_data.items() if data.get(key) != value}
                
                normalized_changes = {
                    k: v
                    for k, v in changes.items()
                    if normalize_value(v) != normalize_value(data.get(k))
                }
                
                if normalized_changes:
                    updated_data = {**data, **normalized_changes} 
                    client.doUpdate(updated_data)
                    
            self.id = data.get("id")
            self.data = data

    def update(self, accountid):
        self.data['account_id'] = accountid
        client.doUpdate(self.data)