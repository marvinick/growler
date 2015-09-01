<?php

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Store.php";
    require_once __DIR__."/../src/Beer.php";
    require_once __DIR__."/../src/Review.php";
    require_once __DIR__."/../src/User.php";

    $app = new Silex\Application();

    $app['debug']=true;

    $server = 'mysql:host=localhost;dbname=growler';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views')
    );

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    //index page
    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig');
    });

    // ------------------------ Begin User Functionality ----------------------------

    //route from user login on index page.  find the user by name and
    //show that user's profile page.
    $app->get("/profile", function() use ($app) {
        $user = User::findName($_GET['user_name']);
        return $app['twig']->render('profile.html.twig', array('user' => $user));
    });

    //route from create user on index page.  create a new user, save, and show
    //that user's profile page.
    $app->post("/create_user", function() use ($app) {
        $user = new User($_POST['user_name'], $_POST['preferred_style'], $_POST['region']);
        $user->save();
        return $app['twig']->render('profile.html.twig', array('user' => $user));
    });

    //route from user profile page.  go to profile edit page.
    //Finds user by ID
    $app->get("/edit_profile/{id}", function($id) use ($app) {
        $user = User::find($id);
        return $app['twig']->render('profile_edit.html.twig', array('user' => $user));
    });

    //Comes from profile_edit.html
    //Renders to profile.html
    //Updates user information
    $app->patch("/user/{id}", function ($id) use ($app) {
        $user = User::find($id);
        $user->updateUserName($_POST['user_name']);
        $user->updatePreferredStyle($_POST['preferred_style']);
        $user->updateRegion($_POST['region']);
        return $app['twig']->render('profile.html.twig', array('user' => $user));
    });


    // ------------------------ Begin Beer Functionality ----------------------------


    //Comes from profile page.
    //Renders to beers.html
    //Gathers all beers from the database
    $app->get("/beers", function() use ($app) {
        return $app['twig']->render('beers.html.twig', array('all_beers' => Beer::getAll()));
    });

    //Comes from beers.html
    //Posts back to beers.html
    //Adds a beer to database
    $app->post("/beers", function() use ($app) {
        $beer_name = $_POST['beer_name'];
        $style = $_POST['style'];
        $abv = $_POST['abv'];
        $ibu = $_POST['ibu'];
        $container = $_POST['container'];
        $brewery = $_POST['brewery'];
        $beer = new Beer($beer_name, $style, $abv, $ibu, $container, $brewery);
        $beer->save();
        return $app['twig']->render('beers.html.twig', array('all_beers' => Beer::getAll()));
    });

    //Comes from not yet defined.
    //Renders to beers.html??
    //View one beer from the database
    $app->get("/beers/{id}", function($id) use ($app) {
        $beer = Beer::find($id);
        return $app['twig']->render('beers.html.twig', array('beer' => $beer, 'beers' => Beer::getAll(), 'users' => $beer->getUsers(), 'all_users' => User::getAll()));
    });



    // ------------------------ Begin Store Functionality ----------------------------





    return $app; //End of app, do not code below here
?>
