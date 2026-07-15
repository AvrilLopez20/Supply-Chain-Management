<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · SupplyChain ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        erp: {
                            darkGreen: '#468C32',
                            lightGreen: '#94CA6B',
                            softYellow: '#E9DD8A',
                            warmOrange: '#F9A12F',
                            bg: '#F8FAFC',
                            card: '#FFFFFF',
                            text: '#1E293B',
                            textMuted: '#64748B',
                            border: '#E2E8F0',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen bg-erp-bg flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-erp-border p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-erp-text">SupplyChain ERP</h1>
            <p class="text-sm text-erp-textMuted mt-2">Sign in to continue</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-erp-text mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border border-erp-border px-3 py-2" placeholder="admin@example.com" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-erp-text mb-1">Password</label>
                <input type="password" name="password" class="w-full rounded-lg border border-erp-border px-3 py-2" placeholder="password" required>
            </div>
            <button type="submit" class="w-full rounded-lg bg-erp-darkGreen px-4 py-2 font-semibold text-white">Login</button>
        </form>
    </div>
</body>
</html>
