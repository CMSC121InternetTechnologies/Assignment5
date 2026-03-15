/*
 * FREEDOM BOARD SCRIPTS
 * Handles client-side interactivity
 */

function toggleReplyForm(postId) {
    // Select specific form using dynamic ID generated in PHP
    var form = document.getElementById('reply-form-' + postId);
    
    // Toggle display property between 'flex' and 'none'
    if (form.style.display === 'none' || form.style.display === '') form.style.display = 'flex';
    else form.style.display = 'none';
}