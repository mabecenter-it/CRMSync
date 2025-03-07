import pandas as pd
from sqlalchemy import and_
from sqlalchemy.orm import sessionmaker
from tqdm import tqdm

from crmsync.api.client import PolicyAssembler
from crmsync.config.config import SyncConfig
from crmsync.config.logging import setup_logging
from crmsync.database.engine import get_engine
from crmsync.database.unit_of_work import UnitOfWork
from crmsync.models.vtigercrm_contactcf import VTigerContactsCF
from crmsync.models.vtigercrm_contactdetails import VTigerContactDetails
from crmsync.models.vtigercrm_crmentity import VTigerCRMEntity
from crmsync.models.vtigercrm_salesorder import VTigerSalesOrder
from crmsync.models.vtigercrm_salesordercf import VTigerSalesOrderCF


class Syncer:
    def __init__(self):
        if not get_engine():
            return False

        self.unit_of_work = UnitOfWork(lambda: sessionmaker(bind=get_engine())())
        self.config = SyncConfig()

    def sync(self):
        try:
            logger = setup_logging()
            logger.info("Syncing data...")
            joins = [
                (VTigerSalesOrder, VTigerSalesOrderCF.salesorderid == VTigerSalesOrder.salesorderid),
                (VTigerContactsCF, VTigerSalesOrder.contactid == VTigerContactsCF.contactid),
                (
                    VTigerCRMEntity,
                    and_(VTigerSalesOrder.salesorderid == VTigerCRMEntity.crmid, VTigerCRMEntity.deleted == 0),
                ),
                (VTigerContactDetails, VTigerSalesOrder.contactid == VTigerContactDetails.contactid),
            ]

            with self.unit_of_work as uow:
                base_query = uow.query(
                    VTigerSalesOrderCF,
                    VTigerSalesOrderCF.broker,
                    VTigerSalesOrderCF.saleswoman,
                    VTigerContactsCF,
                    VTigerContactDetails,
                ).select_from(VTigerSalesOrderCF)

                # Aplicar los JOINs de forma recursiva
                query = self.recursive_join(base_query, joins)

                # Aplicar los filtros según la lógica original
                query = (
                    query.filter(VTigerSalesOrderCF.cf_2141.notin_(['Cancelación', 'Prospecto']))
                    .filter(VTigerSalesOrderCF.cf_2059 >= '2025-01-01')
                    .filter(VTigerSalesOrderCF.cf_2067 != 'Otro Broker')
                    .limit(10)
                )

                # Ejecutar la consulta y mostrar los resultados
                df = pd.read_sql(query.statement, uow.bind)

                for _, row in tqdm(df.iterrows(), total=df.shape[0]):
                    PolicyAssembler(row)

                """ self.client.doCreate('Contacts', {
                    "firstname": "Testing2",
                    "lastname": "Testing2",
                    "birthday":"2025-01-01",
                    "cf_860": "FEMALE",
                    "assigned_user_id": "19x1"
                })
                logger.info("Data synced successfully.") """

        except Exception as e:
            print(f"Error: {e}")
            return False

    def recursive_join(self, query, join_list):
        """
        Función recursiva que añade JOINs a la consulta.

        :param query: Consulta base.
        :param join_list: Lista de tuplas (modelo, condición de join).
        :return: Consulta con los JOINs aplicados.
        """
        if not join_list:
            return query
        model, condition = join_list[0]
        new_query = query.join(model, condition)
        return self.recursive_join(new_query, join_list[1:])
