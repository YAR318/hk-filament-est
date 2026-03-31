<div wire:poll.10s="checkNotifications" style="display: none;">
    <!-- Listen for play-audio event to synthesize beep -->
    @script
    <script>
        $wire.on('play-audio', () => {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                
                // Beep 1
                let osc1 = ctx.createOscillator();
                let gain1 = ctx.createGain();
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(800, ctx.currentTime);
                gain1.gain.setValueAtTime(0.5, ctx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                
                osc1.start(ctx.currentTime);
                osc1.stop(ctx.currentTime + 0.1);
                
                // Beep 2
                let osc2 = ctx.createOscillator();
                let gain2 = ctx.createGain();
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(1200, ctx.currentTime + 0.15);
                gain2.gain.setValueAtTime(0.5, ctx.currentTime + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                
                osc2.start(ctx.currentTime + 0.15);
                osc2.stop(ctx.currentTime + 0.4);
            } catch(e) {
                console.log('Web Audio API not supported or blocked', e);
            }
        });
    </script>
    @endscript
</div>
