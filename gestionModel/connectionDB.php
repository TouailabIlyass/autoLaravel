<?php

class ConnectionDB{
        static $PDO = NULL;

        private static function Connection($dbname)
        {
                try{


                self::$PDO = new PDO("mysql:host=localhost;dbname=$dbname;charset=UTF8",'root','',
                [	
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => true,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

                ]);

                }
                catch(PDOException $e)
                {
                        die($e->getMessage());
                }

        }
        public static function getConnectionDB($dbname)
        {
                if (self::$PDO == NULL)
                {
                        self::Connection($dbname);
                }
                return self::$PDO;
        }


}

