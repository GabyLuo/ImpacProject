create table crm_destinos (id bigserial not null primary key,
						   code t_name, 
						   domicilio text,
						   recibe_persona text,
						   nombre_destino text,
						  created_by bigint, 
						  created date default now());

