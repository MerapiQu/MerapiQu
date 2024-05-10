<?php

namespace MerapiPanel\Module\Auth\Controller;

use Google_Client;
use MerapiPanel\Box;
use MerapiPanel\Box\Module\__Fragment;
use MerapiPanel\Utility\AES;
use MerapiPanel\Views\View;
use MerapiPanel\Database\DB;
use MerapiPanel\Utility\Http\Request;
use MerapiPanel\Utility\Router;
use PDO;
use Throwable;

class Guest extends __Fragment
{

    protected $module;
    function onCreate(Box\Module\Entity\Module $module)
    {
        $this->module = $module;
    }


    public function register()
    {

        if (isset($_ENV["__MP_ADMIN__"]['prefix'])) {

            Router::POST("/auth/api/" . ltrim($_ENV["__MP_ADMIN__"]['prefix'], "/"), "GoogleAuth", self::class);
            Router::POST("/auth/" . ltrim($_ENV["__MP_ADMIN__"]['prefix'], "/"), "Login", self::class);
        }
    }


    function GoogleAuth(Request $req)
    {

        $referer = $req->http("referer");
        try {

            if (!$referer) {
                $referer = $_ENV['__MP_' . strtoupper($_ENV["__MP_ACCESS__"]) . '__']['prefix'] . "/";
            }

            require_once __DIR__ . "/../vendor/autoload.php";

            $config = $this->module->getConfig();
            $credential = $req->credential();
            $g_csrf_token = $req->g_csrf_token();

            $client = new Google_Client([
                "client_id" => $config->get('google_oauth_id')
            ]);


            $payload = $client->verifyIdToken($credential);
            if ($payload) {

                if (!$payload['email_verified']) {
                    return View::render("response.html.twig", [
                        "status" => "error",
                        "message" => "Your email is not verified",
                        "redirect" => $referer
                    ]);
                }

                $email = $payload['email'];

                if ($user = DB::table("users")->select("*")->where("email")->equals($email)->execute()->fetch(PDO::FETCH_ASSOC)) {

                    // $config = $this->module->getConfig();
                    // $cookie_name  = $config->get("cookie_name");
                    // $session_time = $config->get("session_time");
                    // $geo          = $config->get("geo");
                    // $geo_range    = $config->get("geo.range");

                    // // check geo location
                    // if ($geo) {

                    //     // check config for geo location
                    //     if (empty($latitude) && empty($logitude)) {
                    //         setcookie($cookie_name, "", time() - 3600, "/");
                    //         return [
                    //             "code" => 400,
                    //             "message" => "Required to verify your location",
                    //         ];
                    //     }

                    //     if (!$this->geoInRange($user['id'], $latitude, $logitude)) {
                    //         setcookie($cookie_name, "", time() - 3600, "/");
                    //         return [
                    //             "code" => 400,
                    //             "message" => "Can't verify your location"
                    //         ];
                    //     }

                    //     if (!self::insertGeo($user['id'], $latitude, $logitude)) {
                    //         setcookie($cookie_name, "", time() - 3600, "/");
                    //         return [
                    //             "code" => 400,
                    //             "message" => "Failed to insert geo location"
                    //         ];
                    //     }
                    // }

                    $token = bin2hex(random_bytes(4));
                    $last_session = DB::table("session_token")
                        ->select("*")
                        ->where("expires")->greaterThan(date("Y-m-d H:i:s"))
                        ->where("user_id")->equals($user["id"])
                        ->execute();

                    if ($last_session->rowCount() > 0) {

                        $token = $last_session->fetch(PDO::FETCH_ASSOC)["token"];

                    } else {

                        DB::table("session_token")->insert([
                            "token" => $token,
                            "expires" => date("Y-m-d H:i:s", strtotime(" + " . $config->get("session_time") . " hours")),
                            "client_ip" => Request::getClientIP(),
                            "user_id" => $user["id"]
                        ])->execute();
                    }

                    if (!setcookie($config->get("cookie_name"), AES::encrypt($token), strtotime(date("Y-m-d H:i:s", strtotime(" + " . $config->get("session_time") . " hours"))), "/")) {

                        return View::render("response.html.twig", [
                            "status" => "danger",
                            "message" => "Error while setting cookie",
                            "redirect" => $referer
                        ]);
                    }


                    if (!$referer) {

                        $referer = $_ENV['__MP_' . strtoupper($_ENV["__MP_ACCESS__"]) . '__']['prefix'] . "/";
                    }

                    return View::render("response.html.twig", [
                        "status" => "success",
                        "message" => "Login successful.",
                        "redirect" => $referer
                    ]);
                }


                return View::render("response.html.twig", [
                    "status" => "warning",
                    "message" => "User with this email not found.",
                    "redirect" => $referer
                ]);

            }


            return View::render("response.html.twig", [
                "status" => "warning",
                "message" => "Invalid credentials.",
                "redirect" => $referer
            ]);

        } catch (Throwable $e) {

            return View::render("response.html.twig", [
                "status" => "danger",
                "message" => $e->getMessage(),
                "redirect" => $referer ?? "/"
            ]);
        }
    }

}