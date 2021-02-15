<?php
	class db_connection{
		protected $conn;

		function __construct(){

			$username = 'batalan_vlad';
			$password = 'student';
			$db = 'localhost/XE';

			$this->conn = oci_connect($username, $password, $db)
				or die(oci_error());

			if($this->conn){
				//echo "Connection established!<br>";
			}
			else{
				echo "Database connection problem!<br>";
			}
		}


		/* ###############################################################
		// ##################### GENERAL COMMANDS ########################
		*/ ###############################################################

		// aceasta functie poate fi folosita pentru orice query pentru a-i adauga comenzi de cautare
		// clauze WHERE simple inlantuite legate prin AND
		private function add_param_query($stmt_param = array()){
			$added = "";

			if(count($stmt_param) > 0){
				$num = 0;
				$added .= " WHERE ";

				foreach ($stmt_param as $key => $value) {
					if($num > 0 && $num < count($stmt_param)){
						$added .= " AND ";
					}

					$added .= $key . " = '" . $value ."'";
					$num ++;
				}
			}

			return $added;
		}

		private function execute_select($sql_command){
			$all = array();
			//echo "Created stmt: <br>". $sql_command . "<br><br>";
			$stmt = oci_parse($this->conn, $sql_command);
			oci_execute($stmt);

			while($row = oci_fetch_object($stmt)){
				$all[] = $row;
			}

			oci_free_statement($stmt);
			return $all;
		}

		private function execute_query($sql_command){

			$stmt = oci_parse($this->conn, $sql_command);
			$r = oci_execute($stmt);

			oci_free_statement($stmt);
			return $r;
		}

		/* ##############################################################
		// ##################### SELECT COMMANDS ########################
		*/ ##############################################################

		// returneaza toate campurile concursurilor
		public function select_concursuri($stmt_param = array()){
			$sql_command = "SELECT * FROM concursuri";

			// adaugam parametrii
			$sql_command .= $this->add_param_query($stmt_param);

			return $this->execute_select($sql_command);
		}

		// selecteaza evenimente
		public function select_evenimente($stmt_param = array()){
			$sql_command = "SELECT * FROM evenimente";

			// adaugam parametrii
			$sql_command .= $this->add_param_query($stmt_param);
			$sql_command .= " ORDER BY data_eveniment ASC";

			return $this->execute_select($sql_command);
		}

		// selecteaza premiile disponibile de oferit 
		public function select_optiuni_premii($stmt_param = array()){
			$sql_command = "SELECT * FROM premii";

			// adaugam parametrii
			$sql_command .= $this->add_param_query($stmt_param);
			$sql_command .= " ORDER BY cost_premiu DESC, nume_premiu ASC";

			return $this->execute_select($sql_command);
		}

		// selecteaza sponsorizarie pentru un concurs
		public function select_sponsorizari($id_concurs){
			$sql_command = "SELECT * FROM sponsorizari";

			// adaugam parametrii
			$sql_command .= $this->add_param_query(['id_concurs' => $id_concurs]);
			$sql_command .= " ORDER BY suma_sponsorizata ASC";

			return $this->execute_select($sql_command);
		}

		public function select_participanti_concurs_premiu($id_concurs = null, $toti_participantii = true, $id_participant = null){

			// in functie de parametrul $toti_participantii, se vor alege toti, sau doar premiantii
			$LEFT = "LEFT";
			if($toti_participantii == false)
				$LEFT = "";

			$sql_command = "
			SELECT 
				part.*,
				p.*,
				prof.*
			FROM
			    participanti part JOIN concursuri_participanti cp ON
			        part.id_participant = cp.id_participant 
			    $LEFT JOIN participanti_premii pp ON
			        pp.id_concursuri_participanti = cp.id_concursuri_participanti
			    $LEFT JOIN premii p ON
			        p.id_premiu = pp.id_premiu 
			    JOIN profesori prof ON
			    	part.id_profesor = prof.id_profesor
			";

			$param = array();
			// adaugam parametrii
			if($id_concurs !== null)
				$param['cp.id_concurs'] = $id_concurs;
			if($id_participant !== null)
				$param['part.id_participant'] = $id_participant;
			$sql_command .= $this->add_param_query($param);
			$sql_command .= "
			    ORDER BY p.cost_premiu DESC";

			return $this->execute_select($sql_command);
		}


		/* #############################################################
		// ##################### COSTURI TOTALE ########################
		*/ #############################################################


		// selectie costuri premii
		public function cost_premii_oferite($id_concurs){
			$sql_command = "
				SELECT SUM(p.cost_premiu) cost_total_premii
				    FROM concursuri_participanti cp JOIN participanti_premii pp ON
				        cp.id_concursuri_participanti = pp.id_concursuri_participanti
				    JOIN premii p ON
				        p.id_premiu = pp.id_premiu 
				    WHERE cp.id_concurs = '".$id_concurs."'
			";
			$r = $this->execute_select($sql_command);
			if(count($r) == 0)
				return 0;
			return $r[0]->COST_TOTAL_PREMII;
		}


		// cost total evenimente
		public function cost_evenimente($id_concurs){
			$sql_command = "
				SELECT SUM(cost_eveniment) cost_total_evenimente FROM evenimente 
					WHERE id_concurs = '".$id_concurs."'
			";
			$r = $this->execute_select($sql_command);
			if(count($r) == 0)
				return 0;
			return $r[0]->COST_TOTAL_EVENIMENTE;
		}

		// suma totala sponsorizari
		public function suma_sponsorizari($id_concurs){
			$sql_command = "
				SELECT SUM(suma_sponsorizata) suma_totala FROM sponsorizari 
					WHERE id_concurs = '".$id_concurs."'
			";
			$r = $this->execute_select($sql_command);
			if(count($r) == 0)
				return 0;
			return $r[0]->SUMA_TOTALA;
		}

		/* #######################################################
		// ##################### NUMARARE ########################
		*/ #######################################################


		// returneaza numarul de participanti pentru un anumit concurs
		public function numar_participanti_concurs($id_concurs){
			$sql_command = "SELECT COUNT(*) AS numar_participanti FROM concursuri_participanti";

			// adaugam parametrii
			$sql_command .= $this->add_param_query(['id_concurs' => $id_concurs]);

			// returnam numarul de participanti
			$numar_participanti = $this->execute_select($sql_command);
			if(count($numar_participanti) == 0)
				return 0;

			return $numar_participanti[0]->NUMAR_PARTICIPANTI;
		}

		// returneaza numarul de participanti pentru un anumit concurs
		public function numar_total_participanti(){
			$sql_command = "SELECT COUNT(*) AS numar_participanti FROM participanti";

			// returnam numarul de participanti
			$numar_participanti = $this->execute_select($sql_command);
			if(count($numar_participanti) == 0)
				return 0;
			
			return $numar_participanti[0]->NUMAR_PARTICIPANTI;
		}




		/* ######################################################
		// ##################### UPDATES ########################
		*/ ######################################################

		public function update_cost_eveniment($id_eveniment, $cost){
			$sql_command = "UPDATE evenimente SET cost_eveniment = '$cost' WHERE id_eveniment = '$id_eveniment'";
			return $this->execute_query($sql_command);
		}

		public function update_cost_premiu($id_premiu, $cost){
			$sql_command = "UPDATE premii SET cost_premiu = '$cost' WHERE id_premiu = '$id_premiu'";
			return $this->execute_query($sql_command);
		}

		public function update_relatie_premiu_participant($id_premiu, $id_participant, $id_concurs){
			$sql_command = "
				UPDATE 
					participanti_premii 
				SET 
					id_premiu = '$id_premiu' 
				WHERE 
					id_concursuri_participanti = 
					(	SELECT 
							id_concursuri_participanti 
						FROM 
							concursuri_participanti 
						WHERE 
							id_concurs = '$id_concurs' AND 
							id_participant = '$id_participant'
					)";
			return $this->execute_query($sql_command);
		}


		/* ##############################################################
		// ##################### INSERT COMMANDS ########################
		*/ ##############################################################
		public function insert_premiu($nume_premiu, $cost_premiu, $id_concurs){
			$nume_premiu = htmlspecialchars($nume_premiu);
			$cost_premiu = htmlspecialchars($cost_premiu);
			$id_concurs = htmlspecialchars($id_concurs);

			$sql_command = "INSERT INTO premii VALUES(null, '$nume_premiu', '$cost_premiu', '$id_concurs')";
			$r = $this->execute_query($sql_command);
			return $r;
		}

		public function insert_asociere_participant_premiu($id_concurs, $id_participant, $id_premiu){
			$id_concurs = htmlspecialchars($id_concurs);
			$id_participant = htmlspecialchars($id_participant);
			$id_premiu = htmlspecialchars($id_premiu);

			$sql_command = "
			INSERT INTO participanti_premii 
				VALUES('$id_premiu', (SELECT id_concursuri_participanti FROM concursuri_participanti WHERE id_concurs = '$id_concurs' AND id_participant = '$id_participant'))";
			$r = $this->execute_query($sql_command);
			return $r;
		}


		/* ##############################################################
		// ##################### DELETE COMMANDS ########################
		*/ ##############################################################

		public function delete_premiu_transaction($id_premiu, $id_concurs='-1'){
			// obs: parametrul $id_concurs are rol de verificare, pentru a nu sterge alte premii
			$params = array(
				'id_premiu' => $id_premiu
			);
			if($id_concurs != '-1')
				$params['id_concurs'] = $id_concurs;

			// daca nu exista id_ul respectiv
			$sql_command = "SELECT id_premiu FROM premii";
			$sql_command .= $this->add_param_query($params); 
			$exista = $this->execute_select($sql_command);
			if(count($exista) != 1){
				//echo "Eroare[$id_premiu]: nu exista premiul selectat!";
				return "Eroare[$id_premiu]: nu exista premiul selectat!";
			}


			// sterge peste tot unde participantii au premiu de id dat
			$sql_command = "DELETE FROM participanti_premii WHERE id_premiu = '$id_premiu'";
			$stmt = oci_parse($this->conn, $sql_command);
			$r = oci_execute($stmt, OCI_NO_AUTO_COMMIT);

			if(!$r){
				//echo "Eroare[$id_premiu]: nu s-a realizat stergerea participanti_premii!";
				return "Eroare[$id_premiu]: nu s-a realizat stergerea participanti_premii!";
			}

			$sql_command = "DELETE FROM  premii WHERE id_premiu = '$id_premiu'";
			$stmt = oci_parse($this->conn, $sql_command);
			$r = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
			if(!$r){
    			oci_rollback($this->conn);  // rollback changes to both tables
				//echo "Eroare[$id_premiu]: nu s-a realizat stergere premiului!";
				return "Eroare[$id_premiu]: nu s-a realizat stergere premiului!";
			}

			// Commit the changes to both tables
			$r = oci_commit($this->conn);
			oci_free_statement($stmt);
			return "Success[$id_premiu]!";
		}

		public function delete_relatie_participant_premiu($id_concurs, $id_concurent){
			$sql_command = "DELETE FROM participanti_premii WHERE id_concursuri_participanti = (SELECT id_concursuri_participanti FROM concursuri_participanti WHERE id_concurs = '$id_concurs' AND id_participant = '$id_concurent')";
			$r = $this->execute_query($sql_command);
			return $r;
		}

		function __destruct(){
			oci_close($this->conn);
		}
	}
	$conn = new db_connection();


	/*
	$concursuri = $conn->select_concursuri();

	foreach ($concursuri as $concurs) {
		echo "Concurs: " . $concurs->NUME_CONCURS . "<br>";
		echo "Editie: " . $concurs->EDITIE_CONCURS . "<br>";
		echo "ORAS: " . $concurs->ORAS . "<br>";
		echo "<br>";
	}


	$participanti = $conn->select_participanti_concurs(['nume_concurs'=> 'Traian Lalescu']);
	foreach($participanti as $part){

		echo "Participant: " . $part->NUME_PARTICIPANT . "<br>";
		if(isset($part->NUME_INSTITUTIE))
			echo "Nume Institutie: " . $part->NUME_INSTITUTIE . "<br>";
		if(isset($part->EMAIL))
			echo "Email: " . $part->EMAIL . "<br>";
		echo "Premiu castigat: " . $part->NUME_PREMIU . "<br>";
		echo "<br>";
	}
	*/


?>