



  ALTER TABLE public.users
      ADD COLUMN type character varying(30) NOT NULL default ‘applicant’;


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
