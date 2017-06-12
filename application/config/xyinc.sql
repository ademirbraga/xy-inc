CREATE TABLE "users" (
    "id" SERIAL NOT NULL,
    "ip_address" varchar(45),
    "username" varchar(100) NULL,
    "password" varchar(255) NOT NULL,
    "salt" varchar(255),
    "email" varchar(100) NOT NULL,
    "activation_code" varchar(40),
    "forgotten_password_code" varchar(40),
    "forgotten_password_time" int,
    "remember_code" varchar(40),
    "created_on" int NOT NULL,
    "last_login" int,
    "active" int4,
    "first_name" varchar(50),
    "last_name" varchar(50),
    "company" varchar(100),
    "phone" varchar(20),
  PRIMARY KEY("id"),
  CONSTRAINT "check_id" CHECK(id >= 0),
  CONSTRAINT "check_active" CHECK(active >= 0)
);


CREATE TABLE "groups" (
    "id" SERIAL NOT NULL,
    "name" varchar(20) NOT NULL,
    "description" varchar(100) NOT NULL,
  PRIMARY KEY("id"),
  CONSTRAINT "check_id" CHECK(id >= 0)
);


CREATE TABLE "users_groups" (
    "id" SERIAL NOT NULL,
    "user_id" integer NOT NULL,
    "group_id" integer NOT NULL,
  PRIMARY KEY("id"),
  CONSTRAINT "uc_users_groups" UNIQUE (user_id, group_id),
  CONSTRAINT "users_groups_check_id" CHECK(id >= 0),
  CONSTRAINT "users_groups_check_user_id" CHECK(user_id >= 0),
  CONSTRAINT "users_groups_check_group_id" CHECK(group_id >= 0)
);


INSERT INTO groups (id, name, description) VALUES
    (1,'admin','Administrator'),
    (2,'members','General User');

INSERT INTO users (ip_address, username, password, salt, email, activation_code, forgotten_password_code, created_on, last_login, active, first_name, last_name, company, phone) VALUES
    ('127.0.0.1','administrator','$2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36','','admin@admin.com','',NULL,'1268889823','1268889823','1','Admin','istrator','ADMIN','0');

INSERT INTO users_groups (user_id, group_id) VALUES
    (1,1),
    (1,2);

CREATE TABLE "login_attempts" (
    "id" SERIAL NOT NULL,
    "ip_address" varchar(15),
    "login" varchar(100) NOT NULL,
    "time" int,
  PRIMARY KEY("id"),
  CONSTRAINT "check_id" CHECK(id >= 0)
);



CREATE TABLE modelo(
  id_modelo   INT PRIMARY KEY      NOT NULL,
  nome_modelo VARCHAR(150) NOT NULL,
  descricao   TEXT NULL,
  ativo       BOOLEAN not null DEFAULT true
);


CREATE TABLE modelo_input(
  id_modelo_input   INT PRIMARY KEY      NOT NULL,
  id_modelo   INT NOT NULL,
  nome_input VARCHAR(150) NOT NULL,
  type VARCHAR(50) NOT NULL,
  required BOOLEAN NOT NULL DEFAULT TRUE,
  tamanho INT NULL DEFAULT NULL,
  ativo       BOOLEAN not null DEFAULT true,
  unico       BOOLEAN not null DEFAULT true,
  constraint fk_modelo_input
  foreign key (id_modelo)
  REFERENCES modelo (id_modelo)
);


CREATE TABLE  ci_sessions (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	ip_address varchar(45) DEFAULT '0' NOT NULL,
	user_agent varchar(120) NOT NULL,
	last_activity bigint DEFAULT 0 NOT NULL,
	user_data text NOT NULL,
	PRIMARY KEY (session_id)
);

CREATE INDEX last_activity_idx ON ci_sessions(last_activity);