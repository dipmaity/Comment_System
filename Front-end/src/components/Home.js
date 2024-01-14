import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Container, Card, Button } from 'react-bootstrap';

const Blog = () => {
  const [blogs, setBlogs] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await fetch('http://localhost:8080/api/blogs');
        if (!response.ok) {
          throw new Error('Failed to fetch blogs');
        }
        let data = await response.json();
        console.log(data);
        setBlogs(data);
      } catch (error) {
        setError(error.message);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []); // Empty dependency array means this effect runs once after the initial render

  const handleBlogClick = (blog) => {
    // Handle the click event as needed
    console.log(`Clicked on blog with ID ${blog.blog_id}`);
  };

  return (
    // <h1>hey</h1>
    <Container>
      <h1 className="mt-3 mb-4">Home Page</h1>

      {loading && <p>Loading...</p>}
      {error && <p className="text-danger">{error}</p>}

      <div>
        {/* Display all blogs as cards */}
        {blogs.map((blog) => (
          <Card key={blog.blog_id} className="mb-4">
            <Card.Body onClick={() => handleBlogClick(blog)}>
              <Card.Title>{blog.blog_name}</Card.Title>
              <Card.Text>{blog.blog_content.substring(0, 150)}...</Card.Text>
              <Link to={`/blog/${blog.blog_id}`}>
                <Button variant="primary">Read More</Button>
              </Link>
            </Card.Body>
          </Card>
        ))}
      </div>
    </Container>
  );
};

export default Blog;
