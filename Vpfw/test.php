<?php
function test($arg1, $arg2) {
    echo 'number of arguments received: ' . func_num_args() . '<br />';
    foreach (func_get_args() as $key => $value) {
        echo 'argument with key ' . $key . ' has value ' . $value . '<br />';
    }
}

function strallpos($haystack, $needle, $offset = 0) {
    $result = array();
    for ($i = $offset; $i < strlen($haystack); $i++) {
        $pos = strpos($haystack, $needle, $i);
        if (false !== $pos) {
            $offset = $pos;
            if ($offset >= $i) {
                $i = $offset;
                $result[] = $offset;
            }
        }
    }
    return $result;
}

$subject = "SELECT * FROM blub WHERE Id = ? AND Name = '?';";
$types = array('%d', '%s');
$values = array(5, 'master');
while (false !== $pos = strpos($subject, '?')) {
    $subject = substr_replace($subject, array_shift($types), $pos, 1);
    echo $subject . '<br />';
}
array_unshift($values, $subject);
echo call_user_func_array('sprintf', $values);
