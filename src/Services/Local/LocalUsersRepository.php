<?php

namespace Osana\Challenge\Services\Local;

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

class LocalUsersRepository implements UsersRepository
{
    private $pathUsers = '../data/users.csv';
    private $pathProfiles = '../data/profiles.csv';
    

    public function findByLogin(Login $login, int $limit = 0): Collection
    {
        $localUsers = $users = array();
        $localUsers = $this->getDataUsers($login->getValue(), $limit);
        
        foreach($localUsers as $user){
            $profileUser = $this->getDataProfile($user["id"]);
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
        $userCSV = $user->getId()->getValue() .",".
                    $user->getLogin()->getValue() .",".
                    $user->getType()->getValue() ."\n";
        $this->setUsersCSV($userCSV);
        
        $profileCSV = $user->getId()->getValue() .",". 
                        $user->getProfile()->getCompany()->getValue() .",".
                        $user->getProfile()->getLocation()->getValue() .",".
                        $user->getProfile()->getName()->getValue() ."\n";
        $this->setProfilesCSV($profileCSV);
    }

    public function userToArray($user): array
    {
        $array = array(
            'id' => $user->getId()->getValue(),
            'login' => $user->getLogin()->getValue(),
            'type' => $user->getType()->getValue(),
            'profile' => array(
                'name' => $user->getProfile()->getName()->getValue(),
                'company' => $user->getProfile()->getCompany()->getValue(),
                'location' => $user->getProfile()->getLocation()->getValue(),
            )
        );
        return $array;
    }

    public function csvToArray($csv) {
        $rows = explode("\n", trim($csv));
        $data = array_slice($rows, 1);
        $keys = array_fill(0, count($data), $rows[0]);
        $array = array_map(function ($row, $key) {
            return array_combine(str_getcsv($key), str_getcsv($row));
        }, $data, $keys);
    
        return $array;
    }

    public function getDataUsers($search = '', $limit): array
    {
        $users = array();
        $csv = $this->getUsersCSV();
        $localUsers = $this->csvToArray($csv);
        $cantUsers = count($localUsers);
        $i = $j = 0;
        while($i < $cantUsers && $j < $limit) {
            $user = $localUsers[$i];
            if(strpos($user["login"], $search) === 0) {
                array_push($users, $user);
                $j++;
            }
            $i++;
        }
        return $users;
    }

    private function getDataProfile($id): array
    {
        $csv = $this->getProfilesCSV();
        $profiles = $this->csvToArray($csv);
        $key = array_search($id, array_column($profiles, 'id'));
        return $profiles[$key];
    }

    public function factoryUser($id, $login, $name, $company, $location): User
    {
        $profile = new Profile(
                        new Name($name), 
                        new Company($company), 
                        new Location($location)
                    );
        $user = new User(
                    new Id($id),
                    new Login($login),
                    Type::Local(), 
                    $profile
                );
        return $user;
    }

    public function getUsersCSV() {
        return file_get_contents($this->pathUsers);
    }

    public function getProfilesCSV() {
        return file_get_contents($this->pathProfiles);
    }

    public function setUsersCSV($userCSV) {
        file_put_contents($this->pathUsers, $userCSV, FILE_APPEND | LOCK_EX);
    }

    public function setProfilesCSV($profileCSV) {
        file_put_contents($this->pathProfiles, $profileCSV, FILE_APPEND | LOCK_EX);
    }
}