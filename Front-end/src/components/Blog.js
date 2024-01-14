
import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';



function Blog() {
  const { blog_id } = useParams();
  const [blog, setBlog] = useState({});
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchData = async () => {
      try {

        // Fetch comments
        const commentsResponse = await fetch(`http://localhost:8080/api/comments/${blog_id}`);
        const commentsData = await commentsResponse.json();
        setComments(Array.isArray(commentsData) ? commentsData : []);


        // Fetch blog
        const blogResponse = await fetch(`http://localhost:8080/api/blogs/${blog_id}`);
        const blogData = await blogResponse.json();
        console.log(blogData);
        setBlog(blogData);
      } 
      catch (error) {
        setError('Error fetching data. Please try again.');
      } 
      finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);



  const handleCommentSubmit = async (e) => {
    e.preventDefault();

    if (newComment.trim() === '') {
      // Add proper validation here
      alert('Please enter a valid comment.');
      return;
    }

    // Send the new comment to the server
    try {

      const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }

      
    // Fetch the token from local storage
    // const user_id = localStorage.getItem('token');

      const data = {
        parent_comment_id: null, // This is a top-level comment
        user_id: "1",
        blog_id: "1",
        comment_content: newComment
      }
      console.log(data);

      const response = await fetch(`http://localhost:8080/api/comments/add`, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        throw new Error('Failed to add comment');
      }

      const newCommentData = await response.json();
      console.log(newCommentData);

      // Update the state with the new comment
      setComments([...comments, newCommentData]);
      setNewComment(''); // Clear the comment input field
    } catch (error) {
      console.error('Error adding comment:', error);
      // Handle error
    }
  };


  return (
    <div className="container mt-5">
      <h1>Blog Details</h1>

      {loading && <p>Loading...</p>}
      {error && <p className="text-danger">{error}</p>}

      {blog.length === 0 && <p>No Blog yet</p>}

      <div key={blog.blog_id} className="card mb-2">
        <div className='card-body text-center'>
            <h1 className='card-title'>{blog.blog_name}</h1>
            <p className='card-text'>{blog.blog_content}</p>
        </div>
        </div>

      {/* Form to add a new comment */}
      <form onSubmit={handleCommentSubmit} className="mt-4">
        <div className="form-group">
          <label htmlFor="newComment">Add a Comment:</label>
          <textarea
            id="newComment"
            className="form-control"
            rows="2"
            value={newComment}
            onChange={(e) => setNewComment(e.target.value)}
          ></textarea>
        </div>
        <button type="submit" className="btn btn-success">
          Add Comment
        </button>
      </form>

      <h3>Comments</h3>

      {comments.length === 0 && <p>No comments yet.</p>}

      {comments.map((comment) => (
        <div key={comment.comment_id} className="card mb-2">
          <div className="card-body">
            <h3 className="card-text">{comment.user_name}</h3>
            <p className="card-text">{comment.comment_content}</p>
            <div>
              <span role="img" aria-label="Upvote">
                &#128077; {comment.upvotes}
              </span>
              <span role="img" aria-label="Downvote">
                &#128078; {comment.downvotes}
              </span>
              <span role="img" aria-label="Reply">
                &#128172; Reply
              </span>
            </div>
          </div>
        </div>
      ))}

    </div>
  );
}

export default Blog;




