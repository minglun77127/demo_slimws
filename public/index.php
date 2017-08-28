<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'classes/service/Database.php';
require 'classes/util/ResultSetUtil.php';
require 'classes/service/ResponseMessage.php';

$app = new \Slim\App;

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

/*----------Program Service----------*/
$app->get('/service/program/get', function(Request $request, Response $response) {
    $result = Database::getInstance()
        ->query("SELECT id, name FROM `tblprogram` WHERE hidden=0 ORDER BY priority");

    return $response->withJson(ResultSetUtil::resultSet_toArray($result));
});


/*----------Course Service----------*/
$app->get('/service/course/get/pID/{pID}', function(Request $request, Response $response) {
    $pID = $request->getAttribute('pID');
    $result = Database::getInstance()
        ->query("SELECT c.* FROM `tblprogram` p
                         INNER JOIN `tblprogcourse` pc ON p.id = pc.progID
                         LEFT JOIN `tblcourse` c ON pc.courseID = c.id
                      WHERE p.hidden = 0 AND c.hidden = 0 AND p.id = \"$pID\"");

    return $response->withJson(ResultSetUtil::resultSet_toArray($result));
});


/*----------Course Schedule Service----------*/
$app->get('/service/courseschedule/get/cID/{cID}', function(Request $request, Response $response) {
    $cID = $request->getAttribute('cID');
    $result = Database::getInstance()
        ->query("SELECT * FROM `tblcourseschedule` WHERE courseID=\"$cID\"");

    return $response->withJson(ResultSetUtil::resultSet_toArray($result));
});
$app->get('/service/courseschedule/get/classID/{classID}', function(Request $request, Response $response) {
    $classID = $request->getAttribute('classID');
    $result = Database::getInstance()
        ->query("SELECT * FROM `tblcourseschedule` WHERE classID=\"$classID\"");

    return $response->withJson(ResultSetUtil::resultSet_toArray($result));
});


/*----------Student Service----------*/
$app->get('/service/student/get', function(Request $request, Response $response) {
    $result = Database::getInstance()
        ->query("SELECT id, firstName, lastName, dateOfBirth, gender, attdType, oen, secondName, perferName, grade 
                        FROM `tblstudent` WHERE role=\"student\" ORDER BY dateAdded DESC, id DESC");

    return $response->withJson(ResultSetUtil::resultSet_toArray($result));
});
$app->get('/service/student/get/scheduleTo/classID/{classID}', function(Request $request, Response $response) {
    $classID = $request->getAttribute('classID');
    $result = Database::getInstance()
        ->query("SELECT cs.csID AS key_csID, s.id AS id, s.firstName AS firstName, s.lastName AS lastName
                         FROM `tblcourseschedule` cs 
                         LEFT JOIN `tblstudcourse` sc ON cs.csID = sc.csID
                         LEFT JOIN `tblstudent` s ON s.id = sc.studID
                      WHERE classID = \"$classID\"");

    return $response->withJson(ResultSetUtil::resultSet_toMap($result));
});


/*----------Student Course Service----------*/
$app->post('/service/studentcourse/create', function(Request $request, Response $response) {
    $csID = $request->getParsedBody()['csID'];
    $students = json_decode($request->getParsedBody()['students'], true);
    $db = Database::getInstance();

    for($i=0; $i < count($students); $i++){
        $db->execStmt("INSERT INTO `tblstudcourse` (studID, csID) VALUES (?,?)", "ss", $students[$i]['id'], $csID);
    }

    return $response->withJson((new ResponseMessage())->setStatus('success')->setMessage('success'));
});


$app->post('/service/studentcourse/delete', function(Request $request, Response $response) {
    $csID = $request->getParsedBody()['csID'];
    $studentID = $request->getParsedBody()['studID'];

    return Database::getInstance()
        ->execStmt("DELETE FROM `tblstudcourse` WHERE csID=? AND studID=?", "ss", $csID, $studentID)?
        $response->withJson((new ResponseMessage())->setStatus('success')->setMessage('success')):
        $response->withJson((new ResponseMessage())->setStatus('error')->setMessage('error'));
});

$app->run();

