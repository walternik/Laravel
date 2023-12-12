<?php

namespace Osana\Challenge\Http\Controllers;

use Osana\Challenge\Services\Local\LocalUsersRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StoreUserController
{
    /** @var LocalUsersRepository */
    private $localUsersRepository;

    public function __construct(LocalUsersRepository $localUsersRepository)
    {
        $this->localUsersRepository = $localUsersRepository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $login = $request->getQueryParams()['login'] ?? '';
        $name = $request->getQueryParams()['name'] ?? '';
        $company = $request->getQueryParams()['company'] ?? '';
        $location = $request->getQueryParams()['location'] ?? '';

        if($login && $name && $company && $location) {
            $csv = $this->localUsersRepository->getUsersCSV();
            $cant = count($this->localUsersRepository->csvToArray($csv)) + 1;
            
            $id = 'CSV'. $cant;
            $user = $this->localUsersRepository->factoryUser($id, $login, $name, $company, $location);
            $this->localUsersRepository->add($user);

            $array = $this->localUsersRepository->userToArray($user);
            $user = collect($array);
            $response->getBody()->write($user->toJson());
            
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201, 'Created');
        } else {
            $response->getBody()->write('{"Error":"Parámetros incorrectos"}');
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400, 'Failded');
        }
    }
}