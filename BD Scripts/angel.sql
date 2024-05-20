ALTER TABLE pmo_proveedores
ADD direccion varchar(100),
ADD banco varchar(50),
ADD clabe varchar(19),
ADD banco2 varchar(50),
ADD clabe2 varchar(19),
ADD telefono varchar(13),
ADD contacto varchar(50);


UPDATE sys_menu_items
   SET  menu_id=4,ord=0 
 WHERE id=7;


 ALTER TABLE pmo_proyecto
  ADD COLUMN lider_proyecto bigint,
  ADD CONSTRAINT FK_lider_proyecto FOREIGN KEY (lider_proyecto)     
      REFERENCES sys_users (id)
      ON DELETE CASCADE    
      ON UPDATE CASCADE;

UPDATE pmo_proyecto
   SET  lider_proyecto=1 