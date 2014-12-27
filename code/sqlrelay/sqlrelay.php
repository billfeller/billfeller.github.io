<?php
$con = sqlrcon_alloc('mysqlpool', 12000, '/tmp/mysqlpool.socket', 'root', '******', 0, 1);

var_dump(sqlrcon_errorNumber ($con));
var_dump(sqlrcon_dbHostName ($con));
var_dump(sqlrcon_dbIpAddress ($con));

$cur = sqlrcur_alloc($con);

sqlrcur_sendQuery($cur, 'select * from test');

var_dump(sqlrcur_totalRows ($cur));

for ($row=0; $row<sqlrcur_rowCount($cur); $row++) {
    for ($col=0; $col<sqlrcur_colCount($cur); $col++) {
        echo sqlrcur_getField($cur,$row,$col);
    }
    echo PHP_EOL;
}

sqlrcur_free($cur);
sqlrcon_free($con);
