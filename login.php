<?php
session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check for notifications from previous submission
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Login - Active Motion Kenya</title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;family=Playfair+Display:wght@700&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style type="text/tailwindcss">
        :root {
            --brand-primary: #1D4ED8;
            --brand-secondary: #3B82F6;
            --background-surface: #FSFFFFF;
            --background-base: #F9FAFB;
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --border-color: #D1D5DB;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-base);
            color: var(--text-primary);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.6s ease-out forwards;
        }
        .main-container {
            min-height: 100vh;
        }
        .left-panel {
            background-image: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
        }
        .left-panel-overlay {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.2));
        }
        .form-input {
            background-color: var(--background-surface);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 0.375rem;
            width: 100%;
            padding: 0.75rem 1rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9375rem;
            font-weight: 400;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .login-btn {
            background-color: var(--brand-primary);
            color: white;
            font-weight: 500;
            border-radius: 0.375rem;
            padding: 0.875rem;
            width: 100%;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            font-size: 1rem;
            letter-spacing: 0.025em;
        }
        .login-btn:hover {
            background-color: #1E40AF; 
            box-shadow: 0 10px 15px -3px rgba(29, 78, 216, 0.2), 0 4px 6px -4px rgba(29, 78, 216, 0.1);
            transform: translateY(-2px);
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }
        .divider:not(:empty)::before { margin-right: 1em; }
        .divider:not(:empty)::after { margin-left: 1em; }
        .social-btn {
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-weight: 500;
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background-color: var(--background-surface);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.9375rem;
        }
        .social-btn:hover {
            background-color: #F3F4F6;
            border-color: var(--brand-secondary);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .social-btn img { width: 22px; height: 22px; }
        .brand-link {
            color: var(--brand-primary);
            font-weight: 500;
            transition: color 0.2s ease-in-out, transform 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }
        .brand-link:hover {
            color: #1E40AF;
            text-decoration: underline;
            transform: translateY(-1px);
        }
        .title-font {
            font-family: 'Playfair Display', serif;
        }
        #auth-nav {
            background-color: var(--background-surface);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        #auth-nav .nav-link {
            position: relative;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }
        #auth-nav .nav-link:hover {
            color: var(--brand-primary);
        }
        #auth-nav .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 50%;
            background-color: var(--brand-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        #auth-nav .nav-link:hover::after {
            width: 100%;
        }

        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            max-width: 24rem;
            z-index: 50;
            transition: all 0.3s ease;
            transform: translateX(150%);
        }
        .notification.show {
            transform: translateX(0);
        }
        .notification.success {
            background-color: #D1FAE5;
            border-left: 4px solid #10B981;
            color: #065F46;
        }
        .notification.error {
            background-color: #FEE2E2;
            border-left: 4px solid #EF4444;
            color: #991B1B;
        }

    </style>
</head>
<body class="bg-gray-50">

    <!-- Notification System -->
    <?php if ($error): ?>
    <div id="error-notification" class="notification error show">
        <div class="flex justify-between items-start p-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
            <button onclick="document.getElementById('error-notification').classList.remove('show')" class="ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div id="success-notification" class="notification success show">
        <div class="flex justify-between items-start p-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
            <button onclick="document.getElementById('success-notification').classList.remove('show')" class="ml-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    <?php endif; ?>



<div class="main-container">
<header id="auth-nav">
<nav class="container mx-auto px-6 py-4 flex justify-between items-center">
<a class="flex items-center gap-2 group" href="#">
<svg class="w-8 h-8 text-[var(--brand-primary)] group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
<span class="text-xl font-bold text-[var(--text-primary)] title-font group-hover:text-[var(--brand-primary)] transition-colors duration-300">Active Motion Kenya</span>
</a>
<div class="hidden md:flex items-center space-x-8">
<a class="nav-link" href="#">Destinations</a>
<a class="nav-link" href="#">Tours</a>
<a class="nav-link" href="#">About Us</a>
<a class="nav-link" href="#">Contact</a>
</div>
<button class="md:hidden text-[var(--text-primary)] hover:text-[var(--brand-primary)] transition-colors duration-200">
<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path d="M4 6h16M4 12h16m-7 6h7" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
</button>
</nav>
</header>
<div class="grid lg:grid-cols-2 min-h-[calc(100vh-74px)]">
<div class="left-panel hidden lg:flex flex-col justify-end text-white p-16 relative">
<div class="left-panel-overlay absolute inset-0"></div>
<div class="relative z-10 animate-fadeIn">
<div class="mb-6">
<svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
<path d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" stroke-linecap="round" stroke-linejoin="round"></path>
</svg>
</div>
<h1 class="title-font text-5xl font-bold leading-tight mb-5">
                    Start Your Unforgettable Journey With Us.
                </h1>
<p class="text-xl text-gray-200 max-w-xl">
                    Discover breathtaking destinations and create timeless memories. Your next great adventure is just a login away.
                </p>
</div>
</div>
<div class="right-panel flex items-center justify-center bg-[var(--background-base)] p-6 sm:p-12">
<div class="w-full max-w-md animate-fadeIn" style="animation-delay: 200ms;">
<div class="mb-10 text-left">
<h2 class="title-font text-4xl font-bold text-[var(--text-primary)]">Welcome Back</h2>
<p class="text-[var(--text-secondary)] mt-3 text-base">Please enter your details to sign in.</p>
</div>
<form action="process_login.php" class="space-y-6" method="POST">
<div>
<label class="block text-sm font-medium text-[var(--text-secondary)] mb-2" for="email">Email Address</label>
<input class="form-input" id="email" name="email" placeholder="you@example.com" required="" type="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) :'';?>"/>
</div>
<div>
<label class="block text-sm font-medium text-[var(--text-secondary)] mb-2" for="password">Password</label>
<input class="form-input" id="password" name="password" placeholder="Enter your password" required="" type="password"/>
</div>
<div class="flex items-center justify-between text-sm">
<div class="flex items-center group">
<input class="h-4 w-4 rounded border-[var(--border-color)] text-[var(--brand-primary)] focus:ring-[var(--brand-secondary)] cursor-pointer" id="remember-me" name="remember-me" type="checkbox" <?php echo isset($_POST['remember-me']) ? 'checked' : ''; ?>/>
<label class="ml-2 block text-[var(--text-secondary)] group-hover:text-[var(--text-primary)] transition-colors duration-200 cursor-pointer" for="remember-me">Remember me</label>
</div>
<a class="brand-link text-sm" href="#">Forgot Password?</a>
</div>
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
<div>
<button class="login-btn" type="submit">Sign In</button>
</div>
</form>
<div class="my-8">
<div class="divider">or continue with</div>
</div>
<div>
<button class="social-btn">
<img alt="Google logo" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDevfcNJg0o3mWvPuBq16y1bFYYbZG9ebY0AJfBpKWXhTPVFP0TsRpTl90rTe1aOqBMWzIjXQmNyJbhsEYx2KMRGQceLAdYERy93FZzBHB7sP7lkVJ4XGlLT4hJcNcdZdmUmbKbyUKiF-NmNlicXWfXQ34JaS2-b2BzRlgI18B4w3n2UcCOM7YRzzwiNqvUhwistyFLTevBqpQtIBp6EY7G0W0pqR5_NVu3rg_1Uip-a-_FxSh6G39Jt85vCQVtS6PqVo_ICmXO9p-i"/>
                        Sign in with Google
                    </button>
</div>
<div class="mt-10 text-center">
<p class="text-sm text-[var(--text-secondary)]">
                        Don't have an account? 
                        <a class="brand-link" href="signup.html">Sign up now</a>
</p>
</div>
</div>
</div>
</div>
</div>

<script>
        setTimeout(() => {
        const notifications = document.querySelectorAll('.notification.show');
        notifications.forEach(notification => {
            notification.classList.remove('show');
        });
    }, 5000);
</script>

</body></html>