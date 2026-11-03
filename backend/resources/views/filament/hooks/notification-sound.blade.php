<script>
    document.addEventListener('livewire:initialized', () => {
        let lastCount = -1;
        
        const playNotificationSound = () => {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                
                const context = new AudioContext();
                
                // Play two notes for a more noticeable "Ding-Dong" effect
                const playNote = (freq, startTime, duration, volume) => {
                    const oscillator = context.createOscillator();
                    const gain = context.createGain();
                    
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(freq, context.currentTime + startTime);
                    
                    gain.gain.setValueAtTime(0, context.currentTime + startTime);
                    gain.gain.linearRampToValueAtTime(volume, context.currentTime + startTime + 0.05);
                    gain.gain.exponentialRampToValueAtTime(0.01, context.currentTime + startTime + duration);
                    
                    oscillator.connect(gain);
                    gain.connect(context.destination);
                    
                    oscillator.start(context.currentTime + startTime);
                    oscillator.stop(context.currentTime + startTime + duration);
                };

                // Play a loud double-tone notification
                playNote(880, 0, 0.5, 0.5);   // Note 1 (Loud)
                playNote(1100, 0.1, 0.6, 0.4); // Note 2 (Loud)
                
                console.log('Loud Notification sound played!');
            } catch (e) {
                console.warn('AudioContext failed:', e);
            }
        };

        const checkBadge = () => {
            const badge = document.querySelector('.fi-topbar-database-notifications-btn .fi-badge, .fi-topbar-item-btn[aria-label*="Notification"] .fi-badge, .fi-topbar-item-badge');
            
            if (badge) {
                const currentCount = parseInt(badge.textContent.trim() || '0');
                if (lastCount !== -1 && currentCount > lastCount) {
                    playNotificationSound();
                }
                lastCount = currentCount;
            } else if (lastCount === -1) {
                lastCount = 0;
            } else {
                lastCount = 0;
            }
        };

        const observer = new MutationObserver(() => {
            checkBadge();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });

        // Brute-force: Close notifications sidebar on click
        document.addEventListener('click', (e) => {
            const isViewLink = e.target.tagName === 'A' && (e.target.textContent.trim() === 'View' || e.target.textContent.trim() === 'عرض');
            
            if (isViewLink) {
                // Find the notification sidebar/modal container
                const container = e.target.closest('.fi-modal-window, .fi-section, [role="dialog"]');
                if (container) {
                    container.style.display = 'none'; // Hide it instantly
                    // Also try to click the real close button to let Livewire know it's closed
                    const closeBtn = container.querySelector('button[aria-label="Close"], button[aria-label="إغلاق"], .fi-modal-close-btn');
                    if (closeBtn) closeBtn.click();
                }
                
                // Also dispatch the event just in case
                window.dispatchEvent(new CustomEvent('close-database-notifications'));
            }
        });

        setInterval(checkBadge, 2000);
    });
</script>
