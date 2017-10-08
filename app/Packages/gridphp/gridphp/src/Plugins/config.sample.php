<?php

// PHP Grid database connection settings, Only need to update these in new project

define("PHPGRID_DBTYPE","{{dbtype}}"); // mysql,oci8(for oracle),mssql,postgres,sybase
define("PHPGRID_DBHOST","{{dbhost}}");
define("PHPGRID_DBUSER","{{dbuser}}");
define("PHPGRID_DBPASS","{{dbpass}}");
define("PHPGRID_DBNAME","{{dbname}}");

// Basepath for lib
define("PHPGRID_LIBPATH",dirname(__FILE__).DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR);