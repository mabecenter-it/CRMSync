from sqlalchemy import MetaData, func
from sqlalchemy.ext.hybrid import hybrid_property

from crmsync.database.base import Base
from crmsync.database.engine import get_engine

metadata = MetaData()
metadata.reflect(bind=get_engine(), only=['vtiger_salesordercf'])


class VTigerSalesOrderCF(Base):
    __table__ = metadata.tables['vtiger_salesordercf']

    @hybrid_property
    def broker(self):
        # Lógica en Python para el objeto ya cargado
        if self.cf_2067:
            return self.cf_2067.strip().lower()
        return None

    @broker.expression  # type: ignore
    def broker(cls):
        return func.lower(func.trim(cls.__table__.c.cf_2067)).label('broker')

    @hybrid_property
    def saleswoman(self):
        if self.cf_2183:
            return self.cf_2183.strip().lower()
        return None

    @saleswoman.expression  # type: ignore
    def saleswoman(cls):
        return func.lower(func.trim(cls.__table__.c.cf_2183)).label('saleswoman')
