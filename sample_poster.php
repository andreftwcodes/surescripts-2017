<?php


include "./base/MedRequest.php";

$o = new MedRequest("http://mem.meditab.com/services/med_checkin_checkout.php");

echo $o->post("173__CHECKIN__1__1336625360__YES");

