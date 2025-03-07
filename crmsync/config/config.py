import os
from dataclasses import dataclass

from dotenv import load_dotenv

# Carga el archivo .env (por defecto busca el archivo en el directorio actual)
load_dotenv()


@dataclass
class SyncConfig:
    def __init__(self):
        self.user = os.getenv('DB_USER')
        self.password = os.getenv('DB_PASSWORD')
        self.host = os.getenv('DB_HOST')
        self.port = os.getenv('DB_PORT')
        self.name_db = os.getenv('DB_NAME')
        self.type = os.getenv('DB_TYPE')
        self.connector = os.getenv('DB_CONNECTOR')
        self.host_api = os.getenv('VTIGER_HOST')
        self.user_api = os.getenv('VTIGER_USERNAME')
        self.token = os.getenv('VTIGER_TOKEN')
        # status_values: list = field(default_factory=lambda: ['Active', 'Initial Enrollment', 'Sin Digitar'])
        # effective_date: date = field(default_factory=lambda: date(2025, 1, 1))
        # sell_date: date = field(default_factory=lambda: date(2024, 10, 28))

    def __post_init__(self):
        # self.mapping_file = self._load_mapping('salesorder')
        # self.handle_file = self._load_mapping('handler')
        pass
