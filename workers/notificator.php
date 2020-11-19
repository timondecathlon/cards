<?php
/**
 * Created by PhpStorm.
 * User: timondecathlon
 * Date: 30.08.20
 * Time: 23:47
 */

require_once('../errors.php');   


define("DB_HOST", 'localhost');
define("DB_NAME", 'cards');
define("DB_USER", 'timon');
define("DB_PASSWORD", '20091993dec');


//Для процедурки
$pdo = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
$pdo->exec("set names utf8");


$path = '/var/www/www-root/data/www/cards.bitkit.pro/system/classes';

//для использования классов
require_once($path. '/Bitkit/Core/Traits/Test.php');
require_once($path. '/Bitkit/Core/Interfaces/UserPower.php');
require_once($path. '/Bitkit/Core/Interfaces/UserAuth.php');
require_once($path. '/Bitkit/Core/Interfaces/UserActions.php');
require_once($path. '/Bitkit/Core/Interfaces/UnitActions.php');
require_once($path. '/Bitkit/Core/Entities/Unit.php');
require_once($path. '/Bitkit/Core/Entities/Post.php');
require_once($path. '/Bitkit/Core/Entities/User.php');
require_once($path. '/Bitkit/Core/Entities/Board.php');
require_once($path. '/Bitkit/Core/Entities/Card.php');
require_once($path. '/Bitkit/Core/Patterns/Singleton.php');
require_once($path. '/Bitkit/Core/Database/Connect.php');
require_once($path. '/Telegram.php');



//var_dump($_GET);

$sql_users = $pdo->prepare("SELECT * FROM core_users");
$sql_users->execute();
while($user_info = $sql_users->fetch(\PDO::FETCH_LAZY)){



        $user = new \Bitkit\Core\Entities\User($user_info->id);

        $user_id = $user->getField('id');

        echo $user_id.'<br>';
        echo $user_info->telegram_id.'<br>';

        $telegram = new \Telegram('1366696590:AAHlCLf9y2oi_orHfHDbhq7rzRaZn1_obmQ');

        $board_columns = [];

        $sql_boards = $pdo->prepare("SELECT * FROM core_boards WHERE user_id=$user_id");
        $sql_boards->execute();
        while ($board = $sql_boards->fetch(\PDO::FETCH_LAZY)) {
            $columns = (array)json_decode($board->board_data,true);

            $board_columns = array_merge($board_columns,$columns);
        }


        if ($board_columns != null) {
            //var_dump($taskboards);

            //собираем id всех карточек со всех досок юзера
            $cards_id = [];

            foreach ($board_columns as $column_name => $column_cards) {
                foreach ($column_cards as $card_id) {
                    $cards_id[] = $card_id;
                }
            }


            //лостаем их из базы ОДНИМ запрсом (попутно набирая id тэгов)
            if ($cards_id) {
                $str_id = implode(',', $cards_id);
                $sql = $pdo->prepare("SELECT * FROM core_cards WHERE id IN($str_id) AND notification=1 AND deleted!=1");
                //echo "SELECT * FROM core_tasks WHERE id IN($str_id) AND notification=1";
                $sql->execute();
                while ($card = $sql->fetch(\PDO::FETCH_LAZY)) {
                    //echo $task->title;
                    if (time() > $card->notification_last + $card->notification_timeout) {
                        $telegram->sendMessage($card->title, $user_info->telegram_id);

                        echo $user_info->telegram_id.'<br>';

                        //это заменить на общий апдейт одним запросом
                        $card = new \Bitkit\Core\Entities\Card($card->id);
                        $card->updateField('notification_last',time());
                    }

                }
            }
        }




}





