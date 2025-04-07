function loadChat(inquiryId) {
    // Remove active class from all items
    document.querySelectorAll('.inquiry-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class to clicked item
    const currentItem = document.querySelector(`[data-inquiry-id="${inquiryId}"]`);
    if (currentItem) {
        currentItem.classList.add('active');
    }

    // Show reply form and set inquiry ID
    const replyForm = document.getElementById('replyForm');
    replyForm.classList.remove('d-none');
    document.getElementById('inquiry_id').value = inquiryId;

    // Load messages
    fetch(`get_messages.php?inquiry_id=${inquiryId}`)
        .then(response => response.json())
        .then(data => {
            const chatArea = document.getElementById('chatArea');
            if (data.success) {
                let html = `
                    <div class="chat-header mb-3">
                        <h5>${data.property_title}</h5>
                        <small class="text-muted">Conversation with ${data.buyer_name}</small>
                    </div>
                `;
                
                html += data.messages.map(msg => `
                    <div class="message ${msg.is_buyer_reply ? 'buyer-message' : 'admin-message'}">
                        <div class="message-content">${msg.message}</div>
                        <small class="text-muted">
                            ${msg.sender_name} - ${new Date(msg.created_at).toLocaleString()}
                        </small>
                    </div>
                `).join('');
                
                chatArea.innerHTML = html;
                chatArea.scrollTop = chatArea.scrollHeight;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Handle form submission
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    fetch('send_reply.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.reset();
            loadChat(formData.get('inquiry_id'));
        } else {
            alert(data.message || 'Failed to send message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send message');
    })
    .finally(() => {
        submitButton.disabled = false;
    });
});

// Auto-refresh chat every 10 seconds
setInterval(() => {
    const inquiryId = document.getElementById('inquiry_id').value;
    if (inquiryId) {
        loadChat(inquiryId);
    }
}, 10000);