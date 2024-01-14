<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;



//get a single user
$app->get('/api/comments/{blog_id}', function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('blog_id');

    $query = "SELECT * FROM comments where blog_id = ?";

    try {
        $db = new db();
        $mysqli = $db->connect();
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $data = array();  // Initialize an array to store all rows
    
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;  // Add each row to the array
        }
    
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
    catch (Exception $error) {
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});

//get all comments
$app->get('/api/comments', function (Request $request, Response $response) {
    
    $query = "SELECT * FROM comments";

    try {
        $db = new db();
        $mysqli = $db->connect();

        $result = $mysqli->query($query);

        // Check if there are rows
        if ($result->num_rows > 0) {
            // Convert the result to an associative array
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            // Return the data as JSON
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        else {
            $response->getBody()->write(json_encode(['error' => 'No data found']));
            return $response->withHeader('Content-Type', 'application/json');
        }
    } catch (Exception $error) {
        $response->getBody()->write('{"msg": {"resp": "' . $error->getMessage() . '"}}');
        return $response->withHeader('Content-Type', 'application/json');
    }
});


// add comment
$app->post('/api/comments/add', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $response->getBody()->write(json_encode(['success' => 'Comment added Successfully', 'data'=>$data]));
    return $response->withHeader('Content-Type', 'application/json');

    if (isset($data['blog_id'], $data['user_id'])) {

        $blog_id = $data['blog_id'];
        $user_id = $data['user_id'];
        $parent_comment_id = $data['parent_comment_id'];
        $comment_content = $data['comment_content'];

        try {
            $db = new db();
            $mysqli = $db->connect();

            $query = "INSERT INTO comments ( user_id, blog_id, comment_content) VALUES (?,?,?)";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                $stmt->bind_param("sss",  $user_id, $blog_id, $comment_content);
                $stmt->execute();

                $response->getBody()->write(json_encode(['success' => 'Comment added Successfully']));
                return $response->withHeader('Content-Type', 'application/json');
            } 
            else {
                $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Prepare statement failed"}}']));
                return $response->withHeader('Content-Type', 'application/json');
            }
        } 
        catch (Exception $error) {
            $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
            return $response->withHeader('Content-Type', 'application/json');
        } 
        finally {
            if (isset($stmt)) {
                $stmt->close();
            }
            $mysqli->close();
        }
    } else {
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Missing required fields in the request"}}', 'blog_id'=>$data['blog_id'], 'user_id'=>$data['user_id']]));
        return $response->withHeader('Content-Type', 'application/json');
    }
});



// delete comment by user itself or by global moderator
$app->post('/api/comments/delete', function(Request $request, Response $response){

    $data = $request->getParsedBody();
    

    $user_id = $data['user_id'];
    $user_email = $data['user_email'];
    $user_password = $data['user_password'];
    $blog_id = $data['blog_id'];
    $comment_id = $data['comment_id'];

    try{
        $db = new db();
        $mysqli = $db->connect();

        $query1 = "SELECT * FROM users WHERE user_id = ?";
        $query2 = "SELECT * FROM users WHERE email = ?";

        $stmt1 = $mysqli->prepare($query1);
        $stmt1->bind_param("s", $user_id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $data1 = $result1->fetch_assoc();

        $stmt2 = $mysqli->prepare($query2);
        $stmt2->bind_param("s", $user_email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $data2 = $result2->fetch_assoc(); 

        if(($data1 && $data1['global_moderator'] === "1") || ($data2 && $user_password === $data2['user_password'])){

            $query3 = "UPDATE comments SET comment_content = 'Comment is deleted' WHERE comment_id = ? and blog_id = ?";
            $stmt3 = $mysqli->prepare($query3);
            $stmt3->bind_param("ss", $comment_id, $blog_id);
            $stmt3->execute();
            $response->getBody()->write(json_encode(['success' => 'Comment deleted Successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        else{
            $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Wrong Input Fields"}}']));
                return $response->withHeader('Content-Type', 'application/json');
        }
    }
    catch(Exception $error){
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});


// hide comment by user itself or by global moderator
$app->post('/api/comments/hide', function(Request $request, Response $response){

    $data = $request->getParsedBody();
    
    $user_id = $data['user_id'];
    $user_email = $data['user_email'];
    $user_password = $data['user_password'];
    $blog_id = $data['blog_id'];
    $comment_id = $data['comment_id'];

    try{
        $db = new db();
        $mysqli = $db->connect();

        $query1 = "SELECT * FROM users WHERE user_id = ?";
        $query2 = "SELECT * FROM users WHERE email = ?";

        $stmt1 = $mysqli->prepare($query1);
        $stmt1->bind_param("s", $user_id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $data1 = $result1->fetch_assoc();

        $stmt2 = $mysqli->prepare($query2);
        $stmt2->bind_param("s", $user_email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $data2 = $result2->fetch_assoc(); 

        if(($data1 && $data1['global_moderator'] === "1") || ($data2 && $user_password === $data2['user_password'])){

            $query3 = "UPDATE comments SET comment_content = 'Comment is hidden' WHERE comment_id = ? and blog_id = ?";
            $stmt3 = $mysqli->prepare($query3);
            $stmt3->bind_param("ss", $comment_id, $blog_id);
            $stmt3->execute();
            $response->getBody()->write(json_encode(['success' => 'Comment hided Successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        else{
            $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Wrong Input Fields"}}']));
                return $response->withHeader('Content-Type', 'application/json');
        }
    }
    catch(Exception $error){
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }

});



// edit comment by user itself or by global moderator
$app->post('/api/comments/edit', function(Request $request, Response $response){

    $data = $request->getParsedBody();

    $user_id = $data['user_id'];
    $user_email = $data['user_email'];
    $user_password = $data['user_password'];
    $blog_id = $data['blog_id'];
    $new_content = $data['new_content'];
    $comment_id = $data['comment_id'];

    try{
        $db = new db();
        $mysqli = $db->connect();

        $query1 = "SELECT * FROM users WHERE user_id = ?";
        $query2 = "SELECT * FROM users WHERE email = ?";

        $stmt1 = $mysqli->prepare($query1);
        $stmt1->bind_param("s", $user_id);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $data1 = $result1->fetch_assoc();

        $stmt2 = $mysqli->prepare($query2);
        $stmt2->bind_param("s", $user_email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $data2 = $result2->fetch_assoc(); 

        if(($data1 && $data1['global_moderator'] === "1") || ($data2 && $user_password === $data2['user_password'])){

            $query3 = "UPDATE comments SET comment_content = ? WHERE comment_id = ? and blog_id = ?";
            $stmt3 = $mysqli->prepare($query3);
            $stmt3->bind_param("sss", $new_content, $comment_id, $blog_id);
            if ($stmt3->execute()) {
                // Query executed successfully
                $response->getBody()->write(json_encode(['success' => 'Comment edited Successfully', 'new_content' => $new_content]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                // Query execution failed
                echo json_encode(['error' => '{"msg": {"resp": "Update query failed"}}']);
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            $response->getBody()->write(json_encode(['success' => 'Comment edited Successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        else{
            $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Wrong Input Fields"}}']));
                return $response->withHeader('Content-Type', 'application/json');
        }
    }
    catch(Exception $error){
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }

});

// upvote comment by user itself or by global moderator
$app->post('/api/comments/upvote', function(Request $request, Response $response){

    $data = $request->getParsedBody();

    $user_email = $data['user_email'];
    $user_password = $data['user_password'];
    $blog_id = $data['blog_id'];
    $upvote_count = $data['upvote'];
    $comment_id = $data['comment_id'];

    try{
        $db = new db();
        $mysqli = $db->connect();

        $query2 = "SELECT * FROM users WHERE email = ?";

        $stmt2 = $mysqli->prepare($query2);
        $stmt2->bind_param("s", $user_email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $data2 = $result2->fetch_assoc(); 

        if($data2 && $user_password === $data2['user_password']){

            $query3 = "UPDATE comments SET upvote = ? WHERE comment_id = ? and blog_id = ?";
           
            $stmt3 = $mysqli->prepare($query3);
            $stmt3->bind_param("sss", $upvote_count, $comment_id, $blog_id);

            if ($stmt3->execute()) {
                $response->getBody()->write(json_encode(['success' => 'Comment upvoted Successfully']));
                return $response->withHeader('Content-Type', 'application/json');
            } 
            else {
                echo json_encode(['error' => '{"msg": {"resp": "Update query failed"}}']);
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            $response->getBody()->write(json_encode(['success' => 'Comment upvoted Successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        else{
            $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "You are not loggedIn"}}', 'cred'=>$data2]));
                return $response->withHeader('Content-Type', 'application/json');
        }
    }
    catch(Exception $error){
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});
