/*
    MICAELA
	22-08-2019  9:03 P.M.

*/
UPDATE pmo_gastos set status='SOLICITADO';

alter table pmo_actividades add column presupuesto_inicial t_amount;
update pmo_actividades set presupuesto_inicial = costo;


----------------------------------------------------------
----------------------------------------------------------
----------------------------------------------------------

/*
	micaela
	23-08-2019      4:51 P.M.
*/
alter table vnt_proveedor add column formato_requisito_id bigint;
ALTER TABLE vnt_proveedor
   ADD CONSTRAINT vnt_proveedor_formato_requisito_id_fkey
   FOREIGN KEY (formato_requisito_id) 
   REFERENCES sys_documents(id);