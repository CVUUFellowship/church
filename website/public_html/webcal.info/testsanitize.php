<?php
        echo "TESTING filter_var <br>";
        echo "PHP VERSION ID ", phpversion(), "<br>";
        $string = "Now is the time.";
        echo "STRING $string <br>";
        $result = filter_var($string, FILTER_SANITIZE_STRING);
        echo "FILTER_SANITIZE_STRING RESULT $result <br>";
        $result2 = filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        echo "FILTER_SANITIZE_FULL_SPECIAL_CHARS RESULT $result2 <br>";

?>