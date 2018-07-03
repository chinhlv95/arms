<?php
namespace App\Repositories\Password;

use App\Repositories\EloquentRepository;

class PasswordRepository extends EloquentRepository implements PasswordRepositoryInterface{

				/**
					* Get model
					* @return string
					*/
				public function getModel(){
					return \App\Models\PasswordResets::class;
				}

				/**
					* @author SonNA6229
					* Check if token Expired
					* @param: $email, $token
					*/
				public function checkTokenExpired($email){
					return $this->_model->where('email', $email)->firstOrFail();
				}
				
				public function checkTokenExist($email){
					return !empty($this->_model->where('email','=',$email)->first());
					
				}

				/**
					* @author SonNA6229
					* Check Expried Time Token
					* @param $token
					*/
				public function checkExpriedTime($email){
								$time = $this->_model->where('email', $email)->firstOrFail();
								$currentHour = strtotime(date("Y-m-d h:i:s" ));
								$afterHour = strtotime(date('Y-m-d H:i:s', strtotime("$time->created_at + 1 hours")));
								
								return $currentHour >= $afterHour;
				}
				
				/**
				 * @author TheuNt
				 * Update status
				 * @param $status, $email
				 */
				public function updateStatus($email, $status){
					return $this->_model->where('email', $email)->update(['status' => $status]);
				}
				
}