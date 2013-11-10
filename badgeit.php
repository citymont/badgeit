<?php
/*
BADG IT !!
  ~~~
Thibaut Villemont / modern~tree

L'objectif n'est pas de mesurer la valeur d'un utilisateur mais plutôt son engagement au sein de la communauté.

Le nombre de point permet d'obtenir un indice sur l'engagement de la personne définissant un éventuel rôle (==> Badge).

*/

Class Badgeit {

	private $db;
	private $prfxr = "fanbase_";
	private $tbl_fan = "fan";
	private $flash;
	private $flash_ending = "<br>";
	
	function __construct() {

		$this->db = new DB();
	}

	public function showFlash() {
		print $this->flash;
	}

	/**
	 * Exploit Library : create an exploit in the library 
	 * @param  array $params (name_exploits,points_earning,category)
	 * @return 
	 */
	public function createExploit($params){

		if($params && is_array($params)){

			$name_exploits=$params[0];
			$points_earning=$params[1];
			$category=$params[2]; 
			
			$req="INSERT INTO ".$this->prfxr."exploits_library VALUES ('','$name_exploits','$points_earning','$category')";

			if($this->db->query($req) === true ) {
				$this->flash.="exploit ajouté à la librairie".$this->flash_ending;
			} else {
				$this->flash.="exploit déjà ajouté à la librairie".$this->flash_ending;
			} 	 

		}

	}

	/**
	 * Exploit Library : Show exploits/exploit
	 * @param  int $id exploit ID
	 * @return sql     results
	 */
	public function showExploit($id = null){
		 
		if($id == null){
			$req="SELECT * FROM ".$this->prfxr."exploits_library";
			return $this->db->query($req);
		} 
		else {
			$req="SELECT * FROM ".$this->prfxr."exploits_library WHERE id = '$id'";
			return $this->db->see($req);
		}
		
	}

	/**
	 * Exploit Library : update an exploit in the library
	 * @param  int $id     exploit ID
	 * @param  array $params (name_exploits,points_earning,category)	
	 * @return 
	 */
	public function updateExploit($id, $params){ 

		$name_exploits=$params[0];
		$points_earning=$params[1];
		$category=$params[2]; 

		$req = "UPDATE ".$this->prfxr."exploits_library SET name='$name_exploits', points='$points_earning', category='$category' WHERE id='$id'";

		if($this->db->query($req) === true ) {
			$this->flash.= "Exploit mis à jour".$this->flash_ending;
		}  
		else {
			$this->flash.="Error : ".__FUNCTION__."()".$this->flash_ending;
		} 



	}	

	/**
	 * Exploit : Update all exploits with new point
	 * @param  int $exploit_id_id exploit ID
	 * @return 
	 */
	public function updateExploitAllPoint($exploit_id_id){ 

		$point=$this->showExploit($exploit_id_id); 
		$pointNew = $point['points'];

		$req = "UPDATE ".$this->prfxr."exploits SET point='$pointNew' WHERE exploit_id='$exploit_id_id'";
		
		if($this->db->query($req) === true ) {
			$this->flash.= "Tous les scores des exploits mis à jour".$this->flash_ending;
		}  
		else {
			$this->flash.="Error : ".__FUNCTION__."()".$this->flash_ending;
		} 

		

	}	

	/**
	 * Exploit : list exploits per user and/or exploit 
	 * @param  int $user_id user ID
	 * @param  int $id      exploit ID
	 * @return sql          results
	 */
	public function showPlayerExploit($user_id, $id = null){
		 
		if($id == null){
			$req="SELECT * FROM ".$this->prfxr."exploits WHERE user_id = '$user_id' order by date DESC";
			return $this->db->query($req);
		} 
		elseif($id == "allByExploit") {
			$req="SELECT * FROM ".$this->prfxr."exploits WHERE exploit_id = '$id'";
			return $this->db->query($req);
		}
		else {
			$req="SELECT * FROM ".$this->prfxr."exploits WHERE user_id = '$user_id' and exploit_id = '$id'";
			return $this->db->query($req);
		}
		
	}

	/**
	 * Exploit : save exploit for one user
	 * @param  int $exploit_id 	exploit_lib ID
	 * @param  int $user_id   
	 * @param  array  $params   exploit params
	 * @param  date $date       date() or null = now
	 * @return 
	 */
	public function savePlayerExploit($exploit_id,$user_id,$params=array(),$date) {

		$point=$this->showExploit($exploit_id); 
		if($date == null) { $date = date('Y-m-d H:i.s', time()); }

		$req="INSERT INTO ".$this->prfxr."exploits VALUES ('','$user_id','$exploit_id','".$point['points']."','".serialize($params)."','$date')";

			if($this->db->query($req) === true ) { 
				$this->flash.="exploit effectué par $user_id".$this->flash_ending;
			} else { 
				$this->flash.="Error : ".__FUNCTION__."()".$this->flash_ending;
			} 	 

	}

	/**
	 * Exploit : update one exploit
	 * @param  int $exploit_id_id 
	 * @param  int $exploit_id    exploit_lib ID
	 * @param  int $user_id       
	 * @param  array  $params     exploit params
	 * @return
	 */
	public function updatePlayerExploit($exploit_id_id, $exploit_id,$user_id,$params=array()) {

		$point=$this->showExploit($exploit_id); 
		$pointNew = $point['points']; 
		$paramsNew=serialize($params);
		$req = "UPDATE ".$this->prfxr."exploits SET point='$pointNew', params='$paramsNew', date=NOW() WHERE id='$exploit_id_id'";

			if($this->db->query($req) === true ) {
				$this->flash.="exploit $exploit_id_id modifié".$this->flash_ending;
			} else {
				$this->flash.="Error : ".__FUNCTION__."()".$this->flash_ending;
			} 	 

	}

	/**
	 * User : update points & exploits
	 * @param  int $user_id 
	 * @return 
	 */
	public function updatePlayerPoints($user_id){ 
		
		$req = "SELECT count(point) as nbexploit, sum(point) as nbpoint FROM ".$this->prfxr."exploits WHERE user_id='$user_id'";
		$res = $this->db->see($req);

		$nbexploit = $res['nbexploit'];
		if($res['nbpoint'] == null) : $nbpoint = 0; else : $nbpoint = $res['nbpoint']; endif;
		$date = date('Y-m-d H:i.s', time());

		$req = "UPDATE ".$this->prfxr."exploits_user SET total_point=$nbpoint, total_exploit=$nbexploit, updated_at='$date' WHERE id='$user_id'";
		$test = $this->db->query($req);

		if($test == false ) {
	
			$this->flash.= "hello newbies, at this time, $user_id have earn $nbpoint points with $nbexploit exploits, this level is : xxx".$this->flash_ending;
		}

		if($test === true ) { 
			if(!$this->db->see("SELECT 1 FROM ".$this->prfxr."exploits_user WHERE id='$user_id'")){
				$req2="INSERT INTO ".$this->prfxr."exploits_user VALUES ('$user_id','$user_id','','$nbpoint','$nbexploit','','$date')";
				$this->db->query($req2);
				$this->flash.= "hello newbies, at this time, $user_id have earn $nbpoint points with $nbexploit exploits, this level is : xxx".$this->flash_ending;
			} else {
				$this->flash.= "at this time, $user_id have earn $nbpoint points with $nbexploit exploits, this level is : xxx".$this->flash_ending;
			}

			
		}

	}

	/**
	 * User : show  info about exploits per unique user or all user
	 * @param  int $user_id 
	 * @return sql          (id,other_id,email,total_point,total_exploit,total_badge,updated_at)
	 */
	public function showPlayer($user_id){
		 
		if($user_id != null){
			$req="SELECT * FROM ".$this->prfxr."exploits_user WHERE id = '$user_id'";
			return $this->db->query($req);
		} 
		else {
			$req="SELECT * FROM ".$this->prfxr."exploits_user "; // fan
			return $this->db->query($req);
		}
		
	}

	/**
	 * Fan : show info about fan
	 * @param  int $user_id 
	 * @return 
	 */
	public function showFan($user_id){
		 
		if($user_id != null){
			$req="SELECT * FROM ".$this->tbl_fan." WHERE id = '$user_id'";
			return $this->db->query($req);
		} 
		else {
			$req="SELECT * FROM ".$this->tbl_fan; 
			return $this->db->query($req);
		}
		
	}

	/**
	 * User : list user per total_point (FUTUR:chosse)
	 * @param  int $limit limit de classement
	 * @return sql        (id,total_point,total_exploit,total_badge,email)
	 */
	public function showPlayerClassement($limit=null){
		 
		$req="SELECT a.id, a.total_point, a.total_exploit, a.total_badge, b.email FROM ".$this->prfxr."exploits_user a, ".$this->tbl_fan." b WHERE a.id = b.id ORDER BY a.total_point DESC LIMIT $limit";
		return $this->db->query($req);
		
	}

	/**
	 * Badge Library : create badge in the library
	 * @param  array $params (name_badge,advantage,rules,min_point,manuorauto,callback)
	 * @return 
	 */
	public function createBadge($params)
	{
		
		if($params && is_array($params)){

			$name_badge = $params[0];
			$advantageforuser = $params[1];
			$rules = $params[2];
			$min_points = $params[3];
			$manuorauto = $params[4];
			$callback = $params[5];


			$req="INSERT INTO ".$this->prfxr."badge_library VALUES ('','$name_badge','".mysql_real_escape_string($advantageforuser)."','$rules','$min_points','$manuorauto','$callback')";
		
			if($this->db->query($req) === true ) {
				$this->flash.="badge ajouté à la librairie".$this->flash_ending;
			} else {
				$this->flash.="badge DEJA ajouté à la librairie".$this->flash_ending;
			} 	 

		}
	
	}

	/**
	 * Badge Library : show
	 * @param  int $id 
	 * @return sql     [description]
	 */
	public function showBadge($id = null){
		 
		if($id == null){
			$req="SELECT * FROM ".$this->prfxr."badge_library";
			return $this->db->query($req);
		} 
		else {
			$req="SELECT * FROM ".$this->prfxr."badge_library WHERE id = '$id'";
			return $this->db->see($req);
		}
		
	}

	/**
	 * Badge Library : update badge
	 * @param  int $id     badge lib ID
	 * @param  array $params (create array)
	 * @return 
	 */
	public function updateBadge($id, $params){ 

		$name_badge=$params[0];
		$avantage=$params[1];
		$rules=$rules[2]; 
		$min_points=$rules[3]; 
		$manuorauto=$rules[4]; 
		$callback=$rules[5]; 

		$req = "UPDATE ".$this->prfxr."badge_library SET name_badge='$name_badge', avantage='$avantage', rules='$rules', min_points='$min_points', manuorauto='$manuorauto', callback='$callback' WHERE id='$id'";

		if($this->db->query($req) === true ) {
			$this->flash.= "Badge mis à jour".$this->flash_ending;
		}  
		else {
			$this->flash.="Error : ".__FUNCTION__."()".$this->flash_ending;
		} 



	}	

	/**
	 * Badge : Check if the user earn badges / Update badge for one user
	 * @param  int $user_id 
	 * @return 
	 */
	public function updatePlayerBadge($user_id){ 
		
		$user = $this->db->see("SELECT * FROM ".$this->prfxr."exploits_user WHERE id='$user_id'");
		$req="SELECT * FROM ".$this->prfxr."badge_library"; 
		$Badges =$this->db->query($req);
		$pass = false;
		$badgeAquired = $user['total_badge'];

		while($row = mysql_fetch_array($Badges)){

			// Minimum de point requis
			if($user['total_point']>$row['min_points']) { $pass = true; }

			if($pass === true) {

				$pass = false; 
				// rules requises
				$rules = explode(';', $row['rules']);

				foreach ($rules as $key => $rule) { //only count id_exploits ( cumulatif )

					$a = explode(':', $rule);
					$b = preg_split('(-|\+|\*|\/|>|<)',$a[1]);
					$c = preg_match('/(-|\+|\*|\/|>|<)/',$a[1],$matches);

					$id = $b[0];
					$count = $b[1];
					$operateur = $matches[0];

					$test = $this->db->see("SELECT count(id) as nb FROM ".$this->prfxr."exploits WHERE exploit_id='$id' AND user_id='$user_id'");
					if( $test == true ) {
						switch ($operateur) {
							case '<':
								if($test['nb']<$count) { $pass = true; } else { break; }
								break;

							case '>':
								if($test['nb']>$count) { $pass = true; } else { break; }
								break;

						}
						
					}

				}
			}
			if($pass === true) {

				$date = date('Y-m-d H:i.s', time());
				$req="INSERT INTO ".$this->prfxr."badge VALUES ('','".$row['id']."','$user_id','$date')";
				
				if($this->db->query($req) === true ) {

					$this->flash.="badge ".$row['name_badge']." ajouté à ".$user['id']."".$this->flash_ending;
					$badgeAquired++; 

				} else {
					$this->flash.="badge ".$row['name_badge']." DEJA ajouté à ".$user['id']."".$this->flash_ending;
				} 	 
				
			}

			
		}

		// update scores 
		$date = date('Y-m-d H:i.s', time());
		$req = "UPDATE ".$this->prfxr."exploits_user SET total_badge='$badgeAquired', updated_at='$date' WHERE id='$user_id'";
		if($this->db->query($req) === true ) {
			$this->flash.="maj count badges for ".$user['id']."".$this->flash_ending;
		}


	}

	/**
	 * Badge : show badge per user, badge or both
	 * @param  int $user_id 
	 * @param  int $id      badge ID
	 * @return [type]          [description]
	 */
	public function showPlayerBadge($user_id, $id = null){
		 
		if($id == null){
			$req="SELECT * FROM ".$this->prfxr."badge WHERE user_id = '$user_id'";
			return $this->db->query($req);
		} 
		elseif($id == "allByExploit") {
			$req="SELECT * FROM ".$this->prfxr."badge WHERE badge_id = '$id'";
			return $this->db->query($req);
		}
		else {
			$req="SELECT * FROM ".$this->prfxr."badge WHERE user_id = '$user_id' and badge_id = '$id'";
			return $this->db->query($req);
		}
		
	}


};
