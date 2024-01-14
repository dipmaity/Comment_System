<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

// get all blogs
$app->get('/api/blogs', function (Request $request, Response $response) {
    
    $query = "SELECT * FROM blogs";

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
    } 
    catch (Exception $error) {
        $response->getBody()->write('{"msg": {"resp": "' . $error->getMessage() . '"}}');
        return $response->withHeader('Content-Type', 'application/json');
    }
});




// add blog
$app->post('/api/blogs/add', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    if (isset($data['blog_name'], $data['blog_content'])) {
        $name = $data['blog_name'];
        $blog_content = $data['blog_content'];

        try {
            $db = new db();
            $mysqli = $db->connect();

            $query = "INSERT INTO blogs (blog_name, blog_content) VALUES (?,?)";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                $stmt->bind_param("ss", $name, $blog_content);
                $stmt->execute();

                $response->getBody()->write(json_encode(['success' => 'User Registered Successfully']));
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
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Missing required fields in the request"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});


// Get a single blog
$app->get('/api/blogs/{id}', function (Request $request, Response $response, array $args) {
    
    $id = $request->getAttribute('id');

    $query = "SELECT * FROM blogs where blog_id = ?";

    try {
        $db = new db();
        $mysqli = $db->connect();
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = $result->fetch_assoc();

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    } 
    catch (Exception $error) {
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});

