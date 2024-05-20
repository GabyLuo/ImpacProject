CREATE SEQUENCE public.crm_cultivos_id_seq
    INCREMENT 1
    START 1
    MINVALUE 1;
CREATE TABLE public.crm_cultivos
(
    id bigint NOT NULL DEFAULT nextval('crm_cultivos_id_seq'::regclass),
	anio_administracion smallint,
	acreditaciones text,
	toma_desiciones text,
	cita_confirmada varchar(20),
	ciudad_origen bigint,
	estado_origen bigint,
	consideraciones text,
	contrato_proveedores text,
	enfoque_cierre text,
	esquema text,
	fecha_cumpleanios date,
	informacion_enviada text,
	monto_asignado t_amount,
	necesidades text,
	partido_politico varchar(100),
	contacto varchar(50),
	taller text,
	tipo_recurso text,
	tipo_servicio text,
    prospecto_id bigint,
	
    CONSTRAINT crm_cultivos_pkey PRIMARY KEY (id),
    CONSTRAINT crm_cultivo_prospectos_id_fkey FOREIGN KEY (prospecto_id) REFERENCES crm_prospectos(id)
);