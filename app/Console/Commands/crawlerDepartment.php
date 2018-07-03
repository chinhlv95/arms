<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Message;
use GuzzleHttp\Message\Response;
use App\Config;
use App\Repositories\Division\DivisionRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\RoleUser\RoleUserRepositoryInterface;

class crawlerDepartment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:departments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Crawler department's information from portal";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DivisionRepositoryInterface $division, 
                                UserRepositoryInterface $user,
                                RoleUserRepositoryInterface $roleUser)
    {
        parent::__construct();
        
        $this->division = $division;
        $this->user = $user;
        $this->roleUser = $roleUser;
        $this->url = config('contains.API_RESOURCE'); // get link API
        $this->resource = config('contains.RESOURCE_DATA');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        
        $response = $client->get($this->url['department_portal']);
        $result = json_decode((string) $response->getBody(), true);
        
        if(count($result) > 0){
            for($i = 0; $i < count($result); $i++){
                $data = [
                'name' => $result[$i]['value'],
                'portal_id' => $result[$i]['id'],
                'id_resource' => $this->resource['portal'] // default
                ];
                // check record in db
                $check = $this->division->findByPortalId($result[$i]['id']); // $check is id of record
                // if not exists => create new; else =>update record
                if($check == 0){
                    //insert new record
                    $this->division->create($data);
                }
                else{
                    $this->division->update($check, $data);
                }
            }
            // mapping parent_id
            for($i = 0; $i < count($result); $i++){
                // mapping parent department
                if($result[$i]['parent_department_id'] != null){ // if item has parent_id
                    $id = $this->division->findByPortalId($result[$i]['id']); // find item and get id for update
                    if($id != 0){
                        $check = $this->division->findByPortalId($result[$i]['parent_department_id']);
                        if($check != 0){
                            $this->division->update($id, ['parent_id' => $check]);
                        }
                    }
                }
                // mapping manage division for user
                if($result[$i]['manager'] != null){
                    $user = $this->user->findByField('member_code', $result[$i]['manager']);
                    if($user){
                        $division_manager = $this->division->findByPortalId($result[$i]['id']); // get id division
                        if($division_manager != 0){
                            $this->user->update($user->id, ['division_id' => $division_manager]);
                        }
                        // add role for user
                        $this->roleUser->insert($user->id, 2);
                    }
                }
            }
        }
    }
}
