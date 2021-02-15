-- ########### INSERT INTO CONCURSURI ###########--
INSERT INTO concursuri VALUES(null, 'Traian Lalescu', 'Editia XIII', 'Cluj');
INSERT INTO concursuri VALUES(null, 'SEEMOUS', 'Editia X', 'Devin, Bulgaria');

-- ########### INSERT INTO ORGANIZATORI ########### --
INSERT INTO organizatori VALUES(null, 'Emil Stoica', 'emil_stoica@gmail.com');
INSERT INTO organizatori VALUES(null, 'Radu Burcoveanu', 'raduburc@gmail.com');
INSERT INTO organizatori VALUES(null, 'Laura Maitai', 'maitailaura@gmail.com');
INSERT INTO organizatori VALUES(null, 'Ecaterina Tudorachi', 'tudo_ecati@gmail.com');
INSERT INTO organizatori VALUES(null, 'Laur Burzau', 'burzau_64@gmail.com');


-- ########### INSERT INTO EVENIMENTE ########### --
-- Traian Lalescu --
INSERT INTO evenimente 
    VALUES
    (
        10, 
        'Primirea participantilor', 
        250.00, 
        '25-NOV-2013', 
        '10:00 - 18:00', 
        'Participantii si profesori',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );
INSERT INTO evenimente 
    VALUES
    (
        11, 
        'Cazarea studentilor', 
        0, 
        '25-NOV-2013', 
        '10:00 - 18:00', 
        'Participantii',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );
INSERT INTO evenimente 
    VALUES
    (
        12, 
        'Desfasurarea concursului', 
        1200.25, 
        '26-NOV-2013', 
        '9:00 - 15:00', 
        'Participantii',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );
INSERT INTO evenimente 
    VALUES
    (
        13, 
        'Cina festiva si premiere', 
        3025.50, 
        '27-NOV-2013', 
        '18:00 - 21:00', 
        'Toata lumea',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );

-- SEEMOUS --
INSERT INTO evenimente 
    VALUES
    (
        14, 
        'Primire participanti', 
        400, 
        '16-MAR-2016', 
        '10:00 - 18:00', 
        'Participantii si profesori',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO evenimente 
    VALUES
    (
        15, 
        'Program liber participanti', 
        0, 
        '17-MAR-2016', 
        null, 
        'Participantii',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO evenimente 
    VALUES
    (
        16, 
        'Desfasurare Concurs', 
        5000.25, 
        '18-MAR-2016', 
        '09:00 - 16:00', 
        'Participantii si profesori',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO evenimente 
    VALUES
    (
        17, 
        'Corectia lucrarilor', 
        435, 
        '18-MAR-2016', 
        '17:00 - 21:00', 
        'Profesori',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO evenimente 
    VALUES
    (
        18, 
        'Excursie', 
        4270.54, 
        '19-MAR-2016', 
        '09:00 - 18:00', 
        'Toata lumea',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO evenimente 
    VALUES
    (
        19, 
        'Decernare premii', 
        1700, 
        '19-MAR-2013', 
        '19:00 - 20:00', 
        'Toata lumea',
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
        
-- ####### INSERT INTO EVENIMENTE_ORGANIZATORI ####### --
INSERT INTO evenimente_organizatori 
    VALUES ( 
        10, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Emil Stoica')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        10, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Ecaterina Tudorachi')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        11, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Emil Stoica')
    );  
INSERT INTO evenimente_organizatori 
    VALUES ( 
        12, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Laura Maitai')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        12, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Laur Burzau')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        12, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Radu Burcoveanu')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        13, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Emil Stoica')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        14, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Emil Stoica')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        14, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Ecaterina Tudorachi')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        16, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Laura Maitai')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        16, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Laur Burzau')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        16, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Radu Burcoveanu')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        17, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Radu Burcoveanu')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        18, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Emil Stoica')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        18, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Radu Burcoveanu')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        18, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Laura Maitai')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        18, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Ecaterina Tudorachi')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        18, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Laur Burzau')
    );
INSERT INTO evenimente_organizatori 
    VALUES ( 
        19, 
        (SELECT id_organizator FROM organizatori WHERE nume_organizator = 'Emil Stoica')
    );
    
-- ########### INSERT INTO SPONSORIZARI ########### --
INSERT INTO sponsorizari 
    VALUES (
        'EuroMath',
        5000,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO sponsorizari 
    VALUES (
        'MAthFactor Europe',
        3000,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO sponsorizari 
    VALUES (
        'Le-Math Project',
        1000,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
INSERT INTO sponsorizari 
    VALUES (
        'BCR',
        3500,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );
INSERT INTO sponsorizari 
    VALUES (
        'Fundatia EMAG',
        2500,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );
INSERT INTO sponsorizari 
    VALUES (
        'Betfair',
        1000,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );
INSERT INTO sponsorizari 
    VALUES (
        'ULLINK',
        500,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );
INSERT INTO sponsorizari 
    VALUES (
        'blue Projects',
        500,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );

-- ########### INSERT INTO PROFESORI ########### --
INSERT INTO profesori 
    VALUES(
        null,
        'Strugariu Radu',
        'Univ. Gh. Asachi, Iasi',
        'radus@tuiasi.ro'
    );
INSERT INTO profesori 
    VALUES(
        null,
        'Axinte Dorica',
        'Colegiul National A.T.L Bt',
        null
    );
INSERT INTO profesori 
    VALUES(
        null,
        'Vasile Pop',
        'Univ. Tehnica, Cluj',
        'vasilepop@utc.ro'
    );



-- ########### INSERT INTO PARTICIPANTI ########### --
INSERT INTO participanti
    VALUES(
        null,
        '1990604076286',
        'Batalan Vlad',
        'Univ. Gh. Asachi, Iasi',
        null,
        (SELECT id_profesor FROM profesori WHERE nume_profesor = 'Strugariu Radu')
    );
    
INSERT INTO participanti
    VALUES(
        null,
        '1990917229887',
        'Stanciu Ioan',
        'Univ. Gh. Asachi, Iasi',
        null,
        (SELECT id_profesor FROM profesori WHERE nume_profesor = 'Strugariu Radu')
    );

INSERT INTO participanti
    VALUES(
        null,
        '6000225348043',
        'Manea Diana',
        'Univ. Tehnica, Cluj',
        null,
        (SELECT id_profesor FROM profesori WHERE nume_profesor = 'Vasile Pop')
    );

INSERT INTO participanti
    VALUES(
        null,
        '1990515098975',
        'Grecu Cristian',
        'Univ. Gh. Asachi, Iasi',
        'greccrist@gmail.com',
        (SELECT id_profesor FROM profesori WHERE nume_profesor = 'Strugariu Radu')
    );

INSERT INTO participanti
    VALUES(
        null,
        '5000811079979',
        'Tomila Ciprian',
        'C.N. A.T. Laurian, Botosani',
        'cipriboss@yahoo.com',
        (SELECT id_profesor FROM profesori WHERE nume_profesor = 'Axinte Dorica')
    );
INSERT INTO participanti
    VALUES(
        null,
        '6000106125835',
        'Barbara Marieta',
        'Univ Tehnica, Cluj',
        'maribarb@gmail.com',
        (SELECT id_profesor FROM profesori WHERE nume_profesor = 'Vasile Pop')
    );    


-- ########### INSERT INTO PREMII ########### --
-- SEEMOUS --
INSERT INTO premii
    VALUES(
        null,
        'Locul I',
        750,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    
    );

INSERT INTO premii
    VALUES(
        null,
        'Premiu special',
        250,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );

INSERT INTO premii
    VALUES(
        null,
        'Locul II',
        300,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );

INSERT INTO premii
    VALUES(
        null,
        'Locul III',
        150,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS')
    );
    
-- TRAIAN LALESCU --
INSERT INTO premii
    VALUES(
        null,
        'Locul I',
        300,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );

INSERT INTO premii
    VALUES(
        null,
        'Locul II',
        200,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );

INSERT INTO premii
    VALUES(
        null,
        'Locul III',
        100,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );

INSERT INTO premii
    VALUES(
        null,
        'Premiu special',
        400,
        (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu')
    );


-- ######## INSERT INTO concursuri_participanti ######## --

-- TRAIAN LALESCU --
INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Batalan Vlad'),
    null
);
INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Stanciu Ioan'),
    null
);
INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Barbara Marieta'),
    null
);
INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'Traian Lalescu'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Tomila Ciprian'),
    null
);

-- SEEMOUS --
INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Batalan Vlad'),
    null
);

INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Grecu Cristian'),
    null
);

INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Manea Diana'),
    null
);

INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Stanciu Ioan'),
    null
);

INSERT INTO concursuri_participanti VALUES(
    (SELECT id_concurs FROM concursuri WHERE nume_concurs = 'SEEMOUS'),
    (SELECT id_participant FROM participanti WHERE nume_participant = 'Tomila Ciprian'),
    null
);


-- ########### INSERT INTO participanti_premii ########### 
-- TRAIAN LALESCU --
INSERT INTO participanti_premii VALUES( 
    (SELECT id_premiu FROM premii WHERE nume_premiu = 'Premiu special' AND
            id_concurs = 1), 1
 );
INSERT INTO participanti_premii VALUES(
    (SELECT id_premiu FROM premii WHERE 
        nume_premiu = 'Locul II' AND
        id_concurs = 1), 2
);
INSERT INTO participanti_premii VALUES(
    (SELECT id_premiu FROM premii WHERE 
        nume_premiu = 'Locul I' AND
        id_concurs = 1), 3
);

-- SEEMOUS --
INSERT INTO participanti_premii VALUES(
    (SELECT id_premiu FROM premii WHERE 
        nume_premiu = 'Locul I' AND
        id_concurs = 2), 7
);
INSERT INTO participanti_premii VALUES(
    (SELECT id_premiu FROM premii WHERE 
        nume_premiu = 'Locul III' AND
        id_concurs = 2), 8
);
INSERT INTO participanti_premii VALUES(
    (SELECT id_premiu FROM premii WHERE 
        nume_premiu = 'Locul II' AND
        id_concurs = 2), 5
);
INSERT INTO participanti_premii VALUES(
    (SELECT id_premiu FROM premii WHERE 
        nume_premiu = 'Locul II' AND
        id_concurs = 2), 6
);
INSERT INTO participanti_premii VALUES(
    (SELECT id_premiu FROM premii WHERE 
        nume_premiu = 'Premiu special' AND
        id_concurs = 2), 9
);
