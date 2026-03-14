
function toggleReplyForm(postId) {
            var form = document.getElementById('reply-form-' + postId);
            
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'flex';
            } else {
                
                form.style.display = 'none';
            }
        }