ALTER TABLE public.inv_movimientos
    ALTER COLUMN created DROP DEFAULT;

ALTER TABLE public.inv_movimientos
    ADD COLUMN fecha_ejecutado timestamp without time zone;

update inv_movimientos set fecha_ejecutado = modified;