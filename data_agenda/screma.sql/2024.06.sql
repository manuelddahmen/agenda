/*
 * Copyright (c) 2024. Manuel Daniel Dahmen
 *
 *
 *    Copyright 2012-2023 Manuel Daniel Dahmen
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

create table global_users
(
    id         integer      not null,
    conn_email varchar(100) not null,
    ddn        date         not null,
    prenom     varchar(20)  not null,
    nom        varchar(20)  not null
);

create table table_activite_christine
(
    id           integer not null
        unique,
    user_id      integer not null,
    site         string  not null,
    patient_id   id_hospitalise,
    personnel_id integer,
    groupe_id    integer,
    quart        integer,
    hour         integer,
    day          integer
);

create table table_activites_autonomie
(
    id           INTEGER,
    nom_activite varchar(30),
    isSolo       boolean,
    user_id      integer
);

create table table_employes
(
    id       integer     default 1000
        primary key,
    nom      varchar(30) default '',
    prenom   varchar(30) default '',
    fonction varchar(10),
    user_id  integer
);

create table table_groupes
(
    id      int                not null
        constraint table_groupes_pk
            primary key,
    name    text,
    user_id integer default -1 not null
);

create table table_hospitalises
(
    chambre        INTEGER
        constraint table_hospitalises_pk
            primary key,
    nom            varchar(30) default '',
    prenom         varchar(30) default '',
    sex            varchar(1)  default 'M',
    rehabilitation INT         default 1,
    user_id        INTEGER,
    birthdate      datetime    default '01/01/1960',
    vaisselle      integer     default 0 not null,
    acti_obli_1    integer,
    acti_obli_2    integer
);

create table table_notes
(
    id              integer  not null
        constraint table_notes_pk
            primary key,
    date_now        datetime not null,
    text_note       text     not null,
    nom_utilisateur varchar(20),
    user_id         integer
);

create unique index table_notes_id_uindex
    on table_notes (id);

create table table_personnel
(
    name    TEXT,
    id      int not null
        constraint id
            primary key,
    user_id integer default -1
);

create table table_planning
(
    id integer not null
        primary key
        unique
);

create table table_recover_password
(
    id          int
        primary key
        constraint table_recover_password_pk
            unique,
    ref_user_id int,
    hash        string,
    timestamp   datatime INT
);

create table table_taches_autonomie
(
    id_activite                      INTEGER
        constraint table_taches_autonomie_table_activites_autonomie_id_fk
            references table_activites_autonomie (id),
    id_hospitalises                  INTEGER
        constraint table_taches_autonomie_table_hospitalises_chambre_fk
            references table_hospitalises,
    id                               INTEGER,
    jour__semaine_demie__heure_temps varchar(30),
    real_date                        datetime,
    user_id                          integer
);

create table table_users
(
    id         int
        primary key,
    username   string,
    theme_name string,
    url        string,
    password   string,
    name       string,
    email      string default '----' not null
);

create table table_activites
(
    id             INTEGER     default 10000
        constraint table_activites_pk
            primary key,
    nom_activite   varchar(30) default '',
    id_employe     INTEGER,
    rehabilitation INTEGER,
    autonomie      INT         default 0,
    user_id        INTEGER
        constraint table_activites_table_users_id_fk
            references table_users
);

create table table_activites_dg_tmp
(
    id             INTEGER     default 10000
        constraint table_activites_pk
            primary key,
    nom_activite   varchar(30) default '',
    id_employe     INTEGER not null,
    rehabilitation INTEGER,
    autonomie      INT         default 0,
    user_id        INTEGER
        constraint table_activites_table_users_id_fk
            references table_users
);

create table table_taches
(
    id_activite                      integer     not null
        constraint table_tache_table_activites_id_activite_fk
            references table_activites (id_activite),
    id                               integer     not null
        constraint table_tache_pk
            primary key,
    jour__semaine_demie__heure_temps varchar(30) not null,
    user_id                          integer
);

create table table_assoc_personnel_activ
(
    id          integer
        constraint table_assoc_personnel_activ_pk
            primary key,
    id_activite integer
        constraint table_assoc_personnel_activ_table_activites_null_fk
            references table_activites,
    id_tache    integer
        constraint table_assoc_personnel_activ_table_taches_null_fk
            references table_taches,
    id_employe  integer
        constraint table_assoc_personnel_activ_table_employes_null_fk
            references table_employes,
    user_id     integer
);

create unique index table_tache_id_uindex
    on table_taches (id);

create table table_taches_patients
(
    id         integer not null
        constraint table_taches_patients_pk
            primary key,
    id_patient integer not null
        references table_hospitalises,
    id_tache   integer
        constraint table_taches_patients___tache_fk
            references table_taches (id_activite),
    user_id    integer
);

create unique index table_taches_patients_id_uindex
    on table_taches_patients (id);

create table table_utilisateurs
(
    nom_utilisateur varchar(20) not null
        constraint table_utilisateurs_pk
            primary key,
    mot_de_passe    varchar(20),
    user_id         integer
);

