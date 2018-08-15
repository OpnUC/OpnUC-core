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
    'strict' => true, //@todo: make this depend on laravel config
    'debug' => env('APP_DEBUG'),
    'sp' => array(
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
        'x509cert' => env('SAML2_SP_CERT', 'dummy'),
        'privateKey' => env('SAML2_SP_KEY', 'dummy'),
    ),
    'idp' => array(
        'entityId' => env('SAML2_ENTITYID', 'dummy'),
        'singleSignOnService' => array(
            'url' => env('SAML2_SSOS', 'http://example.com/dummy/'),
        ),
        'singleLogoutService' => array(
            'url' => env('SAML2_SLS', ''),
        ),
        'x509cert' => env('SAML2_IDP_CERT', ''),
        'certFingerprint' => env('SAML2_IDP_FINGERPRINT', ''),
    ),
    'security' => array(
        'nameIdEncrypted' => false,
        'authnRequestsSigned' => true,
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,
        'signMetadata' => false,
        'wantMessagesSigned' => false,
        'wantAssertionsSigned' => false,
        'wantNameIdEncrypted' => false,
        'requestedAuthnContext' => true,
    ),
);