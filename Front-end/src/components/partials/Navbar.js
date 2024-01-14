import React from 'react';
import {  Link } from 'react-router-dom';
import {  Navbar, Nav, Container, Button } from 'react-bootstrap';

function CustomNavbar() {

  const handleLogout = () => {
    // Remove the 'token' from localStorage
    localStorage.removeItem('token');

    // Redirect or perform any other action after logout
    window.location.href = '/home';
  };

  return (
    <Navbar bg="light" expand="lg" style={{ padding: '2px' }}>
      <Container>

        <Navbar.Collapse id="basic-navbar-nav" className="justify-content-center">
          <Nav>
            <Nav.Link as={Link} to="/" className='justify-content-start'>
              Home
            </Nav.Link> 

            <Button variant="outline-dark" className="ml-2" onClick={handleLogout}>
              <span>Logout</span>
            </Button>

          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
}

export default CustomNavbar;