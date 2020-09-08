<?php

class ConnectionDB{

        //pdo static var
        static $PDO = NULL;

        /*
        Input: take a database name as input
        Role: create a connection to Mysql database
        Output: None
        */
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

        /*
        Input: take a database name as input
        Role: return an connection instance
        Output: cnx instance
        */
        public static function getConnectionDB($dbname)
        {
                if (self::$PDO == NULL)
                {
                        self::Connection($dbname);
                }
                return self::$PDO;
        }


}

