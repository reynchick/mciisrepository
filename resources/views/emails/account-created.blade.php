@component('mail::message')
# Welcome to Research Repository

Hello {{ $user->first_name }},

Your account has been created successfully! We're excited to have you on board.

**You have been assigned the following role(s):**

{{ implode(' | ', $rolesWithEmojis) }}

To get started, please log in to your account using your USeP email address. You'll authenticate via Google SSO for security.

@component('mail::button', ['url' => route('login')])
Login to Your Account
@endcomponent

@if($user->hasRole('Faculty') || $user->hasRole('Student'))

---

**Important:** After logging in for the first time, you'll need to complete your profile before you can access the full system. This helps us ensure we have accurate information about you.

@endif

If you have any questions or need assistance, please don't hesitate to reach out to our support team.

Thank you for being part of the Research Repository community!

Best regards,<br>
{{ config('app.name') }} Team
@endcomponent
