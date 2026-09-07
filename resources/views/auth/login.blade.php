<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gateway Access | OLT Network Engine</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('olt_index/css/login.css') }}" />

</head>
<body>

    <!-- Ambient Glowing Light -->
    <div class="ambient-light"></div>

    <!-- Live Canvas Network Node Background -->
    <canvas id="network-canvas"></canvas>

    <!-- Main Login Card -->
    <div class="login-wrapper">
        <div class="login-card">

            <!-- System Title Block -->
            <div class="brand-header">
                <div class="terminal-badge">
                    <span class="status-dot"></span>
                    <span>OLT-SYS :: ONLINE</span>
                </div>
                <h1 class="brand-title">Gateway Control</h1>
                <p class="brand-subtitle">Enter credentials to establish secure session</p>
            </div>

            <!-- Session Status Alert -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="form-group @error('email') error-field @enderror">
                    <label class="field-label">Operator Identification</label>
                    <div class="input-control">
                        <input type="email" name="email" placeholder="operator@isp-net.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                        <i class="fa-solid fa-network-wired field-icon"></i>
                    </div>
                    @error('email')
                        <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group @error('password') error-field @enderror">
                    <label class="field-label">Access Token</label>
                    <div class="input-control">
                        <input type="password" id="passwordInput" name="password" placeholder="••••••••••••" required autocomplete="current-password">
                        <i class="fa-solid fa-key field-icon"></i>
                        <button type="button" class="toggle-password" id="togglePasswordBtn">
                            <i class="fa-solid fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button class="btn-submit" type="submit">
                    <span>Login</span>
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </button>
            </form>

            <!-- Diagnostic Telemetry Footer -->
            <div class="system-status">
                <div>PROTO: <span style="color: var(--text-main);">TLS 1.3</span></div>
                <div class="latency-indicator">
                    <i class="fa-solid fa-bolt"></i>
                    <span>PING:</span>
                    <span class="ping-time" id="livePing">12 ms</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Node Network Animation Engine & UI Scripts -->
    <script>
        // Password Visibility Toggle
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('passwordInput');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePasswordBtn.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        });

        // Dynamic Ping Telemetry Simulation
        setInterval(() => {
            const ping = Math.floor(Math.random() * (16 - 8 + 1)) + 8;
            document.getElementById('livePing').textContent = `${ping} ms`;
        }, 3000);

        // Network Nodes Canvas Engine
        const canvas = document.getElementById('network-canvas');
        const ctx = canvas.getContext('2d');

        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            initNodes();
        });

        class Node {
            constructor() {
                this.x = Math.random() * width;
                this.y = Math.random() * height;
                this.vx = (Math.random() - 0.5) * 0.8;
                this.vy = (Math.random() - 0.5) * 0.8;
                this.radius = Math.random() * 2 + 1.5;
            }

            update() {
                this.x += this.vx;
                this.y += this.vy;

                if (this.x < 0 || this.x > width) this.vx *= -1;
                if (this.y < 0 || this.y > height) this.vy *= -1;
            }

            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = '#3b82f6';
                ctx.fill();
            }
        }

        let nodes = [];
        function initNodes() {
            nodes = [];
            const count = Math.floor((width * height) / 18000);
            for (let i = 0; i < count; i++) {
                nodes.push(new Node());
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);

            for (let i = 0; i < nodes.length; i++) {
                nodes[i].update();
                nodes[i].draw();

                for (let j = i + 1; j < nodes.length; j++) {
                    const dx = nodes[i].x - nodes[j].x;
                    const dy = nodes[i].y - nodes[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < 130) {
                        ctx.beginPath();
                        ctx.moveTo(nodes[i].x, nodes[i].y);
                        ctx.lineTo(nodes[j].x, nodes[j].y);
                        ctx.strokeStyle = `rgba(59, 130, 246, ${1 - dist / 130})`;
                        ctx.lineWidth = 0.6;
                        ctx.stroke();
                    }
                }
            }
            requestAnimationFrame(animate);
        }

        initNodes();
        animate();
    </script>
</body>
</html>