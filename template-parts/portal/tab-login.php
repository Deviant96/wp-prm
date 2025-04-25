<?php if (is_user_logged_in()) : ?>
  <p class="text-green-600">You are already logged in.</p>
<?php else : ?>
  <?php if (isset($_GET['login']) && $_GET['login'] === 'failed') : ?>
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
      Login failed. Please check your credentials.
    </div>
  <?php endif; ?>

  <form action="<?php echo wp_login_url(home_url('/dashboard')); ?>" method="post" class="space-y-4 max-w-md">
    <div>
      <label for="user_login" class="block mb-1 font-medium">Username or Email</label>
      <input type="text" name="log" id="user_login" required class="w-full border px-3 py-2 rounded" />
    </div>

    <div>
      <label for="user_pass" class="block mb-1 font-medium">Password</label>
      <input type="password" name="pwd" id="user_pass" required class="w-full border px-3 py-2 rounded" />
    </div>

    <div class="flex items-center justify-between">
      <label class="flex items-center">
        <input type="checkbox" name="rememberme" value="forever" class="mr-2">
        Remember me
      </label>
      <a href="<?php echo wp_lostpassword_url(); ?>" class="text-sm text-blue-600 hover:underline">Lost password?</a>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Login
    </button>
  </form>
<?php endif; ?>