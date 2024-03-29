<?php
    namespace Config;
/**
 * Generated by framework installer - Mon, 11 Jan 2021 19:11:29 +0000
*/
    class Config
    {
        public const BASEDNAME	= '';
        public const PUTORPATCH	= 'PATCH';
        public const SESSIONNAME	= 'PSIlogger';
        public const DBTYPE	= 'mysql';
        public const DBHOST	= 'localhost';
        public const DB	= 'framework';
        public const DBUSER	= 'framework';
        public const DBPW	= 'framework';
        public const SITENAME	= 'logger';
        public const SITENOREPLY	= 'noreply@localhost.org';
        public const SYSADMIN	= 'c.d.tran2@newcastle.ac.uk';
        public const DBRX	= FALSE;
        public const REGISTER	= FALSE;
        public const CONFEMAIL	= FALSE;
        public const UPUBLIC	= FALSE;
        public const UPRIVATE	= TRUE;
        public const RECAPTCHA	= 0;
        public const RECAPTCHAKEY	= 'framework';
        public const RECAPTCHASECRET	= 'framework';
        public const USEPHPM	= FALSE;
        public const SMTPHOST	= '';
        public const SMTPPORT	= '';
        public const PROTOCOL	= '';
        public const SMTPUSER	= '';
        public const SMTPPW	= '';

        public static function setup()
        {
            \Framework\Web\Web::getinstance()->addheader([
            'Date'                   => gmstrftime('%b %d %Y %H:%M:%S', time()),
            'Window-Target'          => '_top',      // deframes things
            'X-Frame-Options'	     => 'DENY',      // deframes things: SAMEORIGIN would allow this site to use frames
            'Content-Language'	     => 'en',
            'Vary'                   => 'Accept-Encoding',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection'       => '1; mode=block',
            ]);
        }


        public static $defaultCSP = [
                'connect-src' => ["'self'"],
                'default-src' => ["'self'"],
                'font-src' => ["'self'", "data:", "*.fontawesome.com"],
                'img-src' => ["'self'", "data:"],
                'script-src' => ["'self'", "cdn.jsdelivr.net", "code.jquery.com", "*.fontawesome.com"],
                'style-src' => ["'self'", "cdn.jsdelivr.net", "*.fontawesome.com"],
        ];
    }

?>