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