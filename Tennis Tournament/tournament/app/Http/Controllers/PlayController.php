<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FemalePLayer;
use App\Models\MalePlayer;

/**
* @OA\Info(
*             title="Tennis Tournament API Simulator", 
*             version="1.0",
*             description="Shows the winner of the tennis tournament given a list of players"
* )
*
* @OA\Server(url="http://127.0.0.1:8000")
*/
class PlayController extends Controller
{
    private $tournament_type;

    /**
     * Show the winner of the tennis tournament
     * @OA\Post (
     *     path="/api/play",
     *     tags={"Play"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="tournament_type",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="players",
     *                          type="array|object"
     *                      )
     *                 ),
     *                 example={
	 *						"tournament_type": "male",
	 *						"players": {
	 *							{
	 *								"name": "Goran Svenson",
	 *								"skill": 95,
	 *								"strength": 120,
	 *								"speed": 18
	 *							},
	 *							{
	 *								"name": "Juan Carrasco",
	 *								"skill": 93,
	 *								"strength": 121,
	 *								"speed": 25
	 *							}
	 *						}
     *                	}
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Ok",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The winner is Goran Svenson.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The number of players must be a power of 2.")
     *          )
     *      )
     * )
     */
    public function play(Request $request) {
        $tournament_type = $request['tournament_type'];
        $players = $request['players'];

        if($tournament_type != 'male' && $tournament_type != 'female') {
            return response()->json(['message' => 'Wrong type of tournament.'], 400);
        }
        $this->tournament_type = $tournament_type;
        $player_list = array();
        foreach($players as $player) {
            if($tournament_type == 'male') {
                $p = new MalePlayer();
                $p->setName($player['name']);
                $p->setSkill($player['skill']);
                $p->setStrength($player['strength']);
                $p->setSpeed($player['speed']);
                $player_list[] = $p;
            } else {
                $p = new FemalePlayer();
                $p->setName($player['name']);
                $p->setSkill($player['skill']);
                $p->setReaction($player['reaction']);
                $player_list[] = $p;
            }
        }
        $total = count($player_list);
        if(($total > 0) && (($total & ($total - 1)) == 0)) {
            // number of players OK
        } else {
            return response()->json(['message' => 'The number of players must be a power of 2.'], 400);
        }
        $winner = $this->simulateTournament($player_list);

        return response()->json(['message' => 'The winner is ' . $winner->getName() ], 200);
    }


    // Tennis match simulation
    public function match($player1, $player2) {
        $c1 = env('COEF_SKILL');
        $c5 = env('COEF_LUCK');
        $luck1 = mt_rand(0, env('MAX_VALUE_LUCK'));
        $luck2 = mt_rand(0, env('MAX_VALUE_LUCK'));
        if($this->tournament_type == 'male') {
            $c2 = env('COEF_STRENGTH');
            $c3 = env('COEF_SPEED');
            $value1 = $c1*$player1->getSkill() + $c2*$player1->getStrength() + $c3*$player1->getSpeed() + $c5*$luck1;
            $value2 = $c1*$player2->getSkill() + $c2*$player2->getStrength() + $c3*$player2->getSpeed() + $c5*$luck2;
        } else {
            $c4 = env('COEF_REACTION');
            $value1 = $c1*$player1->getSkill() + $c4*$player1->getReaction() + $c5*$luck1;
            $value2 = $c1*$player2->getSkill() + $c4*$player2->getReaction() + $c5*$luck2;
        }
        $winner = ($value1 > $value2) ? $player1 : $player2;
        return $winner;
    }


    public function simulateTournament($players) {
        while (count($players) > 1) {
            $newPlayers = array();
            // Iterate over the players in steps of two
            for ($i = 0; $i < count($players); $i += 2) {
                $player1 = $players[$i];
                $player2 = $players[$i + 1];
                $winner = $this->match($player1, $player2);
                // The winner moves on to the next round
                $newPlayers[] = $winner;
            }
            // To the next round
            $players = $newPlayers;
        }
        // Return the winner of the tennis tournament
        return $players[0];
    }
}
