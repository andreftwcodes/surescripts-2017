<?php




require_once './setup.inc';

foreach ($dsns as $dbms => $dsn) {
    echo "======== $dbms ========\n";
    $options['persistent'] = false;
    $dbh =& DB::connect($dsn, $options);
    if (DB::isError($dbh)) {
        echo 'PROBLEM: ' . $dbh->getUserInfo() . "\n";
        continue;
    }

    if ($dbh->provides('new_link')
        && version_compare(phpversion(), $dbh->provides('new_link'), '>='))
    {
        $probs = false;
        $dsn = DB::parseDSN($dsn);
        $dsn['new_link'] = true;
        $dbh =& DB::connect($dsn, $options);
        if (DB::isError($dbh)) {
            echo 'NEW LINK PROBLEM: ' . $dbh->getUserInfo() . "\n";
            $probs = true;
        }

        if ($dbh->provides('pconnect')) {
            $options['persistent'] = true;
            $dbh->disconnect();
            $dbh =& DB::connect($dsn, $options);
            if (DB::isError($dbh)) {
                echo 'PERSIST NEWCON PROBLEM: ' . $dbh->getUserInfo() . "\n";
                $probs = true;
            }

            unset($dsn['new_link']);
            $dbh->disconnect();
            $dbh =& DB::connect($dsn, $options);
            if (DB::isError($dbh)) {
                echo 'PERSIST OLDCON PROBLEM: ' . $dbh->getUserInfo() . "\n";
                $probs = true;
            }
        }
        if ($probs) {
            continue;
        }
        $dbh->disconnect();

    } elseif ($dbh->provides('pconnect')) {
        $options['persistent'] = true;
        $dbh->disconnect();
        $dbh =& DB::connect($dsn, $options);
        if (DB::isError($dbh)) {
            echo 'PERSIST PROBLEM: ' . $dbh->getUserInfo() . "\n";
            continue;
        }
        $dbh->disconnect();
    }
    echo "GOOD\n";
}
