<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Ceklis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-xl border border-gray-200">
            
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-extrabold text-blue-600">Checklist App</h2>
                <p class="text-sm text-gray-500 mt-2"></p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="text-sm text-red-700 list-disc pl-2 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div>
                    <label for="id_number" class="block font-semibold text-sm text-gray-700">User ID</label>
                    <input id="id_number" 
                        class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg shadow-sm py-2 px-3 border transition duration-150 ease-in-out" 
                        type="text" 
                        name="id_number" 
                        value="{{ old('id_number') }}" 
                        required 
                        autofocus 
                        placeholder="Enter your user ID">
                </div>

                <div class="mt-5">
                    <label for="password" class="block font-semibold text-sm text-gray-700">Password</label>
                    <input id="password" 
                        class="block mt-1 w-full border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 rounded-lg shadow-sm py-2 px-3 border transition duration-150 ease-in-out" 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="Enter your password">
                </div>

                <div class="flex items-center justify-end mt-8">
                    <button type="submit" 
                        class="w-full flex justify-center items-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-bold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 transition ease-in-out duration-150 shadow-md">
                        Sign In
                    </button>
                </div>
            </form>

        </div>
    </div>
</body>
</html>