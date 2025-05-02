<?php if (isset($_GET['registration'])) : ?>
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50">
        <div class="<?php echo $_GET['registration'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?> border px-4 py-3 rounded relative shadow-lg max-w-md mx-auto" role="alert">
            <strong class="font-bold">
                <?php echo $_GET['registration'] === 'success' ? 'Success!' : 'Error!'; ?>
            </strong>
            <span class="block sm:inline">
                <?php
                if ($_GET['registration'] === 'success') {
                    echo 'Your request has been submitted successfully! We\'ll review your information and contact you shortly.';
                } else {
                    echo 'There was an error processing your request. Please try again or contact support.';
                }
                ?>
            </span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 <?php echo $_GET['registration'] === 'success' ? 'text-green-500' : 'text-red-500'; ?>" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                </svg>
            </span>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.querySelector('[role="alert"]').style.display = 'none';
        }, 5000);
    </script>
<?php endif; ?>