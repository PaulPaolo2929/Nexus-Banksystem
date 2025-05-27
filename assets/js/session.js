// Session management
document.addEventListener('DOMContentLoaded', function() {
    // Set session timeout to 15 minutes (900000 ms)
    const inactivityTime = 900000;
    let inactivityTimer;
    let warningTimer;
    let refreshTimer;
    const warningTime = 60000; // Show warning 1 minute before timeout
    const refreshInterval = 300000; // Refresh session every 5 minutes instead of every minute

    const refreshSession = async () => {
        try {
            const response = await fetch('../user/refresh_session.php');
            const data = await response.json();
            
            if (!data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
                return;
            }

            // Only show warning if we're close to timeout
            if (data.remainingTime <= 60) { // If less than 1 minute remaining
                showWarning();
            }
        } catch (error) {
            console.error('Error refreshing session:', error);
        }
    };

    const resetInactivityTimer = () => {
        // Clear existing timers
        if (inactivityTimer) clearTimeout(inactivityTimer);
        if (warningTimer) clearTimeout(warningTimer);
        if (refreshTimer) clearTimeout(refreshTimer);

        // Set warning timer
        warningTimer = setTimeout(() => {
            showWarning();
        }, inactivityTime - warningTime);

        // Set timeout
        inactivityTimer = setTimeout(() => {
            window.location.href = '../logout.php?timeout=1';
        }, inactivityTime);

        // Set refresh timer
        refreshTimer = setTimeout(() => {
            refreshSession();
            resetInactivityTimer(); // Reset all timers after refresh
        }, refreshInterval);
    };

    const showWarning = () => {
        // Create warning element if it doesn't exist
        let warning = document.getElementById('session-warning');
        if (!warning) {
            warning = document.createElement('div');
            warning.id = 'session-warning';
            warning.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background-color: #ff4444;
                color: white;
                padding: 15px;
                border-radius: 5px;
                z-index: 9999;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                cursor: pointer;
            `;
            warning.onclick = () => {
                warning.remove();
                resetInactivityTimer();
            };
            document.body.appendChild(warning);
        }
        warning.textContent = 'Your session will expire in 1 minute. Click anywhere to stay logged in.';
    };

    // Reset timer on user activity
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'submit'];
    events.forEach(event => {
        document.addEventListener(event, resetInactivityTimer);
    });

    // Add form submit handler
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            resetInactivityTimer();
        });
    }

    // Initial timer start and session refresh
    resetInactivityTimer();
    refreshSession();
}); 