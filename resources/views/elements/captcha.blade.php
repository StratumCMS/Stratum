@if(setting('captcha.type') === 'recaptcha')
    @push('scripts')
        <script src="https://www.recaptcha.net/recaptcha/api.js?hl={{ app()->getLocale() }}" async defer></script>
    @endpush

    <div class="g-recaptcha {{ ($center ?? false) ? 'text-center' : '' }}"
         data-sitekey="{{ setting('captcha.site_key') }}"
         data-callback="submitCaptchaForm"
         data-size="invisible">
    </div>

    @push('footer-scripts')
        <script>
            document.getElementById('captcha-form')?.addEventListener('submit', function (e) {
                if (typeof grecaptcha !== 'undefined') {
                    e.preventDefault();
                    grecaptcha.execute();
                }
            });

            function submitCaptchaForm() {
                document.getElementById('captcha-form')?.submit();
            }
        </script>
    @endpush

@elseif(setting('captcha.type') === 'hcaptcha')
    @push('scripts')
        <script src="https://hcaptcha.com/1/api.js?hl={{ app()->getLocale() }}" async defer></script>
    @endpush

    <div class="h-captcha {{ ($center ?? false) ? 'text-center' : '' }}"
         data-sitekey="{{ setting('captcha.site_key') }}"
         data-theme="{{ ($dark ?? false) ? 'dark' : 'light' }}">
    </div>

@elseif(setting('captcha.type') === 'turnstile')
    @push('scripts')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush

    <div class="cf-turnstile {{ ($center ?? false) ? 'text-center' : '' }}"
         data-sitekey="{{ setting('captcha.site_key') }}"
         data-theme="{{ ($dark ?? false) ? 'dark' : 'light' }}">
    </div>
@endif
