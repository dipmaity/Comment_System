import React, { useState } from 'react';

const Login = () => {
  // State variables to store email, password, and response from the server
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [response, setResponse] = useState(null);

  // Function to handle form submission
  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      // Make a POST request to the server
      const url = 'http://localhost:8080/api/users/login';
      const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
      const data = {
        email: email,
        password: password
      }
      const response = await fetch(url, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(data),
      });

      // Parse the response as JSON
      const responseData = await response.json();

      // Update the state with the response
      setResponse(responseData);
    } catch (error) {
      console.error('Error:', error);
    }
  };

  return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
      <div style={{ border: '2px solid #ccc', padding: '20px', borderRadius: '10px', backgroundColor: '#f5f5f5' }}>
        <h2>Login</h2>
        <form onSubmit={handleSubmit}>
          <label>
            Email:
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </label>
          <br />
          <label>
            Password:
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </label>
          <br />
          <button type="submit" style={{ backgroundColor: 'green', color: 'white', padding: '10px', borderRadius: '5px', cursor: 'pointer' }}>Login</button>
        </form>

        {/* Display the response from the server */}
        {response && (
          <div>
            <h3>Response from Server:</h3>
            <pre>{JSON.stringify(response, null, 2)}</pre>
          </div>
        )}
      </div>
    </div>
  );
};

export default Login;
