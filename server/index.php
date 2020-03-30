<?php

require __DIR__ . '/vendor/autoload.php';



use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;


//just for reference
$config = array('client_id'=> 'ad65745b99e00211dd613e8bb4cd9a76',
                'client_secret' => '769449d33436c1996fdaf0f4c6d95a26',
                'api_endpoint'=> 'https://api.codechef.com/',
                'authorization_code_endpoint'=> 'https://api.codechef.com/oauth/authorize',
                'access_token_endpoint'=> 'https://api.codechef.com/oauth/token',
                'redirect_uri'=> 'http://localhost:80/',
                'website_base_url' => 'http://localhost:80/');



include './curlFunctions.php';
include './functions.php';


$app = AppFactory::create();

$app->addRoutingMiddleware();

//later changed to /getuser from /
$app->get('/getuser', function (Request $request, Response $response, $args) {

    if(isset($_GET['code'])){
        $a = get_auth_token($_GET['code']);
        $token = $a['access_token'];
        $user = get_user($token);
        $password = login($token, $a['refresh_token'], "{$user['username']}");

        $cred = array(
            'username' => $user['username'],
            'password' => $password
            );

        //return user id and password when got from login function in a 
        //json format using json_encode()

        $payload = json_encode($cred);
        $response->getBody()->write($payload);
        $response = $response->withHeader('Content-Type', 'application/json');
        
        
        // $response->getBody()->write("{$result}");
    }

    else{
    //serve the index.html
    $response->getBody()->write("no code");
    }
    
    return $response;
});






$app->redirect('/login', 'https://api.codechef.com/oauth/authorize?response_type=code&client_id=ad65745b99e00211dd613e8bb4cd9a76&state=xyz&redirect_uri=http://localhost:80/', 301);


//get all contests
$app->get('/contestlist', function (Request $request, Response $response, $args) {
    
    //pending->do this with universal token
    // $token = get_universal_token($_GET['username'], $_GET['password']);
    if(isset($_GET['username']) and isset($_GET['password'])){
        $token = get_token_from_db($_GET['username'], $_GET['password']);
        $result = curl_get_all_contests($token);
        $response->getBody()->write($result);
        $response = $response->withHeader('Content-Type', 'application/json');
    }
else{
    $file = './index.html';
        if (file_exists($file)) {
            $response->getBody()->write(file_get_contents($file));
            $response = $response->withHeader('Content-Type', 'text/html');
            return $response;
        } else {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
}
    return $response;
});



//get ranklist for a particular contest
$app->get('/ranklist', function (Request $request, Response $response, $args) {
    
    if(isset($_GET['username']) and isset($_GET['password'])){
        //get contest details with user auth to get rank also
        $contest = $_GET['contest'];
        $token = get_token_from_db($_GET['username'], $_GET['password']);
        $result = curl_ranklist($contest, $token);
        $response->getBody()->write($result);
        $response = $response->withHeader('Content-Type', 'application/json');
    }

    else{
        //get contest details without user auth
        $file = './index.html';
        if (file_exists($file)) {
            $response->getBody()->write(file_get_contents($file));
            $response = $response->withHeader('Content-Type', 'text/html');
            return $response;
        } else {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }

    return $response;
});


//search using contest code and returnig contest details
$app->get('/contest', function (Request $request, Response $response, $args) {
    
    if(isset($_GET['username']) and isset($_GET['password'])){
        //get contest details with user auth to get rank also
        $contest = $_GET['contest'];
        $token = get_token_from_db($_GET['username'], $_GET['password']);
        $result = curl_contest_details($contest, $token);
        $response->getBody()->write($result);
        $response = $response->withHeader('Content-Type', 'application/json');
    }

    else{
        //get contest details without user auth
        $file = './index.html';
        if (file_exists($file)) {
            $response->getBody()->write(file_get_contents($file));
            $response = $response->withHeader('Content-Type', 'text/html');
            return $response;
        } else {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }

    return $response;
});



//curl_submission_details($contest, $problem, $token)

$app->get('/submission', function (Request $request, Response $response, $args) {
    
    if(isset($_GET['username']) and isset($_GET['password'])){
        
        $contest = $_GET['contest'];
        $problem = $_GET['problem'];
        $token = get_token_from_db($_GET['username'], $_GET['password']);
        $result = curl_submission_details($contest, $problem, $token);
        $response->getBody()->write($result);
        $response = $response->withHeader('Content-Type', 'application/json');
        
    }

    else{
        //get contest details without user auth
        $file = './index.html';
        if (file_exists($file)) {
            $response->getBody()->write(file_get_contents($file));
            $response = $response->withHeader('Content-Type', 'text/html');
            return $response;
        } else {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }

    
    return $response;
});




$app->get('/problem', function (Request $request, Response $response, $args) {
    
    if(isset($_GET['username']) and isset($_GET['password'])){
        
        $contest = $_GET['contest'];
        $problem = $_GET['problem'];
        $token = get_token_from_db($_GET['username'], $_GET['password']);
        $result = curl_problem_details($contest, $problem, $token);
        $response->getBody()->write($result);
        $response = $response->withHeader('Content-Type', 'application/json');
        
    }


    else{
        $file = './index.html';
        if (file_exists($file)) {
            $response->getBody()->write(file_get_contents($file));
            $response = $response->withHeader('Content-Type', 'text/html');
            return $response;
        } else {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
    }

    
    return $response;
});




$app->get('/', function (Request $request, Response $response, $args) {
    
    $file = './index.html';
    if (file_exists($file)) {
        $response->getBody()->write(file_get_contents($file));
        $response = $response->withHeader('Content-Type', 'text/html');
        return $response;
    } else {
        throw new \Slim\Exception\NotFoundException($request, $response);
    }
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();