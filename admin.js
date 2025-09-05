// MyPaper - Admin Interaction Script v1.0

document.addEventListener('DOMContentLoaded', function() {
    // --- Decline Wallpaper Confirmation ---
    // Find all the "Decline" buttons
    const declineButtons = document.querySelectorAll('a.button-danger');

    declineButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Ask for confirmation before proceeding
            const confirmed = confirm('Are you sure you want to decline and delete this wallpaper? This action cannot be undone.');
            
            // If the admin clicks "Cancel", prevent the link from being followed
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    // --- User Badge Change Confirmation ---
    // Find all the badge management forms
    const badgeForms = document.querySelectorAll('.badge-form');

    badgeForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const selectedBadge = form.querySelector('select[name="new_badge"]').value;
            const username = form.closest('tr').querySelector('td:nth-child(2)').textContent;

            // Ask for confirmation
            const confirmed = confirm(`Are you sure you want to change ${username}'s badge to "${selectedBadge}"?`);

            // If the admin clicks "Cancel", prevent the form from submitting
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});
