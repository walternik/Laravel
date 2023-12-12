<?php

namespace Osana\Challenge\Http\Controllers;

use Osana\Challenge\Domain\Users\Login;
use Osana\Challenge\Domain\Users\Type;
use Osana\Challenge\Services\Local\LocalUsersRepository;
use Osana\Challenge\Services\GitHub\GitHubUsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tightenco\Collect\Support\Collection;

class ShowUserController
{
    /** @var LocalUsersRepository */
    private $localUsersRepository;

    /** @var GitHubUsersRepository */
    private $gitHubUsersRepository;

    public function __construct(LocalUsersRepository $localUsersRepository, GitHubUsersRepository $gitHubUsersRepository)
    {
        $this->localUsersRepository = $localUsersRepository;
        $this->gitHubUsersRepository = $gitHubUsersRepository;
    }

    public function __invoke(Request $request, Response $response, array $params): Response
    {
        $type = new Type($params['type']);
        $login = new Login($params['login']);

        if($type->getValue() == Type::Local())
            $user = $this->localUsersRepository->getByLogin($login);   
        else
            $user = $this->gitHubUsersRepository->getByLogin($login); 
        
        $array = $this->localUsersRepository->userToArray($user);

        if($array["id"] == '') {
            $response->getBody()->write('{"Not Found":"Usuario '.$login->getValue().' no encontrado"}');
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200, 'OK');
        }
        $user = collect($array);
        
        $response->getBody()->write($user->toJson());
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200, 'OK');
    }
}