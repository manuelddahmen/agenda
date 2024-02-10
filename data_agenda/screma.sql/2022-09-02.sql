/*
 * Copyright (c) 2023-2024. Manuel Daniel Dahmen
 *
 *

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

-- we don't know how to generate root <with-no-name> (class Root) :(
create table table_activites
(
    id           integer     default 10000
        constraint table_activites_pk
            primary key,
    nom_activite varchar(30) default '',
    id_employe   integer,
    date_start   date        default '2022-11-01',
    date_end     date        default '2022-11-01',
    isSolo       boolean     default true not null
);

create table table_employes
(
    id     integer     default 1000
        primary key,
    nom    varchar(30) default '',
    prenom varchar(30) default ''
);

create table table_hospitalises
(
    chambre integer     default 300
        primary key,
    nom     varchar(30) default '',
    prenom  varchar(30) default '',
    sex     varchar(1)  default 'M'
);

create table table_notes
(
    id              integer  not null
        constraint table_notes_pk
            primary key,
    date_now        datetime not null,
    text_note       text     not null,
    nom_utilisateur varchar(20)
);

create unique index table_notes_id_uindex
    on table_notes (id);

create table table_taches
(
    id_activite                      integer     not null
        constraint table_tache_table_activites_id_activite_fk
            references table_activites (id_activite),
    id_hospitalises                  integer     not null
        constraint table_tache_table_hospitalise_fk
            references table_hospitalises,
    id                               integer     not null
        constraint table_tache_pk
            primary key,
    jour__semaine_demie__heure_temps varchar(30) not null,
    real_date                        datetime
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
            references table_taches (id_activite)
);

create unique index table_taches_patients_id_uindex
    on table_taches_patients (id);

create table table_utilisateurs
(
    nom_utilisateur varchar(20) not null
        constraint table_utilisateurs_pk
            primary key,
    mot_de_passe    varchar(20)
);

