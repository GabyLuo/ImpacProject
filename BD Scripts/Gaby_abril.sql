ALTER TABLE public.vnt_remisiones
    ADD COLUMN cancelado boolean DEFAULT false;

ALTER TABLE public.vnt_remisiones_facturas
    ADD COLUMN cancelado boolean DEFAULT false;