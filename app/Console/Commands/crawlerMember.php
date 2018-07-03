<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Message;
use GuzzleHttp\Message\Response;
use App\Config;
use App\Repositories\User\UserRepositoryInterface;

class crawlerMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Get list user's information from portal";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $user)
    {
        parent::__construct();
        $this->user = $user;
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
        
        $response = $client->get($this->url['member_portal']);
        $result = json_decode((string) $response->getBody(), true);
        if(count($result) > 0){
            for($i = 0; $i < count($result); $i++){
                $data = [
                    'member_code' => $result[$i]['member_code'],
                    'intergreated_user_id' => $result[$i]['id'],
                    'id_resource' => $this->resource['portal'],
                    'fullname' => $result[$i]['name'],
                    'phone' => $result[$i]['phone'],
                    'skype' => $result[$i]['skype'],
                    'password' => bcrypt('12345678')
                ];
                // check email => get username
                if($result[$i]['email'] != null)
                {
                    $arr = explode("@",$result[$i]['email']);
                    $data['email'] = $result[$i]['email'];
                    $data['username'] = $arr[0];
                }
                if($result[$i]['email'] == null){
                    $data['username'] = $result[$i]['user_name'];
                }
                // check unique username
                $checkUniqueUsename = $this->user->checkUniqueCreate('username', $data['username']);
                if($checkUniqueUsename == 0){// create new record
                    $this->user->create($data);
                }
                if($checkUniqueUsename == 1){// update infor
                    $user = $this->user->findByField('username', $data['username']);
                    if($user){
                        $user_id = $user->id;
                        $data_update = [
                            'member_code' => $result[$i]['member_code'],
                            'intergreated_user_id' => $result[$i]['id'],
                            'id_resource' => $this->resource['portal']
                        ];
                        $this->user->update($user_id, $data_update);
                    }
                }
            }
            // mapping manager_id
            for($i = 0; $i < count($result); $i++){
                if($result[$i]['manage_id'] != null){
                    $manager = $this->user->findByField('intergreated_user_id', $result[$i]['manage_id']);
                    $user = $this->user->findByField('intergreated_user_id', $result[$i]['id']);
                    if($manager){
                        if($user){
                            $this->user->update($user->id, ['manage_id' => $manager->id]);
                        }
                    }
                }
            }
        }
    }
}
