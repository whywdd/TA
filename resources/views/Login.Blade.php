<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to bottom, #00c6ff, #0072ff, #ff00ff, #ff00ff);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-80">
        <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
        <form>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-600" for="username">
                    <i class="fas fa-user"></i> Type your username
                </label>
                <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" id="username" placeholder="Type your username">
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-600" for="password">
                    <i class="fas fa-lock"></i> Type your password
                </label>
                <input class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" id="password" placeholder="Type your password">
            </div>
            <div class="text-right mb-4">
                <a href="#" class="text-sm text-gray-600 hover:underline">Forgot password?</a>
            </div>
            <button type="submit" formaction="{{ route('home') }}" class="w-full py-2 mb-4 text-white bg-gradient-to-r from-teal-400 to-pink-500 rounded-lg hover:from-teal-500 hover:to-pink-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">LOGIN</button>
        </form>
        <div class="text-center text-sm text-gray-600 mb-4">Or Sign Up Using</div>
        <div class="flex justify-center space-x-4">
            <a href="#" class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center text-white">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white">
                <i class="fab fa-google"></i>
            </a>
            <a href="#" class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center text-white">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>
</body>
</html>