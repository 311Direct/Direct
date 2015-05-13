<?php

const P_FULL_CONTROL    = 0b100000000;
const P_READ            = 0b010000000;
const P_WRITE           = 0b001000000;
const P_CREATE          = 0b000100000;
const P_DELETE          = 0b000010000;
const P_MODIFY_ATTR     = 0b000001000;
const P_CHANGE_ACCESS   = 0b000000100;
const P_LIST_CONTENTS   = 0b000000010;
const P_INHERITS_PARENT = 0b000000001;
const P_ACCESS_ADD      = 0b001;
const P_ACCESS_UPDATE   = 0b010;
const P_ACCESS_DELETE   = 0b100;

define("P_MAX_ENTRY", P_FULL_CONTROL | P_READ | P_WRITE | P_CREATE | P_DELETE | P_MODIFY_ATTR | P_CHANGE_ACCESS | P_LIST_CONTENTS | P_INHERITS_PARENT);
define("P_ROOT_LEVEL", 1000 + ( P_FULL_CONTROL | P_READ | P_WRITE | P_CREATE | P_DELETE | P_MODIFY_ATTR | P_CHANGE_ACCESS | P_LIST_CONTENTS));

define("P_ACCESS_MIN", (P_ACCESS_DELETE & P_ACCESS_UPDATE & P_ACCESS_ADD));
define("P_ACCESS_MAX", (P_ACCESS_ADD | P_ACCESS_UPDATE | P_ACCESS_DELETE));

?>