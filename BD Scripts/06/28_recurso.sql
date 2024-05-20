ALTER TABLE public.vnt_recurso
    ADD COLUMN licitacion_id bigint;

ALTER TABLE vnt_recurso
ALTER COLUMN nombre type text;