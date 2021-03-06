<?php
namespace App\Model;

use Silex\Application;

class UserModel {

	private $db;

	public function __construct(Application $app) {
		$this->db = $app['db'];
        $this->id = -1;
	}

	public function verif_login_mdp_Utilisateur($login,$mdp){
		$sql = "SELECT login,password,droit,id FROM users WHERE login = ? AND password = ?";
		$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
		if($res->rowCount()==1)
			return $res->fetch();
		else
			return false;
	}

	public function getId($login)
    {
        $sql = "SELECT id FROM users WHERE login = ?";
        $res=$this->db->executeQuery($sql,[$login]);   //md5($mdp);
        if($res->rowCount()==1)
            return $res['id'];
        else
            return false;
    }
}