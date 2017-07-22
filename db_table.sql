-- noinspection SqlNoDataSourceInspectionForFile


/* client_order table */

  ﻿CREATE TABLE public.client_order
(
    id serial NOT NULL ,
    applicant_name character varying(100) COLLATE pg_catalog."default" NOT NULL,
    case_no integer NOT NULL,
    case_type smallint NOT NULL,
    case_year smallint NOT NULL,
    document_date date NOT NULL,
    document_type character varying(100) COLLATE pg_catalog."default" NOT NULL,
    upload_document character varying(200) COLLATE pg_catalog."default",
    paid_amount smallint,
    rejection_reason text COLLATE pg_catalog."default",
    payment_type character varying(10) COLLATE pg_catalog."default" NOT NULL,
    upload_date date,
    order_status character varying(10) COLLATE pg_catalog."default",
    applicant_doc_status character varying(10) COLLATE pg_catalog."default",
    applicant_doc_rejection_reason text COLLATE pg_catalog."default",
    upload_date_change_reason text COLLATE pg_catalog."default",
    licence_no character varying(50) COLLATE pg_catalog."default",
    order_id character varying(50) COLLATE pg_catalog."default"
)

  /*new changes for client side login */


  ALTER TABLE public.users
      ADD COLUMN type character varying(30) NOT NULL default 'applicant';


  ALTER TABLE public.client_order
      ADD COLUMN user_id integer;
  ALTER TABLE public.client_order
      ADD CONSTRAINT client_order_users_fk FOREIGN KEY (user_id)
  REFERENCES public.users (id) MATCH SIMPLE
  ON UPDATE NO ACTION
  ON DELETE CASCADE;


  username : super_admin
  pwd : super_admin123

  ﻿INSERT INTO public.users(name, username, email, password, type)
  VALUES ('super_admin', 'super_admin', 'super.admin@gmail.com', '32e9acac86bc621ff2f792be4d0035388ef6b466a3676166085bd38ea6c8c209c3d4decd0abf21daed6e6037039d60f6d52819591bc6e92621849917fefd37d3', 'super-admin');


/* add new columns in client_order table*/

  ﻿ALTER TABLE public.client_order
    ADD COLUMN apply_date date;


  ﻿ALTER TABLE public.client_order
    ADD COLUMN fill_no integer;

  ALTER TABLE public.client_order
    ADD COLUMN fill_year smallint;


  ALTER TABLE public.client_order
    ADD COLUMN lapsed_reason text;

  ALTER TABLE public.client_order
    ADD COLUMN issued_date date;

  ﻿ALTER TABLE public.client_order
    ADD COLUMN apply_year smallint NOT NULL default 2017;