<?php
session_start();
if (!isset($_POST['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
error_log("[DEBUG] Token generation: " . $_SESSION['csrf_token']);
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Active Motion Kenya - Sign Up</title>
<link href="data:image/x-icon;base64," rel="icon" type="image/x-icon"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style type="text/tailwindcss">
        :root {
            --primary-color: #3b82f6;
            --background-color: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--background-color);
        }
    </style>
</head>
<body class="text-[var(--text-primary)]">
<div class="flex min-h-screen">
<div class="relative hidden w-0 flex-1 lg:block">
<div class="absolute inset-0 h-full w-full animate-pulse bg-gray-300"></div>
<img alt="Tropical beach scene" class="absolute inset-0 h-full w-full object-cover" loading="lazy" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD1dOUxmbW_IA6OozLIt0ugH3zakEmhefPQE-vc_q8VKUBLZ5TgrjxbjZWocaJSqCtn4oDlwxHwzp1rsUt-886nu-acJWfaLoGnpaMwR1btu8chAkCGt3c1K0BtJT4VIx3ls6pg-qYac7Ve9DWNuxc7RGqJKtZfM7vbi7OrkCxnGuKJAC7sztJfAxi2dlG7pByt0v4NSmQNhtEeUJnjlG6DZZG73nyiawFpx9AovLAyCM9AeUaRbEfziYpY3veTQ2TzrrZWmzake8eI"/>
<div class="absolute inset-0 bg-black/20"></div>
<div class="absolute top-8 left-8">
<a class="flex items-center gap-2 text-xl font-bold tracking-tighter text-white" href="#">
<svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
<path clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill="currentColor" fill-rule="evenodd"></path>
</svg>
<span>Active Motion Kenya</span>
</a>
</div>
<div class="absolute bottom-8 left-8 p-8">
<h3 class="text-3xl font-bold text-white shadow-lg">Start Your Journey</h3>
<p class="mt-2 max-w-md text-lg text-gray-200 shadow-lg">Create an account to explore breathtaking destinations and create unforgettable memories.</p>
</div>
</div>
<div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:px-20 xl:px-24">
<div class="absolute top-8 right-8">
<a class="flex items-center gap-2 rounded-md py-2 px-3 text-sm font-medium text-[var(--text-secondary)] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2" href="tours.html">
<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
<path clip-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" fill-rule="evenodd"></path>
</svg>
<span>Back to Home</span>
</a>
</div>
<div class="mx-auto w-full max-w-sm lg:w-96">
<div>
<h1 class="text-3xl font-extrabold tracking-tight">Create an Account</h1>
<p class="mt-2 text-base text-[var(--text-secondary)]">Let's get started on your next adventure!</p>
</div>
<div class="mt-8">



<form action="process_signup.php" class="space-y-6" method="POST">
<div>
<label class="block text-sm font-medium text-[var(--text-primary)]" for="first-name">First Name</label>
<div class="mt-1">
<input autocomplete="given-name" class="block w-full appearance-none rounded-md border border-[var(--border-color)] px-3 py-2 placeholder-[var(--text-secondary)] shadow-sm focus:border-[var(--primary-color)] focus:outline-none focus:ring-[var(--primary-color)] sm:text-sm" id="first-name" name="firstname" placeholder="John" required="" type="text"/>
</div>
</div>
<div>
<label class="block text-sm font-medium text-[var(--text-primary)]" for="last-name">Last Name</label>
<div class="mt-1">
<input autocomplete="family-name" class="block w-full appearance-none rounded-md border border-[var(--border-color)] px-3 py-2 placeholder-[var(--text-secondary)] shadow-sm focus:border-[var(--primary-color)] focus:outline-none focus:ring-[var(--primary-color)] sm:text-sm" id="last-name" name="lastname" placeholder="Doe" required="" type="text"/>
</div>
</div>
<div>
<label class="block text-sm font-medium text-[var(--text-primary)]" for="email">Email address</label>
<div class="mt-1">
<input autocomplete="email" class="block w-full appearance-none rounded-md border border-[var(--border-color)] px-3 py-2 placeholder-[var(--text-secondary)] shadow-sm focus:border-[var(--primary-color)] focus:outline-none focus:ring-[var(--primary-color)] sm:text-sm" id="email" name="email" placeholder="you@example.com" required="" type="email"/>
</div>
</div>
<div>
<label class="block text-sm font-medium text-[var(--text-primary)]" for="password">Password</label>
<div class="mt-1">
<input autocomplete="new-password" class="block w-full appearance-none rounded-md border border-[var(--border-color)] px-3 py-2 placeholder-[var(--text-secondary)] shadow-sm focus:border-[var(--primary-color)] focus:outline-none focus:ring-[var(--primary-color)] sm:text-sm" id="password" name="password" placeholder="••••••••" required="" type="password"/>
</div>
</div>
<div>
<label class="block text-sm font-medium text-[var(--text-primary)]" for="confirm-password">Confirm Password</label>
<div class="mt-1">
<input autocomplete="new-password" class="block w-full appearance-none rounded-md border border-[var(--border-color)] px-3 py-2 placeholder-[var(--text-secondary)] shadow-sm focus:border-[var(--primary-color)] focus:outline-none focus:ring-[var(--primary-color)] sm:text-sm" id="confirm-password" name="confirm-password" placeholder="••••••••" required="" type="password"/>
</div>
</div>
<div class="flex items-center">
<input class="h-4 w-4 rounded border-gray-300 text-[var(--primary-color)] focus:ring-[var(--primary-color)]" id="terms-and-privacy" name="terms-and-privacy" required="" type="checkbox"/>
<label class="ml-2 block text-sm text-[var(--text-secondary)]" for="terms-and-privacy">I agree to the
                                <a class="font-medium text-[var(--primary-color)] hover:text-blue-500" href="#">Terms</a> and
                                <a class="font-medium text-[var(--primary-color)] hover:text-blue-500" href="#">Privacy Policy</a>.
                            </label>
</div>

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

<div>
<button class="flex w-full justify-center rounded-md border border-transparent bg-[var(--primary-color)] px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" type="submit">Create Account</button>
</div>
</form>


<div class="relative my-6">
<div aria-hidden="true" class="absolute inset-0 flex items-center">
<div class="w-full border-t border-[var(--border-color)]"></div>
</div>
<div class="relative flex justify-center text-sm">
<span class="bg-[var(--background-color)] px-2 text-[var(--text-secondary)]">Or sign up with</span>
</div>
</div>
<div>
<a class="inline-flex w-full justify-center items-center rounded-md border border-[var(--border-color)] bg-white px-4 py-2.5 text-sm font-medium text-[var(--text-secondary)] shadow-sm hover:bg-gray-50" href="#">
<svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
<path d="M22.001 12.002c0-.82-.07-1.62-.21-2.4H12v4.51h5.6c-.24 1.48-1.04 2.75-2.29 3.61v3.01h3.86c2.25-2.08 3.54-5.11 3.54-8.73z"></path>
<path d="M12 22.002c3.27 0 6.02-1.08 8.02-2.9l-3.86-3.01c-1.08.72-2.45 1.15-4.16 1.15-3.19 0-5.89-2.15-6.86-5.03H1.23v3.1C3.25 19.34 7.27 22.002 12 22.002z"></path>
<path d="M5.14 14.23c-.2-.6-.31-1.24-.31-1.9s.11-1.3.31-1.9V7.32H1.23C.45 8.8.001 10.36.001 12.002s.45 3.2 1.23 4.68l3.91-3.13v.001z"></path>
<path d="M12 5.162c1.78 0 3.34.62 4.59 1.81l3.43-3.43C18.02 1.95 15.27 0 12 0 7.27 0 3.25 2.66 1.23 6.69l3.91 3.12c.97-2.88 3.67-5.03 6.86-5.03z"></path>
</svg>
<span class="ml-3">Continue with Google</span>
</a>
</div>
<p class="mt-8 text-center text-sm text-[var(--text-secondary)]">
                        Already have an account?
                        <a class="font-medium text-[var(--primary-color)] hover:text-blue-500" href="login.html">Log in</a>
</p>
</div>
</div>
</div>
</div>

</body></html>