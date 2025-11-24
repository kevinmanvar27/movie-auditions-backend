<div id="loadingIndicator" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-theme-surface rounded-lg shadow-xl border border-theme-border p-6 flex flex-col items-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-theme-primary mb-4"></div>
        <p class="text-theme-text">Loading...</p>
    </div>
</div>

<script>
    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('hidden');
        document.getElementById('loadingIndicator').classList.add('flex');
    }
    
    function hideLoading() {
        document.getElementById('loadingIndicator').classList.remove('flex');
        document.getElementById('loadingIndicator').classList.add('hidden');
    }
    
    // Add loading indicator to all form submissions
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                showLoading();
            });
        });
        
        // Add loading indicator to all links with data-loading attribute
        const loadingLinks = document.querySelectorAll('a[data-loading]');
        loadingLinks.forEach(link => {
            link.addEventListener('click', function() {
                showLoading();
            });
        });
    });
</script>