CREATE TABLE public.sat_formas_pagos (
    id bigserial primary key NOT NULL,
    created timestamp without time zone DEFAULT now(),
    created_by bigint,
    modified timestamp without time zone DEFAULT now(),
    modified_by bigint,
    clave character varying(4) NOT NULL,
    descripcion character varying(50) NOT NULL
);

INSERT INTO public.sat_formas_pagos VALUES (1, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '01', 'Efectivo');
INSERT INTO public.sat_formas_pagos VALUES (2, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '02', 'Cheque nominativo');
INSERT INTO public.sat_formas_pagos VALUES (3, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '03', 'Transferencia electrónica de fondos');
INSERT INTO public.sat_formas_pagos VALUES (4, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '04', 'Tarjeta de crédito');
INSERT INTO public.sat_formas_pagos VALUES (5, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '05', 'Monedero electrónico');
INSERT INTO public.sat_formas_pagos VALUES (6, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '06', 'Dinero electrónico');
INSERT INTO public.sat_formas_pagos VALUES (7, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '08', 'Vales de despensa');
INSERT INTO public.sat_formas_pagos VALUES (8, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '12', 'Dación en pago');
INSERT INTO public.sat_formas_pagos VALUES (9, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '13', 'Pago por subrogación');
INSERT INTO public.sat_formas_pagos VALUES (10, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '14', 'Pago por consignación');
INSERT INTO public.sat_formas_pagos VALUES (11, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '15', 'Condonación');
INSERT INTO public.sat_formas_pagos VALUES (12, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '17', 'Compensación');
INSERT INTO public.sat_formas_pagos VALUES (13, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '23', 'Novación');
INSERT INTO public.sat_formas_pagos VALUES (14, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '24', 'Confusión');
INSERT INTO public.sat_formas_pagos VALUES (15, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '25', 'Remisión de deuda');
INSERT INTO public.sat_formas_pagos VALUES (16, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '26', 'Prescripción o caducidad');
INSERT INTO public.sat_formas_pagos VALUES (17, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '27', 'A satisfacción del acreedor');
INSERT INTO public.sat_formas_pagos VALUES (18, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '28', 'Tarjeta de débito');
INSERT INTO public.sat_formas_pagos VALUES (19, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '29', 'Tarjeta de servicios');
INSERT INTO public.sat_formas_pagos VALUES (20, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '30', 'Aplicación de anticipos');
INSERT INTO public.sat_formas_pagos VALUES (21, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '31', 'Intermediario pagos');
INSERT INTO public.sat_formas_pagos VALUES (22, '2019-09-25 11:38:52.637227', 1, '2019-09-25 11:38:52.637227', 1, '99', 'Por definir');

CREATE TABLE public.vnt_remisiones (
    id bigserial PRIMARY KEY NOT NULL,
    created timestamp without time zone DEFAULT now(),
    created_by bigint,
    modified timestamp without time zone DEFAULT now(),
    modified_by bigint,
    empresa_id bigint NOT NULL,
    cliente_id bigint NOT NULL,
    fecha_venta timestamp without time zone  NOT NULL,
    status text NOT NULL DEFAULT 'NUEVO'
);

alter table vnt_remisiones add column folio_fiscal bigint;
alter table vnt_remisiones add column serie text;
alter table vnt_remisiones add column fecha_factura timestamp without time zone;
alter table vnt_remisiones add column tipo_comprobante varchar(1);
alter table vnt_remisiones add column metodo_pago varchar(3);
alter table vnt_remisiones add column forma_pago bigint;
alter table vnt_remisiones add column uso_cfdi bigint;
alter table vnt_remisiones add column lugar_expedicion bigint;
alter table vnt_remisiones add column status_timbrado bigint default 0;
alter table vnt_remisiones add column id_request text;
alter table vnt_remisiones add column message text;
alter table vnt_remisiones add column uuid text;
alter table vnt_remisiones add column id_cancelacion text;
alter table vnt_remisiones add column message_cancelacion text;
alter table vnt_remisiones add column id_cancelacion_asc text;
alter table vnt_remisiones add column acuseSat_cancelacion text;

CREATE TABLE public.vnt_remision_detalles (
    id bigserial PRIMARY KEY NOT NULL,
    created timestamp without time zone DEFAULT now(),
    created_by bigint,
    modified timestamp without time zone DEFAULT now(),
    modified_by bigint,
    remision_id bigint NOT NULL,
    producto_id bigint NOT NULL,
    cantidad bigint NOT NULL,
    precio_unitario numeric(15,2)  NOT NULL
);

alter table vnt_remision_detalles add column descripcion text;
alter table vnt_remision_detalles add column impuesto boolean;
alter table vnt_remision_detalles add column impuesto_importe numeric(15,2);


CREATE TABLE sat_uso_cfdi (
    id bigserial PRIMARY KEY NOT NULL,
    created timestamp without time zone DEFAULT now(),
    created_by bigint,
    modified timestamp without time zone DEFAULT now(),
    modified_by bigint,
    clave character varying(3) NOT NULL,
    descripcion character varying(100) NOT NULL
);

INSERT INTO sat_uso_cfdi VALUES (2, '2018-10-08 16:29:44.124899', 1, '2018-10-08 16:29:44.124899', 1, 'G02', 'Devoluciones, descuentos o bonificaciones');
INSERT INTO sat_uso_cfdi VALUES (6, '2018-10-08 16:29:44.124899', 1, '2018-10-08 16:29:44.124899', 1, 'I03', 'Equipo de transporte');
INSERT INTO sat_uso_cfdi VALUES (3, '2018-10-08 16:29:44.124899', 6, '2020-10-02 13:35:06', 6, 'G03', 'Gastos en general');
INSERT INTO sat_uso_cfdi VALUES (1, '2018-10-08 16:29:44.124899', 8, '2020-10-05 08:17:56', 50, 'G01', 'Adquisición de mercancias');
INSERT INTO sat_uso_cfdi VALUES (22, '2018-10-08 16:29:44.124899', 12, '2020-09-28 16:26:53', 50, 'P01', 'Por definir');

CREATE TABLE vnt_remision_pagos (
    id bigserial PRIMARY KEY NOT NULL,
    created timestamp without time zone DEFAULT now(),
    created_by bigint,
    modified timestamp without time zone DEFAULT now(),
    modified_by bigint,
    remision_id bigint NOT NULL,
    fecha_pago timestamp without time zone NOT NULL,
    num_parcialidad bigint NOT NULL,
    total numeric(15,2) NOT NULL
);

alter table vnt_remision_pagos add column forma_pago bigint;
alter table vnt_remision_pagos add column status_timbrado bigint default 0;
alter table vnt_remision_pagos add column id_request text;
alter table vnt_remision_pagos add column message text;
alter table vnt_remision_pagos add column uuid text;
alter table vnt_remision_pagos add column id_cancelacion text;
alter table vnt_remision_pagos add column message_cancelacion text;
alter table vnt_remision_pagos add column id_cancelacion_asc text;
alter table vnt_remision_pagos add column acuseSat_cancelacion text;

alter table vnt_empresa add column batuta_id bigint;