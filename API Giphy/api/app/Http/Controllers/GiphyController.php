<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
use App\Models\Fav;

class GiphyController extends Controller
{
    public function get_gifs($query, $limit = '', $offset = '') {
        $url = env('API_ENDPOINT');
        $api_key = env('API_KEY');
        
        $url .= 'search?api_key=' . $api_key;
        $url .= '&q=' . $query;
        if($limit!='') $url .= '&limit=' . $limit;
        if($offset!='') $url .= '&offset=' . $offset;

        $response = Http::get($url);
        
        $this->storeLog($url, 'get_gifs', $response);

        return $response->json();
    }


    public function get_gif_id($id) {
        $url = env('API_ENDPOINT');
        $api_key = env('API_KEY');
        
        $url .= $id;
        $url .= '?api_key=' . $api_key;

        $response = Http::get($url);

        $this->storeLog($url, 'get_gif_id', $response);

        return $response->json();
    }


    public function set_fav_gif($gif_id, $alias, $user_id) {
        
        $this->storeFav($gif_id, $alias);

        $url = env('API_ENDPOINT');
        
        $url .= $gif_id . "/" . $alias . "/" . $user_id;
        $response['meta']['status'] = '200';
        $response['meta']['msg'] = 'OK';
        $this->storeLog($url, 'set_fav_gif', $response);
    }


    private function storeLog($url, $servicio, $response) {
        $logData['usuario'] = Auth::user()->id;
        $logData['servicio'] = $servicio;
        $logData['cuerpo_peticion'] = $url;
        $logData['codigo_respuesta'] = $response['meta']['status'];
        $logData['cuerpo_respuesta'] = $response['meta']['msg'];
        $logData['ip_origen'] = \request()->ip();
        $log = Log::create($logData);
    }


    private function storeFav($gif_id, $alias) {
        $favData['gif_id'] = $gif_id;
        $favData['alias'] = $alias;
        $favData['user_id'] = Auth::user()->id;
        $fav = Fav::create($favData);
    }
}
