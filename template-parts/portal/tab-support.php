<?php if (isset($_GET['support_status'])): ?>
    <div
        class="mb-4 px-4 py-2 rounded 
                <?php echo $_GET['support_status'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
        <?php echo $_GET['support_status'] === 'success' ? 'Support request sent successfully!' : 'Please fill in all fields.'; ?>
    </div>
<?php endif; ?>
<p>If you have any issues, please fill out the form below:</p>
<form method="post" class="space-y-4">
    <input type="text" name="support_name" placeholder="Your Name" required class="w-full px-4 py-2 border rounded" />
    <input type="email" name="support_email" placeholder="Your Email" required
        class="w-full px-4 py-2 border rounded" />
    <textarea name="support_message" placeholder="Describe your issue" required
        class="w-full px-4 py-2 border rounded"></textarea>
    <button type="submit" name="submit_support" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Send
        Request</button>
</form>