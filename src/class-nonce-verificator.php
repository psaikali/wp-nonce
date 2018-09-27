<?php

namespace Inpsyde\Nonce;

class Nonce_Verificator {
    function __construct( Nonce_Abstract $nonce ) {
        $valid = $nonce->verify();
    }

    function ifSuccessful( Callable $callback ) {

    }

    function ifInvalid( Callable $callback ) {
        
    }
}