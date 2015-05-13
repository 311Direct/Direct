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


define("P_MAX_ENTRY", P_FULL_CONTROL | P_READ | P_WRITE | P_CREATE | P_DELETE | P_MODIFY_ATTR | P_CHANGE_ACCESS | P_LIST_CONTENTS | P_INHERITS_PARENT);
define("P_ROOT_LEVEL", 1000 + ( P_FULL_CONTROL | P_READ | P_WRITE | P_CREATE | P_DELETE | P_MODIFY_ATTR | P_CHANGE_ACCESS | P_LIST_CONTENTS));

// Unpacking will involve first int: project ID, second char: User or Role, third int: primary DB id, fourth value: int of above consts.

const P_DB_FORMAT_V1 = "IA1IIx";
const P_DB_UFORMT_V1 = "IP/AT/IW/IV/x"
?>