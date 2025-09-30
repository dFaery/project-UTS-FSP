    <?php
    require_once("data.php");

    class classParent
    {
        protected $mysqli;

        public function __construct()
        {
            $this->mysqli = new mysqli(SERVER, UID, PWD, DB);
        }

        function __destruct()
        {
            $this->mysqli->close();
        }
    }
    ?>