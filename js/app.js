document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('[data-theme-toggle]');

    const applyTheme = (theme) => {
        const dark = theme === 'dark';
        document.body.classList.toggle('dark-theme', dark);
        if (themeToggle) {
            themeToggle.textContent = dark ? 'Light mode' : 'Dark mode';
        }
    };

    const savedTheme = localStorage.getItem('lostlink-theme');
    applyTheme(
        savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
    );

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const nextTheme = document.body.classList.contains('dark-theme') ? 'light' : 'dark';
            localStorage.setItem('lostlink-theme', nextTheme);
            applyTheme(nextTheme);
        });
    }

    document.querySelectorAll('[data-filter-table]').forEach((input) => {
        input.addEventListener('input', () => {
            const query = input.value.toLowerCase().trim();
            const selector = input.dataset.filterTable + ' tbody tr';
            document.querySelectorAll(selector).forEach((row) => {
                row.hidden = !row.innerText.toLowerCase().includes(query);
            });
        });
    });

    const chat = document.querySelector('[data-chat]');
    if (chat) {
        const refresh = () => {
            fetch('fetch_messages.php?item_id=' + encodeURIComponent(chat.dataset.item))
                .then((r) => (r.ok ? r.text() : ''))
                .then((html) => {
                    if (html) {
                        chat.innerHTML = html;
                        chat.scrollTop = chat.scrollHeight;
                    }
                })
                .catch(() => {});
        };

        refresh();
        setInterval(refresh, 5000);
    }
});
