// Import necessary modules from React and React Router
import React from 'react'
import  { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Home from './components/Home.js'
import Login from './components/Login.js'
import Blog from './components/Blog.js'
import Register from './components/Register.js'
import Navbar from './components/partials/Navbar.js'
import NotFound404 from './components/NotFound404.js'


function App() {
  return (
    <Router>
      <Navbar />
      <Routes>
        <Route exact path="/" element={ <Home />} />
        <Route path="/login" element={ <Login/>} />
        <Route path="/signup" element={ <Register/>} />
        <Route path="/blog/:blog_id" element={<Blog/>}/>
        <Route path="*" element={<NotFound404 />} />
      </Routes>
    </Router>
  );
}


export default App;




