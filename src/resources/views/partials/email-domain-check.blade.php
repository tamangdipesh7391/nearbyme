{{-- Instant feedback for the sign up email field. The server enforces the same rule (App\Rules\AllowedEmailDomain). --}}
<script>
    (function () {
        var allowed = @json(\App\Rules\AllowedEmailDomain::allowedDomains());

        function levenshtein(a, b) {
            var prev = [], cur, i, j;
            for (j = 0; j <= b.length; j++) prev[j] = j;
            for (i = 1; i <= a.length; i++) {
                cur = [i];
                for (j = 1; j <= b.length; j++) {
                    cur[j] = Math.min(prev[j] + 1, cur[j - 1] + 1, prev[j - 1] + (a[i - 1] === b[j - 1] ? 0 : 1));
                }
                prev = cur;
            }
            return prev[b.length];
        }

        function suggest(domain) {
            var withoutDigits = domain.replace(/\d+/g, '');
            if (allowed.indexOf(withoutDigits) !== -1) return withoutDigits;
            var best = null, bestDistance = 3;
            allowed.forEach(function (candidate) {
                var d = levenshtein(domain, candidate);
                if (d < bestDistance) { best = candidate; bestDistance = d; }
            });
            return best;
        }

        function errorFor(value) {
            value = value.trim();
            if (value === '') return 'Please enter your email address.';
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Please enter a valid email address, e.g. name@gmail.com.';
            var domain = value.split('@').pop().toLowerCase();
            if (allowed.indexOf(domain) !== -1) return '';
            var message = 'The email domain "@' + domain + '" is not supported.';
            var suggestion = suggest(domain);
            if (suggestion) message += ' Did you mean "@' + suggestion + '"?';
            return message + ' Please use a standard email provider such as Gmail, Outlook, Hotmail, Yahoo or iCloud.';
        }

        document.querySelectorAll('input[name="email"]').forEach(function (input) {
            var form = input.form;
            var feedback = input.parentNode.querySelector('.email-feedback');

            function check() {
                var message = errorFor(input.value);
                if (feedback) feedback.textContent = message;
                input.classList.toggle('is-invalid', message !== '');
                return message === '';
            }

            input.addEventListener('blur', check);
            input.addEventListener('input', function () {
                if (input.classList.contains('is-invalid')) check();
            });
            if (form) {
                form.addEventListener('submit', function (e) {
                    if (!check()) {
                        e.preventDefault();
                        input.focus();
                    }
                });
            }
        });
    })();
</script>
