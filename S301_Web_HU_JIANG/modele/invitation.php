<?php
class Groupe {
    private $IdEmail;
    private $Email;
	private $Token;
    private $DateExpiration;
    private $DateInvitation;


    public function getIdEmail() { return $this->IdEmail; }
    public function getEmail() { return $this->Email; }
	public function getToken() { return $this->Token; }
    public function getDateExpiration() { return $this->DateExpiration; }
	public function getDateInvitation() { return $this->DateInvitation; }
    
    public function setIdEmail($idEmail) { $this->IdEmail = $idEmail; }
    public function setEmail($email) { $this->Email = $email; }
    public function setToken($token) { $this->Token = $token; }
    public function setDateExpiration($expiration) { $this->DateExpiration = $expiration; }
    public function setDateInvitation($Invitation) { $this->DateInvitation = $Invitation; }
    
	public function __construct($idEmail = NULL, $email = NULL, $token = NULL, $expiration = NULL, $invitation = NULL) {
        if (!is_null($idEmail)) {
            $this->IdEmail = $idEmail;
            $this->Email = $email;
			$this->Token = $token;
            $this->DateExpiration = $expiration;
            $this->DateInvation = $invitation;
        }
    }

    



}
?>
