<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// mysql routes...
$route['api/mysqlquery']['GET'] = 'MysqlAPI/execQuery';
$route['api/mysqlquery']['POST'] = 'MysqlAPI/execQuery';
$route['api/mysqldump']['POST'] = 'MysqlAPI/dumpToSQL';
$route['api/runQuery']['POST'] = 'MysqlAPI/runQuery';

// graph routes
$route['api/addGraph']['POST'] = 'GraphController/addGraph';
$route['api/saveGraph']['POST'] = 'GraphController/saveGraph';
$route['api/deleteGraph']['POST'] = 'GraphController/deleteGraph';
$route['api/deleteAllGraphs']['POST'] = 'GraphController/deleteAllGraphs';
$route['api/getGraph']['POST'] = 'GraphController/getGraph';
$route['api/getAllGraphs']['POST'] = 'GraphController/getAllGraphs';
$route['api/getAllGraphs']['GET'] = 'GraphController/getAllGraphs';

// application routes
$route['api/getAllApplications']['GET'] = 'ApplicationsController/getAllApplications';
$route['api/getApplication']['POST'] = 'ApplicationsController/getApplication';
$route['api/saveApplication']['POST'] = 'ApplicationsController/saveApplication';
$route['api/deleteApplication']['POST'] = 'ApplicationsController/deleteApplication';
$route['api/deleteAllApplications']['POST'] = 'ApplicationsController/deleteAllApplications';
$route['api/addApplication']['POST'] = 'ApplicationsController/addApplication';

// forms
$route['api/getAllForms']['GET'] = 'FormsController/getAllForms';
$route['api/getForm']['POST'] = 'FormsController/getForm';
$route['api/saveForm']['POST'] = 'FormsController/saveForm';
$route['api/deleteForm']['POST'] = 'FormsController/deleteForm';
$route['api/deleteAllForms']['POST'] = 'FormsController/deleteAllForms';
$route['api/addForm']['POST'] = 'FormsController/addForm';
$route['api/submitForm']['POST'] = 'FormsController/submitForm';

// query routes
$route['api/getAllQueries']['GET'] = 'QueriesController/getAllQueries';
$route['api/getQuery']['POST'] = 'QueriesController/getQuery';
$route['api/saveQuery']['POST'] = 'QueriesController/saveQuery';
$route['api/deleteQuery']['POST'] = 'QueriesController/deleteQuery';
$route['api/deleteAllQueries']['POST'] = 'QueriesController/deleteAllQueries';
$route['api/addQuery']['POST'] = 'QueriesController/addQuery';

//cistpm routes...
$route['signup']['GET'] = 'Signup/index';
$route['signup']['POST'] = 'Signup/create';
//
$route['login']['GET'] = 'Login/index';
$route['login']['POST'] = 'Login/find';

$route['getuserprofile']['POST'] = 'GetUserProfile/find';
$route['getalluser']['POST'] = 'Getalluser/find';