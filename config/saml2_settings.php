<?php
return $settings = array(
    'useSaml2Auth' => env('SAML2_ENABLE', false),
    'useRoutes' => true,
    'routesPrefix' => '/extensions/saml2',
    'routesMiddleware' => ['saml'],
    'retrieveParametersFromServer' => false,
    'logoutRoute' => '/',
    // Vue側ですでにログイン済みとして処理するため、 mode を restore とする
    'loginRoute' => '/loginRestore',

    'errorRoute' => '/login',
    // If 'proxyVars' is True, then the Saml lib will trust proxy headers
    // e.g X-Forwarded-Proto / HTTP_X_FORWARDED_PROTO. This is useful if
    // your application is running behind a load balancer which terminates
    // SSL.
    'proxyVars' => false,

    /**
     * Array of IDP prefixes to be configured e.g. 'idpNames' => ['test1', 'test2', 'test3'],
     * Separate routes will be automatically registered for each IDP specified with IDP name as prefix
     * Separate config file saml2/<idpName>_idp_settings.php should be added & configured accordingly
     */
    'idpNames' => ['kcprd'],

    /**
     * (Optiona) Which class implements the route functions.
     * If left blank, defaults to this lib's controller (Aacotroneo\Saml2\Http\Controllers\Saml2Controller).
     * If you need to extend Saml2Controller (e.g. to override the `login()` function to pass
     * a `$returnTo` argument), this value allows you to pass your own controller, and have
     * it used in the routes definition.
     */
    //'saml2_controller' => '',
);