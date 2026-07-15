<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'SupplyChain ERP' }} · SupplyChain ERP</title>
    
    <!-- Tailwind CSS CDN -->
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
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-erp-bg">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-auto">
            <!-- Header -->
            @include('components.header')
            
            <!-- Content -->
            <main class="flex-1 overflow-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
