<?php

namespace Osana\Challenge\Services\GitHub;

use Osana\Challenge\Domain\Users\Company;
use Osana\Challenge\Domain\Users\Id;
use Osana\Challenge\Domain\Users\Location;
use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\Name;
use Osana\Challenge\Domain\Users\Profile;
use Osana\Challenge\Domain\Users\Type;
use Osana\Challenge\Domain\Users\User;
use Osana\Challenge\Domain\Users\UsersRepository;
use Osana\Challenge\Domain\Users\UserDoesNotExistsException;
use Tightenco\Collect\Support\Collection;

class GitHubUsersRepository implements UsersRepository
{
    private $apiGitHub = 'https://api.github.com';
    
    public function findByLogin(Login $login, int $limit = 0): Collection
    {
        $gitHubUsers = $users = array();
        
        if($login->getValue()) {
            $url =  $this->apiGitHub. "/search/users?q=" . $login->getValue() . "&per_page=" . $limit;
            $gitHubUsers = $this->getDataGitHub($url)["items"];
        }
        else {
            $url = $this->apiGitHub . "/users?per_page=" . $limit;
            $gitHubUsers = $this->getDataGitHub($url);
        }
        
        foreach($gitHubUsers as $user){
            $profileUser = $this->getDataGitHub($user["url"]);
            $name = $profileUser["name"] ?? '';
            $company = $profileUser["company"] ?? '';
            $location = $profileUser["location"] ?? '';
            
            $newUser = $this->factoryUser($user["id"], $user["login"], $name, $company, $location);
            array_push($users, $newUser);
        }

        return collect($users);
    }

    public function getByLogin(Login $login, int $limit = 0): User
    {
        $userCollect = $this->findByLogin($login, 1);
        if(empty($userCollect->all()))
            $user = $this->factoryUser('','','','','');
        else
            $user = $userCollect->all()[0];

        return $user;
    }

    public function add(User $user): void
    {
        throw new OperationNotAllowedException();
    }

    public function getDataGitHub($url): array
    {
        $context = stream_context_create(
            array(
                "http" => array(
                    "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
                )
            )
        );
        $json = file_get_contents($url, false, $context);
        return json_decode($json, true);
    }

    public function factoryUser($id, $login, $name, $company, $location): User
    {
        $profile = new Profile(
                        new Name($name), 
                        new Company($company), 
                        new Location($location)
                    );
        $newUser = new User(
                    new Id($id), 
                    new Login($login), 
                    Type::GitHub(), 
                    $profile
                );
        return $newUser;
    }
}