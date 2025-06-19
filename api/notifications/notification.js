setInterval(function() {
    fetch('get_notifications.php')
        .then(res => res.json())
        .then(data => {
            let container = document.getElementById("notification-container");
            container.innerHTML = "";
            data.forEach(note => {
                container.innerHTML += `<div class="notification">${note.message}</div>`;
            });
        });
}, 5000); // check every 5 seconds

function markAsRead(id) {
    fetch('mark_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    });
}
