-- 22/08/2019 11:55
ALTER TABLE public.sys_log_levels
   ADD PRIMARY KEY (id);

ALTER TABLE public.pmo_proveedores
   ALTER COLUMN razon_social DROP NOT NULL;

ALTER TABLE public.pmo_proveedores
   ALTER COLUMN rfc DROP NOT NULL;

ALTER TABLE public.pmo_proveedores
   ALTER COLUMN curp DROP NOT NULL;