<?php

use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

//get all users
$app->get('/api/users', function (Request $request, Response $response) {
    
    $query = "SELECT * FROM users";

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

//get a single user
$app->get('/api/users/{id}', function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');

    $query = "SELECT * FROM users where user_id = ?";

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
    } catch (Exception $error) {
        $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
        return $response->withHeader('Content-Type', 'application/json');
    }
});

// made moderator
$app->post('/api/users/moderator', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    if (isset($data['user_id'])) {

        $id = $data['user_id'];

        try {
            $db = new db();
            $mysqli = $db->connect();

            $query = "UPDATE users SET global_moderator = 1 where user_id = ?";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                $stmt->bind_param("s", $id);
                $stmt->execute();

                $response->getBody()->write(json_encode(['success' => 'User became moderator Successfully']));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
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


// Add Users
$app->post('/api/users/add', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    if (isset($data['email'], $data['user_name'], $data['user_password'])) {
        $email = $data['email'];
        $name = $data['user_name'];
        $password = $data['user_password'];

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $db = new db();
            $mysqli = $db->connect();

            $query = "INSERT INTO users (email, user_name, user_password) VALUES (?,?,?)";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                $stmt->bind_param("sss", $email, $name, $hashedPassword);
                $stmt->execute();

                $response->getBody()->write(json_encode(['success' => 'User Registered Successfully']));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Prepare statement failed"}}']));
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $error) {
            $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
            return $response->withHeader('Content-Type', 'application/json');
        } finally {
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


// LogIn User
$app->post('/api/users/login', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    if (isset($data['email'], $data['user_password'])) {
        $email = $data['email'];
        $password = $data['user_password'];

        try {
            $db = new db();
            $mysqli = $db->connect();

            $query = "SELECT * FROM users WHERE email = ?";
            $stmt = $mysqli->prepare($query);

            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                $userData = $result->fetch_assoc();

                if ($userData && password_verify($password, $userData['user_password'])) {
                    $response->getBody()->write(json_encode(['success' => 'User Logged In Successfully', 'user_data' => $userData]));
                    return $response->withHeader('Content-Type', 'application/json');
                } 
                else {
                    $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Invalid email or password"}}']));
                    return $response->withHeader('Content-Type', 'application/json');
                }
            } else {
                $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "Prepare statement failed"}}']));
                return $response->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $error) {
            $response->getBody()->write(json_encode(['error' => '{"msg": {"resp": "' . $error->getMessage() . '"}}']));
            return $response->withHeader('Content-Type', 'application/json');
        } finally {
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




// Delete user
$app->delete('/api/users/delete/{id}', function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');

    try {
        $db = new db();
        $mysqli = $db->connect();

        $query = "DELETE FROM users WHERE user_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();

        $responseBody = [
            'notice' => [
                'text' => 'User with ' . $id . ' has been deleted'
            ]
        ];

        $response->getBody()->write(json_encode($responseBody));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $errorResponse = [
            'error' => [
                'msg' => [
                    'resp' => $e->getMessage()
                ]
            ]
        ];

        $response->getBody()->write(json_encode($errorResponse));
        return $response->withHeader('Content-Type', 'application/json');
    }
});

$app->run();

