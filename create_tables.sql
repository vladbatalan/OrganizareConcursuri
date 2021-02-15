
CREATE TABLE concursuri (
    id_concurs      NUMBER(4) NOT NULL,
    nume_concurs    VARCHAR2(50) NOT NULL,
    editie_concurs  VARCHAR2(30) NOT NULL,
    oras            VARCHAR2(30) NOT NULL
);

ALTER TABLE concursuri ADD CONSTRAINT concursuri_pk PRIMARY KEY ( id_concurs );

ALTER TABLE concursuri ADD CONSTRAINT concursuri_nume_un UNIQUE ( nume_concurs,
                                                                  editie_concurs );

CREATE TABLE concursuri_participanti (
    id_concurs                  NUMBER(4) NOT NULL,
    id_participant              NUMBER(5) NOT NULL,
    id_concursuri_participanti  NUMBER NOT NULL
);

ALTER TABLE concursuri_participanti ADD CONSTRAINT concursuri_participanti_pk PRIMARY KEY ( id_concursuri_participanti );

CREATE TABLE evenimente (
    id_eveniment    NUMBER(5) NOT NULL,
    nume_eveniment  VARCHAR2(50) NOT NULL,
    cost_eveniment  NUMBER(9, 2) DEFAULT 0 NOT NULL,
    data_eveniment  DATE NOT NULL,
    interval_timp   VARCHAR2(30),
    public_tinta    VARCHAR2(50),
    id_concurs      NUMBER(4) NOT NULL
);

ALTER TABLE evenimente ADD CONSTRAINT evenimente_cost_eveniment_ck CHECK ( cost_eveniment >= 0 );

ALTER TABLE evenimente ADD CONSTRAINT evenimente_pk PRIMARY KEY ( id_eveniment );

CREATE TABLE evenimente_organizatori (
    id_eveniment    NUMBER(5) NOT NULL,
    id_organizator  NUMBER(4) NOT NULL
);

ALTER TABLE evenimente_organizatori ADD CONSTRAINT event_org_pk PRIMARY KEY ( id_eveniment,
                                                                              id_organizator );

CREATE TABLE organizatori (
    id_organizator     NUMBER(4) NOT NULL,
    nume_organizator   VARCHAR2(30) NOT NULL,
    email_organizator  VARCHAR2(40)
);

ALTER TABLE organizatori
    ADD CONSTRAINT organizatori_nume_ck CHECK ( REGEXP_LIKE ( nume_organizator,
                                                              '[a-z A-Z]+' ) );

ALTER TABLE organizatori ADD CHECK ( REGEXP_LIKE ( email_organizator,
                                                   '[a-z0-9._%-]+@[a-z0-9._%-]+\.[a-z]{2,4}' ) );

ALTER TABLE organizatori ADD CONSTRAINT organizatori_pk PRIMARY KEY ( id_organizator );

ALTER TABLE organizatori ADD CONSTRAINT org_email_org_un UNIQUE ( email_organizator );

CREATE TABLE participanti (
    id_participant    NUMBER(5) NOT NULL,
    cnp_participant   VARCHAR2(13) NOT NULL,
    nume_participant  VARCHAR2(30) NOT NULL,
    nume_institutie   VARCHAR2(50),
    email             VARCHAR2(40),
    id_profesor       NUMBER(5) NOT NULL
);

ALTER TABLE participanti
    ADD CONSTRAINT participant_cnp_ck CHECK ( REGEXP_LIKE ( cnp_participant,
                                                            '[1-8]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])(0[1-9]|[1-4]\d|5[0-2]|99)\d{4}' ) );

ALTER TABLE participanti
    ADD CONSTRAINT participanti_nume_ck CHECK ( REGEXP_LIKE ( nume_participant,
                                                              '[a-z A-Z]+' ) );

ALTER TABLE participanti
    ADD CONSTRAINT participanti_email_ck CHECK ( REGEXP_LIKE ( email,
                                                               '[a-z0-9._%-]+@[a-z0-9._%-]+\.[a-z]{2,4}' ) );

ALTER TABLE participanti ADD CONSTRAINT participanti_pk PRIMARY KEY ( id_participant );

ALTER TABLE participanti ADD CONSTRAINT participanti_cnp_part_un UNIQUE ( cnp_participant );

ALTER TABLE participanti ADD CONSTRAINT participanti_email_un UNIQUE ( email );

CREATE TABLE participanti_premii (
    id_premiu                   NUMBER(5) NOT NULL,
    id_concursuri_participanti  NUMBER NOT NULL
);

CREATE UNIQUE INDEX participanti_premii__idx ON
    participanti_premii (
        id_concursuri_participanti
    ASC );

CREATE TABLE premii (
    id_premiu    NUMBER(5) NOT NULL,
    nume_premiu  VARCHAR2(30) NOT NULL,
    cost_premiu  NUMBER(9, 2) DEFAULT 0 NOT NULL,
    id_concurs   NUMBER(4) NOT NULL
);

ALTER TABLE premii ADD CONSTRAINT premii_cost_premiu_ck CHECK ( cost_premiu >= 0 );

ALTER TABLE premii ADD CONSTRAINT premii_pk PRIMARY KEY ( id_premiu );

CREATE TABLE profesori (
    id_profesor      NUMBER(5) NOT NULL,
    nume_profesor    VARCHAR2(30) NOT NULL,
    nume_institutie  VARCHAR2(50),
    email            VARCHAR2(40)
);

ALTER TABLE profesori
    ADD CONSTRAINT profesori_nume_ck CHECK ( REGEXP_LIKE ( nume_profesor,
                                                           '[a-z A-Z]+' ) );

ALTER TABLE profesori
    ADD CONSTRAINT profesori_email_ck CHECK ( REGEXP_LIKE ( email,
                                                            '[a-z0-9._%-]+@[a-z0-9._%-]+\.[a-z]{2,4}' ) );

ALTER TABLE profesori ADD CONSTRAINT profesori_pk PRIMARY KEY ( id_profesor );

ALTER TABLE profesori ADD CONSTRAINT profesori_email_un UNIQUE ( email );

CREATE TABLE sponsorizari (
    nume_sponsorizare  VARCHAR2(30) NOT NULL,
    suma_sponsorizata  NUMBER(9, 2) DEFAULT 0 NOT NULL,
    id_concurs         NUMBER(4) NOT NULL
);

--  ERROR: Column Sponsorizari.suma_sponsorizata check constraint name length exceeds maximum allowed length(30) 

ALTER TABLE sponsorizari ADD CHECK ( suma_sponsorizata >= 0 );

ALTER TABLE concursuri_participanti
    ADD CONSTRAINT con_part_con_fk FOREIGN KEY ( id_concurs )
        REFERENCES concursuri ( id_concurs );

ALTER TABLE concursuri_participanti
    ADD CONSTRAINT con_part_part_fk FOREIGN KEY ( id_participant )
        REFERENCES participanti ( id_participant );

ALTER TABLE evenimente
    ADD CONSTRAINT event_con_fk FOREIGN KEY ( id_concurs )
        REFERENCES concursuri ( id_concurs );

ALTER TABLE evenimente_organizatori
    ADD CONSTRAINT event_org_event_fk FOREIGN KEY ( id_eveniment )
        REFERENCES evenimente ( id_eveniment );

ALTER TABLE evenimente_organizatori
    ADD CONSTRAINT event_org_org_fk FOREIGN KEY ( id_organizator )
        REFERENCES organizatori ( id_organizator );

ALTER TABLE participanti_premii
    ADD CONSTRAINT part_premii_con_part_fk FOREIGN KEY ( id_concursuri_participanti )
        REFERENCES concursuri_participanti ( id_concursuri_participanti );

ALTER TABLE participanti_premii
    ADD CONSTRAINT part_premii_premii_fk FOREIGN KEY ( id_premiu )
        REFERENCES premii ( id_premiu );

ALTER TABLE participanti
    ADD CONSTRAINT participanti_profesori_fk FOREIGN KEY ( id_profesor )
        REFERENCES profesori ( id_profesor );

ALTER TABLE premii
    ADD CONSTRAINT premii_concursuri_fk FOREIGN KEY ( id_concurs )
        REFERENCES concursuri ( id_concurs );

ALTER TABLE sponsorizari
    ADD CONSTRAINT sponsorizari_con_fk FOREIGN KEY ( id_concurs )
        REFERENCES concursuri ( id_concurs );

CREATE SEQUENCE concursuri_id_concurs_seq START WITH 1 NOCACHE ORDER;

CREATE OR REPLACE TRIGGER concursuri_id_concurs_trg BEFORE
    INSERT ON concursuri
    FOR EACH ROW
    WHEN ( new.id_concurs IS NULL )
BEGIN
    :new.id_concurs := concursuri_id_concurs_seq.nextval;
END;
/

CREATE SEQUENCE concursuri_participanti_id_con START WITH 1 NOCACHE ORDER;

CREATE OR REPLACE TRIGGER concursuri_participanti_id_con BEFORE
    INSERT ON concursuri_participanti
    FOR EACH ROW
    WHEN ( new.id_concursuri_participanti IS NULL )
BEGIN
    :new.id_concursuri_participanti := concursuri_participanti_id_con.nextval;
END;
/

CREATE SEQUENCE evenimente_id_eveniment_seq START WITH 1 NOCACHE ORDER;

CREATE OR REPLACE TRIGGER evenimente_id_eveniment_trg BEFORE
    INSERT ON evenimente
    FOR EACH ROW
    WHEN ( new.id_eveniment IS NULL )
BEGIN
    :new.id_eveniment := evenimente_id_eveniment_seq.nextval;
END;
/

CREATE SEQUENCE organizatori_id_organizator START WITH 1 NOCACHE ORDER;

CREATE OR REPLACE TRIGGER organizatori_id_organizator BEFORE
    INSERT ON organizatori
    FOR EACH ROW
    WHEN ( new.id_organizator IS NULL )
BEGIN
    :new.id_organizator := organizatori_id_organizator.nextval;
END;
/

CREATE SEQUENCE participanti_id_participant START WITH 1 NOCACHE ORDER;

CREATE OR REPLACE TRIGGER participanti_id_participant BEFORE
    INSERT ON participanti
    FOR EACH ROW
    WHEN ( new.id_participant IS NULL )
BEGIN
    :new.id_participant := participanti_id_participant.nextval;
END;
/

CREATE SEQUENCE premii_id_premiu_seq START WITH 1 NOCACHE ORDER;

CREATE OR REPLACE TRIGGER premii_id_premiu_trg BEFORE
    INSERT ON premii
    FOR EACH ROW
    WHEN ( new.id_premiu IS NULL )
BEGIN
    :new.id_premiu := premii_id_premiu_seq.nextval;
END;
/

CREATE SEQUENCE profesori_id_profesor_seq START WITH 1 NOCACHE ORDER;

CREATE OR REPLACE TRIGGER profesori_id_profesor_trg BEFORE
    INSERT ON profesori
    FOR EACH ROW
    WHEN ( new.id_profesor IS NULL )
BEGIN
    :new.id_profesor := profesori_id_profesor_seq.nextval;
END;
/
