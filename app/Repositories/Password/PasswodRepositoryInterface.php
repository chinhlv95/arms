<?php
namespace App\Repositories\Password;

interface PasswordRepositoryInterface{

				/**
					* @author SonNA6229
					* Check if token Expired
					* @param: $email, $token
					*/
				public function checkTokenExpired($email);

				/**
					* @author SonNA6229
					* Check Expried Time Token
					* @param $token
					*/
				public function checkExpriedTime($email);
				
				/**
				 	*
				 	* @author TheuNT6809
				 	* Create Token, status
				 	* @param $token, $status
				 	*/
				public function updateStatus($email, $status);
				
					
}
