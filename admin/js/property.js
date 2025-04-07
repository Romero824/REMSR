function loadChat(inquiryId) {
    document.querySelectorAll('.inquiry-item').forEach(item => {
        item.classList.remove('active');
    });
    event.currentTarget.classList.add('active');

    document.getElementById('replyForm').classList.remove('d-none');
    document.getElementById('inquiry_id').value = inquiryId;

    fetch(`get_messages.php?inquiry_id=${inquiryId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const chatArea = document.getElementById('chatArea');
                chatArea.innerHTML = data.messages.map(msg => `
                    <div class="message ${msg.is_buyer_reply ? 'buyer-message' : 'admin-message'}">
                        <div class="message-content">${msg.message}</div>
                        <small class="text-muted">
                            ${msg.sender_name} - ${new Date(msg.created_at).toLocaleString()}
                        </small>
                    </div>
                `).join('');
                chatArea.scrollTop = chatArea.scrollHeight;
            }
        });
}

// Auto-refresh chat
setInterval(() => {
    const inquiryId = document.getElementById('inquiry_id').value;
    if (inquiryId) {
        loadChat(inquiryId);
    }
}, 10000);

// Handle form submission
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
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
    });
});